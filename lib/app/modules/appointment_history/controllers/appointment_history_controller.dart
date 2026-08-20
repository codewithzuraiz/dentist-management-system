import 'package:flutter/material.dart';
import 'package:get/get.dart';

class AppointmentModel {
  final String id;
  final String doctorName;
  final String specialty;
  final String date;
  final String time;
  final String status; // Upcoming, Completed, Cancelled
  final String type; // In-Clinic, Video

  AppointmentModel({
    required this.id,
    required this.doctorName,
    required this.specialty,
    required this.date,
    required this.time,
    required this.status,
    required this.type,
  });
}

class AppointmentHistoryController extends GetxController with GetSingleTickerProviderStateMixin {
  late TabController tabController;

  final appointments = <AppointmentModel>[
    AppointmentModel(
      id: 'APT-101',
      doctorName: 'Dr. Alex Smith',
      specialty: 'Orthodontist',
      date: 'Tomorrow, 21 Aug',
      time: '10:30 AM',
      status: 'Upcoming',
      type: 'In-Clinic',
    ),
    AppointmentModel(
      id: 'APT-102',
      doctorName: 'Dr. Emma Watson',
      specialty: 'Endodontist',
      date: '25 Aug 2026',
      time: '02:00 PM',
      status: 'Upcoming',
      type: 'Video',
    ),
    AppointmentModel(
      id: 'APT-098',
      doctorName: 'Dr. Michael Chen',
      specialty: 'Pediatric',
      date: '10 Aug 2026',
      time: '11:00 AM',
      status: 'Completed',
      type: 'In-Clinic',
    ),
    AppointmentModel(
      id: 'APT-085',
      doctorName: 'Dr. Sophia Martinez',
      specialty: 'General Dentist',
      date: '01 Aug 2026',
      time: '04:30 PM',
      status: 'Cancelled',
      type: 'In-Clinic',
    ),
  ].obs;

  @override
  void onInit() {
    super.onInit();
    tabController = TabController(length: 3, vsync: this);
  }

  void cancelAppointment(String id) {
    Get.dialog(
      AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Text('Cancel Appointment'),
        content: const Text('Are you sure you want to cancel this appointment?'),
        actions: [
          TextButton(
            onPressed: () => Get.back(),
            child: const Text('No, keep it'),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: Colors.redAccent),
            onPressed: () {
              final index = appointments.indexWhere((a) => a.id == id);
              if (index != -1) {
                final old = appointments[index];
                appointments[index] = AppointmentModel(
                  id: old.id,
                  doctorName: old.doctorName,
                  specialty: old.specialty,
                  date: old.date,
                  time: old.time,
                  status: 'Cancelled',
                  type: old.type,
                );
              }
              Get.back();
              Get.snackbar(
                'Cancelled',
                'Appointment $id has been cancelled.',
                snackPosition: SnackPosition.BOTTOM,
                backgroundColor: Colors.redAccent,
                colorText: Colors.white,
              );
            },
            child: const Text('Yes, Cancel'),
          ),
        ],
      ),
    );
  }

  @override
  void onClose() {
    tabController.dispose();
    super.onClose();
  }
}
