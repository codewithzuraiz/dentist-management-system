import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../../../routes/app_routes.dart';

class LoginController extends GetxController {
  final emailController = TextEditingController();
  final passwordController = TextEditingController();
  final isPasswordHidden = true.obs;

  void togglePasswordVisibility() {
    isPasswordHidden.value = !isPasswordHidden.value;
  }

  void login() {
    final email = emailController.text.trim();
    final password = passwordController.text;

    if (email.isEmpty || password.isEmpty) {
      Get.snackbar(
        'Required Fields',
        'Please enter both email and password.',
        snackPosition: SnackPosition.BOTTOM,
        backgroundColor: Colors.redAccent.withValues(alpha: 0.9),
        colorText: Colors.white,
        margin: const EdgeInsets.all(16),
      );
      return;
    }

    Get.snackbar(
      'Welcome Back!',
      'Login successful.',
      snackPosition: SnackPosition.BOTTOM,
      backgroundColor: const Color(0xFF329D9C),
      colorText: Colors.white,
      margin: const EdgeInsets.all(16),
    );

    Get.offAllNamed(Routes.dashboard);
  }

  void goToRegister() {
    Get.toNamed(Routes.register);
  }

  void goToForgotPassword() {
    Get.toNamed(Routes.forgotPassword);
  }

  @override
  void onClose() {
    emailController.dispose();
    passwordController.dispose();
    super.onClose();
  }
}
