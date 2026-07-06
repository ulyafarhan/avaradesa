import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../database/database_provider.dart';

final syncQueueCountProvider = FutureProvider<int>((ref) async {
  final db = ref.read(databaseProvider);
  final pending = await db.pendingSyncItems();
  return pending.length;
});
