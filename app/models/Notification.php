<?php
class Notification extends Model {
    protected $table = 'notifications';
    protected $fillable = [
        'user_id', 'type', 'title', 'message', 'related_entity_type',
        'related_entity_id', 'is_read', 'sent', 'sent_at'
    ];
    protected $searchable = ['title', 'message'];
    protected $filterable = ['is_read', 'sent', 'type'];

    public function getUser() {
        $sql = "SELECT u.* FROM users u WHERE u.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->user_id]);
        return $stmt->fetch();
    }

    public function markAsRead() {
        $this->update($this->id, ['is_read' => 'yes']);
    }

    public function send() {
        // In a real implementation, this would send the notification
        // through email, SMS, push notification, or in-app notification
        $this->update($this->id, ['sent' => 'yes', 'sent_at' => date('Y-m-d H:i:s')]);
    }
}
