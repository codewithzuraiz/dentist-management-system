import 'package:flutter/material.dart';
import 'package:get/get.dart';

/// GetX Navigation Controller for bottom nav bar & app routing
class NavigationController extends GetxController {
  final RxInt currentIndex = 0.obs;

  void changeTab(int index) {
    currentIndex.value = index;
  }
}

/// GetX Medical History Controller
class MedicalHistoryController extends GetxController {
  final RxList<Map<String, dynamic>> records = <Map<String, dynamic>>[
    {
      'id': 'm1',
      'patientName': 'Sarah Johnson',
      'condition': 'Hypertension & Penicillin Allergy',
      'category': 'Chronic / Allergy',
      'severity': 'High',
      'date': '18 May 2024',
      'notes': 'Patient reacts to Penicillin. Monitor BP prior to dental surgeries.',
      'allergies': ['Penicillin', 'Aspirin'],
    },
    {
      'id': 'm2',
      'patientName': 'John Smith',
      'condition': 'Past Root Canal & Crown Placement',
      'category': 'Procedure History',
      'severity': 'Normal',
      'date': '12 Apr 2024',
      'notes': 'Successful RCT on Tooth #14. Porcelain crown attached.',
      'allergies': ['None'],
    },
    {
      'id': 'm3',
      'patientName': 'Emma Johnson',
      'condition': 'Enamel Sensitivity',
      'category': 'Dental History',
      'severity': 'Moderate',
      'date': '10 Jan 2024',
      'notes': 'Cold sensitivity on lower molars. Applied desensitizing fluoride varnish.',
      'allergies': ['Amoxicillin'],
    },
  ].obs;

  void addRecord(Map<String, dynamic> record) {
    records.insert(0, record);
  }
}

/// GetX Digital Prescription Controller
class PrescriptionController extends GetxController {
  final RxList<Map<String, dynamic>> prescriptions = <Map<String, dynamic>>[
    {
      'id': 'rx101',
      'patientName': 'Michael Chen',
      'date': '20 May 2024',
      'doctor': 'Dr. Ahmed',
      'medicines': [
        {
          'name': 'Amoxicillin 500mg',
          'dosage': '1 Capsule',
          'frequency': '3 times daily (Every 8h)',
          'duration': '5 Days',
          'instruction': 'Take after meal',
        },
        {
          'name': 'Ibuprofen 400mg',
          'dosage': '1 Tablet',
          'frequency': 'Twice daily as needed',
          'duration': '3 Days',
          'instruction': 'Take with water for pain',
        },
      ],
      'qrCode': 'RX-2024-8901',
    },
    {
      'id': 'rx102',
      'patientName': 'Sarah Lee',
      'date': '19 May 2024',
      'doctor': 'Dr. Ahmed',
      'medicines': [
        {
          'name': 'Chlorhexidine Mouthwash 0.12%',
          'dosage': '15 ml',
          'frequency': 'Rinse twice daily',
          'duration': '7 Days',
          'instruction': 'Do not swallow. Rinse after brushing.',
        },
      ],
      'qrCode': 'RX-2024-8902',
    },
  ].obs;

  void addPrescription(Map<String, dynamic> rx) {
    prescriptions.insert(0, rx);
  }
}

/// GetX X-Rays & Dental Images Controller
class XRaysController extends GetxController {
  final RxString selectedCategory = 'All Images'.obs;

  final RxList<Map<String, dynamic>> images = <Map<String, dynamic>>[
    {
      'id': 'x1',
      'patientName': 'Robert Williams',
      'type': 'Panoramic OPG',
      'category': 'Panoramics',
      'date': '18 May 2024',
      'toothNumber': 'Full Arch',
      'imageUrl': 'assets/logo/logo.png', // Mock asset
      'doctorNotes': 'Wisdom teeth impacted on lower jaw #38 and #48.',
    },
    {
      'id': 'x2',
      'patientName': 'John Smith',
      'type': 'Periapical Radiograph',
      'category': 'Periapical X-Rays',
      'date': '15 May 2024',
      'toothNumber': 'Tooth #14',
      'imageUrl': 'assets/logo/logo.png',
      'doctorNotes': 'Bone density healthy around root apex.',
    },
    {
      'id': 'x3',
      'patientName': 'Sarah Lee',
      'type': '3D CBCT Scan',
      'category': '3D Scans',
      'date': '10 May 2024',
      'toothNumber': 'Upper Maxilla',
      'imageUrl': 'assets/logo/logo.png',
      'doctorNotes': 'Implant site evaluation for Tooth #21.',
    },
  ].obs;

  void addXRay(Map<String, dynamic> xray) {
    images.insert(0, xray);
  }
}

/// GetX Treatment Plans Controller
class TreatmentPlansController extends GetxController {
  final RxList<Map<String, dynamic>> plans = <Map<String, dynamic>>[
    {
      'id': 'tp1',
      'patientName': 'Robert Williams',
      'planTitle': 'Full Arch Restoration',
      'totalCost': '\$1,450',
      'status': 'In Progress',
      'progress': 0.60,
      'phases': [
        {'title': 'Phase 1: Scaling & Deep Hygiene', 'status': 'Completed', 'cost': '\$200'},
        {'title': 'Phase 2: Tooth Extraction (#38)', 'status': 'Completed', 'cost': '\$350'},
        {'title': 'Phase 3: Dental Crown Placement', 'status': 'In Progress', 'cost': '\$900'},
      ],
    },
    {
      'id': 'tp2',
      'patientName': 'Sarah Lee',
      'planTitle': 'Orthodontic Realignment',
      'totalCost': '\$2,800',
      'status': 'Approved',
      'progress': 0.25,
      'phases': [
        {'title': 'Phase 1: Initial Impression & Braces Fit', 'status': 'Completed', 'cost': '\$1,000'},
        {'title': 'Phase 2: Monthly Archwire Adjustment', 'status': 'In Progress', 'cost': '\$1,200'},
        {'title': 'Phase 3: Retainer Fitting', 'status': 'Pending', 'cost': '\$600'},
      ],
    },
  ].obs;
}

/// GetX Chat & Patient Inbox Controller
class ChatController extends GetxController {
  final RxList<Map<String, dynamic>> conversations = <Map<String, dynamic>>[
    {
      'id': 'c1',
      'patientName': 'Sarah Lee',
      'initials': 'SL',
      'lastMessage': 'Doctor, should I take the prescription before or after meals?',
      'time': '10:42 AM',
      'unreadCount': 2,
      'isOnline': true,
      'messages': [
        {'sender': 'patient', 'text': 'Hello Dr. Ahmed, I had a quick question.', 'time': '10:40 AM'},
        {'sender': 'doctor', 'text': 'Hello Sarah! Sure, how can I help you?', 'time': '10:41 AM'},
        {'sender': 'patient', 'text': 'Doctor, should I take the prescription before or after meals?', 'time': '10:42 AM'},
      ],
    },
    {
      'id': 'c2',
      'patientName': 'John Smith',
      'initials': 'JS',
      'lastMessage': 'Thank you! The pain has completely gone away.',
      'time': 'Yesterday',
      'unreadCount': 0,
      'isOnline': false,
      'messages': [
        {'sender': 'doctor', 'text': 'Hi John, checking in after your dental checkup.', 'time': 'Yesterday'},
        {'sender': 'patient', 'text': 'Thank you! The pain has completely gone away.', 'time': 'Yesterday'},
      ],
    },
  ].obs;

  void sendMessage(String conversationId, String messageText) {
    final index = conversations.indexWhere((c) => c['id'] == conversationId);
    if (index != -1) {
      final msgs = List<Map<String, String>>.from(conversations[index]['messages']);
      msgs.add({'sender': 'doctor', 'text': messageText, 'time': 'Just now'});
      conversations[index]['messages'] = msgs;
      conversations[index]['lastMessage'] = messageText;
      conversations[index]['time'] = 'Just now';
      conversations.refresh();
    }
  }
}

/// GetX Notifications Controller
class NotificationsController extends GetxController {
  final RxList<Map<String, dynamic>> notificationsList = <Map<String, dynamic>>[
    {
      'id': 'n1',
      'title': 'New Appointment Request',
      'desc': 'Sarah Lee requested Orthodontic Consultation for 02:00 PM today.',
      'time': '10m ago',
      'category': 'Appointment',
      'isRead': false,
      'icon': Icons.event_available,
    },
    {
      'id': 'n2',
      'title': 'New Patient Message',
      'desc': 'John Smith sent you a new message regarding post-op recovery.',
      'time': '1h ago',
      'category': 'Message',
      'isRead': false,
      'icon': Icons.chat_bubble_outline,
    },
    {
      'id': 'n3',
      'title': 'Treatment Plan Approved',
      'desc': 'Robert Williams accepted the Full Arch Restoration treatment plan.',
      'time': '3h ago',
      'category': 'Plan',
      'isRead': true,
      'icon': Icons.task_alt,
    },
  ].obs;

  void markAllAsRead() {
    for (var n in notificationsList) {
      n['isRead'] = true;
    }
    notificationsList.refresh();
  }
}
