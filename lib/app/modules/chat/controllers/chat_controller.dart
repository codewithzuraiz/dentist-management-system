import 'package:flutter/material.dart';
import 'package:get/get.dart';

class ChatMessage {
  final String sender; // 'user' or 'doctor'
  final String text;
  final String time;

  ChatMessage({required this.sender, required this.text, required this.time});
}

class ChatConversation {
  final String doctorName;
  final String specialty;
  final String lastMessage;
  final String time;
  final int unreadCount;

  ChatConversation({
    required this.doctorName,
    required this.specialty,
    required this.lastMessage,
    required this.time,
    required this.unreadCount,
  });
}

class ChatController extends GetxController {
  final messageInputController = TextEditingController();

  final conversations = <ChatConversation>[
    ChatConversation(
      doctorName: 'Dr. Alex Smith',
      specialty: 'Orthodontist',
      lastMessage: 'Your dental alignment progress looks great! See you tomorrow.',
      time: '10:15 AM',
      unreadCount: 2,
    ),
    ChatConversation(
      doctorName: 'Dr. Emma Watson',
      specialty: 'Endodontist',
      lastMessage: 'Please remember to use the mouthwash twice daily.',
      time: 'Yesterday',
      unreadCount: 0,
    ),
  ].obs;

  final activeMessages = <ChatMessage>[
    ChatMessage(sender: 'doctor', text: 'Hello Sarah! How is your tooth pain feeling today?', time: '10:00 AM'),
    ChatMessage(sender: 'user', text: 'Hi Dr. Alex! Pain is much better after taking the prescribed medicine.', time: '10:02 AM'),
    ChatMessage(sender: 'doctor', text: 'Great to hear! Your dental alignment progress looks great! See you tomorrow.', time: '10:15 AM'),
  ].obs;

  void sendMessage() {
    final text = messageInputController.text.trim();
    if (text.isEmpty) return;

    final now = DateTime.now();
    final timeStr = '${now.hour}:${now.minute.toString().padLeft(2, '0')}';

    activeMessages.add(
      ChatMessage(sender: 'user', text: text, time: timeStr),
    );

    messageInputController.clear();

    // Auto doctor reply mock
    Future.delayed(const Duration(seconds: 1), () {
      activeMessages.add(
        ChatMessage(
          sender: 'doctor',
          text: 'Thank you for reaching out. I have updated your chart accordingly.',
          time: timeStr,
        ),
      );
    });
  }

  @override
  void onClose() {
    messageInputController.dispose();
    super.onClose();
  }
}
