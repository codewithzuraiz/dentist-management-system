import 'package:get/get.dart';
import '../controllers/find_dentist_controller.dart';

class FindDentistBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<FindDentistController>(
      () => FindDentistController(),
    );
  }
}
