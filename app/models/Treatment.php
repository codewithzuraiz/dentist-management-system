<?php
class Treatment extends Model {
    protected $table = 'treatments';
    protected $fillable = ['name', 'description', 'cost', 'duration_minutes', 'required_equipment', 'status'];
    protected $searchable = ['name', 'description'];
    protected $filterable = ['status', 'cost'];

    public function getInvoices($limit = 10) {
        $sql = "SELECT i.* FROM invoices i JOIN invoice_items ii ON i.id = ii.invoice_id WHERE ii.treatment_id = ? ORDER BY i.created_at DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->id, $limit]);
        return $stmt->fetchAll();
    }
}
