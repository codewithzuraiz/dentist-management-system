import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../../../core/values/app_colors.dart';
import '../controllers/medical_records_controller.dart';

class MedicalRecordsView extends GetView<MedicalRecordsController> {
  const MedicalRecordsView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0.5,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new, color: AppColors.textPrimary, size: 20),
          onPressed: () => Get.back(),
        ),
        title: const Text(
          'Medical Records & X-Rays',
          style: TextStyle(color: AppColors.textPrimary, fontWeight: FontWeight.bold),
        ),
      ),
      body: Obx(() {
        if (controller.records.isEmpty) {
          return const Center(child: Text('No records uploaded yet.'));
        }

        return ListView.separated(
          padding: const EdgeInsets.all(16),
          itemCount: controller.records.length,
          separatorBuilder: (context, index) => const SizedBox(height: 14),
          itemBuilder: (context, index) {
            final record = controller.records[index];
            return Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(20),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.04),
                    blurRadius: 16,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: AppColors.primaryLight,
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: Icon(_getRecordIcon(record.type), color: AppColors.primary, size: 28),
                  ),
                  const SizedBox(width: 14),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          record.title,
                          style: const TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: AppColors.textPrimary),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          '${record.doctorName} • ${record.date}',
                          style: const TextStyle(fontSize: 12, color: AppColors.textSecondary),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          '${record.type} • ${record.fileSize}',
                          style: const TextStyle(fontSize: 11, color: AppColors.primary, fontWeight: FontWeight.w600),
                        ),
                      ],
                    ),
                  ),
                  IconButton(
                    icon: const Icon(Icons.remove_red_eye_outlined, color: AppColors.primary),
                    onPressed: () {
                      Get.snackbar('Preview', 'Viewing ${record.title}', snackPosition: SnackPosition.BOTTOM);
                    },
                  ),
                  IconButton(
                    icon: const Icon(Icons.file_download_outlined, color: AppColors.textSecondary),
                    onPressed: () {
                      Get.snackbar('Downloading', 'Downloading ${record.title}...', snackPosition: SnackPosition.BOTTOM);
                    },
                  ),
                ],
              ),
            );
          },
        );
      }),
      floatingActionButton: FloatingActionButton.extended(
        backgroundColor: AppColors.primary,
        onPressed: controller.showUploadModal,
        icon: const Icon(Icons.upload_file, color: Colors.white),
        label: const Text('Upload Report', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
      ),
    );
  }

  IconData _getRecordIcon(String type) {
    switch (type) {
      case 'X-Ray':
        return Icons.photo_camera_back_outlined;
      case 'Lab Report':
        return Icons.assignment_outlined;
      case 'Dental Chart':
        return Icons.grid_view_outlined;
      default:
        return Icons.insert_drive_file_outlined;
    }
  }
}
