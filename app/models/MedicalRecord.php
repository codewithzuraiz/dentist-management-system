<?php
class MedicalRecord extends Model {
    protected $table = 'medical_records';
    protected $fillable = [
        'patient_id', 'dentist_id', 'appointment_id', 'record_type', 'title',
        'description', 'file_path', 'file_type', 'file_size', 'uploaded_by'
    ];
    protected $searchable = ['title', 'description'];
    protected $filterable = ['record_type', 'created_at'];

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

    public function getUploadedBy() {
        $sql = "SELECT u.* FROM users u WHERE u.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->uploaded_by]);
        return $stmt->fetch();
    }
}
