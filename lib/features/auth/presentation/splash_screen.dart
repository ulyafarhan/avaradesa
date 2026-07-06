import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'provider/auth_provider.dart';

class SplashScreen extends ConsumerWidget {
  const SplashScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    ref.listen(authProvider, (prev, next) {
      if (!next.isLoading) {
        if (next.isAuthenticated) {
          final isAdmin = next.user?.isAdmin ?? false;
          context.go(isAdmin ? '/admin/home' : '/home');
        } else {
          context.go('/login');
        }
      }
    });

    return Scaffold(
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.home_work_outlined, size: 80, color: Theme.of(context).colorScheme.primary),
            const SizedBox(height: 24),
            Text('AvaraDesa', style: Theme.of(context).textTheme.headlineLarge),
            const SizedBox(height: 8),
            Text('Sistem Informasi Desa', style: TextStyle(color: Colors.grey[600])),
            const SizedBox(height: 32),
            const CircularProgressIndicator(),
          ],
        ),
      ),
    );
  }
}
