import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/api/api_client.dart';
import '../../../core/api/api_endpoints.dart';
import '../../auth/presentation/provider/auth_provider.dart';

final profileDataProvider = FutureProvider<Map<String, dynamic>>((ref) async {
  final dio = ref.read(dioProvider);
  final response = await dio.get(ApiEndpoints.profile);
  final body = response.data as Map<String, dynamic>;
  return (body['data'] as Map<String, dynamic>?) ?? {};
});

class ProfileScreen extends ConsumerWidget {
  const ProfileScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final authState = ref.watch(authProvider);
    final profile = ref.watch(profileDataProvider);

    return Scaffold(
      appBar: AppBar(title: const Text('Profil')),
      body: profile.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (e, _) => Center(child: Text('Gagal memuat profil')),
        data: (data) => ListView(
          padding: const EdgeInsets.all(16),
          children: [
            Center(
              child: Column(
                children: [
                  CircleAvatar(
                    radius: 48,
                    backgroundColor: Theme.of(context).colorScheme.primaryContainer,
                    child: Text(authState.user?.namaLengkap?.substring(0, 1).toUpperCase() ?? '?',
                      style: TextStyle(fontSize: 32, color: Theme.of(context).colorScheme.onPrimaryContainer)),
                  ),
                  const SizedBox(height: 12),
                  Text(authState.user?.namaLengkap ?? data['nama_lengkap'] ?? 'Pengguna',
                    style: Theme.of(context).textTheme.titleLarge),
                  Text(_roleLabel(data), style: Theme.of(context).textTheme.bodyMedium?.copyWith(color: Colors.grey)),
                ],
              ),
            ),
            const SizedBox(height: 24),
            Card(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Data Diri', style: Theme.of(context).textTheme.titleMedium),
                    const Divider(),
                    _ProfileRow('NIK', data['nik'] ?? '-'),
                    _ProfileRow('No. KK', data['no_kk'] ?? '-'),
                    _ProfileRow('Tempat Lahir', data['tempat_lahir'] ?? '-'),
                    _ProfileRow('Tanggal Lahir', _formatDate(data['tanggal_lahir'] as String?)),
                    _ProfileRow('Jenis Kelamin', data['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'),
                    _ProfileRow('Agama', data['agama'] ?? '-'),
                    _ProfileRow('Pendidikan', data['pendidikan'] ?? '-'),
                    _ProfileRow('Pekerjaan', data['pekerjaan'] ?? '-'),
                    _ProfileRow('Status', data['status_perkawinan'] ?? '-'),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 16),
            Card(
              child: Column(
                children: [
                  ListTile(
                    leading: const Icon(Icons.people),
                    title: const Text('Data Keluarga'),
                    trailing: const Icon(Icons.chevron_right),
                    onTap: () => context.push('/profil/keluarga'),
                  ),
                  const Divider(height: 1),
                  ListTile(
                    leading: const Icon(Icons.qr_code),
                    title: const Text('Verifikasi Surat'),
                    trailing: const Icon(Icons.chevron_right),
                    onTap: () {
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(content: Text('Fitur verifikasi akan tersedia')),
                      );
                    },
                  ),
                  const Divider(height: 1),
                  ListTile(
                    leading: const Icon(Icons.settings),
                    title: const Text('Pengaturan'),
                    trailing: const Icon(Icons.chevron_right),
                    onTap: () => context.push('/pengaturan'),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 16),
            OutlinedButton.icon(
              onPressed: () {
                ref.read(authProvider.notifier).logout();
                context.go('/login');
              },
              icon: const Icon(Icons.logout, color: Colors.red),
              label: const Text('Keluar', style: TextStyle(color: Colors.red)),
            ),
          ],
        ),
      ),
    );
  }

  String _roleLabel(Map<String, dynamic> data) {
    if (data['role'] != null) return data['role'] as String;
    return 'Warga';
  }

  String _formatDate(String? date) {
    if (date == null) return '-';
    try {
      final d = DateTime.parse(date);
      return '${d.day}/${d.month}/${d.year}';
    } catch (_) {
      return date;
    }
  }
}

class ProfilKeluargaScreen extends StatelessWidget {
  const ProfilKeluargaScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Data Keluarga')),
      body: const Center(child: Text('Halaman data keluarga')),
    );
  }
}

class PengaturanScreen extends StatelessWidget {
  const PengaturanScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Pengaturan')),
      body: const Center(child: Text('Halaman pengaturan')),
    );
  }
}

class _ProfileRow extends StatelessWidget {
  final String label;
  final String value;
  const _ProfileRow(this.label, this.value);

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(width: 120, child: Text(label, style: TextStyle(color: Colors.grey[600]))),
          Expanded(child: Text(value, style: const TextStyle(fontWeight: FontWeight.w500))),
        ],
      ),
    );
  }
}
