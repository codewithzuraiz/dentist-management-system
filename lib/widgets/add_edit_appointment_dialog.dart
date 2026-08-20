import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../models/appointment_model.dart';
import '../theme/app_colors.dart';

class AddEditAppointmentDialog extends StatefulWidget {
  final Appointment? appointment;
  final Function(Appointment) onSave;

  const AddEditAppointmentDialog({
    super.key,
    this.appointment,
    required this.onSave,
  });

  @override
  State<AddEditAppointmentDialog> createState() =>
      _AddEditAppointmentDialogState();
}

class _AddEditAppointmentDialogState
    extends State<AddEditAppointmentDialog> {
  final _formKey = GlobalKey<FormState>();
  late TextEditingController _nameController;
  late TextEditingController _doctorController;
  late TextEditingController _notesController;

  String _selectedTreatment = 'Teeth Cleaning';
  String _selectedTime = '10:30 AM';
  String _selectedDate = '20 May';
  AppointmentStatus _selectedStatus = AppointmentStatus.upcoming;

  final List<String> _treatments = [
    'Teeth Cleaning',
    'Dental Filling',
    'Tooth Extraction',
    'Orthodontic Consultation',
    'Teeth Whitening',
    'Crown Placement',
    'Root Canal Therapy',
    'Dental Checkup',
  ];

  final List<String> _timeSlots = [
    '08:00 AM',
    '09:00 AM',
    '10:30 AM',
    '12:00 PM',
    '02:00 PM',
    '03:30 PM',
    '05:00 PM',
  ];

  final List<String> _dates = [
    '20 May',
    '21 May',
    '22 May',
    '23 May',
    '24 May',
  ];

  @override
  void initState() {
    super.initState();
    _nameController =
        TextEditingController(text: widget.appointment?.patientName ?? '');
    _doctorController =
        TextEditingController(text: widget.appointment?.doctorName ?? 'Dr. Ahmed');
    _notesController =
        TextEditingController(text: widget.appointment?.notes ?? '');

    if (widget.appointment != null) {
      if (_treatments.contains(widget.appointment!.treatment)) {
        _selectedTreatment = widget.appointment!.treatment;
      }
      _selectedTime = widget.appointment!.time;
      _selectedDate = widget.appointment!.date;
      _selectedStatus = widget.appointment!.status;
    }
  }

  @override
  void dispose() {
    _nameController.dispose();
    _doctorController.dispose();
    _notesController.dispose();
    super.dispose();
  }

  void _submit() {
    if (_formKey.currentState!.validate()) {
      final name = _nameController.text.trim();
      final doctor = _doctorController.text.trim();
      final notes = _notesController.text.trim();

      final appointment = Appointment(
        id: widget.appointment?.id ??
            DateTime.now().millisecondsSinceEpoch.toString(),
        patientName: name,
        treatment: _selectedTreatment,
        doctorName: doctor.isEmpty ? 'Dr. Ahmed' : doctor,
        time: _selectedTime,
        date: _selectedDate,
        status: _selectedStatus,
        initials: Appointment.getInitials(name),
        notes: notes.isEmpty ? null : notes,
      );

      widget.onSave(appointment);
      Navigator.of(context).pop();
    }
  }

  @override
  Widget build(BuildContext context) {
    final isEditing = widget.appointment != null;

    return Container(
      decoration: const BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      padding: EdgeInsets.only(
        top: 20,
        left: 24,
        right: 24,
        bottom: MediaQuery.of(context).viewInsets.bottom + 24,
      ),
      child: SingleChildScrollView(
        child: Form(
          key: _formKey,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Top indicator handle bar
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

              // Title Row
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    isEditing ? 'Edit Appointment' : 'Book New Appointment',
                    style: GoogleFonts.poppins(
                      fontSize: 18,
                      fontWeight: FontWeight.w700,
                      color: AppColors.textPrimary,
                    ),
                  ),
                  IconButton(
                    icon: const Icon(Icons.close, color: AppColors.textHint),
                    onPressed: () => Navigator.of(context).pop(),
                  ),
                ],
              ),
              const SizedBox(height: 16),

              // Patient Name Field
              Text(
                'Patient Name',
                style: GoogleFonts.poppins(
                  fontSize: 13,
                  fontWeight: FontWeight.w600,
                  color: AppColors.textPrimary,
                ),
              ),
              const SizedBox(height: 6),
              TextFormField(
                controller: _nameController,
                style: GoogleFonts.poppins(
                  fontSize: 14,
                  color: AppColors.textPrimary,
                ),
                validator: (val) {
                  if (val == null || val.trim().isEmpty) {
                    return 'Please enter patient name';
                  }
                  return null;
                },
                decoration: InputDecoration(
                  hintText: 'e.g. John Smith',
                  prefixIcon: const Icon(Icons.person_outline, size: 20),
                  filled: true,
                  fillColor: const Color(0xFFF6F8F9),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(14),
                    borderSide: BorderSide.none,
                  ),
                  contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                ),
              ),
              const SizedBox(height: 16),

              // Treatment Procedure Dropdown
              Text(
                'Treatment Procedure',
                style: GoogleFonts.poppins(
                  fontSize: 13,
                  fontWeight: FontWeight.w600,
                  color: AppColors.textPrimary,
                ),
              ),
              const SizedBox(height: 6),
              DropdownButtonFormField<String>(
                initialValue: _selectedTreatment,
                isExpanded: true,
                items: _treatments.map((t) {
                  return DropdownMenuItem(
                    value: t,
                    child: Text(
                      t,
                      style: GoogleFonts.poppins(fontSize: 14),
                    ),
                  );
                }).toList(),
                onChanged: (val) {
                  if (val != null) {
                    setState(() => _selectedTreatment = val);
                  }
                },
                decoration: InputDecoration(
                  prefixIcon: const Icon(Icons.medical_services_outlined, size: 20),
                  filled: true,
                  fillColor: const Color(0xFFF6F8F9),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(14),
                    borderSide: BorderSide.none,
                  ),
                  contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                ),
              ),
              const SizedBox(height: 16),

              // Doctor Name Field
              Text(
                'Doctor',
                style: GoogleFonts.poppins(
                  fontSize: 13,
                  fontWeight: FontWeight.w600,
                  color: AppColors.textPrimary,
                ),
              ),
              const SizedBox(height: 6),
              TextFormField(
                controller: _doctorController,
                style: GoogleFonts.poppins(
                  fontSize: 14,
                  color: AppColors.textPrimary,
                ),
                decoration: InputDecoration(
                  hintText: 'e.g. Dr. Ahmed',
                  prefixIcon: const Icon(Icons.badge_outlined, size: 20),
                  filled: true,
                  fillColor: const Color(0xFFF6F8F9),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(14),
                    borderSide: BorderSide.none,
                  ),
                  contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                ),
              ),
              const SizedBox(height: 16),

              // Date Selection Chips
              Text(
                'Select Date',
                style: GoogleFonts.poppins(
                  fontSize: 13,
                  fontWeight: FontWeight.w600,
                  color: AppColors.textPrimary,
                ),
              ),
              const SizedBox(height: 8),
              SingleChildScrollView(
                scrollDirection: Axis.horizontal,
                child: Row(
                  children: _dates.map((d) {
                    final selected = _selectedDate == d;
                    return Padding(
                      padding: const EdgeInsets.only(right: 8),
                      child: ChoiceChip(
                        label: Text(d),
                        selected: selected,
                        selectedColor: AppColors.primary,
                        backgroundColor: const Color(0xFFF6F8F9),
                        labelStyle: GoogleFonts.poppins(
                          fontSize: 12,
                          fontWeight: FontWeight.w500,
                          color: selected ? Colors.white : AppColors.textPrimary,
                        ),
                        onSelected: (val) {
                          if (val) setState(() => _selectedDate = d);
                        },
                      ),
                    );
                  }).toList(),
                ),
              ),
              const SizedBox(height: 16),

              // Time Slots Chips
              Text(
                'Time Slot',
                style: GoogleFonts.poppins(
                  fontSize: 13,
                  fontWeight: FontWeight.w600,
                  color: AppColors.textPrimary,
                ),
              ),
              const SizedBox(height: 8),
              Wrap(
                spacing: 8,
                runSpacing: 8,
                children: _timeSlots.map((time) {
                  final selected = _selectedTime == time;
                  return ChoiceChip(
                    label: Text(time),
                    selected: selected,
                    selectedColor: AppColors.primary,
                    backgroundColor: const Color(0xFFF6F8F9),
                    labelStyle: GoogleFonts.poppins(
                      fontSize: 12,
                      fontWeight: FontWeight.w500,
                      color: selected ? Colors.white : AppColors.textPrimary,
                    ),
                    onSelected: (val) {
                      if (val) setState(() => _selectedTime = time);
                    },
                  );
                }).toList(),
              ),
              const SizedBox(height: 16),

              // Status Dropdown (If Editing)
              if (isEditing) ...[
                Text(
                  'Status',
                  style: GoogleFonts.poppins(
                    fontSize: 13,
                    fontWeight: FontWeight.w600,
                    color: AppColors.textPrimary,
                  ),
                ),
                const SizedBox(height: 6),
                DropdownButtonFormField<AppointmentStatus>(
                  initialValue: _selectedStatus,
                  items: const [
                    DropdownMenuItem(
                      value: AppointmentStatus.upcoming,
                      child: Text('Upcoming'),
                    ),
                    DropdownMenuItem(
                      value: AppointmentStatus.completed,
                      child: Text('Completed'),
                    ),
                    DropdownMenuItem(
                      value: AppointmentStatus.cancelled,
                      child: Text('Cancelled'),
                    ),
                  ],
                  onChanged: (val) {
                    if (val != null) {
                      setState(() => _selectedStatus = val);
                    }
                  },
                  decoration: InputDecoration(
                    prefixIcon: const Icon(Icons.info_outline, size: 20),
                    filled: true,
                    fillColor: const Color(0xFFF6F8F9),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(14),
                      borderSide: BorderSide.none,
                    ),
                    contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                  ),
                ),
                const SizedBox(height: 16),
              ],

              // Notes Field
              Text(
                'Notes (Optional)',
                style: GoogleFonts.poppins(
                  fontSize: 13,
                  fontWeight: FontWeight.w600,
                  color: AppColors.textPrimary,
                ),
              ),
              const SizedBox(height: 6),
              TextFormField(
                controller: _notesController,
                maxLines: 2,
                style: GoogleFonts.poppins(
                  fontSize: 14,
                  color: AppColors.textPrimary,
                ),
                decoration: InputDecoration(
                  hintText: 'Add treatment or patient notes...',
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

              // Action Button
              SizedBox(
                width: double.infinity,
                height: 52,
                child: ElevatedButton(
                  onPressed: _submit,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.primary,
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(14),
                    ),
                    elevation: 0,
                  ),
                  child: Text(
                    isEditing ? 'Save Changes' : 'Book Appointment',
                    style: GoogleFonts.poppins(
                      fontSize: 16,
                      fontWeight: FontWeight.w600,
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
}
