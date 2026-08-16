import 'dart:async';
import 'dart:math';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'login_screen.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen>
    with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double> _fadeAnimation;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1500),
    );
    _fadeAnimation = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(parent: _controller, curve: Curves.easeInOut),
    );
    _controller.forward();

    Timer(const Duration(seconds: 3), () {
      if (mounted) {
        Navigator.of(context).pushReplacement(
          MaterialPageRoute(builder: (context) => const LoginScreen()),
        );
      }
    });
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF0F5F2),
      body: Stack(
        children: [
          // Background scattered dental icons
          Positioned.fill(
            child: CustomPaint(
              painter: _DentalPatternPainter(),
            ),
          ),

          // Center content
          Center(
            child: FadeTransition(
              opacity: _fadeAnimation,
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Image.asset(
                    'assets/logo/logo.png',
                    width: 180,
                    height: 180,
                    fit: BoxFit.contain,
                  ),
                  const SizedBox(height: 16),
                  Text(
                    'Premium Dental Care & Workflow',
                    style: GoogleFonts.poppins(
                      fontSize: 13,
                      fontWeight: FontWeight.w400,
                      color: const Color(0xFF8A9BA8),
                      letterSpacing: 0.3,
                    ),
                  ),
                ],
              ),
            ),
          ),

          // Bottom teal bar
          Positioned(
            bottom: 0,
            left: 0,
            right: 0,
            child: Container(
              height: 4,
              decoration: const BoxDecoration(
                gradient: LinearGradient(
                  colors: [
                    Color(0xFF2ABFA4),
                    Color(0xFF1BAF91),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _DentalPatternPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = const Color(0xFFC5DDD7)
      ..style = PaintingStyle.stroke
      ..strokeWidth = 2.2
      ..strokeCap = StrokeCap.round
      ..strokeJoin = StrokeJoin.round;

    // --- Top-left tooth ---
    canvas.save();
    canvas.translate(-size.width * 0.06, -size.height * 0.02);
    canvas.rotate(0.45);
    _drawTooth(canvas, 40, paint);
    canvas.restore();

    // --- Top-right tooth (larger, rotated) ---
    canvas.save();
    canvas.translate(size.width * 0.88, -size.height * 0.03);
    canvas.rotate(-0.3);
    _drawTooth(canvas, 50, paint);
    canvas.restore();

    // --- Right side water drop ---
    canvas.save();
    canvas.translate(size.width * 0.82, size.height * 0.15);
    canvas.rotate(0.15);
    _drawWaterDrop(canvas, 22, paint);
    canvas.restore();

    // --- Left-center small tooth ---
    canvas.save();
    canvas.translate(size.width * 0.06, size.height * 0.42);
    canvas.rotate(0.25);
    _drawTooth(canvas, 28, paint);
    canvas.restore();

    // --- Bottom-left tooth ---
    canvas.save();
    canvas.translate(-size.width * 0.04, size.height * 0.82);
    canvas.rotate(-0.2);
    _drawTooth(canvas, 38, paint);
    canvas.restore();

    // --- Bottom-right tooth ---
    canvas.save();
    canvas.translate(size.width * 0.88, size.height * 0.85);
    canvas.rotate(0.35);
    _drawTooth(canvas, 36, paint);
    canvas.restore();
  }

  void _drawTooth(Canvas canvas, double s, Paint paint) {
    final path = Path();
    // Crown top
    path.moveTo(-s * 0.4, -s * 0.8);
    path.cubicTo(-s * 0.4, -s * 1.1, s * 0.4, -s * 1.1, s * 0.4, -s * 0.8);
    // Right side down
    path.cubicTo(s * 0.55, -s * 0.3, s * 0.45, s * 0.2, s * 0.25, s * 0.5);
    // Right root
    path.cubicTo(s * 0.15, s * 0.75, s * 0.1, s * 1.0, 0, s * 0.85);
    // Left root
    path.cubicTo(-s * 0.1, s * 1.0, -s * 0.15, s * 0.75, -s * 0.25, s * 0.5);
    // Left side up
    path.cubicTo(-s * 0.45, s * 0.2, -s * 0.55, -s * 0.3, -s * 0.4, -s * 0.8);

    canvas.drawPath(path, paint);
  }

  void _drawWaterDrop(Canvas canvas, double s, Paint paint) {
    final path = Path();
    path.moveTo(0, -s * 1.2);
    path.cubicTo(s * 0.1, -s * 0.9, s * 0.7, -s * 0.1, s * 0.5, s * 0.4);
    path.cubicTo(s * 0.3, s * 0.8, -s * 0.3, s * 0.8, -s * 0.5, s * 0.4);
    path.cubicTo(-s * 0.7, -s * 0.1, -s * 0.1, -s * 0.9, 0, -s * 1.2);
    canvas.drawPath(path, paint);
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}
