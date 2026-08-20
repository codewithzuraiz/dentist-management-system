enum PatientStatus {
  active,
  inactive,
  newPatient,
}

class Patient {
  final String id;
  final String name;
  final String phone;
  final String email;
  final String gender;
  final int age;
  final String lastVisit;
  final PatientStatus status;
  final String initials;
  final String? medicalHistory;

  Patient({
    required this.id,
    required this.name,
    required this.phone,
    required this.email,
    required this.gender,
    required this.age,
    required this.lastVisit,
    required this.status,
    required this.initials,
    this.medicalHistory,
  });

  Patient copyWith({
    String? id,
    String? name,
    String? phone,
    String? email,
    String? gender,
    int? age,
    String? lastVisit,
    PatientStatus? status,
    String? initials,
    String? medicalHistory,
  }) {
    return Patient(
      id: id ?? this.id,
      name: name ?? this.name,
      phone: phone ?? this.phone,
      email: email ?? this.email,
      gender: gender ?? this.gender,
      age: age ?? this.age,
      lastVisit: lastVisit ?? this.lastVisit,
      status: status ?? this.status,
      initials: initials ?? this.initials,
      medicalHistory: medicalHistory ?? this.medicalHistory,
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

  static List<Patient> getInitialSamplePatients() {
    return [
      Patient(
        id: 'p1',
        name: 'John Smith',
        phone: '+1 (555) 123-4567',
        email: 'john.smith@gmail.com',
        gender: 'Male',
        age: 34,
        lastVisit: '18 May 2024',
        status: PatientStatus.active,
        initials: 'JS',
        medicalHistory: 'No major allergies. Routine dental checkups.',
      ),
      Patient(
        id: 'p2',
        name: 'Emma Johnson',
        phone: '+1 (555) 234-5678',
        email: 'emma.j@outlook.com',
        gender: 'Female',
        age: 28,
        lastVisit: '16 May 2024',
        status: PatientStatus.active,
        initials: 'EM',
        medicalHistory: 'Sensitive teeth. Amoxicillin allergy.',
      ),
      Patient(
        id: 'p3',
        name: 'Robert Williams',
        phone: '+1 (555) 345-6789',
        email: 'robert.williams@yahoo.com',
        gender: 'Male',
        age: 45,
        lastVisit: '14 May 2024',
        status: PatientStatus.active,
        initials: 'RW',
        medicalHistory: 'History of gum disease. Tooth extraction done.',
      ),
      Patient(
        id: 'p4',
        name: 'Sarah Lee',
        phone: '+1 (555) 456-7890',
        email: 'sarah.lee@gmail.com',
        gender: 'Female',
        age: 22,
        lastVisit: '12 May 2024',
        status: PatientStatus.active,
        initials: 'SL',
        medicalHistory: 'Orthodontic braces treatment ongoing.',
      ),
      Patient(
        id: 'p5',
        name: 'Michael Chen',
        phone: '+1 (555) 567-8901',
        email: 'm.chen@techcorp.com',
        gender: 'Male',
        age: 38,
        lastVisit: '10 May 2024',
        status: PatientStatus.active,
        initials: 'MC',
        medicalHistory: 'Teeth whitening completed.',
      ),
      Patient(
        id: 'p6',
        name: 'Amanda Davis',
        phone: '+1 (555) 678-9012',
        email: 'amanda.davis@hotmail.com',
        gender: 'Female',
        age: 51,
        lastVisit: '08 May 2024',
        status: PatientStatus.inactive,
        initials: 'AD',
        medicalHistory: 'Dental bridge treatment 2 years ago.',
      ),
      Patient(
        id: 'p7',
        name: 'David Miller',
        phone: '+1 (555) 789-0123',
        email: 'david.m@company.org',
        gender: 'Male',
        age: 29,
        lastVisit: '05 May 2024',
        status: PatientStatus.newPatient,
        initials: 'DM',
        medicalHistory: 'First consultation scheduled.',
      ),
    ];
  }
}
