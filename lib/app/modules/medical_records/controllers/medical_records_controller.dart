import 'package:flutter/material.dart';
import 'package:get/get.dart';

class MedicalRecordModel {
  final String title;
  final String doctorName;
  final String date;
  final String type; // X-Ray, Lab Report, Dental Chart
  final String fileSize;

  MedicalRecordModel({
    required this.title,
    required this.doctorName,
    required this.date,
    required this.type,
    required this.fileSize,
  });
}

class MedicalRecordsController extends GetxController {
  final records = <MedicalRecordModel>[
    MedicalRecordModel(
      title: 'Panoramic Dental X-Ray',
      doctorName: 'Dr. Alex Smith',
      date: '15 Jul 2026',
      type: 'X-Ray',
      fileSize: '3.4 MB',
    ),
    MedicalRecordModel(
      title: 'Root Canal Diagnosis Report',
      doctorName: 'Dr. Emma Watson',
      date: '02 Jun 2026',
      type: 'Lab Report',
      fileSize: '1.2 MB',
    ),
    MedicalRecordModel(
      title: 'Teeth Alignment Chart',
      doctorName: 'Dr. Alex Smith',
      date: '20 Jan 2026',
      type: 'Dental Chart',
      fileSize: '2.8 MB',
    ),
  ].obs;

  void showUploadModal() {
    final titleController = TextEditingController();
    String selectedType = 'X-Ray';

    Get.bottomSheet(
      isScrollControlled: true,
      Container(
        padding: const EdgeInsets.all(22),
        decoration: const BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
        ),
        child: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text(
                    'Upload Medical Report / X-Ray',
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                  ),
                  IconButton(
                    icon: const Icon(Icons.close),
                    onPressed: () => Get.back(),
                  ),
                ],
              ),
              const SizedBox(height: 14),
              TextField(
                controller: titleController,
                decoration: const InputDecoration(
                  hintText: 'Document Title (e.g. Upper Jaw X-Ray)',
                ),
              ),
              const SizedBox(height: 14),
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(24),
                decoration: BoxDecoration(
                  color: const Color(0xFFF3FAF9),
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: const Color(0xFF329D9C), style: BorderStyle.solid),
                ),
                child: Column(
                  children: const [
                    Icon(Icons.cloud_upload_outlined, size: 40, color: Color(0xFF329D9C)),
                    SizedBox(height: 8),
                    Text(
                      'Tap to choose file or photo',
                      style: TextStyle(color: Color(0xFF329D9C), fontWeight: FontWeight.bold),
                    ),
                    SizedBox(height: 4),
                    Text('JPG, PNG, PDF up to 10MB', style: TextStyle(fontSize: 12, color: Colors.grey)),
                  ],
                ),
              ),
              const SizedBox(height: 20),
              SizedBox(
                width: double.infinity,
                height: 50,
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF329D9C)),
                  onPressed: () {
                    final title = titleController.text.trim();
                    if (title.isNotEmpty) {
                      records.insert(
                        0,
                        MedicalRecordModel(
                          title: title,
                          doctorName: 'Self Uploaded',
                          date: 'Today',
                          type: selectedType,
                          fileSize: '2.1 MB',
                        ),
                      );
                    }
                    Get.back();
                    Get.snackbar(
                      'Uploaded!',
                      'Medical record uploaded successfully.',
                      snackPosition: SnackPosition.BOTTOM,
                      backgroundColor: const Color(0xFF329D9C),
                      colorText: Colors.white,
                    );
                  },
                  child: const Text('Save Record', style: TextStyle(fontSize: 16)),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
