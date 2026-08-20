import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../models/appointment_model.dart';
import '../theme/app_colors.dart';
import '../widgets/add_edit_appointment_dialog.dart';
import '../widgets/appointment_details_dialog.dart';
import '../widgets/dentiflow_bottom_nav.dart';

class AppointmentsScreen extends StatefulWidget {
  const AppointmentsScreen({super.key});

  @override
  State<AppointmentsScreen> createState() => _AppointmentsScreenState();
}

class _AppointmentsScreenState extends State<AppointmentsScreen> {
  late List<Appointment> _appointments;
  int _selectedDateIndex = 0;
  int _selectedFilterIndex = 0; // 0: All, 1: Upcoming, 2: Completed, 3: Cancelled

  final List<Map<String, String>> _dates = [
    {'day': 'Today', 'date': '20', 'month': 'May'},
    {'day': 'Wed', 'date': '21', 'month': 'May'},
    {'day': 'Thu', 'date': '22', 'month': 'May'},
    {'day': 'Fri', 'date': '23', 'month': 'May'},
    {'day': 'Sat', 'date': '24', 'month': 'May'},
  ];

  @override
  void initState() {
    super.initState();
    _appointments = Appointment.getInitialSampleAppointments();
  }

  // Helper counts for filters
  int get _countAll => _appointments.length;
  int get _countUpcoming =>
      _appointments.where((a) => a.status == AppointmentStatus.upcoming).length;
  int get _countCompleted =>
      _appointments.where((a) => a.status == AppointmentStatus.completed).length;
  int get _countCancelled =>
      _appointments.where((a) => a.status == AppointmentStatus.cancelled).length;

  List<Appointment> get _filteredAppointments {
    switch (_selectedFilterIndex) {
      case 1:
        return _appointments
            .where((a) => a.status == AppointmentStatus.upcoming)
            .toList();
      case 2:
        return _appointments
            .where((a) => a.status == AppointmentStatus.completed)
            .toList();
      case 3:
        return _appointments
            .where((a) => a.status == AppointmentStatus.cancelled)
            .toList();
      default:
        return _appointments;
    }
  }

  // CRUD Actions
  void _addAppointment(Appointment newAppt) {
    setState(() {
      _appointments.insert(0, newAppt);
    });
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text('Appointment booked for ${newAppt.patientName}'),
        backgroundColor: AppColors.primary,
        behavior: SnackBarBehavior.floating,
      ),
    );
  }

  void _updateAppointment(Appointment updatedAppt) {
    setState(() {
      final index = _appointments.indexWhere((a) => a.id == updatedAppt.id);
      if (index != -1) {
        _appointments[index] = updatedAppt;
      }
    });
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text('Updated appointment for ${updatedAppt.patientName}'),
        backgroundColor: AppColors.primary,
        behavior: SnackBarBehavior.floating,
      ),
    );
  }

  void _updateStatus(String id, AppointmentStatus status) {
    setState(() {
      final index = _appointments.indexWhere((a) => a.id == id);
      if (index != -1) {
        _appointments[index] = _appointments[index].copyWith(status: status);
      }
    });
  }

  void _deleteAppointment(String id) {
    setState(() {
      _appointments.removeWhere((a) => a.id == id);
    });
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text('Appointment record removed'),
        backgroundColor: AppColors.error,
        behavior: SnackBarBehavior.floating,
      ),
    );
  }

  void _openAddModal() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => AddEditAppointmentDialog(
        onSave: _addAppointment,
      ),
    );
  }

  void _openEditModal(Appointment appointment) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => AddEditAppointmentDialog(
        appointment: appointment,
        onSave: _updateAppointment,
      ),
    );
  }

  void _openDetailsModal(Appointment appointment) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => AppointmentDetailsDialog(
        appointment: appointment,
        onEdit: _openEditModal,
        onStatusChange: _updateStatus,
        onDelete: _deleteAppointment,
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
                    const SizedBox(height: 20),
                    _buildDateRibbon(),
                    const SizedBox(height: 20),
                    _buildFilterPills(),
                    const SizedBox(height: 24),
                    _buildAppointmentsList(),
                    const SizedBox(height: 20),
                  ],
                ),
              ),
            ),
            const DentiFlowBottomNav(currentIndex: 1),
          ],
        ),
      ),
    );
  }

  // 1. Top Bar Header (Logo, DentiFlow, Notification, Avatar)
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
        // Notification bell with red dot
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

  // 2. Sub Header Row (Back Arrow, Today's Appointments Title, Calendar Icon)
  Widget _buildSubHeader() {
    return Row(
      children: [
        GestureDetector(
          onTap: () {
            if (Navigator.of(context).canPop()) {
              Navigator.of(context).pop();
            }
          },
          child: const Icon(
            Icons.arrow_back_rounded,
            color: AppColors.textPrimary,
            size: 24,
          ),
        ),
        const SizedBox(width: 14),
        Text(
          "Today's Appointments",
          style: GoogleFonts.poppins(
            fontSize: 20,
            fontWeight: FontWeight.w700,
            color: AppColors.textPrimary,
          ),
        ),
        const Spacer(),
        GestureDetector(
          onTap: _openAddModal,
          child: Container(
            width: 40,
            height: 40,
            decoration: BoxDecoration(
              color: AppColors.tealLight,
              borderRadius: BorderRadius.circular(12),
            ),
            child: const Icon(
              Icons.edit_calendar_outlined,
              color: AppColors.primary,
              size: 20,
            ),
          ),
        ),
      ],
    );
  }

  // 3. Horizontal Date Selector Ribbon
  Widget _buildDateRibbon() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: List.generate(_dates.length, (index) {
        final item = _dates[index];
        final isSelected = _selectedDateIndex == index;

        return Expanded(
          child: GestureDetector(
            onTap: () => setState(() => _selectedDateIndex = index),
            child: Container(
              margin: EdgeInsets.only(right: index < _dates.length - 1 ? 8 : 0),
              padding: const EdgeInsets.symmetric(vertical: 14),
              decoration: BoxDecoration(
                color: isSelected ? AppColors.primary : AppColors.surface,
                borderRadius: BorderRadius.circular(18),
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
              child: Column(
                children: [
                  Text(
                    item['day']!,
                    style: GoogleFonts.poppins(
                      fontSize: 11,
                      fontWeight: FontWeight.w500,
                      color: isSelected ? Colors.white70 : AppColors.textSecondary,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    item['date']!,
                    style: GoogleFonts.poppins(
                      fontSize: 18,
                      fontWeight: FontWeight.w700,
                      color: isSelected ? Colors.white : AppColors.textPrimary,
                    ),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    item['month']!,
                    style: GoogleFonts.poppins(
                      fontSize: 11,
                      fontWeight: FontWeight.w500,
                      color: isSelected ? Colors.white70 : AppColors.textSecondary,
                    ),
                  ),
                ],
              ),
            ),
          ),
        );
      }),
    );
  }

  // 4. Category Filter Bar Pills (All, Upcoming, Completed, Cancelled)
  Widget _buildFilterPills() {
    final filters = [
      {'label': 'All', 'count': _countAll, 'icon': Icons.grid_view_rounded},
      {'label': 'Upcoming', 'count': _countUpcoming, 'icon': Icons.schedule_rounded},
      {'label': 'Completed', 'count': _countCompleted, 'icon': Icons.check_circle_rounded},
      {'label': 'Cancelled', 'count': _countCancelled, 'icon': Icons.cancel_rounded},
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
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
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
                  Icon(
                    item['icon'] as IconData,
                    size: 15,
                    color: isSelected ? Colors.white : AppColors.textSecondary,
                  ),
                  const SizedBox(width: 6),
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

  // 5. Grouped Appointments List
  Widget _buildAppointmentsList() {
    final filtered = _filteredAppointments;

    if (filtered.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.symmetric(vertical: 40),
          child: Column(
            children: [
              Icon(Icons.event_busy_outlined, size: 48, color: AppColors.textHint),
              const SizedBox(height: 12),
              Text(
                'No appointments found',
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

    final upcomingList =
        filtered.where((a) => a.status == AppointmentStatus.upcoming).toList();
    final completedList =
        filtered.where((a) => a.status == AppointmentStatus.completed).toList();
    final cancelledList =
        filtered.where((a) => a.status == AppointmentStatus.cancelled).toList();

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        if (upcomingList.isNotEmpty) ...[
          Text(
            'Upcoming',
            style: GoogleFonts.poppins(
              fontSize: 16,
              fontWeight: FontWeight.w700,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 12),
          ...upcomingList.map((appt) => _buildAppointmentCard(appt)),
          const SizedBox(height: 20),
        ],
        if (completedList.isNotEmpty) ...[
          Text(
            'Completed',
            style: GoogleFonts.poppins(
              fontSize: 16,
              fontWeight: FontWeight.w700,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 12),
          ...completedList.map((appt) => _buildAppointmentCard(appt)),
          const SizedBox(height: 20),
        ],
        if (cancelledList.isNotEmpty) ...[
          Text(
            'Cancelled',
            style: GoogleFonts.poppins(
              fontSize: 16,
              fontWeight: FontWeight.w700,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 12),
          ...cancelledList.map((appt) => _buildAppointmentCard(appt)),
          const SizedBox(height: 20),
        ],
      ],
    );
  }

  // Appointment Card Widget matching image design
  Widget _buildAppointmentCard(Appointment appt) {
    Color statusBg;
    Color statusFg;
    String statusText;

    switch (appt.status) {
      case AppointmentStatus.upcoming:
        statusBg = const Color(0xFFE6F7F5);
        statusFg = AppColors.primary;
        statusText = 'Upcoming';
        break;
      case AppointmentStatus.completed:
        statusBg = const Color(0xFFE8F8EE);
        statusFg = AppColors.success;
        statusText = 'Completed';
        break;
      case AppointmentStatus.cancelled:
        statusBg = const Color(0xFFFEE2E2);
        statusFg = AppColors.error;
        statusText = 'Cancelled';
        break;
    }

    return GestureDetector(
      onTap: () => _openDetailsModal(appt),
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: AppColors.border, width: 1),
        ),
        child: Row(
          children: [
            // Left Status Indicator Dot / Checkmark
            if (appt.status == AppointmentStatus.upcoming)
              Container(
                width: 8,
                height: 8,
                margin: const EdgeInsets.only(right: 10),
                decoration: const BoxDecoration(
                  color: AppColors.primary,
                  shape: BoxShape.circle,
                ),
              )
            else if (appt.status == AppointmentStatus.completed)
              Container(
                margin: const EdgeInsets.only(right: 8),
                child: const Icon(
                  Icons.check_circle_rounded,
                  color: AppColors.success,
                  size: 16,
                ),
              )
            else
              Container(
                width: 8,
                height: 8,
                margin: const EdgeInsets.only(right: 10),
                decoration: const BoxDecoration(
                  color: AppColors.error,
                  shape: BoxShape.circle,
                ),
              ),

            // Patient Initials Avatar
            CircleAvatar(
              radius: 24,
              backgroundColor: const Color(0xFFE0F4F2),
              child: Text(
                appt.initials,
                style: GoogleFonts.poppins(
                  fontSize: 15,
                  fontWeight: FontWeight.w700,
                  color: const Color(0xFF3C6A7A),
                ),
              ),
            ),
            const SizedBox(width: 12),

            // Content Column (Name, Treatment, Doctor)
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    appt.patientName,
                    style: GoogleFonts.poppins(
                      fontSize: 15,
                      fontWeight: FontWeight.w700,
                      color: AppColors.textPrimary,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 2),
                  Text(
                    appt.treatment,
                    style: GoogleFonts.poppins(
                      fontSize: 12,
                      fontWeight: FontWeight.w500,
                      color: AppColors.textSecondary,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 4),
                  Row(
                    children: [
                      const Icon(
                        Icons.medical_services_outlined,
                        size: 13,
                        color: Color(0xFF8A9BA8),
                      ),
                      const SizedBox(width: 4),
                      Expanded(
                        child: Text(
                          appt.doctorName,
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

            // Right Info Column (Time, Status Badge, Three Dots Menu)
            Column(
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    const Icon(
                      Icons.access_time_rounded,
                      size: 14,
                      color: AppColors.textSecondary,
                    ),
                    const SizedBox(width: 4),
                    Text(
                      appt.time,
                      style: GoogleFonts.poppins(
                        fontSize: 11,
                        fontWeight: FontWeight.w500,
                        color: AppColors.textSecondary,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 8),
                Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 10, vertical: 4),
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
                    const SizedBox(width: 4),
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
                          _openDetailsModal(appt);
                        } else if (val == 'edit') {
                          _openEditModal(appt);
                        } else if (val == 'complete') {
                          _updateStatus(appt.id, AppointmentStatus.completed);
                        } else if (val == 'cancel') {
                          _updateStatus(appt.id, AppointmentStatus.cancelled);
                        } else if (val == 'delete') {
                          _deleteAppointment(appt.id);
                        }
                      },
                      itemBuilder: (ctx) => [
                        const PopupMenuItem(
                          value: 'details',
                          child: Text('View Details'),
                        ),
                        const PopupMenuItem(
                          value: 'edit',
                          child: Text('Edit / Reschedule'),
                        ),
                        if (appt.status == AppointmentStatus.upcoming)
                          const PopupMenuItem(
                            value: 'complete',
                            child: Text('Mark as Completed'),
                          ),
                        if (appt.status == AppointmentStatus.upcoming)
                          const PopupMenuItem(
                            value: 'cancel',
                            child: Text('Cancel Appointment'),
                          ),
                        const PopupMenuItem(
                          value: 'delete',
                          child: Text('Delete Record',
                              style: TextStyle(color: AppColors.error)),
                        ),
                      ],
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

  // 6. Bottom Navigation Bar matching image design

}
