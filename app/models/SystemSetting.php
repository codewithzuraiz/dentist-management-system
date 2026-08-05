<?php
class SystemSetting extends Model {
    protected $table = 'system_settings';
    protected $fillable = [
        'clinic_name', 'clinic_logo', 'clinic_address', 'clinic_phone',
        'clinic_email', 'clinic_website', 'currency', 'timezone',
        'tax_rate', 'appointment_slot_duration', 'max_daily_appointments',
        'appointment_reminder_hours', 'email_enabled', 'sms_enabled',
        'whatsapp_enabled'
    ];

    public static function getInstance() {
        static $instance = null;

        if ($instance === null) {
            $database = new Database();
            $instance = new SystemSetting($database);
        }

        return $instance;
    }

    public function getSetting($key) {
        $sql = "SELECT * FROM system_settings WHERE ? IN (clinic_name, clinic_logo, clinic_address, clinic_phone, clinic_email, clinic_website, currency, timezone, tax_rate, appointment_slot_duration, max_daily_appointments, appointment_reminder_hours, email_enabled, sms_enabled, whatsapp_enabled)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $settings = $stmt->fetchAll();

        $settingMap = [];
        foreach ($settings as $setting) {
            $settingMap[$setting['key']] = $setting['value'];
        }

        return $settingMap[$key] ?? null;
    }

    public function updateSetting($key, $value) {
        $allowedColumns = [
            'clinic_name', 'clinic_logo', 'clinic_address', 'clinic_phone',
            'clinic_email', 'clinic_website', 'currency', 'timezone',
            'tax_rate', 'appointment_slot_duration', 'max_daily_appointments',
            'appointment_reminder_hours', 'email_enabled', 'sms_enabled',
            'whatsapp_enabled'
        ];

        if (!in_array($key, $allowedColumns)) {
            throw new Exception("Invalid setting key: $key");
        }

        $this->update(1, [$key => $value]);
    }
}
