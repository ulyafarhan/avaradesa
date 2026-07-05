import 'package:drift/drift.dart';

class SuratKategori extends Table {
  IntColumn get id => integer().autoIncrement()();
  TextColumn get nama => text()();
  TextColumn get deskripsi => text().nullable()();
  TextColumn get icon => text().nullable()();
  TextColumn get persyaratan => text()();
}

class SuratPengajuan extends Table {
  IntColumn get id => integer().autoIncrement()();
  IntColumn get kategoriId => integer().references(SuratKategori, #id)();
  TextColumn get kodePengajuan => text()();
  TextColumn get status => text().withDefault(const Constant('menunggu'))();
  TextColumn get dataPengajuan => text()();
  TextColumn get keterangan => text().nullable()();
  TextColumn get nomorSurat => text().nullable()();
  TextColumn get fileSurat => text().nullable()();
  TextColumn get hashVerifikasi => text().nullable()();
  TextColumn get catatanPenolakan => text().nullable()();
  TextColumn get serverId => text().nullable()();
  DateTimeColumn get createdAt => dateTime().withDefault(currentDateAndTime)();
  DateTimeColumn get updatedAt => dateTime().withDefault(currentDateAndTime)();
}

class MutasiPenduduk extends Table {
  IntColumn get id => integer().autoIncrement()();
  TextColumn get jenisMutasi => text()();
  DateTimeColumn get tanggalMutasi => dateTime()();
  TextColumn get alamatAsal => text()();
  TextColumn get alasan => text()();
  TextColumn get status => text().withDefault(const Constant('menunggu'))();
  TextColumn get serverId => text().nullable()();
  DateTimeColumn get createdAt => dateTime().withDefault(currentDateAndTime)();
}

class InformasiPublik extends Table {
  IntColumn get id => integer().autoIncrement()();
  TextColumn get judul => text()();
  TextColumn get slug => text().customConstraint('UNIQUE NOT NULL')();
  TextColumn get konten => text()();
  TextColumn get gambar => text().nullable()();
  TextColumn get penulis => text().nullable()();
  TextColumn get kategori => text().nullable()();
  BoolColumn get isPublished => boolean().withDefault(const Constant(true))();
  DateTimeColumn get createdAt => dateTime().withDefault(currentDateAndTime)();
}

class PendudukProfile extends Table {
  IntColumn get id => integer().autoIncrement()();
  TextColumn get nik => text()();
  TextColumn get namaLengkap => text()();
  TextColumn get tempatLahir => text().nullable()();
  DateTimeColumn get tanggalLahir => dateTime().nullable()();
  TextColumn get jenisKelamin => text()();
  TextColumn get agama => text().nullable()();
  TextColumn get noKk => text()();
  TextColumn get fotoProfil => text().nullable()();
  TextColumn get telegramChatId => text().nullable()();
}

class FasilitasDesa extends Table {
  IntColumn get id => integer().autoIncrement()();
  TextColumn get nama => text()();
  TextColumn get jenisFasilitas => text()();
  TextColumn get alamat => text().nullable()();
  TextColumn get kondisi => text().nullable()();
}

class SyncQueue extends Table {
  TextColumn get clientId => text()();
  TextColumn get type => text()();
  TextColumn get action => text()();
  TextColumn get data => text()();
  DateTimeColumn get createdAt => dateTime()();
  TextColumn get status => text().withDefault(const Constant('pending'))();
  TextColumn get error => text().nullable()();
  IntColumn get serverId => integer().nullable()();

  @override
  Set<Column> get primaryKey => {clientId};
}

class Settings extends Table {
  TextColumn get key => text()();
  TextColumn get value => text()();

  @override
  Set<Column> get primaryKey => {key};
}
