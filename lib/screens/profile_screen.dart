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

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  String _doctorName = 'Dr. Ahmed (DDS)';
  String _doctorSpecialty = 'Senior Dental Surgeon';
  String _clinicName = 'DentiFlow Clinic';
  String _doctorPhone = '+1 (555) 019-2834';
  String _doctorEmail = 'dr.ahmed@dentiflow.com';

  void _openEditDoctorProfileModal() {
    final nameCtrl = TextEditingController(text: _doctorName);
    final specialtyCtrl = TextEditingController(text: _doctorSpecialty);
    final clinicCtrl = TextEditingController(text: _clinicName);
    final phoneCtrl = TextEditingController(text: _doctorPhone);
    final emailCtrl = TextEditingController(text: _doctorEmail);

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => Container(
        decoration: const BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
        ),
        padding: EdgeInsets.only(
          top: 20,
          left: 24,
          right: 24,
          bottom: MediaQuery.of(ctx).viewInsets.bottom + 24,
        ),
        child: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Center(
                child: Container(
                  width: 40,
                  height: 4,
                  decoration: BoxDecoration(
                    color: AppColors.border,
                    borderRadius: BorderRadius.circular(2),
                  ),
                ),
              ),
              const SizedBox(height: 16),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    'Edit Doctor Profile',
                    style: GoogleFonts.poppins(
                      fontSize: 18,
                      fontWeight: FontWeight.w700,
                      color: AppColors.textPrimary,
                    ),
                  ),
                  IconButton(
                    icon: const Icon(Icons.close, color: AppColors.textHint),
                    onPressed: () => Navigator.pop(ctx),
                  ),
                ],
              ),
              const SizedBox(height: 16),

              // Profile Image Preview with camera icon
              Center(
                child: Stack(
                  children: [
                    Container(
                      width: 72,
                      height: 72,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        border: Border.all(color: AppColors.primary, width: 2.5),
                      ),
                      child: const CircleAvatar(
                        backgroundColor: AppColors.primaryDark,
                        child: Icon(Icons.person, color: Colors.white, size: 40),
                      ),
                    ),
                    Positioned(
                      right: 0,
                      bottom: 0,
                      child: Container(
                        width: 26,
                        height: 26,
                        decoration: BoxDecoration(
                          color: AppColors.primary,
                          shape: BoxShape.circle,
                          border: Border.all(color: Colors.white, width: 2),
                        ),
                        child: const Icon(Icons.camera_alt_rounded,
                            color: Colors.white, size: 13),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 18),

              // Doctor Name Field
              Text(
                'Doctor Full Name',
                style: GoogleFonts.poppins(
                  fontSize: 13,
                  fontWeight: FontWeight.w600,
                  color: AppColors.textPrimary,
                ),
              ),
              const SizedBox(height: 6),
              TextField(
                controller: nameCtrl,
                style: GoogleFonts.poppins(fontSize: 13),
                decoration: InputDecoration(
                  hintText: 'e.g. Dr. Ahmed (DDS)',
                  prefixIcon: const Icon(Icons.person_outline, size: 20, color: AppColors.primary),
                  filled: true,
                  fillColor: const Color(0xFFF6F8F9),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(14),
                    borderSide: BorderSide.none,
                  ),
                  contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                ),
              ),
              const SizedBox(height: 14),

              // Specialty Field
              Text(
                'Specialty / Role',
                style: GoogleFonts.poppins(
                  fontSize: 13,
                  fontWeight: FontWeight.w600,
                  color: AppColors.textPrimary,
                ),
              ),
              const SizedBox(height: 6),
              TextField(
                controller: specialtyCtrl,
                style: GoogleFonts.poppins(fontSize: 13),
                decoration: InputDecoration(
                  hintText: 'e.g. Senior Dental Surgeon',
                  prefixIcon: const Icon(Icons.medical_services_outlined, size: 20, color: AppColors.primary),
                  filled: true,
                  fillColor: const Color(0xFFF6F8F9),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(14),
                    borderSide: BorderSide.none,
                  ),
                  contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                ),
              ),
              const SizedBox(height: 14),

              // Clinic Name Field
              Text(
                'Clinic Name',
                style: GoogleFonts.poppins(
                  fontSize: 13,
                  fontWeight: FontWeight.w600,
                  color: AppColors.textPrimary,
                ),
              ),
              const SizedBox(height: 6),
              TextField(
                controller: clinicCtrl,
                style: GoogleFonts.poppins(fontSize: 13),
                decoration: InputDecoration(
                  hintText: 'e.g. DentiFlow Clinic',
                  prefixIcon: const Icon(Icons.local_hospital_outlined, size: 20, color: AppColors.primary),
                  filled: true,
                  fillColor: const Color(0xFFF6F8F9),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(14),
                    borderSide: BorderSide.none,
                  ),
                  contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                ),
              ),
              const SizedBox(height: 14),

              // Phone Field
              Text(
                'Phone Number',
                style: GoogleFonts.poppins(
                  fontSize: 13,
                  fontWeight: FontWeight.w600,
                  color: AppColors.textPrimary,
                ),
              ),
              const SizedBox(height: 6),
              TextField(
                controller: phoneCtrl,
                keyboardType: TextInputType.phone,
                style: GoogleFonts.poppins(fontSize: 13),
                decoration: InputDecoration(
                  hintText: 'e.g. +1 (555) 019-2834',
                  prefixIcon: const Icon(Icons.phone_outlined, size: 20, color: AppColors.primary),
                  filled: true,
                  fillColor: const Color(0xFFF6F8F9),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(14),
                    borderSide: BorderSide.none,
                  ),
                  contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                ),
              ),
              const SizedBox(height: 14),

              // Email Field
              Text(
                'Email Address',
                style: GoogleFonts.poppins(
                  fontSize: 13,
                  fontWeight: FontWeight.w600,
                  color: AppColors.textPrimary,
                ),
              ),
              const SizedBox(height: 6),
              TextField(
                controller: emailCtrl,
                keyboardType: TextInputType.emailAddress,
                style: GoogleFonts.poppins(fontSize: 13),
                decoration: InputDecoration(
                  hintText: 'e.g. dr.ahmed@dentiflow.com',
                  prefixIcon: const Icon(Icons.email_outlined, size: 20, color: AppColors.primary),
                  filled: true,
                  fillColor: const Color(0xFFF6F8F9),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(14),
                    borderSide: BorderSide.none,
                  ),
                  contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                ),
              ),
              const SizedBox(height: 24),

              // Save Changes Button
              SizedBox(
                width: double.infinity,
                height: 50,
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.primary,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(14),
                    ),
                    elevation: 0,
                  ),
                  onPressed: () {
                    if (nameCtrl.text.trim().isNotEmpty) {
                      setState(() {
                        _doctorName = nameCtrl.text.trim();
                        _doctorSpecialty = specialtyCtrl.text.trim();
                        _clinicName = clinicCtrl.text.trim();
                        _doctorPhone = phoneCtrl.text.trim();
                        _doctorEmail = emailCtrl.text.trim();
                      });
                      Navigator.pop(ctx);
                      Get.snackbar(
                        'Profile Updated',
                        'Doctor profile updated successfully!',
                        backgroundColor: AppColors.primary,
                        colorText: Colors.white,
                        snackPosition: SnackPosition.BOTTOM,
                        margin: const EdgeInsets.all(16),
                      );
                    }
                  },
                  child: Text(
                    'Save Doctor Profile',
                    style: GoogleFonts.poppins(
                      fontSize: 15,
                      fontWeight: FontWeight.w600,
                      color: Colors.white,
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

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

              // Doctor Header Card with Image & Edit Button
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
                    // Profile Image with Camera Edit Overlay Badge
                    GestureDetector(
                      onTap: _openEditDoctorProfileModal,
                      child: Stack(
                        children: [
                          Container(
                            width: 66,
                            height: 66,
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              border: Border.all(color: AppColors.primary, width: 2.5),
                            ),
                            child: const CircleAvatar(
                              backgroundColor: AppColors.primaryDark,
                              child: Icon(Icons.person, color: Colors.white, size: 36),
                            ),
                          ),
                          Positioned(
                            right: 0,
                            bottom: 0,
                            child: Container(
                              width: 24,
                              height: 24,
                              decoration: BoxDecoration(
                                color: AppColors.primary,
                                shape: BoxShape.circle,
                                border: Border.all(color: Colors.white, width: 1.5),
                              ),
                              child: const Icon(Icons.edit,
                                  color: Colors.white, size: 12),
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(width: 14),

                    // Doctor Details Column
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            _doctorName,
                            style: GoogleFonts.poppins(
                              fontSize: 17,
                              fontWeight: FontWeight.w700,
                              color: Colors.white,
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                          Text(
                            '$_doctorSpecialty • $_clinicName',
                            style: GoogleFonts.poppins(
                              fontSize: 11,
                              color: Colors.white70,
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
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

                    // Edit Profile Action Button right next to image/name
                    GestureDetector(
                      onTap: _openEditDoctorProfileModal,
                      child: Container(
                        padding: const EdgeInsets.all(9),
                        decoration: BoxDecoration(
                          color: Colors.white.withValues(alpha: 0.15),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(
                            color: Colors.white.withValues(alpha: 0.3),
                            width: 1,
                          ),
                        ),
                        child: const Icon(
                          Icons.edit_outlined,
                          color: Colors.white,
                          size: 20,
                        ),
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
