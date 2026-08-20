import 'package:get/get.dart';
import '../modules/appointment_history/bindings/appointment_history_binding.dart';
import '../modules/appointment_history/views/appointment_history_view.dart';
import '../modules/book_appointment/bindings/book_appointment_binding.dart';
import '../modules/book_appointment/views/book_appointment_view.dart';
import '../modules/chat/bindings/chat_binding.dart';
import '../modules/chat/views/chat_detail_view.dart';
import '../modules/chat/views/chat_list_view.dart';
import '../modules/dashboard/bindings/dashboard_binding.dart';
import '../modules/dashboard/views/dashboard_view.dart';
import '../modules/edit_profile/bindings/edit_profile_binding.dart';
import '../modules/edit_profile/views/edit_profile_view.dart';
import '../modules/find_dentist/bindings/find_dentist_binding.dart';
import '../modules/find_dentist/views/find_dentist_view.dart';
import '../modules/forgot_password/bindings/forgot_password_binding.dart';
import '../modules/forgot_password/views/forgot_password_view.dart';
import '../modules/login/bindings/login_binding.dart';
import '../modules/login/views/login_view.dart';
import '../modules/medical_records/bindings/medical_records_binding.dart';
import '../modules/medical_records/views/medical_records_view.dart';
import '../modules/notifications/bindings/notifications_binding.dart';
import '../modules/notifications/views/notifications_view.dart';
import '../modules/online_payments/bindings/online_payments_binding.dart';
import '../modules/online_payments/views/online_payments_view.dart';
import '../modules/prescriptions/bindings/prescriptions_binding.dart';
import '../modules/prescriptions/views/prescriptions_view.dart';
import '../modules/register/bindings/register_binding.dart';
import '../modules/register/views/register_view.dart';
import '../modules/treatment_progress/bindings/treatment_progress_binding.dart';
import '../modules/treatment_progress/views/treatment_progress_view.dart';
import 'app_routes.dart';

class AppPages {
  AppPages._();

  static const initial = Routes.login;

  static final routes = [
    GetPage(
      name: _Paths.login,
      page: () => const LoginView(),
      binding: LoginBinding(),
    ),
    GetPage(
      name: _Paths.register,
      page: () => const RegisterView(),
      binding: RegisterBinding(),
    ),
    GetPage(
      name: _Paths.forgotPassword,
      page: () => const ForgotPasswordView(),
      binding: ForgotPasswordBinding(),
    ),
    GetPage(
      name: _Paths.dashboard,
      page: () => const DashboardView(),
      binding: DashboardBinding(),
    ),
    GetPage(
      name: _Paths.findDentist,
      page: () => const FindDentistView(),
      binding: FindDentistBinding(),
    ),
    GetPage(
      name: _Paths.bookAppointment,
      page: () => const BookAppointmentView(),
      binding: BookAppointmentBinding(),
    ),
    GetPage(
      name: _Paths.appointmentHistory,
      page: () => const AppointmentHistoryView(),
      binding: AppointmentHistoryBinding(),
    ),
    GetPage(
      name: _Paths.medicalRecords,
      page: () => const MedicalRecordsView(),
      binding: MedicalRecordsBinding(),
    ),
    GetPage(
      name: _Paths.prescriptions,
      page: () => const PrescriptionsView(),
      binding: PrescriptionsBinding(),
    ),
    GetPage(
      name: _Paths.onlinePayments,
      page: () => const OnlinePaymentsView(),
      binding: OnlinePaymentsBinding(),
    ),
    GetPage(
      name: _Paths.chat,
      page: () => const ChatListView(),
      binding: ChatBinding(),
    ),
    GetPage(
      name: _Paths.chatDetail,
      page: () => const ChatDetailView(),
      binding: ChatBinding(),
    ),
    GetPage(
      name: _Paths.treatmentProgress,
      page: () => const TreatmentProgressView(),
      binding: TreatmentProgressBinding(),
    ),
    GetPage(
      name: _Paths.notifications,
      page: () => const NotificationsView(),
      binding: NotificationsBinding(),
    ),
    GetPage(
      name: _Paths.editProfile,
      page: () => const EditProfileView(),
      binding: EditProfileBinding(),
    ),
  ];
}

abstract class _Paths {
  _Paths._();
  static const login = '/login';
  static const register = '/register';
  static const forgotPassword = '/forgot-password';
  static const dashboard = '/dashboard';
  static const findDentist = '/find-dentist';
  static const bookAppointment = '/book-appointment';
  static const appointmentHistory = '/appointment-history';
  static const medicalRecords = '/medical-records';
  static const prescriptions = '/prescriptions';
  static const onlinePayments = '/online-payments';
  static const chat = '/chat';
  static const chatDetail = '/chat-detail';
  static const treatmentProgress = '/treatment-progress';
  static const notifications = '/notifications';
  static const editProfile = '/edit-profile';
}
