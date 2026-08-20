import 'package:get/get.dart';
import '../controllers/treatment_progress_controller.dart';

class TreatmentProgressBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<TreatmentProgressController>(
      () => TreatmentProgressController(),
    );
  }
}
