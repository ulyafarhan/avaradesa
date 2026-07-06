import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/api/api_client.dart';
import '../../../core/api/api_endpoints.dart';
final adminSuratProvider = FutureProvider<List<Map<String, dynamic>>>((ref) async {
  final dio = ref.read(dioProvider);
  final response = await dio.get('/admin/surat/pengajuan');
  final data = response.data as Map<String, dynamic>;
  return (data['data'] as List).cast<Map<String, dynamic>>();
});

class AdminDashboardScreen extends ConsumerWidget {
  const AdminDashboardScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return Scaffold(
      appBar: AppBar(title: const Text('Dashboard Admin')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Card(
            child: Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Icon(Icons.admin_panel_settings, size: 48, color: Theme.of(context).colorScheme.primary),
                  const SizedBox(height: 12),
                  Text('Selamat datang di Panel Admin', style: Theme.of(context).textTheme.titleLarge),
                  const SizedBox(height: 4),
                  Text('Kelola surat, mutasi, dan informasi desa', style: TextStyle(color: Colors.grey[600])),
                ],
              ),
            ),
          ),
          const SizedBox(height: 16),
          _MenuCard(
            icon: Icons.description, label: 'Kelola Surat', desc: 'Approve/tolak pengajuan surat',
            onTap: () => _navigate(context, 0),
          ),
          const SizedBox(height: 12),
          _MenuCard(
            icon: Icons.swap_horiz, label: 'Kelola Mutasi', desc: 'Verifikasi mutasi penduduk',
            onTap: () => _navigate(context, 1),
          ),
          const SizedBox(height: 12),
          _MenuCard(
            icon: Icons.article, label: 'Kelola Informasi', desc: 'Publikasikan informasi desa',
            onTap: () => _navigate(context, 2),
          ),
        ],
      ),
    );
  }

  void _navigate(BuildContext context, int index) {
    final titles = ['Kelola Surat', 'Kelola Mutasi', 'Kelola Informasi'];
    Navigator.push(context, MaterialPageRoute(
      builder: (_) => AdminSuratListScreen(title: titles[index]),
    ));
  }
}

class _MenuCard extends StatelessWidget {
  final IconData icon;
  final String label;
  final String desc;
  final VoidCallback onTap;
  const _MenuCard({required this.icon, required this.label, required this.desc, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return Card(
      child: ListTile(
        leading: CircleAvatar(child: Icon(icon)),
        title: Text(label),
        subtitle: Text(desc),
        trailing: const Icon(Icons.chevron_right),
        onTap: onTap,
      ),
    );
  }
}

class AdminSuratListScreen extends ConsumerWidget {
  final String title;
  const AdminSuratListScreen({super.key, required this.title});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final surat = ref.watch(adminSuratProvider);

    return Scaffold(
      appBar: AppBar(title: Text(title)),
      body: surat.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (e, _) => Center(child: Text('Gagal memuat')),
        data: (items) => ListView.builder(
          padding: const EdgeInsets.all(16),
          itemCount: items.length,
          itemBuilder: (_, i) {
            final item = items[i];
            return Card(
              margin: const EdgeInsets.only(bottom: 12),
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Expanded(
                          child: Text(item['kode_pengajuan'] as String? ?? item['nomor_registrasi'] as String? ?? '-',
                            style: const TextStyle(fontWeight: FontWeight.bold)),
                        ),
                        _StatusChip(item['status'] as String?),
                      ],
                    ),
                    const SizedBox(height: 4),
                    Text(item['kategori_nama'] as String? ?? 'Surat', style: TextStyle(color: Colors.grey[600])),
                    const SizedBox(height: 12),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.end,
                      children: [
                        OutlinedButton.icon(
                          icon: const Icon(Icons.close, size: 18),
                          label: const Text('Tolak'),
                          style: OutlinedButton.styleFrom(foregroundColor: Colors.red),
                          onPressed: () => _showRejectDialog(context, ref, item['id'] as int),
                        ),
                        const SizedBox(width: 8),
                        ElevatedButton.icon(
                          icon: const Icon(Icons.check, size: 18),
                          label: const Text('Setujui'),
                          style: ElevatedButton.styleFrom(backgroundColor: Colors.green, foregroundColor: Colors.white),
                          onPressed: () => _approve(context, ref, item['id'] as int),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            );
          },
        ),
      ),
    );
  }

  Future<void> _approve(BuildContext context, WidgetRef ref, int id) async {
    try {
      final dio = ref.read(dioProvider);
      await dio.post(ApiEndpoints.adminSuratApprove(id));
      if (!context.mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Surat disetujui'), backgroundColor: Colors.green),
      );
      ref.invalidate(adminSuratProvider);
    } catch (e) {
      if (!context.mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Gagal menyetujui surat'), backgroundColor: Colors.red),
      );
    }
  }

  Future<void> _showRejectDialog(BuildContext context, WidgetRef ref, int id) async {
    final reasonCtrl = TextEditingController();
    final result = await showDialog<String>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Tolak Pengajuan'),
        content: TextField(
          controller: reasonCtrl,
          decoration: const InputDecoration(hintText: 'Alasan penolakan', border: OutlineInputBorder()),
          maxLines: 3,
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Batal')),
          ElevatedButton(onPressed: () => Navigator.pop(ctx, reasonCtrl.text), child: const Text('Tolak')),
        ],
      ),
    );

    if (result == null || result.isEmpty) return;
    try {
      final dio = ref.read(dioProvider);
      await dio.post(ApiEndpoints.adminSuratReject(id), data: {'catatan_penolakan': result});
      if (!context.mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Surat ditolak'), backgroundColor: Colors.orange),
      );
      ref.invalidate(adminSuratProvider);
    } catch (e) {
      if (!context.mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Gagal menolak surat'), backgroundColor: Colors.red),
      );
    }
  }
}

class AdminSuratScreen extends AdminSuratListScreen {
  const AdminSuratScreen({super.key}) : super(title: 'Kelola Surat');
}

class AdminSuratDetailScreen extends StatelessWidget {
  final int id;
  const AdminSuratDetailScreen({super.key, required this.id});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Detail Surat')),
      body: Center(child: Text('Surat #$id')),
    );
  }
}

class AdminMutasiScreen extends StatelessWidget {
  const AdminMutasiScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Kelola Mutasi')),
      body: const Center(child: Text('Daftar mutasi')),
    );
  }
}

class AdminMutasiDetailScreen extends StatelessWidget {
  final int id;
  const AdminMutasiDetailScreen({super.key, required this.id});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Detail Mutasi')),
      body: Center(child: Text('Mutasi #$id')),
    );
  }
}

class AdminInformasiScreen extends StatelessWidget {
  const AdminInformasiScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Kelola Informasi')),
      body: const Center(child: Text('Daftar informasi')),
    );
  }
}

class _StatusChip extends StatelessWidget {
  final String? status;
  const _StatusChip(this.status);

  @override
  Widget build(BuildContext context) {
    final (color, label) = switch (status) {
      'menunggu' || 'Pending' => (Colors.amber, 'Menunggu'),
      'diproses' => (Colors.blue, 'Diproses'),
      'disetujui' || 'APPROVED' => (Colors.green, 'Disetujui'),
      'ditolak' || 'REJECTED' => (Colors.red, 'Ditolak'),
      _ => (Colors.grey, status ?? ''),
    };
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(color: color.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(12)),
      child: Text(label, style: TextStyle(color: color, fontSize: 12, fontWeight: FontWeight.w600)),
    );
  }
}
