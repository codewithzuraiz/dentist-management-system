import 'package:flutter/material.dart';
import 'package:get/get.dart';

class NotificationModel {
  final String title;
  final String message;
  final String time;
  final IconData icon;
  final bool isRead;

  NotificationModel({
    required this.title,
    required this.message,
    required this.time,
    required this.icon,
    required this.isRead,
  });
}

class NotificationsController extends GetxController {
  final notifications = <NotificationModel>[
    NotificationModel(
      title: 'Appointment Reminder',
      message: 'Your checkup visit with Dr. Alex Smith is scheduled for tomorrow at 10:30 AM.',
      time: '10 mins ago',
      icon: Icons.calendar_today,
      isRead: false,
    ),
    NotificationModel(
      title: 'Medication Alert',
      message: 'Time to take Amoxicillin (500mg) after lunch.',
      time: '2 hours ago',
      icon: Icons.medication_outlined,
      isRead: false,
    ),
    NotificationModel(
      title: 'Payment Receipt',
      message: 'Payment of \$45.00 for Invoice #INV-4401 received successfully.',
      time: '1 day ago',
      icon: Icons.receipt_long,
      isRead: true,
    ),
    NotificationModel(
      title: 'X-Ray Report Ready',
      message: 'Dr. Alex Smith has uploaded your 3D Jaw X-Ray report.',
      time: '3 days ago',
      icon: Icons.file_present_outlined,
      isRead: true,
    ),
  ].obs;

  void markAllAsRead() {
    for (int i = 0; i < notifications.length; i++) {
      final old = notifications[i];
      notifications[i] = NotificationModel(
        title: old.title,
        message: old.message,
        time: old.time,
        icon: old.icon,
        isRead: true,
      );
    }
    Get.snackbar('Notifications', 'All notifications marked as read.');
  }
}
