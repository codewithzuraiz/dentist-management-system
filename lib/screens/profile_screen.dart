import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import '../theme/app_colors.dart';
import '../widgets/dentiflow_bottom_nav.dart';
import 'chat_inbox_screen.dart';
import 'digital_prescription_screen.dart';
import 'login_screen.dart';
import 'medical_history_screen.dart';
import 'notifications_screen.dart';
import 'schedule_management_screen.dart';
import 'treatment_notes_screen.dart';
import 'treatment_plans_screen.dart';
import 'xray_gallery_screen.dart';

class ProfileScreen extends StatelessWidget {
  const ProfileScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFB),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const SizedBox(height: 10),
              // Doctor Header Card
              Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    colors: [
                      Color(0xFF2C5364),
                      Color(0xFF203A43),
                    ],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  borderRadius: BorderRadius.circular(22),
                  boxShadow: [
                    BoxShadow(
                      color: const Color(0xFF203A43).withValues(alpha: 0.3),
                      blurRadius: 12,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: Row(
                  children: [
                    Container(
                      width: 64,
                      height: 64,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        border: Border.all(color: AppColors.primary, width: 2.5),
                      ),
                      child: const CircleAvatar(
                        backgroundColor: AppColors.primaryDark,
                        child: Icon(Icons.person, color: Colors.white, size: 36),
                      ),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'Dr. Ahmed (DDS)',
                            style: GoogleFonts.poppins(
                              fontSize: 18,
                              fontWeight: FontWeight.w700,
                              color: Colors.white,
                            ),
                          ),
                          Text(
                            'Senior Dental Surgeon • DentiFlow Clinic',
                            style: GoogleFonts.poppins(
                              fontSize: 11,
                              color: Colors.white70,
                            ),
                          ),
                          const SizedBox(height: 6),
                          Container(
                            padding: const EdgeInsets.symmetric(
                                horizontal: 10, vertical: 3),
                            decoration: BoxDecoration(
                              color: AppColors.primary,
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: Text(
                              'License Verified ★ 4.9',
                              style: GoogleFonts.poppins(
                                fontSize: 10,
                                fontWeight: FontWeight.w600,
                                color: Colors.white,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 24),

              Text(
                'Dental Management Modules',
                style: GoogleFonts.poppins(
                  fontSize: 16,
                  fontWeight: FontWeight.w700,
                  color: AppColors.textPrimary,
                ),
              ),
              const SizedBox(height: 12),

              // Feature Menu List
              _buildMenuItem(
                icon: Icons.history_edu_rounded,
                title: 'Patient Medical History',
                subtitle: 'Allergies, chronic conditions & timeline',
                onTap: () => Get.to(() => const MedicalHistoryScreen()),
              ),
              _buildMenuItem(
                icon: Icons.note_alt_outlined,
                title: 'Clinical Treatment Notes',
                subtitle: 'SOAP notes & tooth numbering logs',
                onTap: () => Get.to(() => const TreatmentNotesScreen()),
              ),
              _buildMenuItem(
                icon: Icons.receipt_long_rounded,
                title: 'Digital Prescriptions (Rx)',
                subtitle: 'Issue digital prescriptions with QR code',
                onTap: () => Get.to(() => const DigitalPrescriptionScreen()),
              ),
              _buildMenuItem(
                icon: Icons.image_search_rounded,
                title: 'Upload X-rays & Images',
                subtitle: '3D CBCT, Panoramics radiograph gallery',
                onTap: () => Get.to(() => const XRayGalleryScreen()),
              ),
              _buildMenuItem(
                icon: Icons.assignment_outlined,
                title: 'Treatment Plans',
                subtitle: 'Multi-stage dental treatment packages',
                onTap: () => Get.to(() => const TreatmentPlansScreen()),
              ),
              _buildMenuItem(
                icon: Icons.edit_calendar_rounded,
                title: 'Schedule & Chair Management',
                subtitle: 'Practice working hours & chair allocation',
                onTap: () => Get.to(() => const ScheduleManagementScreen()),
              ),
              _buildMenuItem(
                icon: Icons.chat_bubble_outline_rounded,
                title: 'Chat with Patients',
                subtitle: 'Patient messaging inbox & instant replies',
                onTap: () => Get.to(() => const ChatInboxScreen()),
              ),
              _buildMenuItem(
                icon: Icons.notifications_none_rounded,
                title: 'Push Notifications Center',
                subtitle: 'Alerts, requests & system updates',
                onTap: () => Get.to(() => const NotificationsScreen()),
              ),
              const SizedBox(height: 16),

              // Log Out Button
              SizedBox(
                width: double.infinity,
                height: 50,
                child: OutlinedButton.icon(
                  icon: const Icon(Icons.logout_rounded, color: AppColors.error),
                  label: Text(
                    'Log Out',
                    style: GoogleFonts.poppins(
                      fontSize: 15,
                      fontWeight: FontWeight.w600,
                      color: AppColors.error,
                    ),
                  ),
                  style: OutlinedButton.styleFrom(
                    side: const BorderSide(color: AppColors.error),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(16),
                    ),
                  ),
                  onPressed: () {
                    Get.offAll(() => const LoginScreen());
                  },
                ),
              ),
              const SizedBox(height: 20),
            ],
          ),
        ),
      ),
      bottomNavigationBar: const DentiFlowBottomNav(currentIndex: 4),
    );
  }

  Widget _buildMenuItem({
    required IconData icon,
    required String title,
    required String subtitle,
    required VoidCallback onTap,
  }) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: ListTile(
        onTap: onTap,
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
        leading: Container(
          padding: const EdgeInsets.all(10),
          decoration: BoxDecoration(
            color: AppColors.tealLight,
            borderRadius: BorderRadius.circular(12),
          ),
          child: Icon(icon, color: AppColors.primary, size: 22),
        ),
        title: Text(
          title,
          style: GoogleFonts.poppins(
            fontSize: 14,
            fontWeight: FontWeight.w600,
            color: AppColors.textPrimary,
          ),
        ),
        subtitle: Text(
          subtitle,
          style: GoogleFonts.poppins(
            fontSize: 11,
            color: AppColors.textSecondary,
          ),
        ),
        trailing: const Icon(Icons.arrow_forward_ios_rounded,
            size: 14, color: AppColors.textHint),
      ),
    );
  }
}
