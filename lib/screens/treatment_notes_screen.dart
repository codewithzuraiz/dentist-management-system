import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import '../theme/app_colors.dart';
import '../widgets/dentiflow_bottom_nav.dart';

class TreatmentNotesScreen extends StatefulWidget {
  const TreatmentNotesScreen({super.key});

  @override
  State<TreatmentNotesScreen> createState() => _TreatmentNotesScreenState();
}

class _TreatmentNotesScreenState extends State<TreatmentNotesScreen> {
  final List<Map<String, dynamic>> _notes = [
    {
      'id': 'tn1',
      'patientName': 'John Smith',
      'date': '20 May 2024',
      'toothNumber': 'Tooth #14 (Upper Right Molar)',
      'procedure': 'Root Canal Therapy - Obstruation Phase',
      'subjective': 'Patient complains of severe throbbing pain on biting.',
      'objective': 'Periapical X-Ray shows lucency around root apex.',
      'assessment': 'Acute irreversible pulpitis.',
      'plan': 'Perform 3-channel root canal cleaning and obturation.',
      'doctor': 'Dr. Ahmed (DDS)',
    },
    {
      'id': 'tn2',
      'patientName': 'Sarah Lee',
      'date': '18 May 2024',
      'toothNumber': 'Tooth #21 (Upper Left Incisor)',
      'procedure': 'Composite Filling',
      'subjective': 'Minor sensitivity with cold drinks.',
      'objective': 'Grade I caries detected on mesial surface.',
      'assessment': 'Enamel caries.',
      'plan': 'Restoration with Shade A2 composite resin.',
      'doctor': 'Dr. Ahmed (DDS)',
    },
  ];

  void _addNote(Map<String, dynamic> newNote) {
    setState(() {
      _notes.insert(0, newNote);
    });
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
          'Clinical Treatment Notes',
          style: GoogleFonts.poppins(
            fontSize: 18,
            fontWeight: FontWeight.w700,
            color: AppColors.textPrimary,
          ),
        ),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'SOAP Notes & Procedure Logs',
                style: GoogleFonts.poppins(
                  fontSize: 16,
                  fontWeight: FontWeight.w700,
                  color: AppColors.textPrimary,
                ),
              ),
              const SizedBox(height: 12),

              ..._notes.map((note) => _buildSOAPNoteCard(note)),
            ],
          ),
        ),
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: _openAddNoteModal,
        backgroundColor: AppColors.primary,
        icon: const Icon(Icons.edit_note_rounded, color: Colors.white),
        label: Text(
          'New Clinical Note',
          style: GoogleFonts.poppins(fontWeight: FontWeight.w600, color: Colors.white),
        ),
      ),
      bottomNavigationBar: const DentiFlowBottomNav(currentIndex: 4),
    );
  }

  Widget _buildSOAPNoteCard(Map<String, dynamic> note) {
    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: AppColors.border),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.02),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Top Row: Patient Name & Date
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                note['patientName'],
                style: GoogleFonts.poppins(
                  fontSize: 16,
                  fontWeight: FontWeight.w700,
                  color: AppColors.textPrimary,
                ),
              ),
              Text(
                note['date'],
                style: GoogleFonts.poppins(
                  fontSize: 11,
                  color: AppColors.textHint,
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),

          // Tooth Number Chip & Procedure
          Row(
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: AppColors.tealLight,
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    const Icon(Icons.health_and_safety_outlined,
                        size: 14, color: AppColors.primary),
                    const SizedBox(width: 4),
                    Text(
                      note['toothNumber'],
                      style: GoogleFonts.poppins(
                        fontSize: 11,
                        fontWeight: FontWeight.w600,
                        color: AppColors.primaryDark,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          const Divider(height: 1, color: AppColors.divider),
          const SizedBox(height: 12),

          // SOAP Section Details
          _buildSoapRow('S (Subjective):', note['subjective']),
          const SizedBox(height: 6),
          _buildSoapRow('O (Objective):', note['objective']),
          const SizedBox(height: 6),
          _buildSoapRow('A (Assessment):', note['assessment']),
          const SizedBox(height: 6),
          _buildSoapRow('P (Plan):', note['plan']),
          const SizedBox(height: 14),

          // Signature Row
          Row(
            mainAxisAlignment: MainAxisAlignment.end,
            children: [
              const Icon(Icons.verified_outlined, size: 14, color: AppColors.success),
              const SizedBox(width: 4),
              Text(
                'Signed by ${note['doctor']}',
                style: GoogleFonts.poppins(
                  fontSize: 11,
                  fontWeight: FontWeight.w500,
                  color: AppColors.textSecondary,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildSoapRow(String prefix, String text) {
    return RichText(
      text: TextSpan(
        text: '$prefix ',
        style: GoogleFonts.poppins(
          fontSize: 12,
          fontWeight: FontWeight.w700,
          color: AppColors.secondary,
        ),
        children: [
          TextSpan(
            text: text,
            style: GoogleFonts.poppins(
              fontSize: 12,
              fontWeight: FontWeight.w400,
              color: AppColors.textSecondary,
            ),
          ),
        ],
      ),
    );
  }

  void _openAddNoteModal() {
    final patientCtrl = TextEditingController();
    final toothCtrl = TextEditingController(text: 'Tooth #14');
    final subjCtrl = TextEditingController();
    final objCtrl = TextEditingController();
    final planCtrl = TextEditingController();

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
              Text(
                'New SOAP Clinical Note',
                style: GoogleFonts.poppins(
                  fontSize: 18,
                  fontWeight: FontWeight.w700,
                  color: AppColors.textPrimary,
                ),
              ),
              const SizedBox(height: 14),
              TextField(
                controller: patientCtrl,
                decoration: const InputDecoration(
                  labelText: 'Patient Name',
                  border: OutlineInputBorder(),
                ),
              ),
              const SizedBox(height: 12),
              TextField(
                controller: toothCtrl,
                decoration: const InputDecoration(
                  labelText: 'Tooth Number (e.g. Tooth #14)',
                  border: OutlineInputBorder(),
                ),
              ),
              const SizedBox(height: 12),
              TextField(
                controller: subjCtrl,
                decoration: const InputDecoration(
                  labelText: 'Subjective (Symptoms / Chief Complaint)',
                  border: OutlineInputBorder(),
                ),
              ),
              const SizedBox(height: 12),
              TextField(
                controller: objCtrl,
                decoration: const InputDecoration(
                  labelText: 'Objective (Clinical Observations)',
                  border: OutlineInputBorder(),
                ),
              ),
              const SizedBox(height: 12),
              TextField(
                controller: planCtrl,
                decoration: const InputDecoration(
                  labelText: 'Plan (Treatment Administered)',
                  border: OutlineInputBorder(),
                ),
              ),
              const SizedBox(height: 20),
              SizedBox(
                width: double.infinity,
                height: 48,
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(backgroundColor: AppColors.primary),
                  onPressed: () {
                    if (patientCtrl.text.isNotEmpty) {
                      _addNote({
                        'id': 'tn_${DateTime.now().millisecondsSinceEpoch}',
                        'patientName': patientCtrl.text.trim(),
                        'date': 'Today',
                        'toothNumber': toothCtrl.text.trim(),
                        'procedure': 'Dental Treatment',
                        'subjective': subjCtrl.text.trim().isEmpty
                            ? 'Patient presented for routine treatment.'
                            : subjCtrl.text.trim(),
                        'objective': objCtrl.text.trim().isEmpty
                            ? 'Clinical evaluation completed.'
                            : objCtrl.text.trim(),
                        'assessment': 'Normal recovery.',
                        'plan': planCtrl.text.trim().isEmpty
                            ? 'Scheduled follow-up.'
                            : planCtrl.text.trim(),
                        'doctor': 'Dr. Ahmed (DDS)',
                      });
                      Navigator.pop(ctx);
                    }
                  },
                  child: Text('Save Clinical Note',
                      style: GoogleFonts.poppins(
                          color: Colors.white, fontWeight: FontWeight.w600)),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
