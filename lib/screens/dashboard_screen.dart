import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../models/appointment_model.dart';
import '../theme/app_colors.dart';
import '../widgets/appointment_details_dialog.dart';
import '../widgets/dentiflow_bottom_nav.dart';
import 'appointments_screen.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  int _appointmentTabIndex = 0; // 0: Upcoming, 1: Completed, 2: Cancelled
  int _activityFilterIndex = 0; // 0: All, 1: Patients, 2: Appointments, 3: Records
  final TextEditingController _searchController = TextEditingController();
  String _searchQuery = '';

  late List<Appointment> _dashboardAppointments;

  @override
  void initState() {
    super.initState();
    _dashboardAppointments = Appointment.getInitialSampleAppointments();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  List<Appointment> get _filteredAppointments {
    AppointmentStatus targetStatus;
    if (_appointmentTabIndex == 1) {
      targetStatus = AppointmentStatus.completed;
    } else if (_appointmentTabIndex == 2) {
      targetStatus = AppointmentStatus.cancelled;
    } else {
      targetStatus = AppointmentStatus.upcoming;
    }

    return _dashboardAppointments.where((a) {
      final matchesStatus = a.status == targetStatus;
      final matchesQuery = _searchQuery.isEmpty ||
          a.patientName.toLowerCase().contains(_searchQuery.toLowerCase()) ||
          a.treatment.toLowerCase().contains(_searchQuery.toLowerCase());
      return matchesStatus && matchesQuery;
    }).toList();
  }

  void _openNotificationSheet() {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      builder: (ctx) => Container(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  'Notifications',
                  style: GoogleFonts.poppins(
                    fontSize: 18,
                    fontWeight: FontWeight.w700,
                    color: AppColors.textPrimary,
                  ),
                ),
                TextButton(
                  onPressed: () => Navigator.pop(ctx),
                  child: Text('Mark all as read',
                      style: GoogleFonts.poppins(color: AppColors.primary)),
                ),
              ],
            ),
            const SizedBox(height: 12),
            _buildNotificationTile(
              title: 'New Appointment Request',
              desc: 'Sarah Lee requested Orthodontic Consultation for 02:00 PM',
              time: '10m ago',
              icon: Icons.event_available,
            ),
            const SizedBox(height: 10),
            _buildNotificationTile(
              title: 'Treatment Completed',
              desc: 'Teeth Whitening session completed for Michael Chen',
              time: '1h ago',
              icon: Icons.check_circle_outline,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildNotificationTile({
    required String title,
    required String desc,
    required String time,
    required IconData icon,
  }) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: const Color(0xFFF8FAFB),
        borderRadius: BorderRadius.circular(14),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: AppColors.tealLight,
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, color: AppColors.primary, size: 20),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: GoogleFonts.poppins(
                      fontSize: 13,
                      fontWeight: FontWeight.w600,
                      color: AppColors.textPrimary),
                ),
                Text(
                  desc,
                  style: GoogleFonts.poppins(
                      fontSize: 11, color: AppColors.textSecondary),
                ),
              ],
            ),
          ),
          Text(
            time,
            style: GoogleFonts.poppins(
                fontSize: 10, color: AppColors.textHint),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
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
                    _buildHeader(),
                    const SizedBox(height: 16),
                    _buildSearchBar(),
                    const SizedBox(height: 20),
                    _buildGreetingCard(),
                    const SizedBox(height: 20),
                    _buildStatsCards(),
                    const SizedBox(height: 24),
                    _buildAppointmentsSection(),
                    const SizedBox(height: 24),
                    _buildRecentActivity(),
                    const SizedBox(height: 24),
                  ],
                ),
              ),
            ),
            const DentiFlowBottomNav(currentIndex: 0),
          ],
        ),
      ),
    );
  }

  Widget _buildHeader() {
    return Row(
      children: [
        Image.asset(
          'assets/logo/logo.png',
          width: 36,
          height: 36,
          fit: BoxFit.contain,
        ),
        const SizedBox(width: 8),
        Text(
          'DentiFlow',
          style: GoogleFonts.poppins(
            fontSize: 20,
            fontWeight: FontWeight.w700,
            color: AppColors.secondary,
          ),
        ),
        const Spacer(),
        GestureDetector(
          onTap: _openNotificationSheet,
          child: _buildNotificationIcon(),
        ),
        const SizedBox(width: 12),
        _buildAvatar(),
      ],
    );
  }

  Widget _buildNotificationIcon() {
    return Stack(
      clipBehavior: Clip.none,
      children: [
        Container(
          width: 42,
          height: 42,
          decoration: BoxDecoration(
            color: AppColors.surface,
            borderRadius: BorderRadius.circular(14),
            border: Border.all(color: AppColors.border, width: 1),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withValues(alpha: 0.03),
                blurRadius: 6,
                offset: const Offset(0, 2),
              ),
            ],
          ),
          child: const Icon(
            Icons.notifications_outlined,
            color: AppColors.textPrimary,
            size: 22,
          ),
        ),
        Positioned(
          top: 9,
          right: 10,
          child: Container(
            width: 9,
            height: 9,
            decoration: BoxDecoration(
              color: AppColors.error,
              shape: BoxShape.circle,
              border: Border.all(color: Colors.white, width: 1.5),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildAvatar() {
    return Container(
      width: 42,
      height: 42,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        border: Border.all(color: AppColors.primary, width: 2),
      ),
      child: const CircleAvatar(
        radius: 18,
        backgroundColor: AppColors.primaryDark,
        child: Icon(Icons.person, color: Colors.white, size: 22),
      ),
    );
  }

  Widget _buildSearchBar() {
    return Container(
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border, width: 1),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.02),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: TextField(
        controller: _searchController,
        onChanged: (val) {
          setState(() => _searchQuery = val.trim());
        },
        style: GoogleFonts.poppins(fontSize: 13, color: AppColors.textPrimary),
        decoration: InputDecoration(
          hintText: 'Search patients, appointments...',
          hintStyle: GoogleFonts.poppins(fontSize: 13, color: AppColors.textHint),
          prefixIcon: const Icon(Icons.search_rounded, color: AppColors.primary, size: 22),
          suffixIcon: _searchQuery.isNotEmpty
              ? IconButton(
                  icon: const Icon(Icons.close_rounded, size: 18),
                  onPressed: () {
                    _searchController.clear();
                    setState(() => _searchQuery = '');
                  },
                )
              : null,
          border: InputBorder.none,
          contentPadding: const EdgeInsets.symmetric(vertical: 14, horizontal: 16),
        ),
      ),
    );
  }

  Widget _buildGreetingCard() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [
            Color(0xFF2C5364),
            Color(0xFF203A43),
            Color(0xFF0F2027),
          ],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(22),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF203A43).withValues(alpha: 0.3),
            blurRadius: 16,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Good Morning,',
                  style: GoogleFonts.poppins(
                    fontSize: 14,
                    color: Colors.white70,
                  ),
                ),
                Text(
                  'Dr. Ahmed 👋',
                  style: GoogleFonts.poppins(
                    fontSize: 22,
                    fontWeight: FontWeight.w700,
                    color: Colors.white,
                  ),
                ),
                const SizedBox(height: 6),
                Text(
                  'You have 12 appointments scheduled today.',
                  style: GoogleFonts.poppins(
                    fontSize: 12,
                    color: const Color(0xFFB0C4DE),
                    height: 1.4,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(width: 12),
          // Circular Progress Widget
          Column(
            children: [
              Stack(
                alignment: Alignment.center,
                children: [
                  SizedBox(
                    width: 64,
                    height: 64,
                    child: CircularProgressIndicator(
                      value: 0.65,
                      strokeWidth: 6,
                      backgroundColor: Colors.white24,
                      color: AppColors.primary,
                    ),
                  ),
                  Text(
                    '65%',
                    style: GoogleFonts.poppins(
                      fontSize: 14,
                      fontWeight: FontWeight.w700,
                      color: Colors.white,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 6),
              Text(
                'Daily Goal',
                style: GoogleFonts.poppins(
                  fontSize: 10,
                  color: Colors.white70,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildStatsCards() {
    return Row(
      children: [
        Expanded(
          child: GestureDetector(
            onTap: () {
              Navigator.of(context).push(
                MaterialPageRoute(
                  builder: (context) => const AppointmentsScreen(),
                ),
              );
            },
            child: _buildStatCard(
              icon: Icons.calendar_today_rounded,
              label: "Today's\nAppointments",
              number: '12',
              trend: '+30%',
              trendUp: true,
              accentColor: AppColors.primary,
            ),
          ),
        ),
        const SizedBox(width: 10),
        Expanded(
          child: _buildStatCard(
            icon: Icons.people_alt_rounded,
            label: 'Total\nPatients',
            number: '1,248',
            trend: '+15%',
            trendUp: true,
            accentColor: const Color(0xFF3B82F6),
          ),
        ),
        const SizedBox(width: 10),
        Expanded(
          child: _buildStatCard(
            icon: Icons.pending_actions_rounded,
            label: 'Pending\nRequests',
            number: '05',
            trend: '0%',
            trendUp: true,
            accentColor: const Color(0xFFF59E0B),
          ),
        ),
      ],
    );
  }

  Widget _buildStatCard({
    required IconData icon,
    required String label,
    required String number,
    required String trend,
    required bool trendUp,
    required Color accentColor,
  }) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: AppColors.border, width: 1),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.02),
            blurRadius: 8,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 36,
            height: 36,
            decoration: BoxDecoration(
              color: accentColor.withValues(alpha: 0.12),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, color: accentColor, size: 18),
          ),
          const SizedBox(height: 10),
          Text(
            label,
            style: GoogleFonts.poppins(
              fontSize: 10,
              color: AppColors.textSecondary,
              height: 1.3,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            number,
            style: GoogleFonts.poppins(
              fontSize: 22,
              fontWeight: FontWeight.w700,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 4),
          Row(
            children: [
              Icon(
                trendUp ? Icons.trending_up_rounded : Icons.trending_down_rounded,
                size: 13,
                color: AppColors.success,
              ),
              const SizedBox(width: 2),
              Text(
                trend,
                style: GoogleFonts.poppins(
                  fontSize: 10,
                  fontWeight: FontWeight.w600,
                  color: AppColors.success,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }



  Widget _buildAppointmentsSection() {
    final appointments = _filteredAppointments;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(
              "Today's Schedule",
              style: GoogleFonts.poppins(
                fontSize: 16,
                fontWeight: FontWeight.w700,
                color: AppColors.textPrimary,
              ),
            ),
            GestureDetector(
              onTap: () {
                Navigator.of(context).push(
                  MaterialPageRoute(
                    builder: (context) => const AppointmentsScreen(),
                  ),
                );
              },
              child: Row(
                children: [
                  Text(
                    'View All',
                    style: GoogleFonts.poppins(
                      fontSize: 13,
                      fontWeight: FontWeight.w600,
                      color: AppColors.primary,
                    ),
                  ),
                  const SizedBox(width: 2),
                  const Icon(
                    Icons.chevron_right_rounded,
                    color: AppColors.primary,
                    size: 18,
                  ),
                ],
              ),
            ),
          ],
        ),
        const SizedBox(height: 12),
        _buildAppointmentTabs(),
        const SizedBox(height: 12),
        if (appointments.isEmpty)
          Container(
            width: double.infinity,
            padding: const EdgeInsets.all(24),
            decoration: BoxDecoration(
              color: AppColors.surface,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: AppColors.border, width: 1),
            ),
            child: Center(
              child: Text(
                'No appointments in this view',
                style: GoogleFonts.poppins(
                  fontSize: 13,
                  color: AppColors.textHint,
                ),
              ),
            ),
          )
        else
          ...appointments.take(3).map((appt) => _buildAppointmentCardItem(appt)),
      ],
    );
  }

  Widget _buildAppointmentTabs() {
    final tabs = [
      {'label': 'Upcoming', 'icon': Icons.schedule_rounded},
      {'label': 'Completed', 'icon': Icons.check_circle_rounded},
      {'label': 'Cancelled', 'icon': Icons.cancel_rounded},
    ];
    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      physics: const BouncingScrollPhysics(),
      child: Row(
        children: List.generate(tabs.length, (index) {
          final isActive = _appointmentTabIndex == index;
          final item = tabs[index];
          return GestureDetector(
            onTap: () => setState(() => _appointmentTabIndex = index),
            child: AnimatedContainer(
              duration: const Duration(milliseconds: 200),
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
              margin: EdgeInsets.only(right: index < tabs.length - 1 ? 8 : 0),
              decoration: BoxDecoration(
                color: isActive ? AppColors.primary : AppColors.surface,
                borderRadius: BorderRadius.circular(20),
                border: Border.all(
                  color: isActive ? AppColors.primary : AppColors.border,
                  width: 1,
                ),
                boxShadow: isActive
                    ? [
                        BoxShadow(
                          color: AppColors.primary.withValues(alpha: 0.25),
                          blurRadius: 8,
                          offset: const Offset(0, 3),
                        ),
                      ]
                    : null,
              ),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(
                    item['icon'] as IconData,
                    size: 14,
                    color: isActive ? Colors.white : AppColors.textSecondary,
                  ),
                  const SizedBox(width: 6),
                  Text(
                    item['label'] as String,
                    style: GoogleFonts.poppins(
                      fontSize: 11,
                      fontWeight: FontWeight.w600,
                      color: isActive ? Colors.white : AppColors.textSecondary,
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

  Widget _buildAppointmentCardItem(Appointment appt) {
    Color statusColor;
    String statusText;
    switch (appt.status) {
      case AppointmentStatus.upcoming:
        statusColor = AppColors.primary;
        statusText = 'Upcoming';
        break;
      case AppointmentStatus.completed:
        statusColor = AppColors.success;
        statusText = 'Completed';
        break;
      case AppointmentStatus.cancelled:
        statusColor = AppColors.error;
        statusText = 'Cancelled';
        break;
    }

    return GestureDetector(
      onTap: () {
        showModalBottomSheet(
          context: context,
          isScrollControlled: true,
          backgroundColor: Colors.transparent,
          builder: (ctx) => AppointmentDetailsDialog(
            appointment: appt,
            onEdit: (updated) {
              setState(() {
                final idx = _dashboardAppointments.indexWhere((a) => a.id == updated.id);
                if (idx != -1) _dashboardAppointments[idx] = updated;
              });
            },
            onStatusChange: (id, newStatus) {
              setState(() {
                final idx = _dashboardAppointments.indexWhere((a) => a.id == id);
                if (idx != -1) {
                  _dashboardAppointments[idx] =
                      _dashboardAppointments[idx].copyWith(status: newStatus);
                }
              });
            },
            onDelete: (id) {
              setState(() {
                _dashboardAppointments.removeWhere((a) => a.id == id);
              });
            },
          ),
        );
      },
      child: Container(
        margin: const EdgeInsets.only(bottom: 10),
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(16),
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
            CircleAvatar(
              radius: 20,
              backgroundColor: const Color(0xFFD8F3F0),
              child: Text(
                appt.initials,
                style: GoogleFonts.poppins(
                  fontSize: 14,
                  fontWeight: FontWeight.w700,
                  color: AppColors.primaryDark,
                ),
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    appt.patientName,
                    style: GoogleFonts.poppins(
                      fontSize: 14,
                      fontWeight: FontWeight.w600,
                      color: AppColors.textPrimary,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 2),
                  Row(
                    children: [
                      const Icon(Icons.medical_services_outlined,
                          size: 12, color: AppColors.textSecondary),
                      const SizedBox(width: 4),
                      Expanded(
                        child: Text(
                          appt.treatment,
                          style: GoogleFonts.poppins(
                            fontSize: 11,
                            color: AppColors.textSecondary,
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 2),
                  Row(
                    children: [
                      const Icon(Icons.access_time_rounded,
                          size: 12, color: AppColors.textSecondary),
                      const SizedBox(width: 4),
                      Expanded(
                        child: Text(
                          appt.time,
                          style: GoogleFonts.poppins(
                            fontSize: 11,
                            color: AppColors.textSecondary,
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
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
              decoration: BoxDecoration(
                color: statusColor.withValues(alpha: 0.12),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Text(
                statusText,
                style: GoogleFonts.poppins(
                  fontSize: 10,
                  fontWeight: FontWeight.w600,
                  color: statusColor,
                ),
              ),
            ),
            const SizedBox(width: 4),
            const Icon(
              Icons.chevron_right_rounded,
              color: AppColors.textHint,
              size: 20,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildRecentActivity() {
    final allActivities = [
      {
        'type': 'patient',
        'category': 'Patients',
        'icon': Icons.person_add_rounded,
        'iconBg': const Color(0xFFEFF6FF),
        'iconFg': const Color(0xFF3B82F6),
        'title': 'New patient registered:',
        'highlight': 'Sarah Johnson',
        'time': '10:15 AM',
        'tag': 'New Patient',
        'tagBg': const Color(0xFFEFF6FF),
        'tagFg': const Color(0xFF2563EB),
        'details': 'Patient file #1048 created. Insurance verified.',
      },
      {
        'type': 'appointment',
        'category': 'Appointments',
        'icon': Icons.check_circle_rounded,
        'iconBg': const Color(0xFFECFDF5),
        'iconFg': const Color(0xFF10B981),
        'title': 'Appointment completed:',
        'highlight': 'Michael Chen',
        'time': '08:00 AM',
        'tag': 'Teeth Whitening',
        'tagBg': const Color(0xFFECFDF5),
        'tagFg': const Color(0xFF059669),
        'details': 'Treatment session completed successfully by Dr. Ahmed.',
      },
      {
        'type': 'record',
        'category': 'Records',
        'icon': Icons.edit_note_rounded,
        'iconBg': const Color(0xFFF5F3FF),
        'iconFg': const Color(0xFF8B5CF6),
        'title': 'Treatment record updated:',
        'highlight': 'Robert Williams',
        'time': 'Yesterday, 04:30 PM',
        'tag': 'Dental Record',
        'tagBg': const Color(0xFFF5F3FF),
        'tagFg': const Color(0xFF7C3AED),
        'details': 'Tooth extraction notes & post-op X-Ray attached.',
      },
      {
        'type': 'record',
        'category': 'Records',
        'icon': Icons.receipt_long_rounded,
        'iconBg': const Color(0xFFFFFBEB),
        'iconFg': const Color(0xFFF59E0B),
        'title': 'Prescription issued:',
        'highlight': 'Emma Johnson',
        'time': 'Yesterday, 02:15 PM',
        'tag': 'Prescription',
        'tagBg': const Color(0xFFFFFBEB),
        'tagFg': const Color(0xFFD97706),
        'details': 'Prescribed Amoxicillin 500mg & Painkillers for 5 days.',
      },
    ];

    List<Map<String, dynamic>> filteredActivities;
    if (_activityFilterIndex == 1) {
      filteredActivities = allActivities.where((a) => a['type'] == 'patient').toList();
    } else if (_activityFilterIndex == 2) {
      filteredActivities = allActivities.where((a) => a['type'] == 'appointment').toList();
    } else if (_activityFilterIndex == 3) {
      filteredActivities = allActivities.where((a) => a['type'] == 'record').toList();
    } else {
      filteredActivities = allActivities;
    }

    final filters = ['All', 'Patients', 'Appointments', 'Records'];

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // Title Row with Live Status
        Row(
          children: [
            Text(
              'Recent Activity',
              style: GoogleFonts.poppins(
                fontSize: 16,
                fontWeight: FontWeight.w700,
                color: AppColors.textPrimary,
              ),
            ),
            const SizedBox(width: 8),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
              decoration: BoxDecoration(
                color: AppColors.success.withValues(alpha: 0.12),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Container(
                    width: 6,
                    height: 6,
                    decoration: const BoxDecoration(
                      color: AppColors.success,
                      shape: BoxShape.circle,
                    ),
                  ),
                  const SizedBox(width: 4),
                  Text(
                    'Live Sync',
                    style: GoogleFonts.poppins(
                      fontSize: 10,
                      fontWeight: FontWeight.w600,
                      color: AppColors.success,
                    ),
                  ),
                ],
              ),
            ),
            const Spacer(),
            GestureDetector(
              onTap: () {
                _showAllActivityLogsSheet(context, allActivities);
              },
              child: Text(
                'View All',
                style: GoogleFonts.poppins(
                  fontSize: 12,
                  fontWeight: FontWeight.w600,
                  color: AppColors.primary,
                ),
              ),
            ),
          ],
        ),
        const SizedBox(height: 12),

        // Activity Category Filter Pills
        SingleChildScrollView(
          scrollDirection: Axis.horizontal,
          child: Row(
            children: List.generate(filters.length, (index) {
              final isSelected = _activityFilterIndex == index;
              return GestureDetector(
                onTap: () => setState(() => _activityFilterIndex = index),
                child: AnimatedContainer(
                  duration: const Duration(milliseconds: 200),
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
                  margin: EdgeInsets.only(right: index < filters.length - 1 ? 8 : 0),
                  decoration: BoxDecoration(
                    color: isSelected ? AppColors.secondary : AppColors.surface,
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(
                      color: isSelected ? AppColors.secondary : AppColors.border,
                      width: 1,
                    ),
                  ),
                  child: Text(
                    filters[index],
                    style: GoogleFonts.poppins(
                      fontSize: 11,
                      fontWeight: isSelected ? FontWeight.w600 : FontWeight.w500,
                      color: isSelected ? Colors.white : AppColors.textSecondary,
                    ),
                  ),
                ),
              );
            }),
          ),
        ),
        const SizedBox(height: 14),

        // Timeline Container
        Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: AppColors.surface,
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: AppColors.border, width: 1),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withValues(alpha: 0.02),
                blurRadius: 10,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          child: Column(
            children: List.generate(filteredActivities.length, (index) {
              final item = filteredActivities[index];
              final isLast = index == filteredActivities.length - 1;

              return Column(
                children: [
                  _buildTimelineItem(item),
                  if (!isLast) ...[
                    const SizedBox(height: 12),
                    const Divider(height: 1, color: AppColors.divider),
                    const SizedBox(height: 12),
                  ],
                ],
              );
            }),
          ),
        ),
      ],
    );
  }

  Widget _buildTimelineItem(Map<String, dynamic> item) {
    final icon = item['icon'] as IconData;
    final iconBg = item['iconBg'] as Color;
    final iconFg = item['iconFg'] as Color;
    final title = item['title'] as String;
    final highlight = item['highlight'] as String;
    final time = item['time'] as String;
    final tag = item['tag'] as String;
    final tagBg = item['tagBg'] as Color;
    final tagFg = item['tagFg'] as Color;

    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: () {
          _showActivityDetailModal(item);
        },
        borderRadius: BorderRadius.circular(12),
        child: Padding(
          padding: const EdgeInsets.symmetric(vertical: 4),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Left Icon Avatar
              Container(
                width: 40,
                height: 40,
                decoration: BoxDecoration(
                  color: iconBg,
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Icon(icon, color: iconFg, size: 20),
              ),
              const SizedBox(width: 12),

              // Content Details
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Expanded(
                          child: RichText(
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            text: TextSpan(
                              text: '$title ',
                              style: GoogleFonts.poppins(
                                fontSize: 12,
                                color: AppColors.textSecondary,
                              ),
                              children: [
                                TextSpan(
                                  text: highlight,
                                  style: GoogleFonts.poppins(
                                    fontSize: 12,
                                    fontWeight: FontWeight.w700,
                                    color: AppColors.textPrimary,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                        const SizedBox(width: 6),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                          decoration: BoxDecoration(
                            color: tagBg,
                            borderRadius: BorderRadius.circular(6),
                          ),
                          child: Text(
                            tag,
                            style: GoogleFonts.poppins(
                              fontSize: 9,
                              fontWeight: FontWeight.w600,
                              color: tagFg,
                            ),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 4),
                    Row(
                      children: [
                        const Icon(
                          Icons.access_time_rounded,
                          size: 12,
                          color: AppColors.textHint,
                        ),
                        const SizedBox(width: 4),
                        Text(
                          time,
                          style: GoogleFonts.poppins(
                            fontSize: 11,
                            color: AppColors.textHint,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  void _showActivityDetailModal(Map<String, dynamic> item) {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      builder: (ctx) => Container(
        padding: const EdgeInsets.all(24),
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
              children: [
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: item['iconBg'] as Color,
                    borderRadius: BorderRadius.circular(14),
                  ),
                  child: Icon(item['icon'] as IconData,
                      color: item['iconFg'] as Color, size: 24),
                ),
                const SizedBox(width: 14),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        item['highlight'] as String,
                        style: GoogleFonts.poppins(
                          fontSize: 18,
                          fontWeight: FontWeight.w700,
                          color: AppColors.textPrimary,
                        ),
                      ),
                      Text(
                        item['title'] as String,
                        style: GoogleFonts.poppins(
                          fontSize: 12,
                          color: AppColors.textSecondary,
                        ),
                      ),
                    ],
                  ),
                ),
                IconButton(
                  icon: const Icon(Icons.close, color: AppColors.textHint),
                  onPressed: () => Navigator.pop(ctx),
                ),
              ],
            ),
            const SizedBox(height: 16),
            const Divider(height: 1, color: AppColors.divider),
            const SizedBox(height: 16),
            Text(
              'Activity Details',
              style: GoogleFonts.poppins(
                fontSize: 13,
                fontWeight: FontWeight.w600,
                color: AppColors.textPrimary,
              ),
            ),
            const SizedBox(height: 6),
            Text(
              item['details'] as String,
              style: GoogleFonts.poppins(
                fontSize: 13,
                color: AppColors.textSecondary,
                height: 1.5,
              ),
            ),
            const SizedBox(height: 20),
            SizedBox(
              width: double.infinity,
              height: 48,
              child: ElevatedButton.icon(
                onPressed: () {
                  Navigator.pop(ctx);
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text('Opening details for ${item['highlight']}'),
                      backgroundColor: AppColors.primary,
                      behavior: SnackBarBehavior.floating,
                    ),
                  );
                },
                icon: const Icon(Icons.arrow_forward_rounded, size: 18),
                label: const Text('View Patient Record'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppColors.primary,
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(14),
                  ),
                  elevation: 0,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  void _showAllActivityLogsSheet(
      BuildContext context, List<Map<String, dynamic>> activities) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => Container(
        height: MediaQuery.of(context).size.height * 0.7,
        decoration: const BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
        ),
        padding: const EdgeInsets.all(24),
        child: Column(
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
                  'Activity History Log',
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
            const SizedBox(height: 12),
            Expanded(
              child: ListView.separated(
                itemCount: activities.length,
                separatorBuilder: (ctx, i) =>
                    const Divider(height: 16, color: AppColors.divider),
                itemBuilder: (ctx, index) {
                  return _buildTimelineItem(activities[index]);
                },
              ),
            ),
          ],
        ),
      ),
    );
  }
}
