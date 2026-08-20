import 'package:flutter/material.dart';
import 'package:get/get.dart';

class ForgotPasswordController extends GetxController {
  final emailController = TextEditingController();

  void sendResetLink() {
    final email = emailController.text.trim();
    if (email.isEmpty) {
      Get.snackbar(
        'Email Required',
        'Please enter your registered email address.',
        snackPosition: SnackPosition.BOTTOM,
        backgroundColor: Colors.redAccent.withValues(alpha: 0.9),
        colorText: Colors.white,
        margin: const EdgeInsets.all(16),
      );
      return;
    }

    Get.snackbar(
      'Reset Link Sent',
      'Password reset instructions have been sent to $email.',
      snackPosition: SnackPosition.BOTTOM,
      backgroundColor: const Color(0xFF329D9C),
      colorText: Colors.white,
      duration: const Duration(seconds: 4),
      margin: const EdgeInsets.all(16),
    );

    Get.back();
  }

  void goToLogin() {
    Get.back();
  }

  @override
  void onClose() {
    emailController.dispose();
    super.onClose();
  }
}
