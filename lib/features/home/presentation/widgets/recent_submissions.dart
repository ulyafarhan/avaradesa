import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../surat/presentation/provider/surat_provider.dart';

class RecentSubmissions extends ConsumerWidget {
  const RecentSubmissions({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final pengajuan = ref.watch(suratPengajuanProvider);

    return pengajuan.when(
      loading: () => const Center(child: CircularProgressIndicator()),
      error: (e, _) => Text('Gagal memuat', style: TextStyle(color: Colors.grey[500])),
      data: (items) {
        if (items.isEmpty) {
          return Card(
            child: Padding(
              padding: const EdgeInsets.all(32),
              child: Center(
                child: Text('Belum ada pengajuan', style: TextStyle(color: Colors.grey[500])),
              ),
            ),
          );
        }

        final recent = items.take(5).toList();
        return Column(
          children: recent.map((item) => Card(
            margin: const EdgeInsets.only(bottom: 8),
            child: ListTile(
              dense: true,
              leading: _statusIcon(item['status'] as String?),
              title: Text(item['kategori_nama'] as String? ?? 'Surat', maxLines: 1),
              subtitle: Text(item['created_at'] as String? ?? '', style: const TextStyle(fontSize: 12)),
              trailing: Text(item['status'] as String? ?? '', style: const TextStyle(fontSize: 12)),
              onTap: () => context.push('/surat/pengajuan/${item['id']}'),
            ),
          )).toList(),
        );
      },
    );
  }

  Widget _statusIcon(String? status) {
    final icon = switch (status) {
      'menunggu' => Icons.hourglass_empty,
      'diproses' => Icons.sync,
      'disetujui' => Icons.check_circle,
      'ditolak' => Icons.cancel,
      _ => Icons.description,
    };
    return Icon(icon, size: 20);
  }
}
