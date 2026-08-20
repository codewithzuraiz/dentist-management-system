import 'package:get/get.dart';
import '../../appointment_history/controllers/appointment_history_controller.dart';
import '../../medical_records/controllers/medical_records_controller.dart';
import '../controllers/dashboard_controller.dart';

class DashboardBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<DashboardController>(
      () => DashboardController(),
    );
    Get.lazyPut<AppointmentHistoryController>(
      () => AppointmentHistoryController(),
    );
    Get.lazyPut<MedicalRecordsController>(
      () => MedicalRecordsController(),
    );
  }
}
