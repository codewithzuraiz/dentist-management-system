import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import '../models/patient_model.dart';
import '../theme/app_colors.dart';
import '../widgets/dentiflow_bottom_nav.dart';

class EditPatientProfileScreen extends StatefulWidget {
  final Patient patient;
  final Function(Patient)? onSave;

  const EditPatientProfileScreen({
    super.key,
    required this.patient,
    this.onSave,
  });

  @override
  State<EditPatientProfileScreen> createState() =>
      _EditPatientProfileScreenState();
}

class _EditPatientProfileScreenState extends State<EditPatientProfileScreen> {
  final _formKey = GlobalKey<FormState>();

  late TextEditingController _nameController;
  late TextEditingController _phoneController;
  late TextEditingController _emailController;
  late TextEditingController _ageController;
  late TextEditingController _addressController;
  late TextEditingController _emergencyContactController;
  late TextEditingController _notesController;

  late String _selectedGender;
  late PatientStatus _selectedStatus;
  String _selectedBloodGroup = 'O+';
  String _selectedDoctor = 'Dr. Ahmed';

  final List<String> _bloodGroups = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
  final List<String> _doctors = ['Dr. Ahmed', 'Dr. Sarah', 'Dr. Usman'];

  @override
  void initState() {
    super.initState();
    _nameController = TextEditingController(text: widget.patient.name);
    _phoneController = TextEditingController(text: widget.patient.phone);
    _emailController = TextEditingController(text: widget.patient.email);
    _ageController = TextEditingController(text: '${widget.patient.age}');
    _addressController =
        TextEditingController(text: '742 Evergreen Terrace, Dental District');
    _emergencyContactController =
        TextEditingController(text: '+1 (555) 987-6543 (Spouse)');
    _notesController =
        TextEditingController(text: widget.patient.medicalHistory ?? '');

    _selectedGender = widget.patient.gender;
    _selectedStatus = widget.patient.status;
  }

  @override
  void dispose() {
    _nameController.dispose();
    _phoneController.dispose();
    _emailController.dispose();
    _ageController.dispose();
    _addressController.dispose();
    _emergencyContactController.dispose();
    _notesController.dispose();
    super.dispose();
  }

  void _saveProfile() {
    if (_formKey.currentState!.validate()) {
      final updated = widget.patient.copyWith(
        name: _nameController.text.trim(),
        phone: _phoneController.text.trim(),
        email: _emailController.text.trim(),
        gender: _selectedGender,
        age: int.tryParse(_ageController.text.trim()) ?? widget.patient.age,
        status: _selectedStatus,
        initials: Patient.getInitials(_nameController.text.trim()),
        medicalHistory: _notesController.text.trim(),
      );

      if (widget.onSave != null) {
        widget.onSave!(updated);
      }

      Get.back();
      Get.snackbar(
        'Profile Updated',
        'Patient profile for ${updated.name} updated successfully!',
        backgroundColor: AppColors.primary,
        colorText: Colors.white,
        snackPosition: SnackPosition.BOTTOM,
        margin: const EdgeInsets.all(16),
        duration: const Duration(seconds: 3),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFB),
      appBar: AppBar(
        backgroundColor: AppColors.surface,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_rounded, color: AppColors.textPrimary),
          onPressed: () => Get.back(),
        ),
        title: Text(
          'Edit Patient Profile',
          style: GoogleFonts.poppins(
            fontSize: 18,
            fontWeight: FontWeight.w700,
            color: AppColors.textPrimary,
          ),
        ),
        actions: [
          Padding(
            padding: const EdgeInsets.only(right: 12, top: 10, bottom: 10),
            child: ElevatedButton.icon(
              onPressed: _saveProfile,
              icon: const Icon(Icons.check_rounded, size: 16),
              label: Text(
                'Save',
                style: GoogleFonts.poppins(
                  fontSize: 12,
                  fontWeight: FontWeight.w600,
                ),
              ),
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.primary,
                foregroundColor: Colors.white,
                elevation: 0,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(10),
                ),
              ),
            ),
          ),
        ],
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(20),
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Avatar Picture Card Header
                _buildAvatarSection(),
                const SizedBox(height: 24),

                // Personal Details Card
                _buildSectionHeader(
                  title: 'Personal Information',
                  icon: Icons.person_outline_rounded,
                ),
                const SizedBox(height: 12),
                _buildPersonalFieldsCard(),
                const SizedBox(height: 24),

                // Contact Details Card
                _buildSectionHeader(
                  title: 'Contact & Address',
                  icon: Icons.location_on_outlined,
                ),
                const SizedBox(height: 12),
                _buildContactFieldsCard(),
                const SizedBox(height: 24),

                // Medical History & Status Card
                _buildSectionHeader(
                  title: 'Dental & Medical Record',
                  icon: Icons.health_and_safety_outlined,
                ),
                const SizedBox(height: 12),
                _buildMedicalFieldsCard(),
                const SizedBox(height: 28),

                // Submit Save Button
                SizedBox(
                  width: double.infinity,
                  height: 52,
                  child: ElevatedButton(
                    onPressed: _saveProfile,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppColors.primary,
                      foregroundColor: Colors.white,
                      elevation: 2,
                      shadowColor: AppColors.primary.withValues(alpha: 0.4),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(16),
                      ),
                    ),
                    child: Text(
                      'Save Changes',
                      style: GoogleFonts.poppins(
                        fontSize: 16,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ),
                ),
                const SizedBox(height: 20),
              ],
            ),
          ),
        ),
      ),
      bottomNavigationBar: const DentiFlowBottomNav(currentIndex: 2),
    );
  }

  Widget _buildAvatarSection() {
    return Center(
      child: Stack(
        children: [
          CircleAvatar(
            radius: 46,
            backgroundColor: const Color(0xFFE0F4F2),
            child: Text(
              widget.patient.initials,
              style: GoogleFonts.poppins(
                fontSize: 32,
                fontWeight: FontWeight.w700,
                color: const Color(0xFF3C6A7A),
              ),
            ),
          ),
          Positioned(
            right: 0,
            bottom: 0,
            child: Container(
              width: 32,
              height: 32,
              decoration: BoxDecoration(
                color: AppColors.primary,
                shape: BoxShape.circle,
                border: Border.all(color: Colors.white, width: 2),
              ),
              child: const Icon(Icons.camera_alt_rounded,
                  color: Colors.white, size: 16),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSectionHeader({required String title, required IconData icon}) {
    return Row(
      children: [
        Container(
          padding: const EdgeInsets.all(6),
          decoration: BoxDecoration(
            color: AppColors.tealLight,
            borderRadius: BorderRadius.circular(8),
          ),
          child: Icon(icon, color: AppColors.primary, size: 18),
        ),
        const SizedBox(width: 10),
        Text(
          title,
          style: GoogleFonts.poppins(
            fontSize: 16,
            fontWeight: FontWeight.w700,
            color: AppColors.textPrimary,
          ),
        ),
      ],
    );
  }

  Widget _buildPersonalFieldsCard() {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildTextFieldLabel('Full Patient Name'),
          const SizedBox(height: 6),
          TextFormField(
            controller: _nameController,
            style: GoogleFonts.poppins(fontSize: 13, color: AppColors.textPrimary),
            validator: (val) =>
                val == null || val.trim().isEmpty ? 'Enter patient name' : null,
            decoration: _inputDecoration('e.g. John Smith', Icons.person_outline),
          ),
          const SizedBox(height: 14),

          // Gender Chips & Age
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _buildTextFieldLabel('Gender'),
                    const SizedBox(height: 6),
                    Row(
                      children: ['Male', 'Female'].map((g) {
                        final selected = _selectedGender == g;
                        return Padding(
                          padding: const EdgeInsets.only(right: 8),
                          child: ChoiceChip(
                            label: Text(g),
                            selected: selected,
                            selectedColor: AppColors.primary,
                            backgroundColor: const Color(0xFFF6F8F9),
                            labelStyle: GoogleFonts.poppins(
                              fontSize: 11,
                              color: selected ? Colors.white : AppColors.textPrimary,
                            ),
                            onSelected: (val) {
                              if (val) setState(() => _selectedGender = g);
                            },
                          ),
                        );
                      }).toList(),
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 10),
              SizedBox(
                width: 90,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _buildTextFieldLabel('Age'),
                    const SizedBox(height: 6),
                    TextFormField(
                      controller: _ageController,
                      keyboardType: TextInputType.number,
                      style: GoogleFonts.poppins(fontSize: 13),
                      decoration: _inputDecoration('30', null),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 14),

          // Blood Group Dropdown
          _buildTextFieldLabel('Blood Group'),
          const SizedBox(height: 6),
          DropdownButtonFormField<String>(
            initialValue: _selectedBloodGroup,
            items: _bloodGroups
                .map((bg) => DropdownMenuItem(value: bg, child: Text(bg)))
                .toList(),
            onChanged: (val) {
              if (val != null) setState(() => _selectedBloodGroup = val);
            },
            decoration: _inputDecoration('', Icons.opacity_outlined),
          ),
        ],
      ),
    );
  }

  Widget _buildContactFieldsCard() {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildTextFieldLabel('Phone Number'),
          const SizedBox(height: 6),
          TextFormField(
            controller: _phoneController,
            keyboardType: TextInputType.phone,
            style: GoogleFonts.poppins(fontSize: 13),
            validator: (val) =>
                val == null || val.trim().isEmpty ? 'Enter phone number' : null,
            decoration: _inputDecoration('+1 (555) 123-4567', Icons.phone_outlined),
          ),
          const SizedBox(height: 14),

          _buildTextFieldLabel('Email Address'),
          const SizedBox(height: 6),
          TextFormField(
            controller: _emailController,
            keyboardType: TextInputType.emailAddress,
            style: GoogleFonts.poppins(fontSize: 13),
            decoration: _inputDecoration('patient@email.com', Icons.email_outlined),
          ),
          const SizedBox(height: 14),

          _buildTextFieldLabel('Emergency Contact'),
          const SizedBox(height: 6),
          TextFormField(
            controller: _emergencyContactController,
            style: GoogleFonts.poppins(fontSize: 13),
            decoration:
                _inputDecoration('Emergency phone & relation', Icons.contact_phone_outlined),
          ),
          const SizedBox(height: 14),

          _buildTextFieldLabel('Residential Address'),
          const SizedBox(height: 6),
          TextFormField(
            controller: _addressController,
            style: GoogleFonts.poppins(fontSize: 13),
            decoration: _inputDecoration('Full address', Icons.home_outlined),
          ),
        ],
      ),
    );
  }

  Widget _buildMedicalFieldsCard() {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildTextFieldLabel('Patient Status'),
          const SizedBox(height: 6),
          DropdownButtonFormField<PatientStatus>(
            initialValue: _selectedStatus,
            items: const [
              DropdownMenuItem(value: PatientStatus.active, child: Text('Active')),
              DropdownMenuItem(value: PatientStatus.inactive, child: Text('Inactive')),
              DropdownMenuItem(value: PatientStatus.newPatient, child: Text('New Patient')),
            ],
            onChanged: (val) {
              if (val != null) setState(() => _selectedStatus = val);
            },
            decoration: _inputDecoration('', Icons.info_outline),
          ),
          const SizedBox(height: 14),

          _buildTextFieldLabel('Assigned Dentist'),
          const SizedBox(height: 6),
          DropdownButtonFormField<String>(
            initialValue: _selectedDoctor,
            items: _doctors
                .map((doc) => DropdownMenuItem(value: doc, child: Text(doc)))
                .toList(),
            onChanged: (val) {
              if (val != null) setState(() => _selectedDoctor = val);
            },
            decoration: _inputDecoration('', Icons.medical_services_outlined),
          ),
          const SizedBox(height: 14),

          _buildTextFieldLabel('Medical History & Allergy Notes'),
          const SizedBox(height: 6),
          TextFormField(
            controller: _notesController,
            maxLines: 3,
            style: GoogleFonts.poppins(fontSize: 13),
            decoration: InputDecoration(
              hintText: 'Add medical history, allergies, notes...',
              filled: true,
              fillColor: const Color(0xFFF6F8F9),
              border: OutlineInputBorder(
                borderRadius: BorderRadius.circular(14),
                borderSide: BorderSide.none,
              ),
              contentPadding: const EdgeInsets.all(14),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTextFieldLabel(String text) {
    return Text(
      text,
      style: GoogleFonts.poppins(
        fontSize: 12,
        fontWeight: FontWeight.w600,
        color: AppColors.textPrimary,
      ),
    );
  }

  InputDecoration _inputDecoration(String hint, IconData? icon) {
    return InputDecoration(
      hintText: hint,
      prefixIcon: icon != null ? Icon(icon, size: 20, color: AppColors.primary) : null,
      filled: true,
      fillColor: const Color(0xFFF6F8F9),
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(14),
        borderSide: BorderSide.none,
      ),
      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
    );
  }
}
