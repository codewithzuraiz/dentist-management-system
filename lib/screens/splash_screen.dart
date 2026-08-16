import 'dart:async';
import 'dart:math';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

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
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF2F6F5),
      body: Stack(
        children: [
          // Background scattered dental icons
          CustomPaint(
            size: MediaQuery.of(context).size,
            painter: DentalBackgroundPainter(),
          ),

          // Main content
          Center(
            child: FadeTransition(
              opacity: _fadeAnimation,
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  // Logo
                  Image.asset(
                    'assets/logo/logo.jpeg',
                    width: 160,
                    height: 160,
                  ),
                  const SizedBox(height: 24),

                  // App name
                  Text(
                    'DentiFlow',
                    style: GoogleFonts.poppins(
                      fontSize: 34,
                      fontWeight: FontWeight.w700,
                      color: const Color(0xFF213047),
                    ),
                  ),
                  const SizedBox(height: 8),

                  // Tagline
                  Text(
                    'Premium Dental Care & Workflow',
                    style: GoogleFonts.poppins(
                      fontSize: 13,
                      fontWeight: FontWeight.w400,
                      color: const Color(0xFF8A9BA8),
                      letterSpacing: 0.5,
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
              height: 5,
              decoration: const BoxDecoration(
                gradient: LinearGradient(
                  colors: [
                    Color(0xFF1BAF91),
                    Color(0xFF14C9A6),
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

class DentalBackgroundPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = const Color(0xFFD6E8E3).withValues(alpha: 0.5)
      ..style = PaintingStyle.stroke
      ..strokeWidth = 2.0;

    final random = Random(42);

    // Draw scattered dental icons
    _drawToothOutline(canvas, size, random, paint);
    _drawWaterDrop(canvas, size, random, paint);
    _drawToothSimple(canvas, size, random, paint);
  }

  void _drawToothOutline(Canvas canvas, Size size, Random random, Paint paint) {
    // Multiple tooth outline positions
    final positions = [
      Offset(size.width * 0.08, size.height * 0.08),
      Offset(size.width * 0.85, size.height * 0.05),
      Offset(size.width * 0.92, size.height * 0.25),
      Offset(size.width * 0.05, size.height * 0.35),
      Offset(size.width * 0.88, size.height * 0.7),
      Offset(size.width * 0.08, size.height * 0.82),
      Offset(size.width * 0.75, size.height * 0.92),
      Offset(size.width * 0.15, size.height * 0.95),
    ];

    for (var pos in positions) {
      canvas.save();
      canvas.translate(pos.dx, pos.dy);
      canvas.rotate(random.nextDouble() * 0.4 - 0.2);

      final path = Path();
      double s = 18 + random.nextDouble() * 12;

      // Simple tooth shape
      path.moveTo(0, -s);
      path.cubicTo(s * 0.8, -s, s, -s * 0.3, s * 0.5, s * 0.3);
      path.cubicTo(s * 0.3, s * 0.6, s * 0.1, s, 0, s);
      path.cubicTo(-s * 0.1, s, -s * 0.3, s * 0.6, -s * 0.5, s * 0.3);
      path.cubicTo(-s, -s * 0.3, -s * 0.8, -s, 0, -s);

      canvas.drawPath(path, paint);
      canvas.restore();
    }
  }

  void _drawWaterDrop(Canvas canvas, Size size, Random random, Paint paint) {
    final positions = [
      Offset(size.width * 0.2, size.height * 0.15),
      Offset(size.width * 0.78, size.height * 0.18),
      Offset(size.width * 0.15, size.height * 0.55),
      Offset(size.width * 0.82, size.height * 0.48),
      Offset(size.width * 0.65, size.height * 0.88),
    ];

    for (var pos in positions) {
      canvas.save();
      canvas.translate(pos.dx, pos.dy);
      canvas.rotate(random.nextDouble() * 0.5 - 0.25);

      final path = Path();
      double s = 10 + random.nextDouble() * 8;

      // Water drop shape
      path.moveTo(0, -s * 1.5);
      path.cubicTo(s * 0.6, -s * 0.5, s, s * 0.3, 0, s);
      path.cubicTo(-s, s * 0.3, -s * 0.6, -s * 0.5, 0, -s * 1.5);

      canvas.drawPath(path, paint);
      canvas.restore();
    }
  }

  void _drawToothSimple(Canvas canvas, Size size, Random random, Paint paint) {
    final positions = [
      Offset(size.width * 0.7, size.height * 0.12),
      Offset(size.width * 0.25, size.height * 0.72),
      Offset(size.width * 0.9, size.height * 0.88),
      Offset(size.width * 0.05, size.height * 0.6),
    ];

    for (var pos in positions) {
      canvas.save();
      canvas.translate(pos.dx, pos.dy);
      canvas.rotate(random.nextDouble() * 0.6 - 0.3);

      final path = Path();
      double s = 12 + random.nextDouble() * 10;

      // U-shape tooth bottom
      path.moveTo(-s * 0.5, -s);
      path.lineTo(-s * 0.5, s * 0.3);
      path.cubicTo(-s * 0.5, s, s * 0.5, s, s * 0.5, s * 0.3);
      path.lineTo(s * 0.5, -s);

      canvas.drawPath(path, paint);
      canvas.restore();
    }
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}
