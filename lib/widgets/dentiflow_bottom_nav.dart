import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import '../controllers/dentiflow_controllers.dart';
import '../screens/appointments_screen.dart';
import '../screens/chat_inbox_screen.dart';
import '../screens/dashboard_screen.dart';
import '../screens/patients_screen.dart';
import '../screens/profile_screen.dart';
import '../theme/app_colors.dart';

class DentiFlowBottomNav extends StatelessWidget {
  final int currentIndex;

  const DentiFlowBottomNav({super.key, required this.currentIndex});

  @override
  Widget build(BuildContext context) {
    final NavigationController navCtrl = Get.put(NavigationController());
    
    // Keep navCtrl synced with current screen index
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (navCtrl.currentIndex.value != currentIndex) {
        navCtrl.currentIndex.value = currentIndex;
      }
    });

    final items = [
      {'icon': Icons.home_rounded, 'label': 'Home'},
      {'icon': Icons.calendar_today_rounded, 'label': 'Appointments'},
      {'icon': Icons.people_rounded, 'label': 'Patients'},
      {'icon': Icons.chat_bubble_outline_rounded, 'label': 'Messages'},
      {'icon': Icons.person_outline_rounded, 'label': 'Profile'},
    ];

    return Container(
      padding: EdgeInsets.only(
        top: 6,
        bottom: MediaQuery.of(context).padding.bottom + 4,
      ),
      decoration: const BoxDecoration(
        color: AppColors.surface,
        border: Border(
          top: BorderSide(color: AppColors.divider, width: 1),
        ),
      ),
      child: Obx(() {
        final activeTab = navCtrl.currentIndex.value;

        return Row(
          mainAxisAlignment: MainAxisAlignment.spaceEvenly,
          children: List.generate(items.length, (index) {
            final isActive = activeTab == index;

            return Expanded(
              child: InkWell(
                onTap: () {
                  if (activeTab == index && currentIndex == index) return;
                  navCtrl.changeTab(index);

                  switch (index) {
                    case 0:
                      Get.offAll(() => const DashboardScreen());
                      break;
                    case 1:
                      Get.offAll(() => const AppointmentsScreen());
                      break;
                    case 2:
                      Get.offAll(() => const PatientsScreen());
                      break;
                    case 3:
                      Get.offAll(() => const ChatInboxScreen());
                      break;
                    case 4:
                      Get.offAll(() => const ProfileScreen());
                      break;
                  }
                },
                splashColor: Colors.transparent,
                highlightColor: Colors.transparent,
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 2),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(
                        items[index]['icon'] as IconData,
                        color: isActive ? AppColors.primary : AppColors.navInactive,
                        size: 22,
                      ),
                      const SizedBox(height: 3),
                      FittedBox(
                        fit: BoxFit.scaleDown,
                        child: Text(
                          items[index]['label'] as String,
                          maxLines: 1,
                          softWrap: false,
                          overflow: TextOverflow.ellipsis,
                          style: GoogleFonts.poppins(
                            fontSize: 10,
                            fontWeight: isActive ? FontWeight.w600 : FontWeight.w500,
                            color: isActive ? AppColors.primary : AppColors.navInactive,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            );
          }),
        );
      }),
    );
  }
}
