import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../../../core/values/app_colors.dart';
import '../controllers/treatment_progress_controller.dart';

class TreatmentProgressView extends GetView<TreatmentProgressController> {
  const TreatmentProgressView({super.key});

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
          'Treatment Progress',
          style: TextStyle(color: AppColors.textPrimary, fontWeight: FontWeight.bold),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(18),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Progress Header Card
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(24),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.04),
                    blurRadius: 16,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Obx(
                    () => Text(
                      controller.treatmentPlanName.value,
                      style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppColors.textPrimary),
                    ),
                  ),
                  const SizedBox(height: 4),
                  Obx(
                    () => Text(
                      'Supervised by ${controller.doctorName.value}',
                      style: const TextStyle(fontSize: 13, color: AppColors.primary, fontWeight: FontWeight.w600),
                    ),
                  ),
                  const SizedBox(height: 18),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text('Overall Progress', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                      Obx(
                        () => Text(
                          '${(controller.overallProgressPercent.value * 100).toInt()}%',
                          style: const TextStyle(fontWeight: FontWeight.bold, color: AppColors.primary, fontSize: 15),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  Obx(
                    () => ClipRRect(
                      borderRadius: BorderRadius.circular(10),
                      child: LinearProgressIndicator(
                        value: controller.overallProgressPercent.value,
                        minHeight: 10,
                        backgroundColor: AppColors.primaryLight,
                        valueColor: const AlwaysStoppedAnimation<Color>(AppColors.primary),
                      ),
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 24),

            // Stages Timeline Title
            const Text(
              'Treatment Plan Stages',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppColors.textPrimary),
            ),
            const SizedBox(height: 14),

            // Timeline Items List
            Obx(
              () => ListView.builder(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                itemCount: controller.stages.length,
                itemBuilder: (context, index) {
                  final stage = controller.stages[index];
                  final isLast = index == controller.stages.length - 1;

                  return Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Column(
                        children: [
                          Container(
                            padding: const EdgeInsets.all(6),
                            decoration: BoxDecoration(
                              color: _getStageColor(stage.status),
                              shape: BoxShape.circle,
                            ),
                            child: Icon(
                              _getStageIcon(stage.status),
                              color: Colors.white,
                              size: 16,
                            ),
                          ),
                          if (!isLast)
                            Container(
                              width: 2,
                              height: 80,
                              color: AppColors.border,
                            ),
                        ],
                      ),
                      const SizedBox(width: 14),
                      Expanded(
                        child: Container(
                          margin: const EdgeInsets.only(bottom: 14),
                          padding: const EdgeInsets.all(16),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(16),
                            border: Border.all(
                              color: stage.status == 'Active' ? AppColors.primary : AppColors.border,
                              width: stage.status == 'Active' ? 1.5 : 1,
                            ),
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  Expanded(
                                    child: Text(
                                      stage.title,
                                      style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: AppColors.textPrimary),
                                    ),
                                  ),
                                  Text(
                                    stage.date,
                                    style: const TextStyle(fontSize: 11, color: AppColors.textHint),
                                  ),
                                ],
                              ),
                              const SizedBox(height: 6),
                              Text(
                                stage.description,
                                style: const TextStyle(fontSize: 12, color: AppColors.textSecondary),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ],
                  );
                },
              ),
            ),
          ],
        ),
      ),
    );
  }

  Color _getStageColor(String status) {
    switch (status) {
      case 'Completed':
        return const Color(0xFF059669);
      case 'Active':
        return AppColors.primary;
      case 'Pending':
        return Colors.grey.shade400;
      default:
        return Colors.grey;
    }
  }

  IconData _getStageIcon(String status) {
    switch (status) {
      case 'Completed':
        return Icons.check;
      case 'Active':
        return Icons.play_arrow_rounded;
      case 'Pending':
        return Icons.hourglass_empty_rounded;
      default:
        return Icons.circle;
    }
  }
}
