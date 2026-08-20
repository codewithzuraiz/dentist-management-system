enum AppointmentStatus {
  upcoming,
  completed,
  cancelled,
}

class Appointment {
  final String id;
  final String patientName;
  final String treatment;
  final String doctorName;
  final String time;
  final String date;
  final AppointmentStatus status;
  final String initials;
  final String? notes;

  Appointment({
    required this.id,
    required this.patientName,
    required this.treatment,
    required this.doctorName,
    required this.time,
    required this.date,
    required this.status,
    required this.initials,
    this.notes,
  });

  Appointment copyWith({
    String? id,
    String? patientName,
    String? treatment,
    String? doctorName,
    String? time,
    String? date,
    AppointmentStatus? status,
    String? initials,
    String? notes,
  }) {
    return Appointment(
      id: id ?? this.id,
      patientName: patientName ?? this.patientName,
      treatment: treatment ?? this.treatment,
      doctorName: doctorName ?? this.doctorName,
      time: time ?? this.time,
      date: date ?? this.date,
      status: status ?? this.status,
      initials: initials ?? this.initials,
      notes: notes ?? this.notes,
    );
  }

  static String getInitials(String name) {
    List<String> parts = name.trim().split(' ');
    if (parts.length >= 2) {
      return '${parts[0][0]}${parts[1][0]}'.toUpperCase();
    } else if (parts.isNotEmpty && parts[0].isNotEmpty) {
      return parts[0].substring(0, parts[0].length >= 2 ? 2 : 1).toUpperCase();
    }
    return 'PT';
  }

  static List<Appointment> getInitialSampleAppointments() {
    return [
      Appointment(
        id: '1',
        patientName: 'John Smith',
        treatment: 'Teeth Cleaning',
        doctorName: 'Dr. Ahmed',
        time: '09:00 AM',
        date: '20 May',
        status: AppointmentStatus.upcoming,
        initials: 'JS',
      ),
      Appointment(
        id: '2',
        patientName: 'Emma Johnson',
        treatment: 'Dental Filling',
        doctorName: 'Dr. Ahmed',
        time: '10:30 AM',
        date: '20 May',
        status: AppointmentStatus.upcoming,
        initials: 'EM',
      ),
      Appointment(
        id: '3',
        patientName: 'Robert Williams',
        treatment: 'Tooth Extraction',
        doctorName: 'Dr. Ahmed',
        time: '12:00 PM',
        date: '20 May',
        status: AppointmentStatus.upcoming,
        initials: 'RW',
      ),
      Appointment(
        id: '4',
        patientName: 'Sarah Lee',
        treatment: 'Orthodontic Consultation',
        doctorName: 'Dr. Ahmed',
        time: '02:00 PM',
        date: '20 May',
        status: AppointmentStatus.upcoming,
        initials: 'SL',
      ),
      Appointment(
        id: '5',
        patientName: 'Michael Chen',
        treatment: 'Teeth Whitening',
        doctorName: 'Dr. Ahmed',
        time: '08:00 AM',
        date: '20 May',
        status: AppointmentStatus.completed,
        initials: 'MC',
      ),
      Appointment(
        id: '6',
        patientName: 'Ali Raza',
        treatment: 'Crown Placement',
        doctorName: 'Dr. Ahmed',
        time: '03:30 PM',
        date: '20 May',
        status: AppointmentStatus.cancelled,
        initials: 'AR',
      ),
    ];
  }
}
