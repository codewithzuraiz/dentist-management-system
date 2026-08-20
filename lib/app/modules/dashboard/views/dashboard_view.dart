import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../../../core/values/app_colors.dart';
import '../../../routes/app_routes.dart';
import '../controllers/dashboard_controller.dart';

class DashboardView extends GetView<DashboardController> {
  const DashboardView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0.5,
        title: Row(
          children: [
            Image.asset(
              'assets/logo/logo.png',
              height: 36,
              fit: BoxFit.contain,
              errorBuilder: (context, error, stackTrace) => const Icon(
                Icons.medical_services_outlined,
                color: AppColors.primary,
                size: 28,
              ),
            ),
            const SizedBox(width: 8),
            const Text(
              'DentiFlow',
              style: TextStyle(
                color: AppColors.primary,
                fontWeight: FontWeight.bold,
                fontSize: 20,
              ),
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.notifications_none_outlined, color: AppColors.textPrimary),
            onPressed: () => Get.toNamed(Routes.notifications),
          ),
          IconButton(
            icon: const Icon(Icons.chat_bubble_outline_rounded, color: AppColors.primary),
            onPressed: () => Get.toNamed(Routes.chat),
          ),
          IconButton(
            icon: const Icon(Icons.logout_outlined, color: Colors.redAccent),
            onPressed: controller.logout,
          ),
        ],
      ),
      body: Obx(
        () => IndexedStack(
          index: controller.selectedTab.value,
          children: [
            _buildHomeTab(context),
            _buildAppointmentsTab(context),
            _buildRecordsTab(context),
            _buildProfileTab(context),
          ],
        ),
      ),
      bottomNavigationBar: Obx(
        () => NavigationBar(
          selectedIndex: controller.selectedTab.value,
          onDestinationSelected: controller.changeTab,
          backgroundColor: Colors.white,
          indicatorColor: AppColors.primaryLight,
          destinations: const [
            NavigationDestination(
              icon: Icon(Icons.home_outlined),
              selectedIcon: Icon(Icons.home, color: AppColors.primary),
              label: 'Home',
            ),
            NavigationDestination(
              icon: Icon(Icons.calendar_today_outlined),
              selectedIcon: Icon(Icons.calendar_today, color: AppColors.primary),
              label: 'Appointments',
            ),
            NavigationDestination(
              icon: Icon(Icons.folder_outlined),
              selectedIcon: Icon(Icons.folder, color: AppColors.primary),
              label: 'Records',
            ),
            NavigationDestination(
              icon: Icon(Icons.person_outline),
              selectedIcon: Icon(Icons.person, color: AppColors.primary),
              label: 'Profile',
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildHomeTab(BuildContext context) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Greeting Header Card
          GestureDetector(
            onTap: () => Get.toNamed(Routes.treatmentProgress),
            child: Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [Color(0xFF329D9C), Color(0xFF206D6B)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(24),
                boxShadow: [
                  BoxShadow(
                    color: AppColors.primary.withValues(alpha: 0.3),
                    blurRadius: 16,
                    offset: const Offset(0, 8),
                  ),
                ],
              ),
              child: Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          'Welcome back 👋',
                          style: TextStyle(color: Colors.white70, fontSize: 14),
                        ),
                        const SizedBox(height: 4),
                        Obx(
                          () => Text(
                            controller.patientName.value,
                            style: const TextStyle(
                              color: Colors.white,
                              fontSize: 22,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),
                        const SizedBox(height: 12),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                          decoration: BoxDecoration(
                            color: Colors.white.withValues(alpha: 0.2),
                            borderRadius: BorderRadius.circular(20),
                          ),
                          child: const Text(
                            'Treatment Progress: 65% • View Plan →',
                            style: TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.w600),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const CircleAvatar(
                    radius: 30,
                    backgroundColor: Colors.white24,
                    child: Icon(Icons.person, size: 36, color: Colors.white),
                  ),
                ],
              ),
            ),
          ),

          const SizedBox(height: 24),

          // Quick Actions Grid
          const Text(
            'Quick Services',
            style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppColors.textPrimary),
          ),
          const SizedBox(height: 14),
          GridView.count(
            crossAxisCount: 2,
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            crossAxisSpacing: 14,
            mainAxisSpacing: 14,
            childAspectRatio: 1.15,
            children: [
              _buildQuickCard(
                icon: Icons.calendar_month_outlined,
                title: 'Book Visit',
                subtitle: 'Find & schedule',
                color: const Color(0xFFE8F6F5),
                iconColor: AppColors.primary,
                onTap: () => Get.toNamed(Routes.findDentist),
              ),
              _buildQuickCard(
                icon: Icons.assignment_outlined,
                title: 'Medical Records',
                subtitle: 'History & X-Rays',
                color: const Color(0xFFFEF3C7),
                iconColor: const Color(0xFFD97706),
                onTap: () => Get.toNamed(Routes.medicalRecords),
              ),
              _buildQuickCard(
                icon: Icons.medication_outlined,
                title: 'Prescriptions',
                subtitle: 'Medicines & dosage',
                color: const Color(0xFFE0E7FF),
                iconColor: const Color(0xFF4F46E5),
                onTap: () => Get.toNamed(Routes.prescriptions),
              ),
              _buildQuickCard(
                icon: Icons.payment_outlined,
                title: 'Online Payments',
                subtitle: 'Invoices & billing',
                color: const Color(0xFFD1FAE5),
                iconColor: const Color(0xFF059669),
                onTap: () => Get.toNamed(Routes.onlinePayments),
              ),
            ],
          ),

          const SizedBox(height: 24),

          // Upcoming Appointment Section
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Upcoming Appointment',
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: AppColors.textPrimary),
              ),
              GestureDetector(
                onTap: () => Get.toNamed(Routes.appointmentHistory),
                child: const Text(
                  'See All',
                  style: TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: AppColors.primary),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          GestureDetector(
            onTap: () => Get.toNamed(Routes.appointmentHistory),
            child: Container(
              padding: const EdgeInsets.all(18),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(20),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.04),
                    blurRadius: 16,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: AppColors.primaryLight,
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: const Icon(Icons.nature_people, color: AppColors.primary, size: 32),
                  ),
                  const SizedBox(width: 14),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: const [
                        Text(
                          'Dr. Alex Smith',
                          style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: AppColors.textPrimary),
                        ),
                        SizedBox(height: 4),
                        Text(
                          'General Dental Checkup & Cleaning',
                          style: TextStyle(fontSize: 12, color: AppColors.textSecondary),
                        ),
                        SizedBox(height: 8),
                        Row(
                          children: [
                            Icon(Icons.access_time, size: 14, color: AppColors.primary),
                            SizedBox(width: 4),
                            Text('10:30 AM, Tomorrow', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w500)),
                          ],
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildQuickCard({
    required IconData icon,
    required String title,
    required String subtitle,
    required Color color,
    required Color iconColor,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: AppColors.border, width: 1),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: color,
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(icon, color: iconColor, size: 22),
            ),
            const SizedBox(height: 8),
            Text(
              title,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: AppColors.textPrimary),
            ),
            const SizedBox(height: 2),
            Text(
              subtitle,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(fontSize: 11, color: AppColors.textSecondary),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildAppointmentsTab(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(20.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.calendar_month, size: 60, color: AppColors.primary),
            const SizedBox(height: 16),
            const Text(
              'Appointments Management',
              style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: AppColors.textPrimary),
            ),
            const SizedBox(height: 8),
            const Text(
              'View upcoming visits, book new appointments, or check past history.',
              textAlign: TextAlign.center,
              style: TextStyle(color: AppColors.textSecondary),
            ),
            const SizedBox(height: 24),
            ElevatedButton.icon(
              style: ElevatedButton.styleFrom(padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12)),
              onPressed: () => Get.toNamed(Routes.appointmentHistory),
              icon: const Icon(Icons.history),
              label: const Text('View Appointment History'),
            ),
            const SizedBox(height: 12),
            OutlinedButton.icon(
              style: OutlinedButton.styleFrom(padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12)),
              onPressed: () => Get.toNamed(Routes.findDentist),
              icon: const Icon(Icons.add),
              label: const Text('Book New Appointment'),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildRecordsTab(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(20.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.folder_shared_outlined, size: 60, color: AppColors.primary),
            const SizedBox(height: 16),
            const Text(
              'Medical Records & X-Rays',
              style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: AppColors.textPrimary),
            ),
            const SizedBox(height: 8),
            const Text(
              'Access your dental X-rays, lab reports, and doctor prescriptions anytime.',
              textAlign: TextAlign.center,
              style: TextStyle(color: AppColors.textSecondary),
            ),
            const SizedBox(height: 24),
            ElevatedButton.icon(
              style: ElevatedButton.styleFrom(padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12)),
              onPressed: () => Get.toNamed(Routes.medicalRecords),
              icon: const Icon(Icons.folder_open),
              label: const Text('Open Medical Records'),
            ),
            const SizedBox(height: 12),
            OutlinedButton.icon(
              style: OutlinedButton.styleFrom(padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12)),
              onPressed: () => Get.toNamed(Routes.prescriptions),
              icon: const Icon(Icons.medication_outlined),
              label: const Text('View Prescriptions'),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildProfileTab(BuildContext context) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(20),
      child: Column(
        children: [
          const SizedBox(height: 20),
          const CircleAvatar(
            radius: 45,
            backgroundColor: AppColors.primaryLight,
            child: Icon(Icons.person, size: 50, color: AppColors.primary),
          ),
          const SizedBox(height: 12),
          Obx(
            () => Text(
              controller.patientName.value,
              style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: AppColors.textPrimary),
            ),
          ),
          const Text('sarah.johnson@example.com', style: TextStyle(fontSize: 14, color: AppColors.textSecondary)),
          const SizedBox(height: 30),
          ListTile(
            leading: const Icon(Icons.person_outline, color: AppColors.primary),
            title: const Text('Edit Personal Profile'),
            trailing: const Icon(Icons.chevron_right),
            onTap: () => Get.toNamed(Routes.editProfile),
          ),
          const Divider(),
          ListTile(
            leading: const Icon(Icons.show_chart, color: AppColors.primary),
            title: const Text('Treatment Progress Timeline'),
            trailing: const Icon(Icons.chevron_right),
            onTap: () => Get.toNamed(Routes.treatmentProgress),
          ),
          const Divider(),
          ListTile(
            leading: const Icon(Icons.payment, color: AppColors.primary),
            title: const Text('Payments & Invoices'),
            trailing: const Icon(Icons.chevron_right),
            onTap: () => Get.toNamed(Routes.onlinePayments),
          ),
          const Divider(),
          ListTile(
            leading: const Icon(Icons.chat_outlined, color: AppColors.primary),
            title: const Text('Consult Doctor Chat'),
            trailing: const Icon(Icons.chevron_right),
            onTap: () => Get.toNamed(Routes.chat),
          ),
          const Divider(),
          ListTile(
            leading: const Icon(Icons.logout, color: Colors.redAccent),
            title: const Text('Logout', style: TextStyle(color: Colors.redAccent)),
            onTap: controller.logout,
          ),
        ],
      ),
    );
  }
}
