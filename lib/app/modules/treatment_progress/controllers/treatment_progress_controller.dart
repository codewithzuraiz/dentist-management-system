import 'package:get/get.dart';

class TreatmentStage {
  final String title;
  final String date;
  final String status; // Completed, Active, Pending
  final String description;

  TreatmentStage({
    required this.title,
    required this.date,
    required this.status,
    required this.description,
  });
}

class TreatmentProgressController extends GetxController {
  final treatmentPlanName = 'Invisalign Teeth Alignment'.obs;
  final doctorName = 'Dr. Alex Smith'.obs;
  final overallProgressPercent = 0.65.obs; // 65%

  final stages = <TreatmentStage>[
    TreatmentStage(
      title: 'Initial 3D Scan & Consultation',
      date: '10 Jan 2026',
      status: 'Completed',
      description: 'Digital 3D jaw scan taken and customized alignment tray plan created.',
    ),
    TreatmentStage(
      title: 'Tray Set 1 - 4 Placement',
      date: '15 Mar 2026',
      status: 'Completed',
      description: 'First phase alignment trays delivered and verified.',
    ),
    TreatmentStage(
      title: 'Mid-Treatment Review & Tray Set 5 - 8',
      date: '15 Jul 2026',
      status: 'Active',
      description: 'Teeth gap closing as expected. Current active phase.',
    ),
    TreatmentStage(
      title: 'Final Alignment & Retainer Fitting',
      date: '15 Dec 2026',
      status: 'Pending',
      description: 'Final teeth polish and permanent retainer fitting.',
    ),
  ].obs;
}
