import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import '../models/patient_model.dart';
import '../theme/app_colors.dart';
import '../widgets/dentiflow_bottom_nav.dart';
import '../widgets/patient_details_dialog.dart';
import 'edit_patient_profile_screen.dart';

class PatientsScreen extends StatefulWidget {
  const PatientsScreen({super.key});

  @override
  State<PatientsScreen> createState() => _PatientsScreenState();
}

class _PatientsScreenState extends State<PatientsScreen> {
  late List<Patient> _patients;
  int _selectedFilterIndex = 0; // 0: All, 1: Active, 2: Inactive, 3: New
  final TextEditingController _searchController = TextEditingController();
  String _searchQuery = '';

  @override
  void initState() {
    super.initState();
    _patients = Patient.getInitialSamplePatients();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  // Counts for statistics
  int get _countTotal => _patients.length + 1241; // Mock matching total
  int get _countActive =>
      _patients.where((p) => p.status == PatientStatus.active).length + 980;
  int get _countInactive =>
      _patients.where((p) => p.status == PatientStatus.inactive).length + 261;
  int get _countNew =>
      _patients.where((p) => p.status == PatientStatus.newPatient).length + 123;

  List<Patient> get _filteredPatients {
    return _patients.where((p) {
      bool matchesFilter;
      switch (_selectedFilterIndex) {
        case 1:
          matchesFilter = p.status == PatientStatus.active;
          break;
        case 2:
          matchesFilter = p.status == PatientStatus.inactive;
          break;
        case 3:
          matchesFilter = p.status == PatientStatus.newPatient;
          break;
        default:
          matchesFilter = true;
      }

      final matchesQuery = _searchQuery.isEmpty ||
          p.name.toLowerCase().contains(_searchQuery.toLowerCase()) ||
          p.phone.contains(_searchQuery);

      return matchesFilter && matchesQuery;
    }).toList();
  }

  // CRUD Actions
  void _updatePatient(Patient updatedPatient) {
    setState(() {
      final index = _patients.indexWhere((p) => p.id == updatedPatient.id);
      if (index != -1) {
        _patients[index] = updatedPatient;
      }
    });
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text('Updated patient details for ${updatedPatient.name}'),
        backgroundColor: AppColors.primary,
        behavior: SnackBarBehavior.floating,
      ),
    );
  }

  void _deletePatient(String id) {
    setState(() {
      _patients.removeWhere((p) => p.id == id);
    });
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text('Patient record removed'),
        backgroundColor: AppColors.error,
        behavior: SnackBarBehavior.floating,
      ),
    );
  }

  void _openEditModal(Patient patient) {
    Get.to(() => EditPatientProfileScreen(
          patient: patient,
          onSave: _updatePatient,
        ));
  }

  void _openDetailsModal(Patient patient) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => PatientDetailsDialog(
        patient: patient,
        onEdit: _openEditModal,
        onDelete: _deletePatient,
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFB),
      body: SafeArea(
        child: Column(
          children: [
            Expanded(
              child: SingleChildScrollView(
                padding: const EdgeInsets.symmetric(horizontal: 20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const SizedBox(height: 12),
                    _buildTopHeader(),
                    const SizedBox(height: 20),
                    _buildSubHeader(),
                    const SizedBox(height: 16),
                    _buildSearchBar(),
                    const SizedBox(height: 20),
                    _buildStatsRibbon(),
                    const SizedBox(height: 20),
                    _buildFilterPills(),
                    const SizedBox(height: 20),
                    _buildPatientsList(),
                    const SizedBox(height: 20),
                  ],
                ),
              ),
            ),
            const DentiFlowBottomNav(currentIndex: 2),
          ],
        ),
      ),
    );
  }

  // 1. Top Header Row
  Widget _buildTopHeader() {
    return Row(
      children: [
        Image.asset(
          'assets/logo/logo.png',
          width: 34,
          height: 34,
          fit: BoxFit.contain,
        ),
        const SizedBox(width: 8),
        Text(
          'DentiFlow',
          style: GoogleFonts.poppins(
            fontSize: 18,
            fontWeight: FontWeight.w700,
            color: AppColors.secondary,
          ),
        ),
        const Spacer(),
        // Notification Icon with red dot
        Stack(
          clipBehavior: Clip.none,
          children: [
            Container(
              width: 40,
              height: 40,
              decoration: BoxDecoration(
                color: AppColors.surface,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: AppColors.border, width: 1),
              ),
              child: const Icon(
                Icons.notifications_outlined,
                color: AppColors.textPrimary,
                size: 21,
              ),
            ),
            Positioned(
              top: 8,
              right: 9,
              child: Container(
                width: 8,
                height: 8,
                decoration: const BoxDecoration(
                  color: AppColors.error,
                  shape: BoxShape.circle,
                ),
              ),
            ),
          ],
        ),
        const SizedBox(width: 12),
        // Doctor avatar
        Container(
          width: 40,
          height: 40,
          decoration: BoxDecoration(
            shape: BoxShape.circle,
            border: Border.all(color: AppColors.primary, width: 2),
          ),
          child: const CircleAvatar(
            backgroundColor: AppColors.primaryDark,
            child: Icon(Icons.person, color: Colors.white, size: 20),
          ),
        ),
      ],
    );
  }

  // 2. Sub-Header Title Bar
  Widget _buildSubHeader() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          'Patients',
          style: GoogleFonts.poppins(
            fontSize: 22,
            fontWeight: FontWeight.w700,
            color: AppColors.textPrimary,
          ),
        ),
        const SizedBox(height: 2),
        Text(
          'Manage and view all your patients',
          style: GoogleFonts.poppins(
            fontSize: 12,
            color: AppColors.textSecondary,
          ),
        ),
      ],
    );
  }

  // 3. Search & Filter Bar
  Widget _buildSearchBar() {
    return Container(
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: AppColors.border, width: 1),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.02),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Row(
        children: [
          const SizedBox(width: 14),
          const Icon(
            Icons.search_rounded,
            color: AppColors.primary,
            size: 22,
          ),
          const SizedBox(width: 10),
          Expanded(
            child: TextField(
              controller: _searchController,
              onChanged: (val) {
                setState(() => _searchQuery = val.trim());
              },
              style: GoogleFonts.poppins(fontSize: 13, color: AppColors.textPrimary),
              decoration: InputDecoration(
                hintText: 'Search patients by name or phone...',
                hintStyle: GoogleFonts.poppins(fontSize: 13, color: AppColors.textHint),
                border: InputBorder.none,
                contentPadding: const EdgeInsets.symmetric(vertical: 14),
              ),
            ),
          ),
          if (_searchQuery.isNotEmpty)
            IconButton(
              icon: const Icon(Icons.close_rounded, size: 18),
              onPressed: () {
                _searchController.clear();
                setState(() => _searchQuery = '');
              },
            ),
          Container(
            height: 24,
            width: 1,
            color: AppColors.divider,
          ),
          IconButton(
            icon: const Icon(Icons.tune_rounded, color: AppColors.primary, size: 20),
            onPressed: () {
              // Toggle filter
            },
          ),
          const SizedBox(width: 4),
        ],
      ),
    );
  }

  // 4. Horizontal Stats Ribbon (Total, New, Active, Inactive)
  Widget _buildStatsRibbon() {
    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      physics: const BouncingScrollPhysics(),
      child: Row(
        children: [
          _buildStatCard(
            icon: Icons.people_outline_rounded,
            label: 'Total Patients',
            count: '1,248',
            trend: '↑ 15% this month',
            isPositive: true,
          ),
          const SizedBox(width: 10),
          _buildStatCard(
            icon: Icons.person_add_alt_outlined,
            label: 'New Patients',
            count: '124',
            trend: '↑ 12% this month',
            isPositive: true,
          ),
          const SizedBox(width: 10),
          _buildStatCard(
            icon: Icons.calendar_today_outlined,
            label: 'Active Patients',
            count: '986',
            trend: '↑ 10% this month',
            isPositive: true,
          ),
          const SizedBox(width: 10),
          _buildStatCard(
            icon: Icons.access_time_rounded,
            label: 'Inactive Patients',
            count: '262',
            trend: '↓ 5% this month',
            isPositive: false,
          ),
        ],
      ),
    );
  }

  Widget _buildStatCard({
    required IconData icon,
    required String label,
    required String count,
    required String trend,
    required bool isPositive,
  }) {
    return Container(
      width: 120,
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border, width: 1),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 34,
            height: 34,
            decoration: BoxDecoration(
              color: AppColors.tealLight,
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, color: AppColors.primary, size: 18),
          ),
          const SizedBox(height: 10),
          Text(
            label,
            style: GoogleFonts.poppins(
              fontSize: 10,
              color: AppColors.textSecondary,
              height: 1.2,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            count,
            style: GoogleFonts.poppins(
              fontSize: 20,
              fontWeight: FontWeight.w700,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            trend,
            style: GoogleFonts.poppins(
              fontSize: 9,
              fontWeight: FontWeight.w600,
              color: isPositive ? AppColors.success : AppColors.error,
            ),
          ),
        ],
      ),
    );
  }

  // 5. Category Filter Pills (All, Active, Inactive, New)
  Widget _buildFilterPills() {
    final filters = [
      {'label': 'All', 'count': _countTotal},
      {'label': 'Active', 'count': _countActive},
      {'label': 'Inactive', 'count': _countInactive},
      {'label': 'New', 'count': _countNew},
    ];

    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      physics: const BouncingScrollPhysics(),
      child: Row(
        children: List.generate(filters.length, (index) {
          final isSelected = _selectedFilterIndex == index;
          final item = filters[index];

          return GestureDetector(
            onTap: () => setState(() => _selectedFilterIndex = index),
            child: AnimatedContainer(
              duration: const Duration(milliseconds: 200),
              margin: EdgeInsets.only(right: index < filters.length - 1 ? 10 : 0),
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
              decoration: BoxDecoration(
                color: isSelected ? AppColors.primary : AppColors.surface,
                borderRadius: BorderRadius.circular(24),
                border: Border.all(
                  color: isSelected ? AppColors.primary : AppColors.border,
                  width: 1,
                ),
                boxShadow: isSelected
                    ? [
                        BoxShadow(
                          color: AppColors.primary.withValues(alpha: 0.3),
                          blurRadius: 10,
                          offset: const Offset(0, 4),
                        ),
                      ]
                    : null,
              ),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Text(
                    item['label'] as String,
                    style: GoogleFonts.poppins(
                      fontSize: 12,
                      fontWeight: isSelected ? FontWeight.w600 : FontWeight.w500,
                      color: isSelected ? Colors.white : AppColors.textSecondary,
                    ),
                  ),
                  const SizedBox(width: 8),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                    decoration: BoxDecoration(
                      color: isSelected ? Colors.white : const Color(0xFFEEF2F5),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Text(
                      '${item['count']}',
                      style: GoogleFonts.poppins(
                        fontSize: 11,
                        fontWeight: FontWeight.w700,
                        color: isSelected ? AppColors.primaryDark : AppColors.textSecondary,
                      ),
                    ),
                  ),
                ],
              ),
            ),
          );
        }),
      ),
    );
  }

  // 6. Patient List Section
  Widget _buildPatientsList() {
    final filtered = _filteredPatients;

    if (filtered.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.symmetric(vertical: 40),
          child: Column(
            children: [
              const Icon(Icons.person_search_outlined, size: 48, color: AppColors.textHint),
              const SizedBox(height: 12),
              Text(
                'No patients found matching criteria',
                style: GoogleFonts.poppins(
                  fontSize: 14,
                  fontWeight: FontWeight.w500,
                  color: AppColors.textSecondary,
                ),
              ),
            ],
          ),
        ),
      );
    }

    return Column(
      children: filtered.map((patient) => _buildPatientCard(patient)).toList(),
    );
  }

  Widget _buildPatientCard(Patient patient) {
    Color statusBg;
    Color statusFg;
    String statusText;

    switch (patient.status) {
      case PatientStatus.active:
        statusBg = const Color(0xFFE6F7F5);
        statusFg = AppColors.success;
        statusText = 'Active';
        break;
      case PatientStatus.inactive:
        statusBg = const Color(0xFFF3F4F6);
        statusFg = const Color(0xFF6B7280);
        statusText = 'Inactive';
        break;
      case PatientStatus.newPatient:
        statusBg = const Color(0xFFEFF6FF);
        statusFg = const Color(0xFF3B82F6);
        statusText = 'New';
        break;
    }

    return GestureDetector(
      onTap: () => _openDetailsModal(patient),
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(18),
          border: Border.all(color: AppColors.border, width: 1),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.02),
              blurRadius: 6,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Row(
          children: [
            // Patient Initials Avatar
            CircleAvatar(
              radius: 24,
              backgroundColor: const Color(0xFFE0F4F2),
              child: Text(
                patient.initials,
                style: GoogleFonts.poppins(
                  fontSize: 15,
                  fontWeight: FontWeight.w700,
                  color: const Color(0xFF3C6A7A),
                ),
              ),
            ),
            const SizedBox(width: 14),

            // Patient Info Column
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    patient.name,
                    style: GoogleFonts.poppins(
                      fontSize: 15,
                      fontWeight: FontWeight.w700,
                      color: AppColors.textPrimary,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 4),
                  Row(
                    children: [
                      const Icon(Icons.phone_outlined, size: 13, color: AppColors.textSecondary),
                      const SizedBox(width: 4),
                      Expanded(
                        child: Text(
                          patient.phone,
                          style: GoogleFonts.poppins(
                            fontSize: 12,
                            color: AppColors.textSecondary,
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 3),
                  Row(
                    children: [
                      const Icon(Icons.calendar_today_outlined,
                          size: 12, color: Color(0xFF8A9BA8)),
                      const SizedBox(width: 4),
                      Expanded(
                        child: Text(
                          'Last visit: ${patient.lastVisit}',
                          style: GoogleFonts.poppins(
                            fontSize: 11,
                            color: const Color(0xFF8A9BA8),
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),

            // Right Status Badge & Popup Action Menu
            Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: statusBg,
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Text(
                    statusText,
                    style: GoogleFonts.poppins(
                      fontSize: 10,
                      fontWeight: FontWeight.w600,
                      color: statusFg,
                    ),
                  ),
                ),
                const SizedBox(width: 2),
                PopupMenuButton<String>(
                  icon: const Icon(
                    Icons.more_vert_rounded,
                    color: AppColors.textHint,
                    size: 20,
                  ),
                  padding: EdgeInsets.zero,
                  constraints: const BoxConstraints(),
                  onSelected: (val) {
                    if (val == 'details') {
                      _openDetailsModal(patient);
                    } else if (val == 'edit') {
                      _openEditModal(patient);
                    } else if (val == 'delete') {
                      _deletePatient(patient.id);
                    }
                  },
                  itemBuilder: (ctx) => [
                    const PopupMenuItem(
                      value: 'details',
                      child: Text('View Profile'),
                    ),
                    const PopupMenuItem(
                      value: 'edit',
                      child: Text('Edit Patient Info'),
                    ),
                    const PopupMenuItem(
                      value: 'delete',
                      child: Text('Delete Record', style: TextStyle(color: AppColors.error)),
                    ),
                  ],
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  // 7. Bottom Navigation Bar matching app design

}
