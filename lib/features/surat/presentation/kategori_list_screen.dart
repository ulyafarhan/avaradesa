import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'provider/surat_provider.dart';

class KategoriListScreen extends ConsumerWidget {
  const KategoriListScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final kategori = ref.watch(suratKategoriProvider);

    return Scaffold(
      appBar: AppBar(title: const Text('Pilih Jenis Surat')),
      body: kategori.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (e, _) => Center(child: Text('Gagal memuat kategori')),
        data: (items) {
          if (items.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.description_outlined, size: 64, color: Colors.grey),
                  const SizedBox(height: 16),
                  Text('Belum ada kategori surat', style: Theme.of(context).textTheme.bodyLarge),
                ],
              ),
            );
          }
          return ListView.builder(
            padding: const EdgeInsets.all(16),
            itemCount: items.length,
            itemBuilder: (_, i) {
              final item = items[i];
              return Card(
                margin: const EdgeInsets.only(bottom: 12),
                child: ListTile(
                  leading: CircleAvatar(child: Icon(_getIcon(item['icon'] as String?))),
                  title: Text(item['nama'] as String? ?? ''),
                  subtitle: Text(item['deskripsi'] as String? ?? ''),
                  trailing: const Icon(Icons.chevron_right),
                  onTap: () => context.push('/surat/buat/${item['id']}'),
                ),
              );
            },
          );
        },
      ),
    );
  }

  IconData _getIcon(String? icon) {
    switch (icon) {
      case 'home': return Icons.home;
      case 'school': return Icons.school;
      case 'work': return Icons.work;
      case 'medical': return Icons.medical_services;
      case 'gavel': return Icons.gavel;
      default: return Icons.description;
    }
  }
}
