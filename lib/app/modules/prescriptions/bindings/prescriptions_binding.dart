import 'package:get/get.dart';
import '../controllers/prescriptions_controller.dart';

class PrescriptionsBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<PrescriptionsController>(
      () => PrescriptionsController(),
    );
  }
}
