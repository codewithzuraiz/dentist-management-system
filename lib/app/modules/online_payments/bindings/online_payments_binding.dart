import 'package:get/get.dart';
import '../controllers/online_payments_controller.dart';

class OnlinePaymentsBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<OnlinePaymentsController>(
      () => OnlinePaymentsController(),
    );
  }
}
