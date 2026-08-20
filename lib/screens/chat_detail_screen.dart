import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import '../controllers/dentiflow_controllers.dart';
import '../theme/app_colors.dart';

class ChatDetailScreen extends StatelessWidget {
  final String conversationId;

  const ChatDetailScreen({super.key, required this.conversationId});

  @override
  Widget build(BuildContext context) {
    final ChatController controller = Get.find<ChatController>();
    final TextEditingController msgCtrl = TextEditingController();

    return Obx(() {
      final conversation = controller.conversations
          .firstWhere((c) => c['id'] == conversationId);
      final messages = conversation['messages'] as List<dynamic>;

      return Scaffold(
        backgroundColor: const Color(0xFFF8FAFB),
        appBar: AppBar(
          backgroundColor: AppColors.surface,
          elevation: 0,
          leading: IconButton(
            icon: const Icon(Icons.arrow_back_rounded, color: AppColors.textPrimary),
            onPressed: () => Get.back(),
          ),
          title: Row(
            children: [
              CircleAvatar(
                radius: 18,
                backgroundColor: const Color(0xFFE0F4F2),
                child: Text(
                  conversation['initials'],
                  style: GoogleFonts.poppins(
                    fontSize: 12,
                    fontWeight: FontWeight.w700,
                    color: const Color(0xFF3C6A7A),
                  ),
                ),
              ),
              const SizedBox(width: 10),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    conversation['patientName'],
                    style: GoogleFonts.poppins(
                      fontSize: 15,
                      fontWeight: FontWeight.w700,
                      color: AppColors.textPrimary,
                    ),
                  ),
                  Text(
                    conversation['isOnline'] == true ? 'Online' : 'Offline',
                    style: GoogleFonts.poppins(
                      fontSize: 11,
                      color: conversation['isOnline'] == true
                          ? AppColors.success
                          : AppColors.textHint,
                    ),
                  ),
                ],
              ),
            ],
          ),
          actions: [
            IconButton(
              icon: const Icon(Icons.phone_outlined, color: AppColors.primary),
              onPressed: () {},
            ),
          ],
        ),
        body: SafeArea(
          child: Column(
            children: [
              // Message Thread
              Expanded(
                child: ListView.builder(
                  padding: const EdgeInsets.all(20),
                  itemCount: messages.length,
                  itemBuilder: (ctx, index) {
                    final msg = messages[index];
                    final bool isDoctor = msg['sender'] == 'doctor';
                    return Align(
                      alignment: isDoctor
                          ? Alignment.centerRight
                          : Alignment.centerLeft,
                      child: Container(
                        margin: const EdgeInsets.only(bottom: 12),
                        padding: const EdgeInsets.symmetric(
                            horizontal: 16, vertical: 12),
                        constraints: BoxConstraints(
                          maxWidth: MediaQuery.of(context).size.width * 0.75,
                        ),
                        decoration: BoxDecoration(
                          color: isDoctor
                              ? AppColors.primary
                              : AppColors.surface,
                          borderRadius: BorderRadius.only(
                            topLeft: const Radius.circular(16),
                            topRight: const Radius.circular(16),
                            bottomLeft: isDoctor
                                ? const Radius.circular(16)
                                : Radius.zero,
                            bottomRight: isDoctor
                                ? Radius.zero
                                : const Radius.circular(16),
                          ),
                          border: isDoctor
                              ? null
                              : Border.all(color: AppColors.border),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withValues(alpha: 0.02),
                              blurRadius: 4,
                              offset: const Offset(0, 2),
                            ),
                          ],
                        ),
                        child: Column(
                          crossAxisAlignment: isDoctor
                              ? CrossAxisAlignment.end
                              : CrossAxisAlignment.start,
                          children: [
                            Text(
                              msg['text'],
                              style: GoogleFonts.poppins(
                                fontSize: 13,
                                color: isDoctor
                                    ? Colors.white
                                    : AppColors.textPrimary,
                              ),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              msg['time'],
                              style: GoogleFonts.poppins(
                                fontSize: 9,
                                color: isDoctor
                                    ? Colors.white70
                                    : AppColors.textHint,
                              ),
                            ),
                          ],
                        ),
                      ),
                    );
                  },
                ),
              ),

              // Quick Reply Chips
              Container(
                height: 40,
                padding: const EdgeInsets.symmetric(horizontal: 16),
                child: ListView(
                  scrollDirection: Axis.horizontal,
                  children: [
                    'Take medicine after meals',
                    'Please schedule follow-up',
                    'Send X-ray photo',
                  ].map((chipText) {
                    return GestureDetector(
                      onTap: () {
                        controller.sendMessage(conversationId, chipText);
                      },
                      child: Container(
                        margin: const EdgeInsets.only(right: 8),
                        padding: const EdgeInsets.symmetric(
                            horizontal: 12, vertical: 6),
                        decoration: BoxDecoration(
                          color: AppColors.tealLight,
                          borderRadius: BorderRadius.circular(16),
                        ),
                        child: Text(
                          chipText,
                          style: GoogleFonts.poppins(
                            fontSize: 11,
                            color: AppColors.primaryDark,
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      ),
                    );
                  }).toList(),
                ),
              ),
              const SizedBox(height: 8),

              // Input Bar
              Container(
                padding: const EdgeInsets.symmetric(
                    horizontal: 16, vertical: 10),
                decoration: const BoxDecoration(
                  color: AppColors.surface,
                  border: Border(top: BorderSide(color: AppColors.divider)),
                ),
                child: Row(
                  children: [
                    IconButton(
                      icon: const Icon(Icons.attach_file_rounded,
                          color: AppColors.textSecondary),
                      onPressed: () {},
                    ),
                    Expanded(
                      child: TextField(
                        controller: msgCtrl,
                        style: GoogleFonts.poppins(fontSize: 13),
                        decoration: InputDecoration(
                          hintText: 'Type your message...',
                          hintStyle: GoogleFonts.poppins(fontSize: 13),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(24),
                            borderSide: BorderSide.none,
                          ),
                          filled: true,
                          fillColor: const Color(0xFFF3F4F6),
                          contentPadding: const EdgeInsets.symmetric(
                              horizontal: 16, vertical: 10),
                        ),
                      ),
                    ),
                    const SizedBox(width: 8),
                    GestureDetector(
                      onTap: () {
                        if (msgCtrl.text.trim().isNotEmpty) {
                          controller.sendMessage(
                              conversationId, msgCtrl.text.trim());
                          msgCtrl.clear();
                        }
                      },
                      child: Container(
                        width: 42,
                        height: 42,
                        decoration: const BoxDecoration(
                          color: AppColors.primary,
                          shape: BoxShape.circle,
                        ),
                        child: const Icon(Icons.send_rounded,
                            color: Colors.white, size: 20),
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      );
    });
  }
}
