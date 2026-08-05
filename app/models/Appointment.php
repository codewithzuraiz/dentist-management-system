<?php
class Appointment extends Model {
    protected $table = 'appointments';
    protected $fillable = [
        'patient_id', 'dentist_id', 'treatment_id', 'appointment_date',
        'start_time', 'end_time', 'status', 'notes', 'created_by'
    ];
    protected $searchable = ['notes'];
    protected $filterable = ['status', 'appointment_date', 'dentist_id', 'treatment_id'];

    public function getPatient() {
        $sql = "SELECT p.* FROM patients p WHERE p.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->patient_id]);
        return $stmt->fetch();
    }

    public function getDentist() {
        $sql = "SELECT d.* FROM dentists d WHERE d.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->dentist_id]);
        return $stmt->fetch();
    }

    public function getTreatment() {
        $sql = "SELECT * FROM treatments WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->treatment_id]);
        return $stmt->fetch();
    }

    public function getCreatedBy() {
        $sql = "SELECT u.* FROM users u WHERE u.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->created_by]);
        return $stmt->fetch();
    }

    public function canBeBooked() {
        $sql = "SELECT COUNT(*) as conflict_count FROM appointments WHERE dentist_id = ? AND appointment_date = ? AND status IN ('pending', 'confirmed') AND (
            (start_time <= ? AND end_time >= ?) OR
            (start_time <= ? AND end_time >= ?) OR
            (start_time <= ? AND end_time >= ?)
        )";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $this->dentist_id,
            $this->appointment_date,
            $this->end_time, $this->start_time,
            $this->start_time, $this->end_time,
            $this->start_time, $this->end_time
        ]);
        $result = $stmt->fetch();
        return $result['conflict_count'] == 0;
    }

    public function checkWorkingHours() {
        $sql = "SELECT working_start_time, working_end_time, break_start_time, break_end_time FROM dentists WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->dentist_id]);
        $dentist = $stmt->fetch();

        $workingStart = new DateTime($dentist['working_start_time']);
        $workingEnd = new DateTime($dentist['working_end_time']);
        $breakStart = new DateTime($dentist['break_start_time']);
        $breakEnd = new DateTime($dentist['break_end_time']);

        $startTime = new DateTime($this->start_time);
        $endTime = new DateTime($this->end_time);

        if ($startTime < $workingStart || $endTime > $workingEnd) {
            return false, 'Appointment outside working hours';
        }

        if ($breakStart && $breakEnd) {
            if ($startTime >= $breakStart && $endTime <= $breakEnd) {
                return false, 'Appointment during break time';
            }
        }

        return true, 'Appointment time is valid';
    }
}
