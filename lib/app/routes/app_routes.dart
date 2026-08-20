abstract class Routes {
  Routes._();
  static const login = _Paths.login;
  static const register = _Paths.register;
  static const forgotPassword = _Paths.forgotPassword;
  static const dashboard = _Paths.dashboard;
  static const findDentist = _Paths.findDentist;
  static const bookAppointment = _Paths.bookAppointment;
  static const appointmentHistory = _Paths.appointmentHistory;
  static const medicalRecords = _Paths.medicalRecords;
  static const prescriptions = _Paths.prescriptions;
  static const onlinePayments = _Paths.onlinePayments;
  static const chat = _Paths.chat;
  static const chatDetail = _Paths.chatDetail;
  static const treatmentProgress = _Paths.treatmentProgress;
  static const notifications = _Paths.notifications;
  static const editProfile = _Paths.editProfile;
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
