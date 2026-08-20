import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../../../routes/app_routes.dart';

class BookAppointmentController extends GetxController {
  final selectedDate = DateTime.now().add(const Duration(days: 1)).obs;
  final selectedTimeSlot = '10:30 AM'.obs;
  final consultationType = 'In-Clinic'.obs;
  final notesController = TextEditingController();

  final timeSlots = [
    '09:00 AM',
    '09:30 AM',
    '10:00 AM',
    '10:30 AM',
    '11:00 AM',
    '02:00 PM',
    '02:30 PM',
    '03:00 PM',
    '04:30 PM',
  ];

  void selectDate(DateTime date) {
    selectedDate.value = date;
  }

  void selectTimeSlot(String slot) {
    selectedTimeSlot.value = slot;
  }

  void setConsultationType(String type) {
    consultationType.value = type;
  }

  void confirmBooking(String doctorName) {
    Get.dialog(
      AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: Row(
          children: const [
            Icon(Icons.check_circle, color: Color(0xFF329D9C), size: 28),
            SizedBox(width: 8),
            Text('Booking Confirmed!'),
          ],
        ),
        content: Text(
          'Your appointment with $doctorName on ${selectedDate.value.day}/${selectedDate.value.month}/${selectedDate.value.year} at ${selectedTimeSlot.value} has been scheduled.',
        ),
        actions: [
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFF329D9C),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
            ),
            onPressed: () {
              Get.back();
              Get.offAllNamed(Routes.dashboard);
            },
            child: const Text('Go to Dashboard'),
          ),
        ],
      ),
    );
  }

  @override
  void onClose() {
    notesController.dispose();
    super.onClose();
  }
}
