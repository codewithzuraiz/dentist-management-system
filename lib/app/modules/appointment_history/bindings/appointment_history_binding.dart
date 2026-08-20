import 'package:get/get.dart';
import '../controllers/appointment_history_controller.dart';

class AppointmentHistoryBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<AppointmentHistoryController>(
      () => AppointmentHistoryController(),
    );
  }
}
