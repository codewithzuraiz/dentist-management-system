import 'package:flutter/material.dart';
import 'package:get/get.dart';

class PaymentMethod {
  final String id;
  final String title;
  final IconData icon;

  PaymentMethod({required this.id, required this.title, required this.icon});
}

class InvoiceModel {
  final String id;
  final String title;
  final String amount;
  final String date;
  final String status; // Paid, Pending

  InvoiceModel({
    required this.id,
    required this.title,
    required this.amount,
    required this.date,
    required this.status,
  });
}

class OnlinePaymentsController extends GetxController {
  final selectedMethod = 'card'.obs;

  final paymentMethods = [
    PaymentMethod(id: 'card', title: 'Credit / Debit Card', icon: Icons.credit_card),
    PaymentMethod(id: 'easypaisa', title: 'EasyPaisa Mobile Account', icon: Icons.account_balance_wallet_outlined),
    PaymentMethod(id: 'jazzcash', title: 'JazzCash Wallet', icon: Icons.phone_android_outlined),
    PaymentMethod(id: 'bank', title: 'Direct Bank Transfer', icon: Icons.account_balance_outlined),
  ];

  final invoices = <InvoiceModel>[
    InvoiceModel(
      id: 'INV-4401',
      title: 'Teeth Cleaning & Scaling Visit',
      amount: '\$45.00',
      date: '15 Aug 2026',
      status: 'Paid',
    ),
    InvoiceModel(
      id: 'INV-4390',
      title: 'Root Canal Stage 1 Consultation',
      amount: '\$120.00',
      date: '02 Jun 2026',
      status: 'Paid',
    ),
    InvoiceModel(
      id: 'INV-4510',
      title: 'Upcoming Dental Alignment Visit',
      amount: '\$60.00',
      date: 'Tomorrow',
      status: 'Pending',
    ),
  ].obs;

  void processPayment(InvoiceModel invoice) {
    Get.snackbar(
      'Processing Payment',
      'Paying ${invoice.amount} via ${selectedMethod.value.toUpperCase()}...',
      snackPosition: SnackPosition.BOTTOM,
      backgroundColor: const Color(0xFF329D9C),
      colorText: Colors.white,
    );
  }
}
