import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/api/api_client.dart';

final fasilitasProvider = FutureProvider<List<Map<String, dynamic>>>((ref) async {
  final dio = ref.read(dioProvider);
  final response = await dio.get('/fasilitas');
  final data = response.data as Map<String, dynamic>;
  return (data['data'] as List).cast<Map<String, dynamic>>();
});

class FasilitasScreen extends ConsumerWidget {
  const FasilitasScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final fasilitas = ref.watch(fasilitasProvider);

    return Scaffold(
      appBar: AppBar(title: const Text('Fasilitas Desa')),
      body: fasilitas.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (e, _) => Center(child: Text('Gagal memuat data')),
        data: (items) => ListView.builder(
          padding: const EdgeInsets.all(16),
          itemCount: items.length,
          itemBuilder: (_, i) => ListTile(
            leading: CircleAvatar(child: Icon(Icons.business)),
            title: Text(items[i]['nama'] as String? ?? ''),
            subtitle: Text(items[i]['jenis_fasilitas'] as String? ?? ''),
          ),
        ),
      ),
    );
  }
}
