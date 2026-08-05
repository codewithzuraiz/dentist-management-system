<?php
/**
 * GRIN DENTAL - API Configuration
 * All API keys and settings in one place
 */

// =====================================================
// APP CONFIGURATION
// =====================================================
define('API_VERSION', 'v1');
define('API_BASE_URL', 'http://localhost/grin/api/v1');
define('API_SECRET_KEY', 'grin_dental_api_secret_2026');
define('TOKEN_EXPIRY_DAYS', 30);
define('REFRESH_TOKEN_EXPIRY_DAYS', 90);

// =====================================================
// FIREBASE (Push Notifications)
// =====================================================
define('FIREBASE_SERVER_KEY', env('FIREBASE_SERVER_KEY', 'YOUR_FIREBASE_SERVER_KEY'));
define('FIREBASE_SENDER_ID', env('FIREBASE_SENDER_ID', 'YOUR_FIREBASE_SENDER_ID'));

// =====================================================
// PAYMENT GATEWAYS
// =====================================================

// Stripe
define('STRIPE_SECRET_KEY', env('STRIPE_SECRET_KEY', 'sk_test_'));
define('STRIPE_PUBLISHABLE_KEY', env('STRIPE_PUBLISHABLE_KEY', 'pk_test_'));
define('STRIPE_WEBHOOK_SECRET', env('STRIPE_WEBHOOK_SECRET', ''));

// JazzCash
define('JAZZCASH_MERCHANT_ID', env('JAZZCASH_MERCHANT_ID', ''));
define('JAZZCASH_PASSWORD', env('JAZZCASH_PASSWORD', ''));

// EasyPaisa
define('EASYPAISA_STORE_ID', env('EASYPAISA_STORE_ID', ''));
define('EASYPAISA_API_KEY', env('EASYPAISA_API_KEY', ''));

// =====================================================
// EMAIL (SMTP)
// =====================================================
define('SMTP_HOST', env('SMTP_HOST', 'smtp.gmail.com'));
define('SMTP_PORT', env('SMTP_PORT', '587'));
define('SMTP_USER', env('SMTP_USER', ''));
define('SMTP_PASS', env('SMTP_PASS', ''));
define('SMTP_FROM_NAME', env('SMTP_FROM_NAME', 'Grin Dental Clinic'));
define('SMTP_FROM_EMAIL', env('SMTP_FROM_EMAIL', ''));

// =====================================================
// SMS GATEWAY (Twilio)
// =====================================================
define('TWILIO_SID', env('TWILIO_SID', ''));
define('TWILIO_AUTH_TOKEN', env('TWILIO_AUTH_TOKEN', ''));
define('TWILIO_PHONE', env('TWILIO_PHONE', ''));

// =====================================================
// GOOGLE MAPS
// =====================================================
define('GOOGLE_MAPS_KEY', env('GOOGLE_MAPS_KEY', ''));

// =====================================================
// RECAPTCHA
// =====================================================
define('RECAPTCHA_SITE_KEY', env('RECAPTCHA_SITE_KEY', ''));
define('RECAPTCHA_SECRET_KEY', env('RECAPTCHA_SECRET_KEY', ''));
