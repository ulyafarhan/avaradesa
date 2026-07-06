import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:dio/dio.dart';
import '../../../core/api/api_client.dart';
import '../../../core/api/api_endpoints.dart';

class BuatSuratScreen extends ConsumerStatefulWidget {
  final int kategoriId;
  const BuatSuratScreen({super.key, required this.kategoriId});

  @override
  ConsumerState<BuatSuratScreen> createState() => _BuatSuratScreenState();
}

class _BuatSuratScreenState extends ConsumerState<BuatSuratScreen> {
  final _formKey = GlobalKey<FormState>();
  final _keperluanCtrl = TextEditingController();
  final _keteranganCtrl = TextEditingController();
  final List<String> _files = [];
  bool _isSubmitting = false;

  @override
  void dispose() {
    _keperluanCtrl.dispose();
    _keteranganCtrl.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isSubmitting = true);
    try {
      final dio = ref.read(dioProvider);
      await dio.post(ApiEndpoints.suratPengajuan, data: {
        'kategori_surat_id': widget.kategoriId,
        'data_isian': [
          _keperluanCtrl.text,
          _keteranganCtrl.text,
        ],
        'file_syarat': _files.isEmpty ? ['-'] : _files,
      });

      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Pengajuan berhasil dikirim'), backgroundColor: Colors.green),
      );
      context.pop();
    } on DioException catch (e) {
      final msg = e.response?.data?['message'] as String? ?? 'Gagal mengirim pengajuan';
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
      appBar: AppBar(title: const Text('Buat Surat')),
      body: Form(
        key: _formKey,
        child: ListView(
          padding: const EdgeInsets.all(16),
          children: [
            Card(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Form Pengajuan Surat', style: Theme.of(context).textTheme.titleMedium),
                    const SizedBox(height: 4),
                    Text('Lengkapi data di bawah ini untuk mengajukan surat',
                      style: Theme.of(context).textTheme.bodySmall?.copyWith(color: Colors.grey)),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _keperluanCtrl,
              decoration: const InputDecoration(
                labelText: 'Keperluan',
                hintText: 'Masukkan keperluan surat',
                border: OutlineInputBorder(),
              ),
              maxLines: 2,
              validator: (v) => v == null || v.isEmpty ? 'Keperluan wajib diisi' : null,
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _keteranganCtrl,
              decoration: const InputDecoration(
                labelText: 'Keterangan Tambahan',
                hintText: 'Opsional',
                border: OutlineInputBorder(),
              ),
              maxLines: 3,
            ),
            const SizedBox(height: 16),
            OutlinedButton.icon(
              onPressed: () {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(content: Text('Fitur upload file akan tersedia')),
                );
              },
              icon: const Icon(Icons.upload_file),
              label: const Text('Upload Syarat'),
            ),
            if (_files.isNotEmpty) ...[
              const SizedBox(height: 8),
              Text('${_files.length} file terupload', style: Theme.of(context).textTheme.bodySmall),
            ],
            const SizedBox(height: 24),
            SizedBox(
              width: double.infinity,
              height: 48,
              child: ElevatedButton(
                onPressed: _isSubmitting ? null : _submit,
                child: _isSubmitting
                    ? const SizedBox(height: 20, width: 20, child: CircularProgressIndicator(strokeWidth: 2))
                    : const Text('Ajukan Surat'),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
