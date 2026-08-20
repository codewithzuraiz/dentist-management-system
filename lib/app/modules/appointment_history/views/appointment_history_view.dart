import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../../../core/values/app_colors.dart';
import '../../../routes/app_routes.dart';
import '../controllers/appointment_history_controller.dart';

class AppointmentHistoryView extends GetView<AppointmentHistoryController> {
  const AppointmentHistoryView({super.key});

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
          'My Appointments',
          style: TextStyle(color: AppColors.textPrimary, fontWeight: FontWeight.bold),
        ),
        bottom: TabBar(
          controller: controller.tabController,
          labelColor: AppColors.primary,
          unselectedLabelColor: AppColors.textSecondary,
          indicatorColor: AppColors.primary,
          indicatorWeight: 3,
          tabs: const [
            Tab(text: 'Upcoming'),
            Tab(text: 'Completed'),
            Tab(text: 'Cancelled'),
          ],
        ),
      ),
      body: TabBarView(
        controller: controller.tabController,
        children: [
          _buildList('Upcoming'),
          _buildList('Completed'),
          _buildList('Cancelled'),
        ],
      ),
    );
  }

  Widget _buildList(String status) {
    return Obx(() {
      final filtered = controller.appointments.where((a) => a.status == status).toList();

      if (filtered.isEmpty) {
        return Center(
          child: Text(
            'No $status appointments.',
            style: const TextStyle(color: AppColors.textSecondary, fontSize: 15),
          ),
        );
      }

      return ListView.separated(
        padding: const EdgeInsets.all(16),
        itemCount: filtered.length,
        separatorBuilder: (context, index) => const SizedBox(height: 14),
        itemBuilder: (context, index) {
          final apt = filtered[index];
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
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      apt.id,
                      style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: AppColors.textHint),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(
                        color: _getStatusBg(apt.status),
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Text(
                        apt.status,
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.bold,
                          color: _getStatusText(apt.status),
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 10),
                Row(
                  children: [
                    CircleAvatar(
                      radius: 24,
                      backgroundColor: AppColors.primaryLight,
                      child: const Icon(Icons.person, color: AppColors.primary, size: 28),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            apt.doctorName,
                            style: const TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: AppColors.textPrimary),
                          ),
                          Text(
                            apt.specialty,
                            style: const TextStyle(fontSize: 12, color: AppColors.primary, fontWeight: FontWeight.w600),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
                const Divider(height: 24),
                Row(
                  children: [
                    const Icon(Icons.calendar_month_outlined, size: 16, color: AppColors.primary),
                    const SizedBox(width: 6),
                    Text(apt.date, style: const TextStyle(fontSize: 13, color: AppColors.textPrimary)),
                    const SizedBox(width: 16),
                    const Icon(Icons.access_time, size: 16, color: AppColors.primary),
                    const SizedBox(width: 6),
                    Text(apt.time, style: const TextStyle(fontSize: 13, color: AppColors.textPrimary)),
                  ],
                ),
                if (apt.status == 'Upcoming') ...[
                  const SizedBox(height: 14),
                  Row(
                    children: [
                      Expanded(
                        child: OutlinedButton(
                          style: OutlinedButton.styleFrom(
                            foregroundColor: Colors.redAccent,
                            side: const BorderSide(color: Colors.redAccent),
                            padding: const EdgeInsets.symmetric(vertical: 10),
                          ),
                          onPressed: () => controller.cancelAppointment(apt.id),
                          child: const Text('Cancel', style: TextStyle(fontSize: 13)),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: ElevatedButton(
                          style: ElevatedButton.styleFrom(
                            padding: const EdgeInsets.symmetric(vertical: 10),
                          ),
                          onPressed: () {
                            Get.toNamed(Routes.bookAppointment);
                          },
                          child: const Text('Reschedule', style: TextStyle(fontSize: 13)),
                        ),
                      ),
                    ],
                  ),
                ],
              ],
            ),
          );
        },
      );
    });
  }

  Color _getStatusBg(String status) {
    switch (status) {
      case 'Upcoming':
        return AppColors.primaryLight;
      case 'Completed':
        return const Color(0xFFD1FAE5);
      case 'Cancelled':
        return const Color(0xFFFEE2E2);
      default:
        return Colors.grey.shade100;
    }
  }

  Color _getStatusText(String status) {
    switch (status) {
      case 'Upcoming':
        return AppColors.primary;
      case 'Completed':
        return const Color(0xFF059669);
      case 'Cancelled':
        return const Color(0xFFDC2626);
      default:
        return Colors.grey;
    }
  }
}
