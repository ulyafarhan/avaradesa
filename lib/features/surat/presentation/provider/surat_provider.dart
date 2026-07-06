import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/api/api_client.dart';

final suratPengajuanProvider = FutureProvider<List<Map<String, dynamic>>>((ref) async {
  final dio = ref.read(dioProvider);
  final response = await dio.get('/surat/pengajuan');
  final data = response.data as Map<String, dynamic>;
  return (data['data'] as List).cast<Map<String, dynamic>>();
});

final suratKategoriProvider = FutureProvider<List<Map<String, dynamic>>>((ref) async {
  final dio = ref.read(dioProvider);
  final response = await dio.get('/surat/kategori');
  final data = response.data as Map<String, dynamic>;
  return (data['data'] as List).cast<Map<String, dynamic>>();
});
