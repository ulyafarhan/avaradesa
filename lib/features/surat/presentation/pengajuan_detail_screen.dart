import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/api/api_client.dart';
import '../../../core/api/api_endpoints.dart';

final pengajuanDetailProvider = FutureProvider.family<Map<String, dynamic>, int>((ref, id) async {
  final dio = ref.read(dioProvider);
  final response = await dio.get(ApiEndpoints.suratPengajuanDetail(id));
  final data = response.data as Map<String, dynamic>;
  return (data['data'] as Map<String, dynamic>?) ?? {};
});

class PengajuanDetailScreen extends ConsumerWidget {
  final int id;
  const PengajuanDetailScreen({super.key, required this.id});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final detail = ref.watch(pengajuanDetailProvider(id));

    return Scaffold(
      appBar: AppBar(title: const Text('Detail Pengajuan')),
      body: detail.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (e, _) => Center(child: Text('Gagal memuat detail pengajuan')),
        data: (data) {
          if (data.isEmpty) {
            return const Center(child: Text('Data tidak ditemukan'));
          }
          return ListView(
            padding: const EdgeInsets.all(16),
            children: [
              _StatusHeader(data),
              const SizedBox(height: 16),
              Card(
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('Informasi Pengajuan', style: Theme.of(context).textTheme.titleMedium),
                      const Divider(),
                      _InfoRow('No. Registrasi', data['nomor_registrasi'] ?? '-'),
                      _InfoRow('Jenis Surat', data['kategori_nama'] ?? '-'),
                      _InfoRow('Tanggal', data['created_at'] ?? '-'),
                      if (data['nomor_surat'] != null)
                        _InfoRow('No. Surat', data['nomor_surat']),
                      if (data['catatan_penolakan'] != null)
                        _InfoRow('Catatan', data['catatan_penolakan']),
                    ],
                  ),
                ),
              ),
              if (data['file_pdf_url'] != null) ...[
                const SizedBox(height: 16),
                SizedBox(
                  width: double.infinity,
                  child: OutlinedButton.icon(
                    onPressed: () {
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(content: Text('Fitur PDF akan tersedia')),
                      );
                    },
                    icon: const Icon(Icons.picture_as_pdf),
                    label: const Text('Lihat Surat PDF'),
                  ),
                ),
              ],
            ],
          );
        },
      ),
    );
  }
}

class _StatusHeader extends StatelessWidget {
  final Map<String, dynamic> data;
  const _StatusHeader(this.data);

  @override
  Widget build(BuildContext context) {
    final status = data['status'] as String? ?? '';
    final (color, icon) = switch (status) {
      'menunggu' => (Colors.amber, Icons.hourglass_empty),
      'diproses' => (Colors.blue, Icons.sync),
      'disetujui' => (Colors.green, Icons.check_circle),
      'ditolak' => (Colors.red, Icons.cancel),
      _ => (Colors.grey, Icons.help_outline),
    };

    return Card(
      color: color.withValues(alpha: 0.1),
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Row(
          children: [
            Icon(icon, size: 48, color: color),
            const SizedBox(width: 16),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(status.isEmpty ? 'Tidak diketahui' : status.toUpperCase(),
                  style: Theme.of(context).textTheme.titleLarge?.copyWith(color: color, fontWeight: FontWeight.bold)),
                Text('Status pengajuan', style: Theme.of(context).textTheme.bodySmall?.copyWith(color: Colors.grey)),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

class _InfoRow extends StatelessWidget {
  final String label;
  final String value;
  const _InfoRow(this.label, this.value);

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
