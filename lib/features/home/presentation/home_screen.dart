import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../auth/presentation/provider/auth_provider.dart';
import '../../surat/presentation/provider/surat_provider.dart';
import 'widgets/stat_card.dart';
import 'widgets/quick_action_grid.dart';
import 'widgets/recent_submissions.dart';

class HomeScreen extends ConsumerWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final user = ref.watch(authProvider).user;

    return Scaffold(
      appBar: AppBar(
        title: const Text('AvaraDesa'),
        actions: [
          IconButton(icon: const Icon(Icons.notifications_outlined), onPressed: () {}),
          IconButton(icon: const Icon(Icons.settings_outlined), onPressed: () {}),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () async => ref.invalidate(suratPengajuanProvider),
        child: ListView(
          padding: const EdgeInsets.all(16),
          children: [
            Text('Selamat datang,\n${user?.namaLengkap ?? 'Pengguna'}!',
              style: Theme.of(context).textTheme.titleLarge),
            const SizedBox(height: 20),
            const StatCard(),
            const SizedBox(height: 20),
            Text('Layanan Cepat', style: Theme.of(context).textTheme.titleMedium),
            const SizedBox(height: 12),
            const QuickActionGrid(),
            const SizedBox(height: 24),
            Text('Pengajuan Terbaru', style: Theme.of(context).textTheme.titleMedium),
            const SizedBox(height: 12),
            const RecentSubmissions(),
          ],
        ),
      ),
    );
  }
}
