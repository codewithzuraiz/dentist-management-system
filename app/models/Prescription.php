<?php
class Prescription extends Model {
    protected $table = 'prescriptions';
    protected $fillable = [
        'prescription_number', 'patient_id', 'dentist_id', 'appointment_id',
        'issued_date', 'expiry_date', 'notes', 'status'
    ];
    protected $searchable = ['prescription_number', 'notes'];
    protected $filterable = ['status', 'issued_date'];

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

    public function getMedicines() {
        $sql = "SELECT * FROM prescription_medicines WHERE prescription_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->id]);
        return $stmt->fetchAll();
    }
}
