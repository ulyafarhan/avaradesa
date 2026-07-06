import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:dio/dio.dart';
import '../../../core/api/api_client.dart';
import '../../../core/api/api_endpoints.dart';

final mutasiProvider = FutureProvider<List<Map<String, dynamic>>>((ref) async {
  final dio = ref.read(dioProvider);
  final response = await dio.get('/mutasi');
  final data = response.data as Map<String, dynamic>;
  return (data['data'] as List).cast<Map<String, dynamic>>();
});

class MutasiListScreen extends ConsumerWidget {
  const MutasiListScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final mutasi = ref.watch(mutasiProvider);

    return Scaffold(
      appBar: AppBar(title: const Text('Mutasi Penduduk')),
      floatingActionButton: FloatingActionButton(
        onPressed: () => context.push('/mutasi/buat'),
        child: const Icon(Icons.add),
      ),
      body: mutasi.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (e, _) => Center(child: Text('Gagal memuat data')),
        data: (items) {
          if (items.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.swap_horiz, size: 64, color: Colors.grey),
                  const SizedBox(height: 16),
                  Text('Belum ada mutasi', style: Theme.of(context).textTheme.bodyLarge),
                ],
              ),
            );
          }
          return RefreshIndicator(
            onRefresh: () async => ref.invalidate(mutasiProvider),
            child: ListView.builder(
              padding: const EdgeInsets.all(16),
              itemCount: items.length,
              itemBuilder: (_, i) {
                final item = items[i];
                return Card(
                  margin: const EdgeInsets.only(bottom: 12),
                  child: ListTile(
                    leading: CircleAvatar(child: Icon(_jenisIcon(item['jenis_mutasi'] as String?))),
                    title: Text(item['jenis_mutasi'] as String? ?? 'Mutasi'),
                    subtitle: Text(item['tanggal_mutasi'] as String? ?? ''),
                    trailing: _StatusBadge(item['status_verifikasi'] as String?),
                  ),
                );
              },
            ),
          );
        },
      ),
    );
  }

  IconData _jenisIcon(String? jenis) {
    switch (jenis) {
      case 'Kelahiran': return Icons.child_care;
      case 'Kematian': return Icons.air;
      case 'Kedatangan': return Icons.login;
      case 'Kepindahan': return Icons.logout;
      default: return Icons.swap_horiz;
    }
  }
}

class _StatusBadge extends StatelessWidget {
  final String? status;
  const _StatusBadge(this.status);

  @override
  Widget build(BuildContext context) {
    final (color, label) = switch (status) {
      'Pending' => (Colors.amber, 'Pending'),
      'Disetujui' => (Colors.green, 'Disetujui'),
      'Ditolak' => (Colors.red, 'Ditolak'),
      _ => (Colors.grey, status ?? ''),
    };
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(color: color.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(12)),
      child: Text(label, style: TextStyle(color: color, fontSize: 12)),
    );
  }
}

class BuatMutasiScreen extends ConsumerStatefulWidget {
  const BuatMutasiScreen({super.key});

  @override
  ConsumerState<BuatMutasiScreen> createState() => _BuatMutasiScreenState();
}

class _BuatMutasiScreenState extends ConsumerState<BuatMutasiScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nikCtrl = TextEditingController();
  final _keteranganCtrl = TextEditingController();
  final _dokumenCtrl = TextEditingController();
  String _jenisMutasi = 'Kelahiran';
  DateTime _tanggalMutasi = DateTime.now();
  bool _isSubmitting = false;

  @override
  void dispose() {
    _nikCtrl.dispose();
    _keteranganCtrl.dispose();
    _dokumenCtrl.dispose();
    super.dispose();
  }

  Future<void> _pickDate() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: _tanggalMutasi,
      firstDate: DateTime(1900),
      lastDate: DateTime.now(),
    );
    if (picked != null) setState(() => _tanggalMutasi = picked);
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isSubmitting = true);
    try {
      final dio = ref.read(dioProvider);
      await dio.post(ApiEndpoints.mutasi, data: {
        'nik': _nikCtrl.text,
        'jenis_mutasi': _jenisMutasi,
        'tanggal_mutasi': _tanggalMutasi.toIso8601String().split('T').first,
        'keterangan': _keteranganCtrl.text,
        'dokumen_bukti': _dokumenCtrl.text.isEmpty ? '-' : _dokumenCtrl.text,
      });

      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Mutasi berhasil diajukan'), backgroundColor: Colors.green),
      );
      context.pop();
    } on DioException catch (e) {
      final msg = e.response?.data?['message'] as String? ?? 'Gagal mengajukan mutasi';
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(msg), backgroundColor: Colors.red),
      );
    } finally {
      if (mounted) setState(() => _isSubmitting = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Buat Mutasi')),
      body: Form(
        key: _formKey,
        child: ListView(
          padding: const EdgeInsets.all(16),
          children: [
            TextFormField(
              controller: _nikCtrl,
              decoration: const InputDecoration(
                labelText: 'NIK',
                hintText: '16 digit NIK',
                border: OutlineInputBorder(),
              ),
              keyboardType: TextInputType.number,
              maxLength: 16,
              validator: (v) => v == null || v.length != 16 ? 'NIK harus 16 digit' : null,
            ),
            const SizedBox(height: 16),
            DropdownButtonFormField<String>(
              value: _jenisMutasi,
              decoration: const InputDecoration(labelText: 'Jenis Mutasi', border: OutlineInputBorder()),
              items: const [
                DropdownMenuItem(value: 'Kelahiran', child: Text('Kelahiran')),
                DropdownMenuItem(value: 'Kematian', child: Text('Kematian')),
                DropdownMenuItem(value: 'Kedatangan', child: Text('Kedatangan')),
                DropdownMenuItem(value: 'Kepindahan', child: Text('Kepindahan')),
              ],
              onChanged: (v) => setState(() => _jenisMutasi = v ?? 'Kelahiran'),
            ),
            const SizedBox(height: 16),
            InkWell(
              onTap: _pickDate,
              child: InputDecorator(
                decoration: const InputDecoration(labelText: 'Tanggal Mutasi', border: OutlineInputBorder()),
                child: Text('${_tanggalMutasi.day}/${_tanggalMutasi.month}/${_tanggalMutasi.year}'),
              ),
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _keteranganCtrl,
              decoration: const InputDecoration(
                labelText: 'Keterangan',
                hintText: 'Alasan mutasi',
                border: OutlineInputBorder(),
              ),
              maxLines: 3,
              validator: (v) => v == null || v.isEmpty ? 'Keterangan wajib diisi' : null,
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _dokumenCtrl,
              decoration: const InputDecoration(
                labelText: 'Dokumen Bukti (opsional)',
                hintText: 'URL atau path dokumen',
                border: OutlineInputBorder(),
              ),
            ),
            const SizedBox(height: 24),
            SizedBox(
              width: double.infinity,
              height: 48,
              child: ElevatedButton(
                onPressed: _isSubmitting ? null : _submit,
                child: _isSubmitting
                    ? const SizedBox(height: 20, width: 20, child: CircularProgressIndicator(strokeWidth: 2))
                    : const Text('Ajukan Mutasi'),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
