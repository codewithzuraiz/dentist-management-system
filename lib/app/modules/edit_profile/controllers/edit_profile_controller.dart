import 'package:flutter/material.dart';
import 'package:get/get.dart';

class EditProfileController extends GetxController {
  final nameController = TextEditingController(text: 'Sarah Johnson');
  final emailController = TextEditingController(text: 'sarah.johnson@example.com');
  final phoneController = TextEditingController(text: '+1 (555) 234-5678');
  final dobController = TextEditingController(text: '14/08/1995');
  final bloodGroupController = TextEditingController(text: 'O+');
  final allergiesController = TextEditingController(text: 'Penicillin');

  final isNotificationsEnabled = true.obs;

  void saveProfile() {
    Get.snackbar(
      'Profile Updated',
      'Your personal details and medical notes have been saved.',
      snackPosition: SnackPosition.BOTTOM,
      backgroundColor: const Color(0xFF329D9C),
      colorText: Colors.white,
    );
    Get.back();
  }

  @override
  void onClose() {
    nameController.dispose();
    emailController.dispose();
    phoneController.dispose();
    dobController.dispose();
    bloodGroupController.dispose();
    allergiesController.dispose();
    super.onClose();
  }
}
