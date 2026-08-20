import 'package:get/get.dart';

class MedicineModel {
  final String name;
  final String dosage; // e.g. 500mg
  final String frequency; // e.g. 1-0-1 after meal
  final String duration; // e.g. 5 days

  MedicineModel({
    required this.name,
    required this.dosage,
    required this.frequency,
    required this.duration,
  });
}

class PrescriptionModel {
  final String id;
  final String doctorName;
  final String date;
  final String diagnosis;
  final List<MedicineModel> medicines;

  PrescriptionModel({
    required this.id,
    required this.doctorName,
    required this.date,
    required this.diagnosis,
    required this.medicines,
  });
}

class PrescriptionsController extends GetxController {
  final prescriptions = <PrescriptionModel>[
    PrescriptionModel(
      id: 'RX-9921',
      doctorName: 'Dr. Alex Smith',
      date: '15 Aug 2026',
      diagnosis: 'Tooth Sensitivity & Gum Inflammation',
      medicines: [
        MedicineModel(name: 'Amoxicillin', dosage: '500mg', frequency: '1-0-1 (After Meal)', duration: '5 Days'),
        MedicineModel(name: 'Ibuprofen', dosage: '400mg', frequency: '1-0-1 (Pain Relief)', duration: '3 Days'),
        MedicineModel(name: 'Sensodyne Mouthwash', dosage: '15ml', frequency: '0-0-1 (Night)', duration: '14 Days'),
      ],
    ),
    PrescriptionModel(
      id: 'RX-8840',
      doctorName: 'Dr. Emma Watson',
      date: '02 Jun 2026',
      diagnosis: 'Post Root Canal Recovery',
      medicines: [
        MedicineModel(name: 'Paracetamol', dosage: '650mg', frequency: '1-1-1 (As Needed)', duration: '4 Days'),
        MedicineModel(name: 'Chlorhexidine Gel', dosage: 'Topical', frequency: 'Apply twice daily', duration: '7 Days'),
      ],
    ),
  ].obs;
}
