<!DOCTYPE html>

<html lang="zxx">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport" />
    <!-- Bootstrap CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Animate CSS -->
    <link href="assets/css/animate.min.css" rel="stylesheet" />
    <!-- MeanMenu CSS -->
    <link href="assets/css/meanmenu.css" rel="stylesheet" />
    <!-- BoxIcons CSS -->
    <link href="assets/css/boxicons.min.css" rel="stylesheet" />
    <!-- FlatIcon CSS -->
    <link href="assets/css/flaticon.css" rel="stylesheet" />
    <!-- Odometer CSS -->
    <link href="assets/css/odometer.min.css" rel="stylesheet" />
    <!-- Nice Select CSS -->
    <link href="assets/css/nice-select.min.css" rel="stylesheet" />
    <!-- Carousel CSS -->
    <link href="assets/css/owl.carousel.min.css" rel="stylesheet" />
    <!-- Carousel Default CSS -->
    <link href="assets/css/owl.theme.default.min.css" rel="stylesheet" />
    <!-- Popup CSS -->
    <link href="assets/css/magnific-popup.min.css" rel="stylesheet" />
    <!-- Swiper CSS -->
    <link href="assets/css/swiper-bundle.min.css" rel="stylesheet" />
    <!-- Style CSS -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- Dark CSS -->
    <link href="assets/css/dark.css" rel="stylesheet" />
    <!-- Responsive CSS -->
    <link href="assets/css/responsive.css" rel="stylesheet" />
    <!-- Title -->
    <title>Grin - Medical Health &amp; Dental Clinic Bootstrap 5 HTML Template</title>
    <!-- Favicon -->
    <link href="assets/images/favicon.png" rel="icon" type="image/png" />
    <!-- Firebase Config -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/12.16.0/firebase-app.js";
        import { getAnalytics } from "https://www.gstatic.com/firebasejs/12.16.0/firebase-analytics.js";

        const firebaseConfig = {
            apiKey: "<?php echo FIREBASE_API_KEY; ?>",
            authDomain: "<?php echo FIREBASE_AUTH_DOMAIN; ?>",
            projectId: "<?php echo FIREBASE_PROJECT_ID; ?>",
            storageBucket: "<?php echo FIREBASE_STORAGE_BUCKET; ?>",
            messagingSenderId: "<?php echo FIREBASE_MESSAGING_SENDER_ID; ?>",
            appId: "<?php echo FIREBASE_APP_ID; ?>",
            measurementId: "<?php echo FIREBASE_MEASUREMENT_ID; ?>"
        };

        const app = initializeApp(firebaseConfig);
        const analytics = getAnalytics(app);
    </script>
</head>

<body>
    <!-- Start Preloader Area -->
    <div class="preloader">
        <div class="loader">
            <div class="sbl-half-circle-spin"></div>
        </div>
    </div>
    <!-- End Preloader Area -->
    <!-- Start Header Area -->
    <header class="header-area">
        <!-- Start Dental Tourism Top Area -->
        <div class="dental-tourism-top-area">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-lg-8 col-md-12">
                        <ul class="top-dental-tourism-information with-left">
                            <li>
                                <i class="bx bxs-phone"></i>
                                <a href="tel:08812365498835">+088 123 654 988 35</a>
                            </li>
                            <li>
                                <i class="bx bxs-map"></i>
                                35 West Dental Street, California 1004
                            </li>
                            <li>
                                <i class="bx bx-envelope-open"></i>
                                <a href="mailto:info@grin.com">info@grin.com</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <ul class="top-dental-tourism-optional">
                            <li>
                                <a href="https://www.facebook.com/" target="_blank">
                                    <i class="bx bxl-facebook"></i>
                                </a>
                            </li>
                            <li>
                                <a href="https://twitter.com/?lang=en" target="_blank">
                                    <i class="bx bxl-twitter"></i>
                                </a>
                            </li>
                            <li>
                                <a href="https://www.linkedin.com/" target="_blank">
                                    <i class="bx bxl-linkedin"></i>
                                </a>
                            </li>
                            <li>
                                <a href="https://www.instagram.com/" target="_blank">
                                    <i class="bx bxl-instagram"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Dental Tourism Top Area -->
        <!-- Start Navbar Area -->
        <div class="navbar-area dental-tourism-navbar">
            <div class="main-responsive-nav">
                <div class="container">
                    <div class="main-responsive-menu">
                        <div class="logo">
                            <a href="index.php">
                                <img alt="logo" class="main-logo" src="assets/images/logo.png" />
                                <img alt="logo" class="white-logo" src="assets/images/logo-2.png" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="main-navbar">
                <div class="container-fluid">
                    <nav class="navbar navbar-expand-md navbar-light">
                        <a class="navbar-brand" href="index.php">Grin</a>
                        <div class="collapse navbar-collapse mean-menu" id="navbarSupportedContent">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#home">Home</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#about">About</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#services">Services</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#dentist">Dentist</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#appointment">Appointment</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#review">Review</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#blog">Blog</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#contact">Contact</a>
                                </li>
                            </ul>
                            <div class="others-options d-flex align-items-center">
                                <div class="option-item">
                                    <div class="navbar-btn">
                                        <a class="default-btn" href="#appointment">Book Appointment</a>
                                    </div>
                                </div>
                                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                                <div class="option-item">
                                    <span style="color: #fff; font-size: 14px; margin-right: 10px;">
                                        <i class="bx bx-user"></i> <?php echo htmlspecialchars($_SESSION['user_email']); ?>
                                    </span>
                                </div>
                                <div class="option-item">
                                    <div class="navbar-btn">
                                        <a class="default-btn" href="logout.php" style="background: #dc3545;">Logout</a>
                                    </div>
                                </div>
                                <?php else: ?>
                                <div class="option-item">
                                    <div class="navbar-btn">
                                        <a class="default-btn" href="login.php">Login</a>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
            <div class="others-option-for-responsive">
                <div class="container">
                    <div class="dot-menu">
                        <div class="inner">
                            <div class="circle circle-one"></div>
                            <div class="circle circle-two"></div>
                            <div class="circle circle-three"></div>
                        </div>
                    </div>
                    <div class="container">
                        <div class="option-inner">
                            <div class="others-options d-flex align-items-center">
                                <div class="option-item">
                                    <div class="navbar-btn">
                                        <a class="default-btn" href="#appointment">Book Appointment</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Navbar Area -->
    </header>
    <!-- End Header Area -->
    <!-- Search Modal -->
    <div class="modal fade fade-scale searchmodal" id="searchmodal" role="dialog" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button class="close" data-bs-dismiss="modal" type="button">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="modal-search-form">
                        <input class="search-field" placeholder="Search..." type="search" />
                        <button type="submit"><i class="bx bx-search-alt"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Search Modal -->
