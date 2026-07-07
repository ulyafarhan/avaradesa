import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../database/app_database.dart';
import '../network/connectivity_service.dart';

enum SyncStatus { idle, syncing, success, error }

final syncStatusProvider = StateProvider<SyncStatus>((ref) => SyncStatus.idle);

class SyncService {
  final AppDatabase _db;
  final Dio _dio;
  final ConnectivityService _connectivity;

  SyncService(this._db, this._dio, this._connectivity);

  Future<void> syncNow() async {
    if (!await _connectivity.isOnline) return;

    try {
      // 1. Push pending items
      final pending = await _db.pendingSyncItems();
      if (pending.isNotEmpty) {
        final payload = pending.map((item) => {
          'client_id': item.clientId,
          'type': item.type,
          'action': item.action,
          'data': item.data,
          'created_at': item.createdAt.toIso8601String(),
        }).toList();

        await _dio.post('/sync/push', data: {'operations': payload});

        for (final item in pending) {
          await _db.updateSyncStatus(item.clientId, 'synced');
        }
      }

      // 2. Pull delta
      final lastSync = await _db.getSetting('last_sync') ?? '';
      if (lastSync.isNotEmpty) {
        final response = await _dio.get('/sync/pull', queryParameters: {'last_sync': lastSync});
        final serverTime = response.data['data']['server_time'] as String?;
        if (serverTime != null) {
          await _db.setSetting('last_sync', serverTime);
        }
      }
    } catch (_) {}
  }
}
