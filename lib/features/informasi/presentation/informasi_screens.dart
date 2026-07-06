import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/api/api_client.dart';
import '../../../../core/api/api_endpoints.dart';

final informasiListProvider = FutureProvider<List<Map<String, dynamic>>>((ref) async {
  final dio = ref.read(dioProvider);
  final response = await dio.get(ApiEndpoints.informasi);
  final data = response.data as Map<String, dynamic>;
  return (data['data'] as List).cast<Map<String, dynamic>>();
});

final informasiDetailProvider = FutureProvider.family<Map<String, dynamic>, String>((ref, slug) async {
  final dio = ref.read(dioProvider);
  final response = await dio.get(ApiEndpoints.informasiDetail(slug));
  final data = response.data as Map<String, dynamic>;
  return (data['data'] as Map<String, dynamic>?) ?? {};
});

class InformasiListScreen extends ConsumerWidget {
  const InformasiListScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final informasi = ref.watch(informasiListProvider);

    return Scaffold(
      appBar: AppBar(title: const Text('Informasi Desa')),
      body: informasi.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (e, _) => Center(child: Text('Gagal memuat data')),
        data: (items) {
          if (items.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.article_outlined, size: 64, color: Colors.grey),
                  const SizedBox(height: 16),
                  Text('Belum ada informasi', style: Theme.of(context).textTheme.bodyLarge),
                ],
              ),
            );
          }
          return RefreshIndicator(
            onRefresh: () async => ref.invalidate(informasiListProvider),
            child: ListView.builder(
              padding: const EdgeInsets.all(16),
              itemCount: items.length,
              itemBuilder: (_, i) {
                final item = items[i];
                return Card(
                  margin: const EdgeInsets.only(bottom: 12),
                  child: ListTile(
                    leading: CircleAvatar(child: Icon(Icons.article_outlined)),
                    title: Text(item['judul'] as String? ?? '', maxLines: 2, overflow: TextOverflow.ellipsis),
                    subtitle: Text(_formatDate(item['created_at'] as String? ?? ''), style: Theme.of(context).textTheme.bodySmall),
                    trailing: const Icon(Icons.chevron_right),
                    onTap: () => context.push('/informasi/${item['slug']}'),
                  ),
                );
              },
            ),
          );
        },
      ),
    );
  }

  String _formatDate(String date) {
    try {
      final d = DateTime.parse(date);
      return '${d.day}/${d.month}/${d.year}';
    } catch (_) {
      return date;
    }
  }
}

class InformasiDetailScreen extends ConsumerWidget {
  final String slug;
  const InformasiDetailScreen({super.key, required this.slug});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final detail = ref.watch(informasiDetailProvider(slug));

    return Scaffold(
      appBar: AppBar(title: const Text('Detail Informasi')),
      body: detail.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (e, _) => Center(child: Text('Gagal memuat informasi')),
        data: (data) {
          if (data.isEmpty) {
            return const Center(child: Text('Informasi tidak ditemukan'));
          }
          return ListView(
            padding: const EdgeInsets.all(16),
            children: [
              Text(data['judul'] as String? ?? '',
                style: Theme.of(context).textTheme.headlineSmall?.copyWith(fontWeight: FontWeight.bold)),
              const SizedBox(height: 8),
              Row(
                children: [
                  Icon(Icons.person_outline, size: 14, color: Colors.grey),
                  const SizedBox(width: 4),
                  Text(data['penulis'] ?? 'Admin', style: Theme.of(context).textTheme.bodySmall?.copyWith(color: Colors.grey)),
                  const SizedBox(width: 16),
                  Icon(Icons.calendar_today, size: 14, color: Colors.grey),
                  const SizedBox(width: 4),
                  Text(_formatDate(data['created_at'] as String? ?? ''), style: Theme.of(context).textTheme.bodySmall?.copyWith(color: Colors.grey)),
                ],
              ),
              if (data['cover_image'] != null && (data['cover_image'] as String).isNotEmpty) ...[
                const SizedBox(height: 16),
                ClipRRect(
                  borderRadius: BorderRadius.circular(12),
                  child: Image.network(
                    data['cover_image'] as String,
                    height: 200,
                    width: double.infinity,
                    fit: BoxFit.cover,
                    errorBuilder: (_, _, _) => Container(
                      height: 200,
                      color: Colors.grey[200],
                      child: const Icon(Icons.broken_image, size: 48),
                    ),
                  ),
                ),
              ],
              const SizedBox(height: 16),
              Text(data['konten'] as String? ?? '',
                style: Theme.of(context).textTheme.bodyLarge?.copyWith(height: 1.6)),
            ],
          );
        },
      ),
    );
  }

  String _formatDate(String date) {
    try {
      final d = DateTime.parse(date);
      return '${d.day}/${d.month}/${d.year}';
    } catch (_) {
      return date;
    }
  }
}
