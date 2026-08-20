import 'package:flutter/material.dart';
import 'package:get/get.dart';

class DentistModel {
  final String id;
  final String name;
  final String specialty;
  final double rating;
  final int reviews;
  final int experienceYears;
  final String fee;
  final String clinicAddress;
  final String imageUrl;

  DentistModel({
    required this.id,
    required this.name,
    required this.specialty,
    required this.rating,
    required this.reviews,
    required this.experienceYears,
    required this.fee,
    required this.clinicAddress,
    required this.imageUrl,
  });
}

class FindDentistController extends GetxController {
  final searchController = TextEditingController();
  final selectedSpecialty = 'All'.obs;

  final specialties = [
    'All',
    'Orthodontist',
    'Endodontist',
    'Pediatric',
    'Periodontist',
    'General Dentist',
  ];

  final allDentists = <DentistModel>[
    DentistModel(
      id: '1',
      name: 'Dr. Alex Smith',
      specialty: 'Orthodontist',
      rating: 4.9,
      reviews: 124,
      experienceYears: 8,
      fee: '\$45',
      clinicAddress: 'DentiFlow Clinic, Downtown',
      imageUrl: 'assets/logo/logo.png',
    ),
    DentistModel(
      id: '2',
      name: 'Dr. Emma Watson',
      specialty: 'Endodontist',
      rating: 4.8,
      reviews: 98,
      experienceYears: 10,
      fee: '\$60',
      clinicAddress: 'Smile Care Center, Westside',
      imageUrl: 'assets/logo/logo.png',
    ),
    DentistModel(
      id: '3',
      name: 'Dr. Michael Chen',
      specialty: 'Pediatric',
      rating: 4.95,
      reviews: 156,
      experienceYears: 12,
      fee: '\$50',
      clinicAddress: 'Kids Dental Hub, Midtown',
      imageUrl: 'assets/logo/logo.png',
    ),
    DentistModel(
      id: '4',
      name: 'Dr. Sophia Martinez',
      specialty: 'General Dentist',
      rating: 4.7,
      reviews: 82,
      experienceYears: 6,
      fee: '\$40',
      clinicAddress: 'City Dental Care, North Plaza',
      imageUrl: 'assets/logo/logo.png',
    ),
  ].obs;

  List<DentistModel> get filteredDentists {
    final query = searchController.text.toLowerCase().trim();
    final specialty = selectedSpecialty.value;

    return allDentists.where((dentist) {
      final matchesSearch = dentist.name.toLowerCase().contains(query) ||
          dentist.specialty.toLowerCase().contains(query) ||
          dentist.clinicAddress.toLowerCase().contains(query);

      final matchesSpecialty = specialty == 'All' || dentist.specialty == specialty;

      return matchesSearch && matchesSpecialty;
    }).toList();
  }

  void selectSpecialty(String specialty) {
    selectedSpecialty.value = specialty;
  }

  @override
  void onClose() {
    searchController.dispose();
    super.onClose();
  }
}
