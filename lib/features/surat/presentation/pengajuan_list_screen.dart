import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'provider/surat_provider.dart';

class PengajuanListScreen extends ConsumerWidget {
  const PengajuanListScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final pengajuan = ref.watch(suratPengajuanProvider);

    return Scaffold(
      appBar: AppBar(title: const Text('Pengajuan Surat')),
      body: pengajuan.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (e, _) => Center(child: Text('Gagal memuat data')),
        data: (items) {
          if (items.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.description_outlined, size: 64, color: Colors.grey),
                  const SizedBox(height: 16),
                  Text('Belum ada pengajuan', style: Theme.of(context).textTheme.bodyLarge),
                ],
              ),
            );
          }
          return RefreshIndicator(
            onRefresh: () async => ref.invalidate(suratPengajuanProvider),
            child: ListView.builder(
              padding: const EdgeInsets.all(16),
              itemCount: items.length,
              itemBuilder: (_, i) => Card(
                margin: const EdgeInsets.only(bottom: 12),
                child: ListTile(
                  leading: _statusIcon(items[i]['status'] as String?),
                  title: Text(items[i]['kategori_nama'] ?? 'Surat'),
                  subtitle: Text(items[i]['kode_pengajuan'] ?? ''),
                  trailing: _statusBadge(items[i]['status'] as String?),
                ),
              ),
            ),
          );
        },
      ),
    );
  }
}

Widget _statusIcon(String? status) {
  final icon = switch (status) {
    'menunggu' => Icons.hourglass_empty,
    'diproses' => Icons.sync,
    'disetujui' => Icons.check_circle,
    'ditolak' => Icons.cancel,
    _ => Icons.description,
  };
  return CircleAvatar(child: Icon(icon));
}

Widget _statusBadge(String? status) {
  final color = switch (status) {
    'menunggu' => Colors.amber,
    'diproses' => Colors.blue,
    'disetujui' => Colors.green,
    'ditolak' => Colors.red,
    _ => Colors.grey,
  };
  return Container(
    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
    decoration: BoxDecoration(color: color.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(12)),
    child: Text(status ?? '', style: TextStyle(color: color, fontSize: 12)),
  );
}
