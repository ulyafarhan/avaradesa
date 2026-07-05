import 'dart:io';
import 'package:drift/drift.dart';
import 'package:drift/native.dart';
import 'package:path_provider/path_provider.dart';
import 'package:path/path.dart' as p;
import 'tables/tables.dart';

part 'app_database.g.dart';

@DriftDatabase(tables: [
  SuratKategori,
  SuratPengajuan,
  MutasiPenduduk,
  InformasiPublik,
  PendudukProfile,
  FasilitasDesa,
  SyncQueue,
  Settings,
])
class AppDatabase extends _$AppDatabase {
  AppDatabase() : super(_openConnection());

  AppDatabase.forTesting(super.e);

  @override
  int get schemaVersion => 1;

  @override
  MigrationStrategy get migration {
    return MigrationStrategy(
      onCreate: (m) => m.createAll(),
    );
  }

  // --- Sync Queue ---
  Future<int> insertSyncItem(SyncQueueCompanion item) =>
      into(syncQueue).insert(item, mode: InsertMode.insertOrReplace);

  Future<List<SyncQueueData>> pendingSyncItems() =>
      (select(syncQueue)..where((t) => t.status.equals('pending'))).get();

  Future<void> updateSyncStatus(String clientId, String status, {String? error, int? serverId}) =>
      (update(syncQueue)..where((t) => t.clientId.equals(clientId))).write(
        SyncQueueCompanion(
          status: Value(status),
          error: Value(error),
          serverId: Value(serverId),
        ),
      );

  Future<int> syncQueueCount() =>
      customSelect('SELECT COUNT(*) as c FROM sync_queue WHERE status = \'pending\'',
          readsFrom: {syncQueue}).getSingle().then((row) => row.read<int>('c'));

  // --- Settings ---
  Future<void> setSetting(String key, String value) =>
      into(settings).insertOnConflictUpdate(SettingsCompanion(
        key: Value(key),
        value: Value(value),
      ));

  Future<String?> getSetting(String key) async {
    final rows = await (select(settings)..where((t) => t.key.equals(key))).get();
    return rows.isNotEmpty ? rows.first.value : null;
  }
}

LazyDatabase _openConnection() {
  return LazyDatabase(() async {
    final dir = await getApplicationDocumentsDirectory();
    final file = File(p.join(dir.path, 'avaradesa.db'));
    return NativeDatabase.createInBackground(file);
  });
}
