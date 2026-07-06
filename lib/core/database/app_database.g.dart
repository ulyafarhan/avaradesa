// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'app_database.dart';

// ignore_for_file: type=lint
class $SuratKategoriTable extends SuratKategori
    with TableInfo<$SuratKategoriTable, SuratKategoriData> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $SuratKategoriTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _idMeta = const VerificationMeta('id');
  @override
  late final GeneratedColumn<int> id = GeneratedColumn<int>(
    'id',
    aliasedName,
    false,
    hasAutoIncrement: true,
    type: DriftSqlType.int,
    requiredDuringInsert: false,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'PRIMARY KEY AUTOINCREMENT',
    ),
  );
  static const VerificationMeta _namaMeta = const VerificationMeta('nama');
  @override
  late final GeneratedColumn<String> nama = GeneratedColumn<String>(
    'nama',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _deskripsiMeta = const VerificationMeta(
    'deskripsi',
  );
  @override
  late final GeneratedColumn<String> deskripsi = GeneratedColumn<String>(
    'deskripsi',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _iconMeta = const VerificationMeta('icon');
  @override
  late final GeneratedColumn<String> icon = GeneratedColumn<String>(
    'icon',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _persyaratanMeta = const VerificationMeta(
    'persyaratan',
  );
  @override
  late final GeneratedColumn<String> persyaratan = GeneratedColumn<String>(
    'persyaratan',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  @override
  List<GeneratedColumn> get $columns => [
    id,
    nama,
    deskripsi,
    icon,
    persyaratan,
  ];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'surat_kategori';
  @override
  VerificationContext validateIntegrity(
    Insertable<SuratKategoriData> instance, {
    bool isInserting = false,
  }) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('id')) {
      context.handle(_idMeta, id.isAcceptableOrUnknown(data['id']!, _idMeta));
    }
    if (data.containsKey('nama')) {
      context.handle(
        _namaMeta,
        nama.isAcceptableOrUnknown(data['nama']!, _namaMeta),
      );
    } else if (isInserting) {
      context.missing(_namaMeta);
    }
    if (data.containsKey('deskripsi')) {
      context.handle(
        _deskripsiMeta,
        deskripsi.isAcceptableOrUnknown(data['deskripsi']!, _deskripsiMeta),
      );
    }
    if (data.containsKey('icon')) {
      context.handle(
        _iconMeta,
        icon.isAcceptableOrUnknown(data['icon']!, _iconMeta),
      );
    }
    if (data.containsKey('persyaratan')) {
      context.handle(
        _persyaratanMeta,
        persyaratan.isAcceptableOrUnknown(
          data['persyaratan']!,
          _persyaratanMeta,
        ),
      );
    } else if (isInserting) {
      context.missing(_persyaratanMeta);
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {id};
  @override
  SuratKategoriData map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return SuratKategoriData(
      id: attachedDatabase.typeMapping.read(
        DriftSqlType.int,
        data['${effectivePrefix}id'],
      )!,
      nama: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}nama'],
      )!,
      deskripsi: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}deskripsi'],
      ),
      icon: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}icon'],
      ),
      persyaratan: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}persyaratan'],
      )!,
    );
  }

  @override
  $SuratKategoriTable createAlias(String alias) {
    return $SuratKategoriTable(attachedDatabase, alias);
  }
}

class SuratKategoriData extends DataClass
    implements Insertable<SuratKategoriData> {
  final int id;
  final String nama;
  final String? deskripsi;
  final String? icon;
  final String persyaratan;
  const SuratKategoriData({
    required this.id,
    required this.nama,
    this.deskripsi,
    this.icon,
    required this.persyaratan,
  });
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['id'] = Variable<int>(id);
    map['nama'] = Variable<String>(nama);
    if (!nullToAbsent || deskripsi != null) {
      map['deskripsi'] = Variable<String>(deskripsi);
    }
    if (!nullToAbsent || icon != null) {
      map['icon'] = Variable<String>(icon);
    }
    map['persyaratan'] = Variable<String>(persyaratan);
    return map;
  }

  SuratKategoriCompanion toCompanion(bool nullToAbsent) {
    return SuratKategoriCompanion(
      id: Value(id),
      nama: Value(nama),
      deskripsi: deskripsi == null && nullToAbsent
          ? const Value.absent()
          : Value(deskripsi),
      icon: icon == null && nullToAbsent ? const Value.absent() : Value(icon),
      persyaratan: Value(persyaratan),
    );
  }

  factory SuratKategoriData.fromJson(
    Map<String, dynamic> json, {
    ValueSerializer? serializer,
  }) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return SuratKategoriData(
      id: serializer.fromJson<int>(json['id']),
      nama: serializer.fromJson<String>(json['nama']),
      deskripsi: serializer.fromJson<String?>(json['deskripsi']),
      icon: serializer.fromJson<String?>(json['icon']),
      persyaratan: serializer.fromJson<String>(json['persyaratan']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'id': serializer.toJson<int>(id),
      'nama': serializer.toJson<String>(nama),
      'deskripsi': serializer.toJson<String?>(deskripsi),
      'icon': serializer.toJson<String?>(icon),
      'persyaratan': serializer.toJson<String>(persyaratan),
    };
  }

  SuratKategoriData copyWith({
    int? id,
    String? nama,
    Value<String?> deskripsi = const Value.absent(),
    Value<String?> icon = const Value.absent(),
    String? persyaratan,
  }) => SuratKategoriData(
    id: id ?? this.id,
    nama: nama ?? this.nama,
    deskripsi: deskripsi.present ? deskripsi.value : this.deskripsi,
    icon: icon.present ? icon.value : this.icon,
    persyaratan: persyaratan ?? this.persyaratan,
  );
  SuratKategoriData copyWithCompanion(SuratKategoriCompanion data) {
    return SuratKategoriData(
      id: data.id.present ? data.id.value : this.id,
      nama: data.nama.present ? data.nama.value : this.nama,
      deskripsi: data.deskripsi.present ? data.deskripsi.value : this.deskripsi,
      icon: data.icon.present ? data.icon.value : this.icon,
      persyaratan: data.persyaratan.present
          ? data.persyaratan.value
          : this.persyaratan,
    );
  }

  @override
  String toString() {
    return (StringBuffer('SuratKategoriData(')
          ..write('id: $id, ')
          ..write('nama: $nama, ')
          ..write('deskripsi: $deskripsi, ')
          ..write('icon: $icon, ')
          ..write('persyaratan: $persyaratan')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(id, nama, deskripsi, icon, persyaratan);
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is SuratKategoriData &&
          other.id == this.id &&
          other.nama == this.nama &&
          other.deskripsi == this.deskripsi &&
          other.icon == this.icon &&
          other.persyaratan == this.persyaratan);
}

class SuratKategoriCompanion extends UpdateCompanion<SuratKategoriData> {
  final Value<int> id;
  final Value<String> nama;
  final Value<String?> deskripsi;
  final Value<String?> icon;
  final Value<String> persyaratan;
  const SuratKategoriCompanion({
    this.id = const Value.absent(),
    this.nama = const Value.absent(),
    this.deskripsi = const Value.absent(),
    this.icon = const Value.absent(),
    this.persyaratan = const Value.absent(),
  });
  SuratKategoriCompanion.insert({
    this.id = const Value.absent(),
    required String nama,
    this.deskripsi = const Value.absent(),
    this.icon = const Value.absent(),
    required String persyaratan,
  }) : nama = Value(nama),
       persyaratan = Value(persyaratan);
  static Insertable<SuratKategoriData> custom({
    Expression<int>? id,
    Expression<String>? nama,
    Expression<String>? deskripsi,
    Expression<String>? icon,
    Expression<String>? persyaratan,
  }) {
    return RawValuesInsertable({
      if (id != null) 'id': id,
      if (nama != null) 'nama': nama,
      if (deskripsi != null) 'deskripsi': deskripsi,
      if (icon != null) 'icon': icon,
      if (persyaratan != null) 'persyaratan': persyaratan,
    });
  }

  SuratKategoriCompanion copyWith({
    Value<int>? id,
    Value<String>? nama,
    Value<String?>? deskripsi,
    Value<String?>? icon,
    Value<String>? persyaratan,
  }) {
    return SuratKategoriCompanion(
      id: id ?? this.id,
      nama: nama ?? this.nama,
      deskripsi: deskripsi ?? this.deskripsi,
      icon: icon ?? this.icon,
      persyaratan: persyaratan ?? this.persyaratan,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (id.present) {
      map['id'] = Variable<int>(id.value);
    }
    if (nama.present) {
      map['nama'] = Variable<String>(nama.value);
    }
    if (deskripsi.present) {
      map['deskripsi'] = Variable<String>(deskripsi.value);
    }
    if (icon.present) {
      map['icon'] = Variable<String>(icon.value);
    }
    if (persyaratan.present) {
      map['persyaratan'] = Variable<String>(persyaratan.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('SuratKategoriCompanion(')
          ..write('id: $id, ')
          ..write('nama: $nama, ')
          ..write('deskripsi: $deskripsi, ')
          ..write('icon: $icon, ')
          ..write('persyaratan: $persyaratan')
          ..write(')'))
        .toString();
  }
}

class $SuratPengajuanTable extends SuratPengajuan
    with TableInfo<$SuratPengajuanTable, SuratPengajuanData> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $SuratPengajuanTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _idMeta = const VerificationMeta('id');
  @override
  late final GeneratedColumn<int> id = GeneratedColumn<int>(
    'id',
    aliasedName,
    false,
    hasAutoIncrement: true,
    type: DriftSqlType.int,
    requiredDuringInsert: false,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'PRIMARY KEY AUTOINCREMENT',
    ),
  );
  static const VerificationMeta _kategoriIdMeta = const VerificationMeta(
    'kategoriId',
  );
  @override
  late final GeneratedColumn<int> kategoriId = GeneratedColumn<int>(
    'kategori_id',
    aliasedName,
    false,
    type: DriftSqlType.int,
    requiredDuringInsert: true,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'REFERENCES surat_kategori (id)',
    ),
  );
  static const VerificationMeta _kodePengajuanMeta = const VerificationMeta(
    'kodePengajuan',
  );
  @override
  late final GeneratedColumn<String> kodePengajuan = GeneratedColumn<String>(
    'kode_pengajuan',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _statusMeta = const VerificationMeta('status');
  @override
  late final GeneratedColumn<String> status = GeneratedColumn<String>(
    'status',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant('menunggu'),
  );
  static const VerificationMeta _dataPengajuanMeta = const VerificationMeta(
    'dataPengajuan',
  );
  @override
  late final GeneratedColumn<String> dataPengajuan = GeneratedColumn<String>(
    'data_pengajuan',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _keteranganMeta = const VerificationMeta(
    'keterangan',
  );
  @override
  late final GeneratedColumn<String> keterangan = GeneratedColumn<String>(
    'keterangan',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _nomorSuratMeta = const VerificationMeta(
    'nomorSurat',
  );
  @override
  late final GeneratedColumn<String> nomorSurat = GeneratedColumn<String>(
    'nomor_surat',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _fileSuratMeta = const VerificationMeta(
    'fileSurat',
  );
  @override
  late final GeneratedColumn<String> fileSurat = GeneratedColumn<String>(
    'file_surat',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _hashVerifikasiMeta = const VerificationMeta(
    'hashVerifikasi',
  );
  @override
  late final GeneratedColumn<String> hashVerifikasi = GeneratedColumn<String>(
    'hash_verifikasi',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _catatanPenolakanMeta = const VerificationMeta(
    'catatanPenolakan',
  );
  @override
  late final GeneratedColumn<String> catatanPenolakan = GeneratedColumn<String>(
    'catatan_penolakan',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _serverIdMeta = const VerificationMeta(
    'serverId',
  );
  @override
  late final GeneratedColumn<String> serverId = GeneratedColumn<String>(
    'server_id',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _createdAtMeta = const VerificationMeta(
    'createdAt',
  );
  @override
  late final GeneratedColumn<DateTime> createdAt = GeneratedColumn<DateTime>(
    'created_at',
    aliasedName,
    false,
    type: DriftSqlType.dateTime,
    requiredDuringInsert: false,
    defaultValue: currentDateAndTime,
  );
  static const VerificationMeta _updatedAtMeta = const VerificationMeta(
    'updatedAt',
  );
  @override
  late final GeneratedColumn<DateTime> updatedAt = GeneratedColumn<DateTime>(
    'updated_at',
    aliasedName,
    false,
    type: DriftSqlType.dateTime,
    requiredDuringInsert: false,
    defaultValue: currentDateAndTime,
  );
  @override
  List<GeneratedColumn> get $columns => [
    id,
    kategoriId,
    kodePengajuan,
    status,
    dataPengajuan,
    keterangan,
    nomorSurat,
    fileSurat,
    hashVerifikasi,
    catatanPenolakan,
    serverId,
    createdAt,
    updatedAt,
  ];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'surat_pengajuan';
  @override
  VerificationContext validateIntegrity(
    Insertable<SuratPengajuanData> instance, {
    bool isInserting = false,
  }) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('id')) {
      context.handle(_idMeta, id.isAcceptableOrUnknown(data['id']!, _idMeta));
    }
    if (data.containsKey('kategori_id')) {
      context.handle(
        _kategoriIdMeta,
        kategoriId.isAcceptableOrUnknown(data['kategori_id']!, _kategoriIdMeta),
      );
    } else if (isInserting) {
      context.missing(_kategoriIdMeta);
    }
    if (data.containsKey('kode_pengajuan')) {
      context.handle(
        _kodePengajuanMeta,
        kodePengajuan.isAcceptableOrUnknown(
          data['kode_pengajuan']!,
          _kodePengajuanMeta,
        ),
      );
    } else if (isInserting) {
      context.missing(_kodePengajuanMeta);
    }
    if (data.containsKey('status')) {
      context.handle(
        _statusMeta,
        status.isAcceptableOrUnknown(data['status']!, _statusMeta),
      );
    }
    if (data.containsKey('data_pengajuan')) {
      context.handle(
        _dataPengajuanMeta,
        dataPengajuan.isAcceptableOrUnknown(
          data['data_pengajuan']!,
          _dataPengajuanMeta,
        ),
      );
    } else if (isInserting) {
      context.missing(_dataPengajuanMeta);
    }
    if (data.containsKey('keterangan')) {
      context.handle(
        _keteranganMeta,
        keterangan.isAcceptableOrUnknown(data['keterangan']!, _keteranganMeta),
      );
    }
    if (data.containsKey('nomor_surat')) {
      context.handle(
        _nomorSuratMeta,
        nomorSurat.isAcceptableOrUnknown(data['nomor_surat']!, _nomorSuratMeta),
      );
    }
    if (data.containsKey('file_surat')) {
      context.handle(
        _fileSuratMeta,
        fileSurat.isAcceptableOrUnknown(data['file_surat']!, _fileSuratMeta),
      );
    }
    if (data.containsKey('hash_verifikasi')) {
      context.handle(
        _hashVerifikasiMeta,
        hashVerifikasi.isAcceptableOrUnknown(
          data['hash_verifikasi']!,
          _hashVerifikasiMeta,
        ),
      );
    }
    if (data.containsKey('catatan_penolakan')) {
      context.handle(
        _catatanPenolakanMeta,
        catatanPenolakan.isAcceptableOrUnknown(
          data['catatan_penolakan']!,
          _catatanPenolakanMeta,
        ),
      );
    }
    if (data.containsKey('server_id')) {
      context.handle(
        _serverIdMeta,
        serverId.isAcceptableOrUnknown(data['server_id']!, _serverIdMeta),
      );
    }
    if (data.containsKey('created_at')) {
      context.handle(
        _createdAtMeta,
        createdAt.isAcceptableOrUnknown(data['created_at']!, _createdAtMeta),
      );
    }
    if (data.containsKey('updated_at')) {
      context.handle(
        _updatedAtMeta,
        updatedAt.isAcceptableOrUnknown(data['updated_at']!, _updatedAtMeta),
      );
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {id};
  @override
  SuratPengajuanData map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return SuratPengajuanData(
      id: attachedDatabase.typeMapping.read(
        DriftSqlType.int,
        data['${effectivePrefix}id'],
      )!,
      kategoriId: attachedDatabase.typeMapping.read(
        DriftSqlType.int,
        data['${effectivePrefix}kategori_id'],
      )!,
      kodePengajuan: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}kode_pengajuan'],
      )!,
      status: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}status'],
      )!,
      dataPengajuan: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}data_pengajuan'],
      )!,
      keterangan: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}keterangan'],
      ),
      nomorSurat: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}nomor_surat'],
      ),
      fileSurat: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}file_surat'],
      ),
      hashVerifikasi: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}hash_verifikasi'],
      ),
      catatanPenolakan: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}catatan_penolakan'],
      ),
      serverId: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}server_id'],
      ),
      createdAt: attachedDatabase.typeMapping.read(
        DriftSqlType.dateTime,
        data['${effectivePrefix}created_at'],
      )!,
      updatedAt: attachedDatabase.typeMapping.read(
        DriftSqlType.dateTime,
        data['${effectivePrefix}updated_at'],
      )!,
    );
  }

  @override
  $SuratPengajuanTable createAlias(String alias) {
    return $SuratPengajuanTable(attachedDatabase, alias);
  }
}

class SuratPengajuanData extends DataClass
    implements Insertable<SuratPengajuanData> {
  final int id;
  final int kategoriId;
  final String kodePengajuan;
  final String status;
  final String dataPengajuan;
  final String? keterangan;
  final String? nomorSurat;
  final String? fileSurat;
  final String? hashVerifikasi;
  final String? catatanPenolakan;
  final String? serverId;
  final DateTime createdAt;
  final DateTime updatedAt;
  const SuratPengajuanData({
    required this.id,
    required this.kategoriId,
    required this.kodePengajuan,
    required this.status,
    required this.dataPengajuan,
    this.keterangan,
    this.nomorSurat,
    this.fileSurat,
    this.hashVerifikasi,
    this.catatanPenolakan,
    this.serverId,
    required this.createdAt,
    required this.updatedAt,
  });
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['id'] = Variable<int>(id);
    map['kategori_id'] = Variable<int>(kategoriId);
    map['kode_pengajuan'] = Variable<String>(kodePengajuan);
    map['status'] = Variable<String>(status);
    map['data_pengajuan'] = Variable<String>(dataPengajuan);
    if (!nullToAbsent || keterangan != null) {
      map['keterangan'] = Variable<String>(keterangan);
    }
    if (!nullToAbsent || nomorSurat != null) {
      map['nomor_surat'] = Variable<String>(nomorSurat);
    }
    if (!nullToAbsent || fileSurat != null) {
      map['file_surat'] = Variable<String>(fileSurat);
    }
    if (!nullToAbsent || hashVerifikasi != null) {
      map['hash_verifikasi'] = Variable<String>(hashVerifikasi);
    }
    if (!nullToAbsent || catatanPenolakan != null) {
      map['catatan_penolakan'] = Variable<String>(catatanPenolakan);
    }
    if (!nullToAbsent || serverId != null) {
      map['server_id'] = Variable<String>(serverId);
    }
    map['created_at'] = Variable<DateTime>(createdAt);
    map['updated_at'] = Variable<DateTime>(updatedAt);
    return map;
  }

  SuratPengajuanCompanion toCompanion(bool nullToAbsent) {
    return SuratPengajuanCompanion(
      id: Value(id),
      kategoriId: Value(kategoriId),
      kodePengajuan: Value(kodePengajuan),
      status: Value(status),
      dataPengajuan: Value(dataPengajuan),
      keterangan: keterangan == null && nullToAbsent
          ? const Value.absent()
          : Value(keterangan),
      nomorSurat: nomorSurat == null && nullToAbsent
          ? const Value.absent()
          : Value(nomorSurat),
      fileSurat: fileSurat == null && nullToAbsent
          ? const Value.absent()
          : Value(fileSurat),
      hashVerifikasi: hashVerifikasi == null && nullToAbsent
          ? const Value.absent()
          : Value(hashVerifikasi),
      catatanPenolakan: catatanPenolakan == null && nullToAbsent
          ? const Value.absent()
          : Value(catatanPenolakan),
      serverId: serverId == null && nullToAbsent
          ? const Value.absent()
          : Value(serverId),
      createdAt: Value(createdAt),
      updatedAt: Value(updatedAt),
    );
  }

  factory SuratPengajuanData.fromJson(
    Map<String, dynamic> json, {
    ValueSerializer? serializer,
  }) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return SuratPengajuanData(
      id: serializer.fromJson<int>(json['id']),
      kategoriId: serializer.fromJson<int>(json['kategoriId']),
      kodePengajuan: serializer.fromJson<String>(json['kodePengajuan']),
      status: serializer.fromJson<String>(json['status']),
      dataPengajuan: serializer.fromJson<String>(json['dataPengajuan']),
      keterangan: serializer.fromJson<String?>(json['keterangan']),
      nomorSurat: serializer.fromJson<String?>(json['nomorSurat']),
      fileSurat: serializer.fromJson<String?>(json['fileSurat']),
      hashVerifikasi: serializer.fromJson<String?>(json['hashVerifikasi']),
      catatanPenolakan: serializer.fromJson<String?>(json['catatanPenolakan']),
      serverId: serializer.fromJson<String?>(json['serverId']),
      createdAt: serializer.fromJson<DateTime>(json['createdAt']),
      updatedAt: serializer.fromJson<DateTime>(json['updatedAt']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'id': serializer.toJson<int>(id),
      'kategoriId': serializer.toJson<int>(kategoriId),
      'kodePengajuan': serializer.toJson<String>(kodePengajuan),
      'status': serializer.toJson<String>(status),
      'dataPengajuan': serializer.toJson<String>(dataPengajuan),
      'keterangan': serializer.toJson<String?>(keterangan),
      'nomorSurat': serializer.toJson<String?>(nomorSurat),
      'fileSurat': serializer.toJson<String?>(fileSurat),
      'hashVerifikasi': serializer.toJson<String?>(hashVerifikasi),
      'catatanPenolakan': serializer.toJson<String?>(catatanPenolakan),
      'serverId': serializer.toJson<String?>(serverId),
      'createdAt': serializer.toJson<DateTime>(createdAt),
      'updatedAt': serializer.toJson<DateTime>(updatedAt),
    };
  }

  SuratPengajuanData copyWith({
    int? id,
    int? kategoriId,
    String? kodePengajuan,
    String? status,
    String? dataPengajuan,
    Value<String?> keterangan = const Value.absent(),
    Value<String?> nomorSurat = const Value.absent(),
    Value<String?> fileSurat = const Value.absent(),
    Value<String?> hashVerifikasi = const Value.absent(),
    Value<String?> catatanPenolakan = const Value.absent(),
    Value<String?> serverId = const Value.absent(),
    DateTime? createdAt,
    DateTime? updatedAt,
  }) => SuratPengajuanData(
    id: id ?? this.id,
    kategoriId: kategoriId ?? this.kategoriId,
    kodePengajuan: kodePengajuan ?? this.kodePengajuan,
    status: status ?? this.status,
    dataPengajuan: dataPengajuan ?? this.dataPengajuan,
    keterangan: keterangan.present ? keterangan.value : this.keterangan,
    nomorSurat: nomorSurat.present ? nomorSurat.value : this.nomorSurat,
    fileSurat: fileSurat.present ? fileSurat.value : this.fileSurat,
    hashVerifikasi: hashVerifikasi.present
        ? hashVerifikasi.value
        : this.hashVerifikasi,
    catatanPenolakan: catatanPenolakan.present
        ? catatanPenolakan.value
        : this.catatanPenolakan,
    serverId: serverId.present ? serverId.value : this.serverId,
    createdAt: createdAt ?? this.createdAt,
    updatedAt: updatedAt ?? this.updatedAt,
  );
  SuratPengajuanData copyWithCompanion(SuratPengajuanCompanion data) {
    return SuratPengajuanData(
      id: data.id.present ? data.id.value : this.id,
      kategoriId: data.kategoriId.present
          ? data.kategoriId.value
          : this.kategoriId,
      kodePengajuan: data.kodePengajuan.present
          ? data.kodePengajuan.value
          : this.kodePengajuan,
      status: data.status.present ? data.status.value : this.status,
      dataPengajuan: data.dataPengajuan.present
          ? data.dataPengajuan.value
          : this.dataPengajuan,
      keterangan: data.keterangan.present
          ? data.keterangan.value
          : this.keterangan,
      nomorSurat: data.nomorSurat.present
          ? data.nomorSurat.value
          : this.nomorSurat,
      fileSurat: data.fileSurat.present ? data.fileSurat.value : this.fileSurat,
      hashVerifikasi: data.hashVerifikasi.present
          ? data.hashVerifikasi.value
          : this.hashVerifikasi,
      catatanPenolakan: data.catatanPenolakan.present
          ? data.catatanPenolakan.value
          : this.catatanPenolakan,
      serverId: data.serverId.present ? data.serverId.value : this.serverId,
      createdAt: data.createdAt.present ? data.createdAt.value : this.createdAt,
      updatedAt: data.updatedAt.present ? data.updatedAt.value : this.updatedAt,
    );
  }

  @override
  String toString() {
    return (StringBuffer('SuratPengajuanData(')
          ..write('id: $id, ')
          ..write('kategoriId: $kategoriId, ')
          ..write('kodePengajuan: $kodePengajuan, ')
          ..write('status: $status, ')
          ..write('dataPengajuan: $dataPengajuan, ')
          ..write('keterangan: $keterangan, ')
          ..write('nomorSurat: $nomorSurat, ')
          ..write('fileSurat: $fileSurat, ')
          ..write('hashVerifikasi: $hashVerifikasi, ')
          ..write('catatanPenolakan: $catatanPenolakan, ')
          ..write('serverId: $serverId, ')
          ..write('createdAt: $createdAt, ')
          ..write('updatedAt: $updatedAt')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(
    id,
    kategoriId,
    kodePengajuan,
    status,
    dataPengajuan,
    keterangan,
    nomorSurat,
    fileSurat,
    hashVerifikasi,
    catatanPenolakan,
    serverId,
    createdAt,
    updatedAt,
  );
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is SuratPengajuanData &&
          other.id == this.id &&
          other.kategoriId == this.kategoriId &&
          other.kodePengajuan == this.kodePengajuan &&
          other.status == this.status &&
          other.dataPengajuan == this.dataPengajuan &&
          other.keterangan == this.keterangan &&
          other.nomorSurat == this.nomorSurat &&
          other.fileSurat == this.fileSurat &&
          other.hashVerifikasi == this.hashVerifikasi &&
          other.catatanPenolakan == this.catatanPenolakan &&
          other.serverId == this.serverId &&
          other.createdAt == this.createdAt &&
          other.updatedAt == this.updatedAt);
}

class SuratPengajuanCompanion extends UpdateCompanion<SuratPengajuanData> {
  final Value<int> id;
  final Value<int> kategoriId;
  final Value<String> kodePengajuan;
  final Value<String> status;
  final Value<String> dataPengajuan;
  final Value<String?> keterangan;
  final Value<String?> nomorSurat;
  final Value<String?> fileSurat;
  final Value<String?> hashVerifikasi;
  final Value<String?> catatanPenolakan;
  final Value<String?> serverId;
  final Value<DateTime> createdAt;
  final Value<DateTime> updatedAt;
  const SuratPengajuanCompanion({
    this.id = const Value.absent(),
    this.kategoriId = const Value.absent(),
    this.kodePengajuan = const Value.absent(),
    this.status = const Value.absent(),
    this.dataPengajuan = const Value.absent(),
    this.keterangan = const Value.absent(),
    this.nomorSurat = const Value.absent(),
    this.fileSurat = const Value.absent(),
    this.hashVerifikasi = const Value.absent(),
    this.catatanPenolakan = const Value.absent(),
    this.serverId = const Value.absent(),
    this.createdAt = const Value.absent(),
    this.updatedAt = const Value.absent(),
  });
  SuratPengajuanCompanion.insert({
    this.id = const Value.absent(),
    required int kategoriId,
    required String kodePengajuan,
    this.status = const Value.absent(),
    required String dataPengajuan,
    this.keterangan = const Value.absent(),
    this.nomorSurat = const Value.absent(),
    this.fileSurat = const Value.absent(),
    this.hashVerifikasi = const Value.absent(),
    this.catatanPenolakan = const Value.absent(),
    this.serverId = const Value.absent(),
    this.createdAt = const Value.absent(),
    this.updatedAt = const Value.absent(),
  }) : kategoriId = Value(kategoriId),
       kodePengajuan = Value(kodePengajuan),
       dataPengajuan = Value(dataPengajuan);
  static Insertable<SuratPengajuanData> custom({
    Expression<int>? id,
    Expression<int>? kategoriId,
    Expression<String>? kodePengajuan,
    Expression<String>? status,
    Expression<String>? dataPengajuan,
    Expression<String>? keterangan,
    Expression<String>? nomorSurat,
    Expression<String>? fileSurat,
    Expression<String>? hashVerifikasi,
    Expression<String>? catatanPenolakan,
    Expression<String>? serverId,
    Expression<DateTime>? createdAt,
    Expression<DateTime>? updatedAt,
  }) {
    return RawValuesInsertable({
      if (id != null) 'id': id,
      if (kategoriId != null) 'kategori_id': kategoriId,
      if (kodePengajuan != null) 'kode_pengajuan': kodePengajuan,
      if (status != null) 'status': status,
      if (dataPengajuan != null) 'data_pengajuan': dataPengajuan,
      if (keterangan != null) 'keterangan': keterangan,
      if (nomorSurat != null) 'nomor_surat': nomorSurat,
      if (fileSurat != null) 'file_surat': fileSurat,
      if (hashVerifikasi != null) 'hash_verifikasi': hashVerifikasi,
      if (catatanPenolakan != null) 'catatan_penolakan': catatanPenolakan,
      if (serverId != null) 'server_id': serverId,
      if (createdAt != null) 'created_at': createdAt,
      if (updatedAt != null) 'updated_at': updatedAt,
    });
  }

  SuratPengajuanCompanion copyWith({
    Value<int>? id,
    Value<int>? kategoriId,
    Value<String>? kodePengajuan,
    Value<String>? status,
    Value<String>? dataPengajuan,
    Value<String?>? keterangan,
    Value<String?>? nomorSurat,
    Value<String?>? fileSurat,
    Value<String?>? hashVerifikasi,
    Value<String?>? catatanPenolakan,
    Value<String?>? serverId,
    Value<DateTime>? createdAt,
    Value<DateTime>? updatedAt,
  }) {
    return SuratPengajuanCompanion(
      id: id ?? this.id,
      kategoriId: kategoriId ?? this.kategoriId,
      kodePengajuan: kodePengajuan ?? this.kodePengajuan,
      status: status ?? this.status,
      dataPengajuan: dataPengajuan ?? this.dataPengajuan,
      keterangan: keterangan ?? this.keterangan,
      nomorSurat: nomorSurat ?? this.nomorSurat,
      fileSurat: fileSurat ?? this.fileSurat,
      hashVerifikasi: hashVerifikasi ?? this.hashVerifikasi,
      catatanPenolakan: catatanPenolakan ?? this.catatanPenolakan,
      serverId: serverId ?? this.serverId,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (id.present) {
      map['id'] = Variable<int>(id.value);
    }
    if (kategoriId.present) {
      map['kategori_id'] = Variable<int>(kategoriId.value);
    }
    if (kodePengajuan.present) {
      map['kode_pengajuan'] = Variable<String>(kodePengajuan.value);
    }
    if (status.present) {
      map['status'] = Variable<String>(status.value);
    }
    if (dataPengajuan.present) {
      map['data_pengajuan'] = Variable<String>(dataPengajuan.value);
    }
    if (keterangan.present) {
      map['keterangan'] = Variable<String>(keterangan.value);
    }
    if (nomorSurat.present) {
      map['nomor_surat'] = Variable<String>(nomorSurat.value);
    }
    if (fileSurat.present) {
      map['file_surat'] = Variable<String>(fileSurat.value);
    }
    if (hashVerifikasi.present) {
      map['hash_verifikasi'] = Variable<String>(hashVerifikasi.value);
    }
    if (catatanPenolakan.present) {
      map['catatan_penolakan'] = Variable<String>(catatanPenolakan.value);
    }
    if (serverId.present) {
      map['server_id'] = Variable<String>(serverId.value);
    }
    if (createdAt.present) {
      map['created_at'] = Variable<DateTime>(createdAt.value);
    }
    if (updatedAt.present) {
      map['updated_at'] = Variable<DateTime>(updatedAt.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('SuratPengajuanCompanion(')
          ..write('id: $id, ')
          ..write('kategoriId: $kategoriId, ')
          ..write('kodePengajuan: $kodePengajuan, ')
          ..write('status: $status, ')
          ..write('dataPengajuan: $dataPengajuan, ')
          ..write('keterangan: $keterangan, ')
          ..write('nomorSurat: $nomorSurat, ')
          ..write('fileSurat: $fileSurat, ')
          ..write('hashVerifikasi: $hashVerifikasi, ')
          ..write('catatanPenolakan: $catatanPenolakan, ')
          ..write('serverId: $serverId, ')
          ..write('createdAt: $createdAt, ')
          ..write('updatedAt: $updatedAt')
          ..write(')'))
        .toString();
  }
}

class $MutasiPendudukTable extends MutasiPenduduk
    with TableInfo<$MutasiPendudukTable, MutasiPendudukData> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $MutasiPendudukTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _idMeta = const VerificationMeta('id');
  @override
  late final GeneratedColumn<int> id = GeneratedColumn<int>(
    'id',
    aliasedName,
    false,
    hasAutoIncrement: true,
    type: DriftSqlType.int,
    requiredDuringInsert: false,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'PRIMARY KEY AUTOINCREMENT',
    ),
  );
  static const VerificationMeta _jenisMutasiMeta = const VerificationMeta(
    'jenisMutasi',
  );
  @override
  late final GeneratedColumn<String> jenisMutasi = GeneratedColumn<String>(
    'jenis_mutasi',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _tanggalMutasiMeta = const VerificationMeta(
    'tanggalMutasi',
  );
  @override
  late final GeneratedColumn<DateTime> tanggalMutasi =
      GeneratedColumn<DateTime>(
        'tanggal_mutasi',
        aliasedName,
        false,
        type: DriftSqlType.dateTime,
        requiredDuringInsert: true,
      );
  static const VerificationMeta _alamatAsalMeta = const VerificationMeta(
    'alamatAsal',
  );
  @override
  late final GeneratedColumn<String> alamatAsal = GeneratedColumn<String>(
    'alamat_asal',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _alasanMeta = const VerificationMeta('alasan');
  @override
  late final GeneratedColumn<String> alasan = GeneratedColumn<String>(
    'alasan',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _statusMeta = const VerificationMeta('status');
  @override
  late final GeneratedColumn<String> status = GeneratedColumn<String>(
    'status',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant('menunggu'),
  );
  static const VerificationMeta _serverIdMeta = const VerificationMeta(
    'serverId',
  );
  @override
  late final GeneratedColumn<String> serverId = GeneratedColumn<String>(
    'server_id',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _createdAtMeta = const VerificationMeta(
    'createdAt',
  );
  @override
  late final GeneratedColumn<DateTime> createdAt = GeneratedColumn<DateTime>(
    'created_at',
    aliasedName,
    false,
    type: DriftSqlType.dateTime,
    requiredDuringInsert: false,
    defaultValue: currentDateAndTime,
  );
  @override
  List<GeneratedColumn> get $columns => [
    id,
    jenisMutasi,
    tanggalMutasi,
    alamatAsal,
    alasan,
    status,
    serverId,
    createdAt,
  ];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'mutasi_penduduk';
  @override
  VerificationContext validateIntegrity(
    Insertable<MutasiPendudukData> instance, {
    bool isInserting = false,
  }) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('id')) {
      context.handle(_idMeta, id.isAcceptableOrUnknown(data['id']!, _idMeta));
    }
    if (data.containsKey('jenis_mutasi')) {
      context.handle(
        _jenisMutasiMeta,
        jenisMutasi.isAcceptableOrUnknown(
          data['jenis_mutasi']!,
          _jenisMutasiMeta,
        ),
      );
    } else if (isInserting) {
      context.missing(_jenisMutasiMeta);
    }
    if (data.containsKey('tanggal_mutasi')) {
      context.handle(
        _tanggalMutasiMeta,
        tanggalMutasi.isAcceptableOrUnknown(
          data['tanggal_mutasi']!,
          _tanggalMutasiMeta,
        ),
      );
    } else if (isInserting) {
      context.missing(_tanggalMutasiMeta);
    }
    if (data.containsKey('alamat_asal')) {
      context.handle(
        _alamatAsalMeta,
        alamatAsal.isAcceptableOrUnknown(data['alamat_asal']!, _alamatAsalMeta),
      );
    } else if (isInserting) {
      context.missing(_alamatAsalMeta);
    }
    if (data.containsKey('alasan')) {
      context.handle(
        _alasanMeta,
        alasan.isAcceptableOrUnknown(data['alasan']!, _alasanMeta),
      );
    } else if (isInserting) {
      context.missing(_alasanMeta);
    }
    if (data.containsKey('status')) {
      context.handle(
        _statusMeta,
        status.isAcceptableOrUnknown(data['status']!, _statusMeta),
      );
    }
    if (data.containsKey('server_id')) {
      context.handle(
        _serverIdMeta,
        serverId.isAcceptableOrUnknown(data['server_id']!, _serverIdMeta),
      );
    }
    if (data.containsKey('created_at')) {
      context.handle(
        _createdAtMeta,
        createdAt.isAcceptableOrUnknown(data['created_at']!, _createdAtMeta),
      );
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {id};
  @override
  MutasiPendudukData map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return MutasiPendudukData(
      id: attachedDatabase.typeMapping.read(
        DriftSqlType.int,
        data['${effectivePrefix}id'],
      )!,
      jenisMutasi: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}jenis_mutasi'],
      )!,
      tanggalMutasi: attachedDatabase.typeMapping.read(
        DriftSqlType.dateTime,
        data['${effectivePrefix}tanggal_mutasi'],
      )!,
      alamatAsal: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}alamat_asal'],
      )!,
      alasan: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}alasan'],
      )!,
      status: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}status'],
      )!,
      serverId: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}server_id'],
      ),
      createdAt: attachedDatabase.typeMapping.read(
        DriftSqlType.dateTime,
        data['${effectivePrefix}created_at'],
      )!,
    );
  }

  @override
  $MutasiPendudukTable createAlias(String alias) {
    return $MutasiPendudukTable(attachedDatabase, alias);
  }
}

class MutasiPendudukData extends DataClass
    implements Insertable<MutasiPendudukData> {
  final int id;
  final String jenisMutasi;
  final DateTime tanggalMutasi;
  final String alamatAsal;
  final String alasan;
  final String status;
  final String? serverId;
  final DateTime createdAt;
  const MutasiPendudukData({
    required this.id,
    required this.jenisMutasi,
    required this.tanggalMutasi,
    required this.alamatAsal,
    required this.alasan,
    required this.status,
    this.serverId,
    required this.createdAt,
  });
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['id'] = Variable<int>(id);
    map['jenis_mutasi'] = Variable<String>(jenisMutasi);
    map['tanggal_mutasi'] = Variable<DateTime>(tanggalMutasi);
    map['alamat_asal'] = Variable<String>(alamatAsal);
    map['alasan'] = Variable<String>(alasan);
    map['status'] = Variable<String>(status);
    if (!nullToAbsent || serverId != null) {
      map['server_id'] = Variable<String>(serverId);
    }
    map['created_at'] = Variable<DateTime>(createdAt);
    return map;
  }

  MutasiPendudukCompanion toCompanion(bool nullToAbsent) {
    return MutasiPendudukCompanion(
      id: Value(id),
      jenisMutasi: Value(jenisMutasi),
      tanggalMutasi: Value(tanggalMutasi),
      alamatAsal: Value(alamatAsal),
      alasan: Value(alasan),
      status: Value(status),
      serverId: serverId == null && nullToAbsent
          ? const Value.absent()
          : Value(serverId),
      createdAt: Value(createdAt),
    );
  }

  factory MutasiPendudukData.fromJson(
    Map<String, dynamic> json, {
    ValueSerializer? serializer,
  }) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return MutasiPendudukData(
      id: serializer.fromJson<int>(json['id']),
      jenisMutasi: serializer.fromJson<String>(json['jenisMutasi']),
      tanggalMutasi: serializer.fromJson<DateTime>(json['tanggalMutasi']),
      alamatAsal: serializer.fromJson<String>(json['alamatAsal']),
      alasan: serializer.fromJson<String>(json['alasan']),
      status: serializer.fromJson<String>(json['status']),
      serverId: serializer.fromJson<String?>(json['serverId']),
      createdAt: serializer.fromJson<DateTime>(json['createdAt']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'id': serializer.toJson<int>(id),
      'jenisMutasi': serializer.toJson<String>(jenisMutasi),
      'tanggalMutasi': serializer.toJson<DateTime>(tanggalMutasi),
      'alamatAsal': serializer.toJson<String>(alamatAsal),
      'alasan': serializer.toJson<String>(alasan),
      'status': serializer.toJson<String>(status),
      'serverId': serializer.toJson<String?>(serverId),
      'createdAt': serializer.toJson<DateTime>(createdAt),
    };
  }

  MutasiPendudukData copyWith({
    int? id,
    String? jenisMutasi,
    DateTime? tanggalMutasi,
    String? alamatAsal,
    String? alasan,
    String? status,
    Value<String?> serverId = const Value.absent(),
    DateTime? createdAt,
  }) => MutasiPendudukData(
    id: id ?? this.id,
    jenisMutasi: jenisMutasi ?? this.jenisMutasi,
    tanggalMutasi: tanggalMutasi ?? this.tanggalMutasi,
    alamatAsal: alamatAsal ?? this.alamatAsal,
    alasan: alasan ?? this.alasan,
    status: status ?? this.status,
    serverId: serverId.present ? serverId.value : this.serverId,
    createdAt: createdAt ?? this.createdAt,
  );
  MutasiPendudukData copyWithCompanion(MutasiPendudukCompanion data) {
    return MutasiPendudukData(
      id: data.id.present ? data.id.value : this.id,
      jenisMutasi: data.jenisMutasi.present
          ? data.jenisMutasi.value
          : this.jenisMutasi,
      tanggalMutasi: data.tanggalMutasi.present
          ? data.tanggalMutasi.value
          : this.tanggalMutasi,
      alamatAsal: data.alamatAsal.present
          ? data.alamatAsal.value
          : this.alamatAsal,
      alasan: data.alasan.present ? data.alasan.value : this.alasan,
      status: data.status.present ? data.status.value : this.status,
      serverId: data.serverId.present ? data.serverId.value : this.serverId,
      createdAt: data.createdAt.present ? data.createdAt.value : this.createdAt,
    );
  }

  @override
  String toString() {
    return (StringBuffer('MutasiPendudukData(')
          ..write('id: $id, ')
          ..write('jenisMutasi: $jenisMutasi, ')
          ..write('tanggalMutasi: $tanggalMutasi, ')
          ..write('alamatAsal: $alamatAsal, ')
          ..write('alasan: $alasan, ')
          ..write('status: $status, ')
          ..write('serverId: $serverId, ')
          ..write('createdAt: $createdAt')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(
    id,
    jenisMutasi,
    tanggalMutasi,
    alamatAsal,
    alasan,
    status,
    serverId,
    createdAt,
  );
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is MutasiPendudukData &&
          other.id == this.id &&
          other.jenisMutasi == this.jenisMutasi &&
          other.tanggalMutasi == this.tanggalMutasi &&
          other.alamatAsal == this.alamatAsal &&
          other.alasan == this.alasan &&
          other.status == this.status &&
          other.serverId == this.serverId &&
          other.createdAt == this.createdAt);
}

class MutasiPendudukCompanion extends UpdateCompanion<MutasiPendudukData> {
  final Value<int> id;
  final Value<String> jenisMutasi;
  final Value<DateTime> tanggalMutasi;
  final Value<String> alamatAsal;
  final Value<String> alasan;
  final Value<String> status;
  final Value<String?> serverId;
  final Value<DateTime> createdAt;
  const MutasiPendudukCompanion({
    this.id = const Value.absent(),
    this.jenisMutasi = const Value.absent(),
    this.tanggalMutasi = const Value.absent(),
    this.alamatAsal = const Value.absent(),
    this.alasan = const Value.absent(),
    this.status = const Value.absent(),
    this.serverId = const Value.absent(),
    this.createdAt = const Value.absent(),
  });
  MutasiPendudukCompanion.insert({
    this.id = const Value.absent(),
    required String jenisMutasi,
    required DateTime tanggalMutasi,
    required String alamatAsal,
    required String alasan,
    this.status = const Value.absent(),
    this.serverId = const Value.absent(),
    this.createdAt = const Value.absent(),
  }) : jenisMutasi = Value(jenisMutasi),
       tanggalMutasi = Value(tanggalMutasi),
       alamatAsal = Value(alamatAsal),
       alasan = Value(alasan);
  static Insertable<MutasiPendudukData> custom({
    Expression<int>? id,
    Expression<String>? jenisMutasi,
    Expression<DateTime>? tanggalMutasi,
    Expression<String>? alamatAsal,
    Expression<String>? alasan,
    Expression<String>? status,
    Expression<String>? serverId,
    Expression<DateTime>? createdAt,
  }) {
    return RawValuesInsertable({
      if (id != null) 'id': id,
      if (jenisMutasi != null) 'jenis_mutasi': jenisMutasi,
      if (tanggalMutasi != null) 'tanggal_mutasi': tanggalMutasi,
      if (alamatAsal != null) 'alamat_asal': alamatAsal,
      if (alasan != null) 'alasan': alasan,
      if (status != null) 'status': status,
      if (serverId != null) 'server_id': serverId,
      if (createdAt != null) 'created_at': createdAt,
    });
  }

  MutasiPendudukCompanion copyWith({
    Value<int>? id,
    Value<String>? jenisMutasi,
    Value<DateTime>? tanggalMutasi,
    Value<String>? alamatAsal,
    Value<String>? alasan,
    Value<String>? status,
    Value<String?>? serverId,
    Value<DateTime>? createdAt,
  }) {
    return MutasiPendudukCompanion(
      id: id ?? this.id,
      jenisMutasi: jenisMutasi ?? this.jenisMutasi,
      tanggalMutasi: tanggalMutasi ?? this.tanggalMutasi,
      alamatAsal: alamatAsal ?? this.alamatAsal,
      alasan: alasan ?? this.alasan,
      status: status ?? this.status,
      serverId: serverId ?? this.serverId,
      createdAt: createdAt ?? this.createdAt,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (id.present) {
      map['id'] = Variable<int>(id.value);
    }
    if (jenisMutasi.present) {
      map['jenis_mutasi'] = Variable<String>(jenisMutasi.value);
    }
    if (tanggalMutasi.present) {
      map['tanggal_mutasi'] = Variable<DateTime>(tanggalMutasi.value);
    }
    if (alamatAsal.present) {
      map['alamat_asal'] = Variable<String>(alamatAsal.value);
    }
    if (alasan.present) {
      map['alasan'] = Variable<String>(alasan.value);
    }
    if (status.present) {
      map['status'] = Variable<String>(status.value);
    }
    if (serverId.present) {
      map['server_id'] = Variable<String>(serverId.value);
    }
    if (createdAt.present) {
      map['created_at'] = Variable<DateTime>(createdAt.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('MutasiPendudukCompanion(')
          ..write('id: $id, ')
          ..write('jenisMutasi: $jenisMutasi, ')
          ..write('tanggalMutasi: $tanggalMutasi, ')
          ..write('alamatAsal: $alamatAsal, ')
          ..write('alasan: $alasan, ')
          ..write('status: $status, ')
          ..write('serverId: $serverId, ')
          ..write('createdAt: $createdAt')
          ..write(')'))
        .toString();
  }
}

class $InformasiPublikTable extends InformasiPublik
    with TableInfo<$InformasiPublikTable, InformasiPublikData> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $InformasiPublikTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _idMeta = const VerificationMeta('id');
  @override
  late final GeneratedColumn<int> id = GeneratedColumn<int>(
    'id',
    aliasedName,
    false,
    hasAutoIncrement: true,
    type: DriftSqlType.int,
    requiredDuringInsert: false,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'PRIMARY KEY AUTOINCREMENT',
    ),
  );
  static const VerificationMeta _judulMeta = const VerificationMeta('judul');
  @override
  late final GeneratedColumn<String> judul = GeneratedColumn<String>(
    'judul',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _slugMeta = const VerificationMeta('slug');
  @override
  late final GeneratedColumn<String> slug = GeneratedColumn<String>(
    'slug',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
    $customConstraints: 'UNIQUE NOT NULL',
  );
  static const VerificationMeta _kontenMeta = const VerificationMeta('konten');
  @override
  late final GeneratedColumn<String> konten = GeneratedColumn<String>(
    'konten',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _gambarMeta = const VerificationMeta('gambar');
  @override
  late final GeneratedColumn<String> gambar = GeneratedColumn<String>(
    'gambar',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _penulisMeta = const VerificationMeta(
    'penulis',
  );
  @override
  late final GeneratedColumn<String> penulis = GeneratedColumn<String>(
    'penulis',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _kategoriMeta = const VerificationMeta(
    'kategori',
  );
  @override
  late final GeneratedColumn<String> kategori = GeneratedColumn<String>(
    'kategori',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _isPublishedMeta = const VerificationMeta(
    'isPublished',
  );
  @override
  late final GeneratedColumn<bool> isPublished = GeneratedColumn<bool>(
    'is_published',
    aliasedName,
    false,
    type: DriftSqlType.bool,
    requiredDuringInsert: false,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'CHECK ("is_published" IN (0, 1))',
    ),
    defaultValue: const Constant(true),
  );
  static const VerificationMeta _createdAtMeta = const VerificationMeta(
    'createdAt',
  );
  @override
  late final GeneratedColumn<DateTime> createdAt = GeneratedColumn<DateTime>(
    'created_at',
    aliasedName,
    false,
    type: DriftSqlType.dateTime,
    requiredDuringInsert: false,
    defaultValue: currentDateAndTime,
  );
  @override
  List<GeneratedColumn> get $columns => [
    id,
    judul,
    slug,
    konten,
    gambar,
    penulis,
    kategori,
    isPublished,
    createdAt,
  ];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'informasi_publik';
  @override
  VerificationContext validateIntegrity(
    Insertable<InformasiPublikData> instance, {
    bool isInserting = false,
  }) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('id')) {
      context.handle(_idMeta, id.isAcceptableOrUnknown(data['id']!, _idMeta));
    }
    if (data.containsKey('judul')) {
      context.handle(
        _judulMeta,
        judul.isAcceptableOrUnknown(data['judul']!, _judulMeta),
      );
    } else if (isInserting) {
      context.missing(_judulMeta);
    }
    if (data.containsKey('slug')) {
      context.handle(
        _slugMeta,
        slug.isAcceptableOrUnknown(data['slug']!, _slugMeta),
      );
    } else if (isInserting) {
      context.missing(_slugMeta);
    }
    if (data.containsKey('konten')) {
      context.handle(
        _kontenMeta,
        konten.isAcceptableOrUnknown(data['konten']!, _kontenMeta),
      );
    } else if (isInserting) {
      context.missing(_kontenMeta);
    }
    if (data.containsKey('gambar')) {
      context.handle(
        _gambarMeta,
        gambar.isAcceptableOrUnknown(data['gambar']!, _gambarMeta),
      );
    }
    if (data.containsKey('penulis')) {
      context.handle(
        _penulisMeta,
        penulis.isAcceptableOrUnknown(data['penulis']!, _penulisMeta),
      );
    }
    if (data.containsKey('kategori')) {
      context.handle(
        _kategoriMeta,
        kategori.isAcceptableOrUnknown(data['kategori']!, _kategoriMeta),
      );
    }
    if (data.containsKey('is_published')) {
      context.handle(
        _isPublishedMeta,
        isPublished.isAcceptableOrUnknown(
          data['is_published']!,
          _isPublishedMeta,
        ),
      );
    }
    if (data.containsKey('created_at')) {
      context.handle(
        _createdAtMeta,
        createdAt.isAcceptableOrUnknown(data['created_at']!, _createdAtMeta),
      );
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {id};
  @override
  InformasiPublikData map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return InformasiPublikData(
      id: attachedDatabase.typeMapping.read(
        DriftSqlType.int,
        data['${effectivePrefix}id'],
      )!,
      judul: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}judul'],
      )!,
      slug: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}slug'],
      )!,
      konten: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}konten'],
      )!,
      gambar: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}gambar'],
      ),
      penulis: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}penulis'],
      ),
      kategori: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}kategori'],
      ),
      isPublished: attachedDatabase.typeMapping.read(
        DriftSqlType.bool,
        data['${effectivePrefix}is_published'],
      )!,
      createdAt: attachedDatabase.typeMapping.read(
        DriftSqlType.dateTime,
        data['${effectivePrefix}created_at'],
      )!,
    );
  }

  @override
  $InformasiPublikTable createAlias(String alias) {
    return $InformasiPublikTable(attachedDatabase, alias);
  }
}

class InformasiPublikData extends DataClass
    implements Insertable<InformasiPublikData> {
  final int id;
  final String judul;
  final String slug;
  final String konten;
  final String? gambar;
  final String? penulis;
  final String? kategori;
  final bool isPublished;
  final DateTime createdAt;
  const InformasiPublikData({
    required this.id,
    required this.judul,
    required this.slug,
    required this.konten,
    this.gambar,
    this.penulis,
    this.kategori,
    required this.isPublished,
    required this.createdAt,
  });
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['id'] = Variable<int>(id);
    map['judul'] = Variable<String>(judul);
    map['slug'] = Variable<String>(slug);
    map['konten'] = Variable<String>(konten);
    if (!nullToAbsent || gambar != null) {
      map['gambar'] = Variable<String>(gambar);
    }
    if (!nullToAbsent || penulis != null) {
      map['penulis'] = Variable<String>(penulis);
    }
    if (!nullToAbsent || kategori != null) {
      map['kategori'] = Variable<String>(kategori);
    }
    map['is_published'] = Variable<bool>(isPublished);
    map['created_at'] = Variable<DateTime>(createdAt);
    return map;
  }

  InformasiPublikCompanion toCompanion(bool nullToAbsent) {
    return InformasiPublikCompanion(
      id: Value(id),
      judul: Value(judul),
      slug: Value(slug),
      konten: Value(konten),
      gambar: gambar == null && nullToAbsent
          ? const Value.absent()
          : Value(gambar),
      penulis: penulis == null && nullToAbsent
          ? const Value.absent()
          : Value(penulis),
      kategori: kategori == null && nullToAbsent
          ? const Value.absent()
          : Value(kategori),
      isPublished: Value(isPublished),
      createdAt: Value(createdAt),
    );
  }

  factory InformasiPublikData.fromJson(
    Map<String, dynamic> json, {
    ValueSerializer? serializer,
  }) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return InformasiPublikData(
      id: serializer.fromJson<int>(json['id']),
      judul: serializer.fromJson<String>(json['judul']),
      slug: serializer.fromJson<String>(json['slug']),
      konten: serializer.fromJson<String>(json['konten']),
      gambar: serializer.fromJson<String?>(json['gambar']),
      penulis: serializer.fromJson<String?>(json['penulis']),
      kategori: serializer.fromJson<String?>(json['kategori']),
      isPublished: serializer.fromJson<bool>(json['isPublished']),
      createdAt: serializer.fromJson<DateTime>(json['createdAt']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'id': serializer.toJson<int>(id),
      'judul': serializer.toJson<String>(judul),
      'slug': serializer.toJson<String>(slug),
      'konten': serializer.toJson<String>(konten),
      'gambar': serializer.toJson<String?>(gambar),
      'penulis': serializer.toJson<String?>(penulis),
      'kategori': serializer.toJson<String?>(kategori),
      'isPublished': serializer.toJson<bool>(isPublished),
      'createdAt': serializer.toJson<DateTime>(createdAt),
    };
  }

  InformasiPublikData copyWith({
    int? id,
    String? judul,
    String? slug,
    String? konten,
    Value<String?> gambar = const Value.absent(),
    Value<String?> penulis = const Value.absent(),
    Value<String?> kategori = const Value.absent(),
    bool? isPublished,
    DateTime? createdAt,
  }) => InformasiPublikData(
    id: id ?? this.id,
    judul: judul ?? this.judul,
    slug: slug ?? this.slug,
    konten: konten ?? this.konten,
    gambar: gambar.present ? gambar.value : this.gambar,
    penulis: penulis.present ? penulis.value : this.penulis,
    kategori: kategori.present ? kategori.value : this.kategori,
    isPublished: isPublished ?? this.isPublished,
    createdAt: createdAt ?? this.createdAt,
  );
  InformasiPublikData copyWithCompanion(InformasiPublikCompanion data) {
    return InformasiPublikData(
      id: data.id.present ? data.id.value : this.id,
      judul: data.judul.present ? data.judul.value : this.judul,
      slug: data.slug.present ? data.slug.value : this.slug,
      konten: data.konten.present ? data.konten.value : this.konten,
      gambar: data.gambar.present ? data.gambar.value : this.gambar,
      penulis: data.penulis.present ? data.penulis.value : this.penulis,
      kategori: data.kategori.present ? data.kategori.value : this.kategori,
      isPublished: data.isPublished.present
          ? data.isPublished.value
          : this.isPublished,
      createdAt: data.createdAt.present ? data.createdAt.value : this.createdAt,
    );
  }

  @override
  String toString() {
    return (StringBuffer('InformasiPublikData(')
          ..write('id: $id, ')
          ..write('judul: $judul, ')
          ..write('slug: $slug, ')
          ..write('konten: $konten, ')
          ..write('gambar: $gambar, ')
          ..write('penulis: $penulis, ')
          ..write('kategori: $kategori, ')
          ..write('isPublished: $isPublished, ')
          ..write('createdAt: $createdAt')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(
    id,
    judul,
    slug,
    konten,
    gambar,
    penulis,
    kategori,
    isPublished,
    createdAt,
  );
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is InformasiPublikData &&
          other.id == this.id &&
          other.judul == this.judul &&
          other.slug == this.slug &&
          other.konten == this.konten &&
          other.gambar == this.gambar &&
          other.penulis == this.penulis &&
          other.kategori == this.kategori &&
          other.isPublished == this.isPublished &&
          other.createdAt == this.createdAt);
}

class InformasiPublikCompanion extends UpdateCompanion<InformasiPublikData> {
  final Value<int> id;
  final Value<String> judul;
  final Value<String> slug;
  final Value<String> konten;
  final Value<String?> gambar;
  final Value<String?> penulis;
  final Value<String?> kategori;
  final Value<bool> isPublished;
  final Value<DateTime> createdAt;
  const InformasiPublikCompanion({
    this.id = const Value.absent(),
    this.judul = const Value.absent(),
    this.slug = const Value.absent(),
    this.konten = const Value.absent(),
    this.gambar = const Value.absent(),
    this.penulis = const Value.absent(),
    this.kategori = const Value.absent(),
    this.isPublished = const Value.absent(),
    this.createdAt = const Value.absent(),
  });
  InformasiPublikCompanion.insert({
    this.id = const Value.absent(),
    required String judul,
    required String slug,
    required String konten,
    this.gambar = const Value.absent(),
    this.penulis = const Value.absent(),
    this.kategori = const Value.absent(),
    this.isPublished = const Value.absent(),
    this.createdAt = const Value.absent(),
  }) : judul = Value(judul),
       slug = Value(slug),
       konten = Value(konten);
  static Insertable<InformasiPublikData> custom({
    Expression<int>? id,
    Expression<String>? judul,
    Expression<String>? slug,
    Expression<String>? konten,
    Expression<String>? gambar,
    Expression<String>? penulis,
    Expression<String>? kategori,
    Expression<bool>? isPublished,
    Expression<DateTime>? createdAt,
  }) {
    return RawValuesInsertable({
      if (id != null) 'id': id,
      if (judul != null) 'judul': judul,
      if (slug != null) 'slug': slug,
      if (konten != null) 'konten': konten,
      if (gambar != null) 'gambar': gambar,
      if (penulis != null) 'penulis': penulis,
      if (kategori != null) 'kategori': kategori,
      if (isPublished != null) 'is_published': isPublished,
      if (createdAt != null) 'created_at': createdAt,
    });
  }

  InformasiPublikCompanion copyWith({
    Value<int>? id,
    Value<String>? judul,
    Value<String>? slug,
    Value<String>? konten,
    Value<String?>? gambar,
    Value<String?>? penulis,
    Value<String?>? kategori,
    Value<bool>? isPublished,
    Value<DateTime>? createdAt,
  }) {
    return InformasiPublikCompanion(
      id: id ?? this.id,
      judul: judul ?? this.judul,
      slug: slug ?? this.slug,
      konten: konten ?? this.konten,
      gambar: gambar ?? this.gambar,
      penulis: penulis ?? this.penulis,
      kategori: kategori ?? this.kategori,
      isPublished: isPublished ?? this.isPublished,
      createdAt: createdAt ?? this.createdAt,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (id.present) {
      map['id'] = Variable<int>(id.value);
    }
    if (judul.present) {
      map['judul'] = Variable<String>(judul.value);
    }
    if (slug.present) {
      map['slug'] = Variable<String>(slug.value);
    }
    if (konten.present) {
      map['konten'] = Variable<String>(konten.value);
    }
    if (gambar.present) {
      map['gambar'] = Variable<String>(gambar.value);
    }
    if (penulis.present) {
      map['penulis'] = Variable<String>(penulis.value);
    }
    if (kategori.present) {
      map['kategori'] = Variable<String>(kategori.value);
    }
    if (isPublished.present) {
      map['is_published'] = Variable<bool>(isPublished.value);
    }
    if (createdAt.present) {
      map['created_at'] = Variable<DateTime>(createdAt.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('InformasiPublikCompanion(')
          ..write('id: $id, ')
          ..write('judul: $judul, ')
          ..write('slug: $slug, ')
          ..write('konten: $konten, ')
          ..write('gambar: $gambar, ')
          ..write('penulis: $penulis, ')
          ..write('kategori: $kategori, ')
          ..write('isPublished: $isPublished, ')
          ..write('createdAt: $createdAt')
          ..write(')'))
        .toString();
  }
}

class $PendudukProfileTable extends PendudukProfile
    with TableInfo<$PendudukProfileTable, PendudukProfileData> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $PendudukProfileTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _idMeta = const VerificationMeta('id');
  @override
  late final GeneratedColumn<int> id = GeneratedColumn<int>(
    'id',
    aliasedName,
    false,
    hasAutoIncrement: true,
    type: DriftSqlType.int,
    requiredDuringInsert: false,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'PRIMARY KEY AUTOINCREMENT',
    ),
  );
  static const VerificationMeta _nikMeta = const VerificationMeta('nik');
  @override
  late final GeneratedColumn<String> nik = GeneratedColumn<String>(
    'nik',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _namaLengkapMeta = const VerificationMeta(
    'namaLengkap',
  );
  @override
  late final GeneratedColumn<String> namaLengkap = GeneratedColumn<String>(
    'nama_lengkap',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _tempatLahirMeta = const VerificationMeta(
    'tempatLahir',
  );
  @override
  late final GeneratedColumn<String> tempatLahir = GeneratedColumn<String>(
    'tempat_lahir',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _tanggalLahirMeta = const VerificationMeta(
    'tanggalLahir',
  );
  @override
  late final GeneratedColumn<DateTime> tanggalLahir = GeneratedColumn<DateTime>(
    'tanggal_lahir',
    aliasedName,
    true,
    type: DriftSqlType.dateTime,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _jenisKelaminMeta = const VerificationMeta(
    'jenisKelamin',
  );
  @override
  late final GeneratedColumn<String> jenisKelamin = GeneratedColumn<String>(
    'jenis_kelamin',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _agamaMeta = const VerificationMeta('agama');
  @override
  late final GeneratedColumn<String> agama = GeneratedColumn<String>(
    'agama',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _noKkMeta = const VerificationMeta('noKk');
  @override
  late final GeneratedColumn<String> noKk = GeneratedColumn<String>(
    'no_kk',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _fotoProfilMeta = const VerificationMeta(
    'fotoProfil',
  );
  @override
  late final GeneratedColumn<String> fotoProfil = GeneratedColumn<String>(
    'foto_profil',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _telegramChatIdMeta = const VerificationMeta(
    'telegramChatId',
  );
  @override
  late final GeneratedColumn<String> telegramChatId = GeneratedColumn<String>(
    'telegram_chat_id',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  @override
  List<GeneratedColumn> get $columns => [
    id,
    nik,
    namaLengkap,
    tempatLahir,
    tanggalLahir,
    jenisKelamin,
    agama,
    noKk,
    fotoProfil,
    telegramChatId,
  ];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'penduduk_profile';
  @override
  VerificationContext validateIntegrity(
    Insertable<PendudukProfileData> instance, {
    bool isInserting = false,
  }) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('id')) {
      context.handle(_idMeta, id.isAcceptableOrUnknown(data['id']!, _idMeta));
    }
    if (data.containsKey('nik')) {
      context.handle(
        _nikMeta,
        nik.isAcceptableOrUnknown(data['nik']!, _nikMeta),
      );
    } else if (isInserting) {
      context.missing(_nikMeta);
    }
    if (data.containsKey('nama_lengkap')) {
      context.handle(
        _namaLengkapMeta,
        namaLengkap.isAcceptableOrUnknown(
          data['nama_lengkap']!,
          _namaLengkapMeta,
        ),
      );
    } else if (isInserting) {
      context.missing(_namaLengkapMeta);
    }
    if (data.containsKey('tempat_lahir')) {
      context.handle(
        _tempatLahirMeta,
        tempatLahir.isAcceptableOrUnknown(
          data['tempat_lahir']!,
          _tempatLahirMeta,
        ),
      );
    }
    if (data.containsKey('tanggal_lahir')) {
      context.handle(
        _tanggalLahirMeta,
        tanggalLahir.isAcceptableOrUnknown(
          data['tanggal_lahir']!,
          _tanggalLahirMeta,
        ),
      );
    }
    if (data.containsKey('jenis_kelamin')) {
      context.handle(
        _jenisKelaminMeta,
        jenisKelamin.isAcceptableOrUnknown(
          data['jenis_kelamin']!,
          _jenisKelaminMeta,
        ),
      );
    } else if (isInserting) {
      context.missing(_jenisKelaminMeta);
    }
    if (data.containsKey('agama')) {
      context.handle(
        _agamaMeta,
        agama.isAcceptableOrUnknown(data['agama']!, _agamaMeta),
      );
    }
    if (data.containsKey('no_kk')) {
      context.handle(
        _noKkMeta,
        noKk.isAcceptableOrUnknown(data['no_kk']!, _noKkMeta),
      );
    } else if (isInserting) {
      context.missing(_noKkMeta);
    }
    if (data.containsKey('foto_profil')) {
      context.handle(
        _fotoProfilMeta,
        fotoProfil.isAcceptableOrUnknown(data['foto_profil']!, _fotoProfilMeta),
      );
    }
    if (data.containsKey('telegram_chat_id')) {
      context.handle(
        _telegramChatIdMeta,
        telegramChatId.isAcceptableOrUnknown(
          data['telegram_chat_id']!,
          _telegramChatIdMeta,
        ),
      );
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {id};
  @override
  PendudukProfileData map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return PendudukProfileData(
      id: attachedDatabase.typeMapping.read(
        DriftSqlType.int,
        data['${effectivePrefix}id'],
      )!,
      nik: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}nik'],
      )!,
      namaLengkap: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}nama_lengkap'],
      )!,
      tempatLahir: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}tempat_lahir'],
      ),
      tanggalLahir: attachedDatabase.typeMapping.read(
        DriftSqlType.dateTime,
        data['${effectivePrefix}tanggal_lahir'],
      ),
      jenisKelamin: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}jenis_kelamin'],
      )!,
      agama: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}agama'],
      ),
      noKk: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}no_kk'],
      )!,
      fotoProfil: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}foto_profil'],
      ),
      telegramChatId: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}telegram_chat_id'],
      ),
    );
  }

  @override
  $PendudukProfileTable createAlias(String alias) {
    return $PendudukProfileTable(attachedDatabase, alias);
  }
}

class PendudukProfileData extends DataClass
    implements Insertable<PendudukProfileData> {
  final int id;
  final String nik;
  final String namaLengkap;
  final String? tempatLahir;
  final DateTime? tanggalLahir;
  final String jenisKelamin;
  final String? agama;
  final String noKk;
  final String? fotoProfil;
  final String? telegramChatId;
  const PendudukProfileData({
    required this.id,
    required this.nik,
    required this.namaLengkap,
    this.tempatLahir,
    this.tanggalLahir,
    required this.jenisKelamin,
    this.agama,
    required this.noKk,
    this.fotoProfil,
    this.telegramChatId,
  });
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['id'] = Variable<int>(id);
    map['nik'] = Variable<String>(nik);
    map['nama_lengkap'] = Variable<String>(namaLengkap);
    if (!nullToAbsent || tempatLahir != null) {
      map['tempat_lahir'] = Variable<String>(tempatLahir);
    }
    if (!nullToAbsent || tanggalLahir != null) {
      map['tanggal_lahir'] = Variable<DateTime>(tanggalLahir);
    }
    map['jenis_kelamin'] = Variable<String>(jenisKelamin);
    if (!nullToAbsent || agama != null) {
      map['agama'] = Variable<String>(agama);
    }
    map['no_kk'] = Variable<String>(noKk);
    if (!nullToAbsent || fotoProfil != null) {
      map['foto_profil'] = Variable<String>(fotoProfil);
    }
    if (!nullToAbsent || telegramChatId != null) {
      map['telegram_chat_id'] = Variable<String>(telegramChatId);
    }
    return map;
  }

  PendudukProfileCompanion toCompanion(bool nullToAbsent) {
    return PendudukProfileCompanion(
      id: Value(id),
      nik: Value(nik),
      namaLengkap: Value(namaLengkap),
      tempatLahir: tempatLahir == null && nullToAbsent
          ? const Value.absent()
          : Value(tempatLahir),
      tanggalLahir: tanggalLahir == null && nullToAbsent
          ? const Value.absent()
          : Value(tanggalLahir),
      jenisKelamin: Value(jenisKelamin),
      agama: agama == null && nullToAbsent
          ? const Value.absent()
          : Value(agama),
      noKk: Value(noKk),
      fotoProfil: fotoProfil == null && nullToAbsent
          ? const Value.absent()
          : Value(fotoProfil),
      telegramChatId: telegramChatId == null && nullToAbsent
          ? const Value.absent()
          : Value(telegramChatId),
    );
  }

  factory PendudukProfileData.fromJson(
    Map<String, dynamic> json, {
    ValueSerializer? serializer,
  }) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return PendudukProfileData(
      id: serializer.fromJson<int>(json['id']),
      nik: serializer.fromJson<String>(json['nik']),
      namaLengkap: serializer.fromJson<String>(json['namaLengkap']),
      tempatLahir: serializer.fromJson<String?>(json['tempatLahir']),
      tanggalLahir: serializer.fromJson<DateTime?>(json['tanggalLahir']),
      jenisKelamin: serializer.fromJson<String>(json['jenisKelamin']),
      agama: serializer.fromJson<String?>(json['agama']),
      noKk: serializer.fromJson<String>(json['noKk']),
      fotoProfil: serializer.fromJson<String?>(json['fotoProfil']),
      telegramChatId: serializer.fromJson<String?>(json['telegramChatId']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'id': serializer.toJson<int>(id),
      'nik': serializer.toJson<String>(nik),
      'namaLengkap': serializer.toJson<String>(namaLengkap),
      'tempatLahir': serializer.toJson<String?>(tempatLahir),
      'tanggalLahir': serializer.toJson<DateTime?>(tanggalLahir),
      'jenisKelamin': serializer.toJson<String>(jenisKelamin),
      'agama': serializer.toJson<String?>(agama),
      'noKk': serializer.toJson<String>(noKk),
      'fotoProfil': serializer.toJson<String?>(fotoProfil),
      'telegramChatId': serializer.toJson<String?>(telegramChatId),
    };
  }

  PendudukProfileData copyWith({
    int? id,
    String? nik,
    String? namaLengkap,
    Value<String?> tempatLahir = const Value.absent(),
    Value<DateTime?> tanggalLahir = const Value.absent(),
    String? jenisKelamin,
    Value<String?> agama = const Value.absent(),
    String? noKk,
    Value<String?> fotoProfil = const Value.absent(),
    Value<String?> telegramChatId = const Value.absent(),
  }) => PendudukProfileData(
    id: id ?? this.id,
    nik: nik ?? this.nik,
    namaLengkap: namaLengkap ?? this.namaLengkap,
    tempatLahir: tempatLahir.present ? tempatLahir.value : this.tempatLahir,
    tanggalLahir: tanggalLahir.present ? tanggalLahir.value : this.tanggalLahir,
    jenisKelamin: jenisKelamin ?? this.jenisKelamin,
    agama: agama.present ? agama.value : this.agama,
    noKk: noKk ?? this.noKk,
    fotoProfil: fotoProfil.present ? fotoProfil.value : this.fotoProfil,
    telegramChatId: telegramChatId.present
        ? telegramChatId.value
        : this.telegramChatId,
  );
  PendudukProfileData copyWithCompanion(PendudukProfileCompanion data) {
    return PendudukProfileData(
      id: data.id.present ? data.id.value : this.id,
      nik: data.nik.present ? data.nik.value : this.nik,
      namaLengkap: data.namaLengkap.present
          ? data.namaLengkap.value
          : this.namaLengkap,
      tempatLahir: data.tempatLahir.present
          ? data.tempatLahir.value
          : this.tempatLahir,
      tanggalLahir: data.tanggalLahir.present
          ? data.tanggalLahir.value
          : this.tanggalLahir,
      jenisKelamin: data.jenisKelamin.present
          ? data.jenisKelamin.value
          : this.jenisKelamin,
      agama: data.agama.present ? data.agama.value : this.agama,
      noKk: data.noKk.present ? data.noKk.value : this.noKk,
      fotoProfil: data.fotoProfil.present
          ? data.fotoProfil.value
          : this.fotoProfil,
      telegramChatId: data.telegramChatId.present
          ? data.telegramChatId.value
          : this.telegramChatId,
    );
  }

  @override
  String toString() {
    return (StringBuffer('PendudukProfileData(')
          ..write('id: $id, ')
          ..write('nik: $nik, ')
          ..write('namaLengkap: $namaLengkap, ')
          ..write('tempatLahir: $tempatLahir, ')
          ..write('tanggalLahir: $tanggalLahir, ')
          ..write('jenisKelamin: $jenisKelamin, ')
          ..write('agama: $agama, ')
          ..write('noKk: $noKk, ')
          ..write('fotoProfil: $fotoProfil, ')
          ..write('telegramChatId: $telegramChatId')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(
    id,
    nik,
    namaLengkap,
    tempatLahir,
    tanggalLahir,
    jenisKelamin,
    agama,
    noKk,
    fotoProfil,
    telegramChatId,
  );
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is PendudukProfileData &&
          other.id == this.id &&
          other.nik == this.nik &&
          other.namaLengkap == this.namaLengkap &&
          other.tempatLahir == this.tempatLahir &&
          other.tanggalLahir == this.tanggalLahir &&
          other.jenisKelamin == this.jenisKelamin &&
          other.agama == this.agama &&
          other.noKk == this.noKk &&
          other.fotoProfil == this.fotoProfil &&
          other.telegramChatId == this.telegramChatId);
}

class PendudukProfileCompanion extends UpdateCompanion<PendudukProfileData> {
  final Value<int> id;
  final Value<String> nik;
  final Value<String> namaLengkap;
  final Value<String?> tempatLahir;
  final Value<DateTime?> tanggalLahir;
  final Value<String> jenisKelamin;
  final Value<String?> agama;
  final Value<String> noKk;
  final Value<String?> fotoProfil;
  final Value<String?> telegramChatId;
  const PendudukProfileCompanion({
    this.id = const Value.absent(),
    this.nik = const Value.absent(),
    this.namaLengkap = const Value.absent(),
    this.tempatLahir = const Value.absent(),
    this.tanggalLahir = const Value.absent(),
    this.jenisKelamin = const Value.absent(),
    this.agama = const Value.absent(),
    this.noKk = const Value.absent(),
    this.fotoProfil = const Value.absent(),
    this.telegramChatId = const Value.absent(),
  });
  PendudukProfileCompanion.insert({
    this.id = const Value.absent(),
    required String nik,
    required String namaLengkap,
    this.tempatLahir = const Value.absent(),
    this.tanggalLahir = const Value.absent(),
    required String jenisKelamin,
    this.agama = const Value.absent(),
    required String noKk,
    this.fotoProfil = const Value.absent(),
    this.telegramChatId = const Value.absent(),
  }) : nik = Value(nik),
       namaLengkap = Value(namaLengkap),
       jenisKelamin = Value(jenisKelamin),
       noKk = Value(noKk);
  static Insertable<PendudukProfileData> custom({
    Expression<int>? id,
    Expression<String>? nik,
    Expression<String>? namaLengkap,
    Expression<String>? tempatLahir,
    Expression<DateTime>? tanggalLahir,
    Expression<String>? jenisKelamin,
    Expression<String>? agama,
    Expression<String>? noKk,
    Expression<String>? fotoProfil,
    Expression<String>? telegramChatId,
  }) {
    return RawValuesInsertable({
      if (id != null) 'id': id,
      if (nik != null) 'nik': nik,
      if (namaLengkap != null) 'nama_lengkap': namaLengkap,
      if (tempatLahir != null) 'tempat_lahir': tempatLahir,
      if (tanggalLahir != null) 'tanggal_lahir': tanggalLahir,
      if (jenisKelamin != null) 'jenis_kelamin': jenisKelamin,
      if (agama != null) 'agama': agama,
      if (noKk != null) 'no_kk': noKk,
      if (fotoProfil != null) 'foto_profil': fotoProfil,
      if (telegramChatId != null) 'telegram_chat_id': telegramChatId,
    });
  }

  PendudukProfileCompanion copyWith({
    Value<int>? id,
    Value<String>? nik,
    Value<String>? namaLengkap,
    Value<String?>? tempatLahir,
    Value<DateTime?>? tanggalLahir,
    Value<String>? jenisKelamin,
    Value<String?>? agama,
    Value<String>? noKk,
    Value<String?>? fotoProfil,
    Value<String?>? telegramChatId,
  }) {
    return PendudukProfileCompanion(
      id: id ?? this.id,
      nik: nik ?? this.nik,
      namaLengkap: namaLengkap ?? this.namaLengkap,
      tempatLahir: tempatLahir ?? this.tempatLahir,
      tanggalLahir: tanggalLahir ?? this.tanggalLahir,
      jenisKelamin: jenisKelamin ?? this.jenisKelamin,
      agama: agama ?? this.agama,
      noKk: noKk ?? this.noKk,
      fotoProfil: fotoProfil ?? this.fotoProfil,
      telegramChatId: telegramChatId ?? this.telegramChatId,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (id.present) {
      map['id'] = Variable<int>(id.value);
    }
    if (nik.present) {
      map['nik'] = Variable<String>(nik.value);
    }
    if (namaLengkap.present) {
      map['nama_lengkap'] = Variable<String>(namaLengkap.value);
    }
    if (tempatLahir.present) {
      map['tempat_lahir'] = Variable<String>(tempatLahir.value);
    }
    if (tanggalLahir.present) {
      map['tanggal_lahir'] = Variable<DateTime>(tanggalLahir.value);
    }
    if (jenisKelamin.present) {
      map['jenis_kelamin'] = Variable<String>(jenisKelamin.value);
    }
    if (agama.present) {
      map['agama'] = Variable<String>(agama.value);
    }
    if (noKk.present) {
      map['no_kk'] = Variable<String>(noKk.value);
    }
    if (fotoProfil.present) {
      map['foto_profil'] = Variable<String>(fotoProfil.value);
    }
    if (telegramChatId.present) {
      map['telegram_chat_id'] = Variable<String>(telegramChatId.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('PendudukProfileCompanion(')
          ..write('id: $id, ')
          ..write('nik: $nik, ')
          ..write('namaLengkap: $namaLengkap, ')
          ..write('tempatLahir: $tempatLahir, ')
          ..write('tanggalLahir: $tanggalLahir, ')
          ..write('jenisKelamin: $jenisKelamin, ')
          ..write('agama: $agama, ')
          ..write('noKk: $noKk, ')
          ..write('fotoProfil: $fotoProfil, ')
          ..write('telegramChatId: $telegramChatId')
          ..write(')'))
        .toString();
  }
}

class $FasilitasDesaTable extends FasilitasDesa
    with TableInfo<$FasilitasDesaTable, FasilitasDesaData> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $FasilitasDesaTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _idMeta = const VerificationMeta('id');
  @override
  late final GeneratedColumn<int> id = GeneratedColumn<int>(
    'id',
    aliasedName,
    false,
    hasAutoIncrement: true,
    type: DriftSqlType.int,
    requiredDuringInsert: false,
    defaultConstraints: GeneratedColumn.constraintIsAlways(
      'PRIMARY KEY AUTOINCREMENT',
    ),
  );
  static const VerificationMeta _namaMeta = const VerificationMeta('nama');
  @override
  late final GeneratedColumn<String> nama = GeneratedColumn<String>(
    'nama',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _jenisFasilitasMeta = const VerificationMeta(
    'jenisFasilitas',
  );
  @override
  late final GeneratedColumn<String> jenisFasilitas = GeneratedColumn<String>(
    'jenis_fasilitas',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _alamatMeta = const VerificationMeta('alamat');
  @override
  late final GeneratedColumn<String> alamat = GeneratedColumn<String>(
    'alamat',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _kondisiMeta = const VerificationMeta(
    'kondisi',
  );
  @override
  late final GeneratedColumn<String> kondisi = GeneratedColumn<String>(
    'kondisi',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  @override
  List<GeneratedColumn> get $columns => [
    id,
    nama,
    jenisFasilitas,
    alamat,
    kondisi,
  ];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'fasilitas_desa';
  @override
  VerificationContext validateIntegrity(
    Insertable<FasilitasDesaData> instance, {
    bool isInserting = false,
  }) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('id')) {
      context.handle(_idMeta, id.isAcceptableOrUnknown(data['id']!, _idMeta));
    }
    if (data.containsKey('nama')) {
      context.handle(
        _namaMeta,
        nama.isAcceptableOrUnknown(data['nama']!, _namaMeta),
      );
    } else if (isInserting) {
      context.missing(_namaMeta);
    }
    if (data.containsKey('jenis_fasilitas')) {
      context.handle(
        _jenisFasilitasMeta,
        jenisFasilitas.isAcceptableOrUnknown(
          data['jenis_fasilitas']!,
          _jenisFasilitasMeta,
        ),
      );
    } else if (isInserting) {
      context.missing(_jenisFasilitasMeta);
    }
    if (data.containsKey('alamat')) {
      context.handle(
        _alamatMeta,
        alamat.isAcceptableOrUnknown(data['alamat']!, _alamatMeta),
      );
    }
    if (data.containsKey('kondisi')) {
      context.handle(
        _kondisiMeta,
        kondisi.isAcceptableOrUnknown(data['kondisi']!, _kondisiMeta),
      );
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {id};
  @override
  FasilitasDesaData map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return FasilitasDesaData(
      id: attachedDatabase.typeMapping.read(
        DriftSqlType.int,
        data['${effectivePrefix}id'],
      )!,
      nama: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}nama'],
      )!,
      jenisFasilitas: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}jenis_fasilitas'],
      )!,
      alamat: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}alamat'],
      ),
      kondisi: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}kondisi'],
      ),
    );
  }

  @override
  $FasilitasDesaTable createAlias(String alias) {
    return $FasilitasDesaTable(attachedDatabase, alias);
  }
}

class FasilitasDesaData extends DataClass
    implements Insertable<FasilitasDesaData> {
  final int id;
  final String nama;
  final String jenisFasilitas;
  final String? alamat;
  final String? kondisi;
  const FasilitasDesaData({
    required this.id,
    required this.nama,
    required this.jenisFasilitas,
    this.alamat,
    this.kondisi,
  });
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['id'] = Variable<int>(id);
    map['nama'] = Variable<String>(nama);
    map['jenis_fasilitas'] = Variable<String>(jenisFasilitas);
    if (!nullToAbsent || alamat != null) {
      map['alamat'] = Variable<String>(alamat);
    }
    if (!nullToAbsent || kondisi != null) {
      map['kondisi'] = Variable<String>(kondisi);
    }
    return map;
  }

  FasilitasDesaCompanion toCompanion(bool nullToAbsent) {
    return FasilitasDesaCompanion(
      id: Value(id),
      nama: Value(nama),
      jenisFasilitas: Value(jenisFasilitas),
      alamat: alamat == null && nullToAbsent
          ? const Value.absent()
          : Value(alamat),
      kondisi: kondisi == null && nullToAbsent
          ? const Value.absent()
          : Value(kondisi),
    );
  }

  factory FasilitasDesaData.fromJson(
    Map<String, dynamic> json, {
    ValueSerializer? serializer,
  }) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return FasilitasDesaData(
      id: serializer.fromJson<int>(json['id']),
      nama: serializer.fromJson<String>(json['nama']),
      jenisFasilitas: serializer.fromJson<String>(json['jenisFasilitas']),
      alamat: serializer.fromJson<String?>(json['alamat']),
      kondisi: serializer.fromJson<String?>(json['kondisi']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'id': serializer.toJson<int>(id),
      'nama': serializer.toJson<String>(nama),
      'jenisFasilitas': serializer.toJson<String>(jenisFasilitas),
      'alamat': serializer.toJson<String?>(alamat),
      'kondisi': serializer.toJson<String?>(kondisi),
    };
  }

  FasilitasDesaData copyWith({
    int? id,
    String? nama,
    String? jenisFasilitas,
    Value<String?> alamat = const Value.absent(),
    Value<String?> kondisi = const Value.absent(),
  }) => FasilitasDesaData(
    id: id ?? this.id,
    nama: nama ?? this.nama,
    jenisFasilitas: jenisFasilitas ?? this.jenisFasilitas,
    alamat: alamat.present ? alamat.value : this.alamat,
    kondisi: kondisi.present ? kondisi.value : this.kondisi,
  );
  FasilitasDesaData copyWithCompanion(FasilitasDesaCompanion data) {
    return FasilitasDesaData(
      id: data.id.present ? data.id.value : this.id,
      nama: data.nama.present ? data.nama.value : this.nama,
      jenisFasilitas: data.jenisFasilitas.present
          ? data.jenisFasilitas.value
          : this.jenisFasilitas,
      alamat: data.alamat.present ? data.alamat.value : this.alamat,
      kondisi: data.kondisi.present ? data.kondisi.value : this.kondisi,
    );
  }

  @override
  String toString() {
    return (StringBuffer('FasilitasDesaData(')
          ..write('id: $id, ')
          ..write('nama: $nama, ')
          ..write('jenisFasilitas: $jenisFasilitas, ')
          ..write('alamat: $alamat, ')
          ..write('kondisi: $kondisi')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(id, nama, jenisFasilitas, alamat, kondisi);
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is FasilitasDesaData &&
          other.id == this.id &&
          other.nama == this.nama &&
          other.jenisFasilitas == this.jenisFasilitas &&
          other.alamat == this.alamat &&
          other.kondisi == this.kondisi);
}

class FasilitasDesaCompanion extends UpdateCompanion<FasilitasDesaData> {
  final Value<int> id;
  final Value<String> nama;
  final Value<String> jenisFasilitas;
  final Value<String?> alamat;
  final Value<String?> kondisi;
  const FasilitasDesaCompanion({
    this.id = const Value.absent(),
    this.nama = const Value.absent(),
    this.jenisFasilitas = const Value.absent(),
    this.alamat = const Value.absent(),
    this.kondisi = const Value.absent(),
  });
  FasilitasDesaCompanion.insert({
    this.id = const Value.absent(),
    required String nama,
    required String jenisFasilitas,
    this.alamat = const Value.absent(),
    this.kondisi = const Value.absent(),
  }) : nama = Value(nama),
       jenisFasilitas = Value(jenisFasilitas);
  static Insertable<FasilitasDesaData> custom({
    Expression<int>? id,
    Expression<String>? nama,
    Expression<String>? jenisFasilitas,
    Expression<String>? alamat,
    Expression<String>? kondisi,
  }) {
    return RawValuesInsertable({
      if (id != null) 'id': id,
      if (nama != null) 'nama': nama,
      if (jenisFasilitas != null) 'jenis_fasilitas': jenisFasilitas,
      if (alamat != null) 'alamat': alamat,
      if (kondisi != null) 'kondisi': kondisi,
    });
  }

  FasilitasDesaCompanion copyWith({
    Value<int>? id,
    Value<String>? nama,
    Value<String>? jenisFasilitas,
    Value<String?>? alamat,
    Value<String?>? kondisi,
  }) {
    return FasilitasDesaCompanion(
      id: id ?? this.id,
      nama: nama ?? this.nama,
      jenisFasilitas: jenisFasilitas ?? this.jenisFasilitas,
      alamat: alamat ?? this.alamat,
      kondisi: kondisi ?? this.kondisi,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (id.present) {
      map['id'] = Variable<int>(id.value);
    }
    if (nama.present) {
      map['nama'] = Variable<String>(nama.value);
    }
    if (jenisFasilitas.present) {
      map['jenis_fasilitas'] = Variable<String>(jenisFasilitas.value);
    }
    if (alamat.present) {
      map['alamat'] = Variable<String>(alamat.value);
    }
    if (kondisi.present) {
      map['kondisi'] = Variable<String>(kondisi.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('FasilitasDesaCompanion(')
          ..write('id: $id, ')
          ..write('nama: $nama, ')
          ..write('jenisFasilitas: $jenisFasilitas, ')
          ..write('alamat: $alamat, ')
          ..write('kondisi: $kondisi')
          ..write(')'))
        .toString();
  }
}

class $SyncQueueTable extends SyncQueue
    with TableInfo<$SyncQueueTable, SyncQueueData> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $SyncQueueTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _clientIdMeta = const VerificationMeta(
    'clientId',
  );
  @override
  late final GeneratedColumn<String> clientId = GeneratedColumn<String>(
    'client_id',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _typeMeta = const VerificationMeta('type');
  @override
  late final GeneratedColumn<String> type = GeneratedColumn<String>(
    'type',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _actionMeta = const VerificationMeta('action');
  @override
  late final GeneratedColumn<String> action = GeneratedColumn<String>(
    'action',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _dataMeta = const VerificationMeta('data');
  @override
  late final GeneratedColumn<String> data = GeneratedColumn<String>(
    'data',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _createdAtMeta = const VerificationMeta(
    'createdAt',
  );
  @override
  late final GeneratedColumn<DateTime> createdAt = GeneratedColumn<DateTime>(
    'created_at',
    aliasedName,
    false,
    type: DriftSqlType.dateTime,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _statusMeta = const VerificationMeta('status');
  @override
  late final GeneratedColumn<String> status = GeneratedColumn<String>(
    'status',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
    defaultValue: const Constant('pending'),
  );
  static const VerificationMeta _errorMeta = const VerificationMeta('error');
  @override
  late final GeneratedColumn<String> error = GeneratedColumn<String>(
    'error',
    aliasedName,
    true,
    type: DriftSqlType.string,
    requiredDuringInsert: false,
  );
  static const VerificationMeta _serverIdMeta = const VerificationMeta(
    'serverId',
  );
  @override
  late final GeneratedColumn<int> serverId = GeneratedColumn<int>(
    'server_id',
    aliasedName,
    true,
    type: DriftSqlType.int,
    requiredDuringInsert: false,
  );
  @override
  List<GeneratedColumn> get $columns => [
    clientId,
    type,
    action,
    data,
    createdAt,
    status,
    error,
    serverId,
  ];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'sync_queue';
  @override
  VerificationContext validateIntegrity(
    Insertable<SyncQueueData> instance, {
    bool isInserting = false,
  }) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('client_id')) {
      context.handle(
        _clientIdMeta,
        clientId.isAcceptableOrUnknown(data['client_id']!, _clientIdMeta),
      );
    } else if (isInserting) {
      context.missing(_clientIdMeta);
    }
    if (data.containsKey('type')) {
      context.handle(
        _typeMeta,
        type.isAcceptableOrUnknown(data['type']!, _typeMeta),
      );
    } else if (isInserting) {
      context.missing(_typeMeta);
    }
    if (data.containsKey('action')) {
      context.handle(
        _actionMeta,
        action.isAcceptableOrUnknown(data['action']!, _actionMeta),
      );
    } else if (isInserting) {
      context.missing(_actionMeta);
    }
    if (data.containsKey('data')) {
      context.handle(
        _dataMeta,
        this.data.isAcceptableOrUnknown(data['data']!, _dataMeta),
      );
    } else if (isInserting) {
      context.missing(_dataMeta);
    }
    if (data.containsKey('created_at')) {
      context.handle(
        _createdAtMeta,
        createdAt.isAcceptableOrUnknown(data['created_at']!, _createdAtMeta),
      );
    } else if (isInserting) {
      context.missing(_createdAtMeta);
    }
    if (data.containsKey('status')) {
      context.handle(
        _statusMeta,
        status.isAcceptableOrUnknown(data['status']!, _statusMeta),
      );
    }
    if (data.containsKey('error')) {
      context.handle(
        _errorMeta,
        error.isAcceptableOrUnknown(data['error']!, _errorMeta),
      );
    }
    if (data.containsKey('server_id')) {
      context.handle(
        _serverIdMeta,
        serverId.isAcceptableOrUnknown(data['server_id']!, _serverIdMeta),
      );
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {clientId};
  @override
  SyncQueueData map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return SyncQueueData(
      clientId: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}client_id'],
      )!,
      type: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}type'],
      )!,
      action: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}action'],
      )!,
      data: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}data'],
      )!,
      createdAt: attachedDatabase.typeMapping.read(
        DriftSqlType.dateTime,
        data['${effectivePrefix}created_at'],
      )!,
      status: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}status'],
      )!,
      error: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}error'],
      ),
      serverId: attachedDatabase.typeMapping.read(
        DriftSqlType.int,
        data['${effectivePrefix}server_id'],
      ),
    );
  }

  @override
  $SyncQueueTable createAlias(String alias) {
    return $SyncQueueTable(attachedDatabase, alias);
  }
}

class SyncQueueData extends DataClass implements Insertable<SyncQueueData> {
  final String clientId;
  final String type;
  final String action;
  final String data;
  final DateTime createdAt;
  final String status;
  final String? error;
  final int? serverId;
  const SyncQueueData({
    required this.clientId,
    required this.type,
    required this.action,
    required this.data,
    required this.createdAt,
    required this.status,
    this.error,
    this.serverId,
  });
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['client_id'] = Variable<String>(clientId);
    map['type'] = Variable<String>(type);
    map['action'] = Variable<String>(action);
    map['data'] = Variable<String>(data);
    map['created_at'] = Variable<DateTime>(createdAt);
    map['status'] = Variable<String>(status);
    if (!nullToAbsent || error != null) {
      map['error'] = Variable<String>(error);
    }
    if (!nullToAbsent || serverId != null) {
      map['server_id'] = Variable<int>(serverId);
    }
    return map;
  }

  SyncQueueCompanion toCompanion(bool nullToAbsent) {
    return SyncQueueCompanion(
      clientId: Value(clientId),
      type: Value(type),
      action: Value(action),
      data: Value(data),
      createdAt: Value(createdAt),
      status: Value(status),
      error: error == null && nullToAbsent
          ? const Value.absent()
          : Value(error),
      serverId: serverId == null && nullToAbsent
          ? const Value.absent()
          : Value(serverId),
    );
  }

  factory SyncQueueData.fromJson(
    Map<String, dynamic> json, {
    ValueSerializer? serializer,
  }) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return SyncQueueData(
      clientId: serializer.fromJson<String>(json['clientId']),
      type: serializer.fromJson<String>(json['type']),
      action: serializer.fromJson<String>(json['action']),
      data: serializer.fromJson<String>(json['data']),
      createdAt: serializer.fromJson<DateTime>(json['createdAt']),
      status: serializer.fromJson<String>(json['status']),
      error: serializer.fromJson<String?>(json['error']),
      serverId: serializer.fromJson<int?>(json['serverId']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'clientId': serializer.toJson<String>(clientId),
      'type': serializer.toJson<String>(type),
      'action': serializer.toJson<String>(action),
      'data': serializer.toJson<String>(data),
      'createdAt': serializer.toJson<DateTime>(createdAt),
      'status': serializer.toJson<String>(status),
      'error': serializer.toJson<String?>(error),
      'serverId': serializer.toJson<int?>(serverId),
    };
  }

  SyncQueueData copyWith({
    String? clientId,
    String? type,
    String? action,
    String? data,
    DateTime? createdAt,
    String? status,
    Value<String?> error = const Value.absent(),
    Value<int?> serverId = const Value.absent(),
  }) => SyncQueueData(
    clientId: clientId ?? this.clientId,
    type: type ?? this.type,
    action: action ?? this.action,
    data: data ?? this.data,
    createdAt: createdAt ?? this.createdAt,
    status: status ?? this.status,
    error: error.present ? error.value : this.error,
    serverId: serverId.present ? serverId.value : this.serverId,
  );
  SyncQueueData copyWithCompanion(SyncQueueCompanion data) {
    return SyncQueueData(
      clientId: data.clientId.present ? data.clientId.value : this.clientId,
      type: data.type.present ? data.type.value : this.type,
      action: data.action.present ? data.action.value : this.action,
      data: data.data.present ? data.data.value : this.data,
      createdAt: data.createdAt.present ? data.createdAt.value : this.createdAt,
      status: data.status.present ? data.status.value : this.status,
      error: data.error.present ? data.error.value : this.error,
      serverId: data.serverId.present ? data.serverId.value : this.serverId,
    );
  }

  @override
  String toString() {
    return (StringBuffer('SyncQueueData(')
          ..write('clientId: $clientId, ')
          ..write('type: $type, ')
          ..write('action: $action, ')
          ..write('data: $data, ')
          ..write('createdAt: $createdAt, ')
          ..write('status: $status, ')
          ..write('error: $error, ')
          ..write('serverId: $serverId')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(
    clientId,
    type,
    action,
    data,
    createdAt,
    status,
    error,
    serverId,
  );
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is SyncQueueData &&
          other.clientId == this.clientId &&
          other.type == this.type &&
          other.action == this.action &&
          other.data == this.data &&
          other.createdAt == this.createdAt &&
          other.status == this.status &&
          other.error == this.error &&
          other.serverId == this.serverId);
}

class SyncQueueCompanion extends UpdateCompanion<SyncQueueData> {
  final Value<String> clientId;
  final Value<String> type;
  final Value<String> action;
  final Value<String> data;
  final Value<DateTime> createdAt;
  final Value<String> status;
  final Value<String?> error;
  final Value<int?> serverId;
  final Value<int> rowid;
  const SyncQueueCompanion({
    this.clientId = const Value.absent(),
    this.type = const Value.absent(),
    this.action = const Value.absent(),
    this.data = const Value.absent(),
    this.createdAt = const Value.absent(),
    this.status = const Value.absent(),
    this.error = const Value.absent(),
    this.serverId = const Value.absent(),
    this.rowid = const Value.absent(),
  });
  SyncQueueCompanion.insert({
    required String clientId,
    required String type,
    required String action,
    required String data,
    required DateTime createdAt,
    this.status = const Value.absent(),
    this.error = const Value.absent(),
    this.serverId = const Value.absent(),
    this.rowid = const Value.absent(),
  }) : clientId = Value(clientId),
       type = Value(type),
       action = Value(action),
       data = Value(data),
       createdAt = Value(createdAt);
  static Insertable<SyncQueueData> custom({
    Expression<String>? clientId,
    Expression<String>? type,
    Expression<String>? action,
    Expression<String>? data,
    Expression<DateTime>? createdAt,
    Expression<String>? status,
    Expression<String>? error,
    Expression<int>? serverId,
    Expression<int>? rowid,
  }) {
    return RawValuesInsertable({
      if (clientId != null) 'client_id': clientId,
      if (type != null) 'type': type,
      if (action != null) 'action': action,
      if (data != null) 'data': data,
      if (createdAt != null) 'created_at': createdAt,
      if (status != null) 'status': status,
      if (error != null) 'error': error,
      if (serverId != null) 'server_id': serverId,
      if (rowid != null) 'rowid': rowid,
    });
  }

  SyncQueueCompanion copyWith({
    Value<String>? clientId,
    Value<String>? type,
    Value<String>? action,
    Value<String>? data,
    Value<DateTime>? createdAt,
    Value<String>? status,
    Value<String?>? error,
    Value<int?>? serverId,
    Value<int>? rowid,
  }) {
    return SyncQueueCompanion(
      clientId: clientId ?? this.clientId,
      type: type ?? this.type,
      action: action ?? this.action,
      data: data ?? this.data,
      createdAt: createdAt ?? this.createdAt,
      status: status ?? this.status,
      error: error ?? this.error,
      serverId: serverId ?? this.serverId,
      rowid: rowid ?? this.rowid,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (clientId.present) {
      map['client_id'] = Variable<String>(clientId.value);
    }
    if (type.present) {
      map['type'] = Variable<String>(type.value);
    }
    if (action.present) {
      map['action'] = Variable<String>(action.value);
    }
    if (data.present) {
      map['data'] = Variable<String>(data.value);
    }
    if (createdAt.present) {
      map['created_at'] = Variable<DateTime>(createdAt.value);
    }
    if (status.present) {
      map['status'] = Variable<String>(status.value);
    }
    if (error.present) {
      map['error'] = Variable<String>(error.value);
    }
    if (serverId.present) {
      map['server_id'] = Variable<int>(serverId.value);
    }
    if (rowid.present) {
      map['rowid'] = Variable<int>(rowid.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('SyncQueueCompanion(')
          ..write('clientId: $clientId, ')
          ..write('type: $type, ')
          ..write('action: $action, ')
          ..write('data: $data, ')
          ..write('createdAt: $createdAt, ')
          ..write('status: $status, ')
          ..write('error: $error, ')
          ..write('serverId: $serverId, ')
          ..write('rowid: $rowid')
          ..write(')'))
        .toString();
  }
}

class $SettingsTable extends Settings with TableInfo<$SettingsTable, Setting> {
  @override
  final GeneratedDatabase attachedDatabase;
  final String? _alias;
  $SettingsTable(this.attachedDatabase, [this._alias]);
  static const VerificationMeta _keyMeta = const VerificationMeta('key');
  @override
  late final GeneratedColumn<String> key = GeneratedColumn<String>(
    'key',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  static const VerificationMeta _valueMeta = const VerificationMeta('value');
  @override
  late final GeneratedColumn<String> value = GeneratedColumn<String>(
    'value',
    aliasedName,
    false,
    type: DriftSqlType.string,
    requiredDuringInsert: true,
  );
  @override
  List<GeneratedColumn> get $columns => [key, value];
  @override
  String get aliasedName => _alias ?? actualTableName;
  @override
  String get actualTableName => $name;
  static const String $name = 'settings';
  @override
  VerificationContext validateIntegrity(
    Insertable<Setting> instance, {
    bool isInserting = false,
  }) {
    final context = VerificationContext();
    final data = instance.toColumns(true);
    if (data.containsKey('key')) {
      context.handle(
        _keyMeta,
        key.isAcceptableOrUnknown(data['key']!, _keyMeta),
      );
    } else if (isInserting) {
      context.missing(_keyMeta);
    }
    if (data.containsKey('value')) {
      context.handle(
        _valueMeta,
        value.isAcceptableOrUnknown(data['value']!, _valueMeta),
      );
    } else if (isInserting) {
      context.missing(_valueMeta);
    }
    return context;
  }

  @override
  Set<GeneratedColumn> get $primaryKey => {key};
  @override
  Setting map(Map<String, dynamic> data, {String? tablePrefix}) {
    final effectivePrefix = tablePrefix != null ? '$tablePrefix.' : '';
    return Setting(
      key: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}key'],
      )!,
      value: attachedDatabase.typeMapping.read(
        DriftSqlType.string,
        data['${effectivePrefix}value'],
      )!,
    );
  }

  @override
  $SettingsTable createAlias(String alias) {
    return $SettingsTable(attachedDatabase, alias);
  }
}

class Setting extends DataClass implements Insertable<Setting> {
  final String key;
  final String value;
  const Setting({required this.key, required this.value});
  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    map['key'] = Variable<String>(key);
    map['value'] = Variable<String>(value);
    return map;
  }

  SettingsCompanion toCompanion(bool nullToAbsent) {
    return SettingsCompanion(key: Value(key), value: Value(value));
  }

  factory Setting.fromJson(
    Map<String, dynamic> json, {
    ValueSerializer? serializer,
  }) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return Setting(
      key: serializer.fromJson<String>(json['key']),
      value: serializer.fromJson<String>(json['value']),
    );
  }
  @override
  Map<String, dynamic> toJson({ValueSerializer? serializer}) {
    serializer ??= driftRuntimeOptions.defaultSerializer;
    return <String, dynamic>{
      'key': serializer.toJson<String>(key),
      'value': serializer.toJson<String>(value),
    };
  }

  Setting copyWith({String? key, String? value}) =>
      Setting(key: key ?? this.key, value: value ?? this.value);
  Setting copyWithCompanion(SettingsCompanion data) {
    return Setting(
      key: data.key.present ? data.key.value : this.key,
      value: data.value.present ? data.value.value : this.value,
    );
  }

  @override
  String toString() {
    return (StringBuffer('Setting(')
          ..write('key: $key, ')
          ..write('value: $value')
          ..write(')'))
        .toString();
  }

  @override
  int get hashCode => Object.hash(key, value);
  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      (other is Setting && other.key == this.key && other.value == this.value);
}

class SettingsCompanion extends UpdateCompanion<Setting> {
  final Value<String> key;
  final Value<String> value;
  final Value<int> rowid;
  const SettingsCompanion({
    this.key = const Value.absent(),
    this.value = const Value.absent(),
    this.rowid = const Value.absent(),
  });
  SettingsCompanion.insert({
    required String key,
    required String value,
    this.rowid = const Value.absent(),
  }) : key = Value(key),
       value = Value(value);
  static Insertable<Setting> custom({
    Expression<String>? key,
    Expression<String>? value,
    Expression<int>? rowid,
  }) {
    return RawValuesInsertable({
      if (key != null) 'key': key,
      if (value != null) 'value': value,
      if (rowid != null) 'rowid': rowid,
    });
  }

  SettingsCompanion copyWith({
    Value<String>? key,
    Value<String>? value,
    Value<int>? rowid,
  }) {
    return SettingsCompanion(
      key: key ?? this.key,
      value: value ?? this.value,
      rowid: rowid ?? this.rowid,
    );
  }

  @override
  Map<String, Expression> toColumns(bool nullToAbsent) {
    final map = <String, Expression>{};
    if (key.present) {
      map['key'] = Variable<String>(key.value);
    }
    if (value.present) {
      map['value'] = Variable<String>(value.value);
    }
    if (rowid.present) {
      map['rowid'] = Variable<int>(rowid.value);
    }
    return map;
  }

  @override
  String toString() {
    return (StringBuffer('SettingsCompanion(')
          ..write('key: $key, ')
          ..write('value: $value, ')
          ..write('rowid: $rowid')
          ..write(')'))
        .toString();
  }
}

abstract class _$AppDatabase extends GeneratedDatabase {
  _$AppDatabase(QueryExecutor e) : super(e);
  $AppDatabaseManager get managers => $AppDatabaseManager(this);
  late final $SuratKategoriTable suratKategori = $SuratKategoriTable(this);
  late final $SuratPengajuanTable suratPengajuan = $SuratPengajuanTable(this);
  late final $MutasiPendudukTable mutasiPenduduk = $MutasiPendudukTable(this);
  late final $InformasiPublikTable informasiPublik = $InformasiPublikTable(
    this,
  );
  late final $PendudukProfileTable pendudukProfile = $PendudukProfileTable(
    this,
  );
  late final $FasilitasDesaTable fasilitasDesa = $FasilitasDesaTable(this);
  late final $SyncQueueTable syncQueue = $SyncQueueTable(this);
  late final $SettingsTable settings = $SettingsTable(this);
  @override
  Iterable<TableInfo<Table, Object?>> get allTables =>
      allSchemaEntities.whereType<TableInfo<Table, Object?>>();
  @override
  List<DatabaseSchemaEntity> get allSchemaEntities => [
    suratKategori,
    suratPengajuan,
    mutasiPenduduk,
    informasiPublik,
    pendudukProfile,
    fasilitasDesa,
    syncQueue,
    settings,
  ];
}

typedef $$SuratKategoriTableCreateCompanionBuilder =
    SuratKategoriCompanion Function({
      Value<int> id,
      required String nama,
      Value<String?> deskripsi,
      Value<String?> icon,
      required String persyaratan,
    });
typedef $$SuratKategoriTableUpdateCompanionBuilder =
    SuratKategoriCompanion Function({
      Value<int> id,
      Value<String> nama,
      Value<String?> deskripsi,
      Value<String?> icon,
      Value<String> persyaratan,
    });

final class $$SuratKategoriTableReferences
    extends
        BaseReferences<_$AppDatabase, $SuratKategoriTable, SuratKategoriData> {
  $$SuratKategoriTableReferences(
    super.$_db,
    super.$_table,
    super.$_typedResult,
  );

  static MultiTypedResultKey<$SuratPengajuanTable, List<SuratPengajuanData>>
  _suratPengajuanRefsTable(_$AppDatabase db) => MultiTypedResultKey.fromTable(
    db.suratPengajuan,
    aliasName: $_aliasNameGenerator(
      db.suratKategori.id,
      db.suratPengajuan.kategoriId,
    ),
  );

  $$SuratPengajuanTableProcessedTableManager get suratPengajuanRefs {
    final manager = $$SuratPengajuanTableTableManager(
      $_db,
      $_db.suratPengajuan,
    ).filter((f) => f.kategoriId.id.sqlEquals($_itemColumn<int>('id')!));

    final cache = $_typedResult.readTableOrNull(_suratPengajuanRefsTable($_db));
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: cache),
    );
  }
}

class $$SuratKategoriTableFilterComposer
    extends Composer<_$AppDatabase, $SuratKategoriTable> {
  $$SuratKategoriTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<int> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get nama => $composableBuilder(
    column: $table.nama,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get deskripsi => $composableBuilder(
    column: $table.deskripsi,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get icon => $composableBuilder(
    column: $table.icon,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get persyaratan => $composableBuilder(
    column: $table.persyaratan,
    builder: (column) => ColumnFilters(column),
  );

  Expression<bool> suratPengajuanRefs(
    Expression<bool> Function($$SuratPengajuanTableFilterComposer f) f,
  ) {
    final $$SuratPengajuanTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.suratPengajuan,
      getReferencedColumn: (t) => t.kategoriId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$SuratPengajuanTableFilterComposer(
            $db: $db,
            $table: $db.suratPengajuan,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }
}

class $$SuratKategoriTableOrderingComposer
    extends Composer<_$AppDatabase, $SuratKategoriTable> {
  $$SuratKategoriTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<int> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get nama => $composableBuilder(
    column: $table.nama,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get deskripsi => $composableBuilder(
    column: $table.deskripsi,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get icon => $composableBuilder(
    column: $table.icon,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get persyaratan => $composableBuilder(
    column: $table.persyaratan,
    builder: (column) => ColumnOrderings(column),
  );
}

class $$SuratKategoriTableAnnotationComposer
    extends Composer<_$AppDatabase, $SuratKategoriTable> {
  $$SuratKategoriTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<int> get id =>
      $composableBuilder(column: $table.id, builder: (column) => column);

  GeneratedColumn<String> get nama =>
      $composableBuilder(column: $table.nama, builder: (column) => column);

  GeneratedColumn<String> get deskripsi =>
      $composableBuilder(column: $table.deskripsi, builder: (column) => column);

  GeneratedColumn<String> get icon =>
      $composableBuilder(column: $table.icon, builder: (column) => column);

  GeneratedColumn<String> get persyaratan => $composableBuilder(
    column: $table.persyaratan,
    builder: (column) => column,
  );

  Expression<T> suratPengajuanRefs<T extends Object>(
    Expression<T> Function($$SuratPengajuanTableAnnotationComposer a) f,
  ) {
    final $$SuratPengajuanTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.id,
      referencedTable: $db.suratPengajuan,
      getReferencedColumn: (t) => t.kategoriId,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$SuratPengajuanTableAnnotationComposer(
            $db: $db,
            $table: $db.suratPengajuan,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return f(composer);
  }
}

class $$SuratKategoriTableTableManager
    extends
        RootTableManager<
          _$AppDatabase,
          $SuratKategoriTable,
          SuratKategoriData,
          $$SuratKategoriTableFilterComposer,
          $$SuratKategoriTableOrderingComposer,
          $$SuratKategoriTableAnnotationComposer,
          $$SuratKategoriTableCreateCompanionBuilder,
          $$SuratKategoriTableUpdateCompanionBuilder,
          (SuratKategoriData, $$SuratKategoriTableReferences),
          SuratKategoriData,
          PrefetchHooks Function({bool suratPengajuanRefs})
        > {
  $$SuratKategoriTableTableManager(_$AppDatabase db, $SuratKategoriTable table)
    : super(
        TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$SuratKategoriTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$SuratKategoriTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$SuratKategoriTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback:
              ({
                Value<int> id = const Value.absent(),
                Value<String> nama = const Value.absent(),
                Value<String?> deskripsi = const Value.absent(),
                Value<String?> icon = const Value.absent(),
                Value<String> persyaratan = const Value.absent(),
              }) => SuratKategoriCompanion(
                id: id,
                nama: nama,
                deskripsi: deskripsi,
                icon: icon,
                persyaratan: persyaratan,
              ),
          createCompanionCallback:
              ({
                Value<int> id = const Value.absent(),
                required String nama,
                Value<String?> deskripsi = const Value.absent(),
                Value<String?> icon = const Value.absent(),
                required String persyaratan,
              }) => SuratKategoriCompanion.insert(
                id: id,
                nama: nama,
                deskripsi: deskripsi,
                icon: icon,
                persyaratan: persyaratan,
              ),
          withReferenceMapper: (p0) => p0
              .map(
                (e) => (
                  e.readTable(table),
                  $$SuratKategoriTableReferences(db, table, e),
                ),
              )
              .toList(),
          prefetchHooksCallback: ({suratPengajuanRefs = false}) {
            return PrefetchHooks(
              db: db,
              explicitlyWatchedTables: [
                if (suratPengajuanRefs) db.suratPengajuan,
              ],
              addJoins: null,
              getPrefetchedDataCallback: (items) async {
                return [
                  if (suratPengajuanRefs)
                    await $_getPrefetchedData<
                      SuratKategoriData,
                      $SuratKategoriTable,
                      SuratPengajuanData
                    >(
                      currentTable: table,
                      referencedTable: $$SuratKategoriTableReferences
                          ._suratPengajuanRefsTable(db),
                      managerFromTypedResult: (p0) =>
                          $$SuratKategoriTableReferences(
                            db,
                            table,
                            p0,
                          ).suratPengajuanRefs,
                      referencedItemsForCurrentItem: (item, referencedItems) =>
                          referencedItems.where((e) => e.kategoriId == item.id),
                      typedResults: items,
                    ),
                ];
              },
            );
          },
        ),
      );
}

typedef $$SuratKategoriTableProcessedTableManager =
    ProcessedTableManager<
      _$AppDatabase,
      $SuratKategoriTable,
      SuratKategoriData,
      $$SuratKategoriTableFilterComposer,
      $$SuratKategoriTableOrderingComposer,
      $$SuratKategoriTableAnnotationComposer,
      $$SuratKategoriTableCreateCompanionBuilder,
      $$SuratKategoriTableUpdateCompanionBuilder,
      (SuratKategoriData, $$SuratKategoriTableReferences),
      SuratKategoriData,
      PrefetchHooks Function({bool suratPengajuanRefs})
    >;
typedef $$SuratPengajuanTableCreateCompanionBuilder =
    SuratPengajuanCompanion Function({
      Value<int> id,
      required int kategoriId,
      required String kodePengajuan,
      Value<String> status,
      required String dataPengajuan,
      Value<String?> keterangan,
      Value<String?> nomorSurat,
      Value<String?> fileSurat,
      Value<String?> hashVerifikasi,
      Value<String?> catatanPenolakan,
      Value<String?> serverId,
      Value<DateTime> createdAt,
      Value<DateTime> updatedAt,
    });
typedef $$SuratPengajuanTableUpdateCompanionBuilder =
    SuratPengajuanCompanion Function({
      Value<int> id,
      Value<int> kategoriId,
      Value<String> kodePengajuan,
      Value<String> status,
      Value<String> dataPengajuan,
      Value<String?> keterangan,
      Value<String?> nomorSurat,
      Value<String?> fileSurat,
      Value<String?> hashVerifikasi,
      Value<String?> catatanPenolakan,
      Value<String?> serverId,
      Value<DateTime> createdAt,
      Value<DateTime> updatedAt,
    });

final class $$SuratPengajuanTableReferences
    extends
        BaseReferences<
          _$AppDatabase,
          $SuratPengajuanTable,
          SuratPengajuanData
        > {
  $$SuratPengajuanTableReferences(
    super.$_db,
    super.$_table,
    super.$_typedResult,
  );

  static $SuratKategoriTable _kategoriIdTable(_$AppDatabase db) =>
      db.suratKategori.createAlias(
        $_aliasNameGenerator(db.suratPengajuan.kategoriId, db.suratKategori.id),
      );

  $$SuratKategoriTableProcessedTableManager get kategoriId {
    final $_column = $_itemColumn<int>('kategori_id')!;

    final manager = $$SuratKategoriTableTableManager(
      $_db,
      $_db.suratKategori,
    ).filter((f) => f.id.sqlEquals($_column));
    final item = $_typedResult.readTableOrNull(_kategoriIdTable($_db));
    if (item == null) return manager;
    return ProcessedTableManager(
      manager.$state.copyWith(prefetchedData: [item]),
    );
  }
}

class $$SuratPengajuanTableFilterComposer
    extends Composer<_$AppDatabase, $SuratPengajuanTable> {
  $$SuratPengajuanTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<int> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get kodePengajuan => $composableBuilder(
    column: $table.kodePengajuan,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get status => $composableBuilder(
    column: $table.status,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get dataPengajuan => $composableBuilder(
    column: $table.dataPengajuan,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get keterangan => $composableBuilder(
    column: $table.keterangan,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get nomorSurat => $composableBuilder(
    column: $table.nomorSurat,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get fileSurat => $composableBuilder(
    column: $table.fileSurat,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get hashVerifikasi => $composableBuilder(
    column: $table.hashVerifikasi,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get catatanPenolakan => $composableBuilder(
    column: $table.catatanPenolakan,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get serverId => $composableBuilder(
    column: $table.serverId,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<DateTime> get createdAt => $composableBuilder(
    column: $table.createdAt,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<DateTime> get updatedAt => $composableBuilder(
    column: $table.updatedAt,
    builder: (column) => ColumnFilters(column),
  );

  $$SuratKategoriTableFilterComposer get kategoriId {
    final $$SuratKategoriTableFilterComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.kategoriId,
      referencedTable: $db.suratKategori,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$SuratKategoriTableFilterComposer(
            $db: $db,
            $table: $db.suratKategori,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$SuratPengajuanTableOrderingComposer
    extends Composer<_$AppDatabase, $SuratPengajuanTable> {
  $$SuratPengajuanTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<int> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get kodePengajuan => $composableBuilder(
    column: $table.kodePengajuan,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get status => $composableBuilder(
    column: $table.status,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get dataPengajuan => $composableBuilder(
    column: $table.dataPengajuan,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get keterangan => $composableBuilder(
    column: $table.keterangan,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get nomorSurat => $composableBuilder(
    column: $table.nomorSurat,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get fileSurat => $composableBuilder(
    column: $table.fileSurat,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get hashVerifikasi => $composableBuilder(
    column: $table.hashVerifikasi,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get catatanPenolakan => $composableBuilder(
    column: $table.catatanPenolakan,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get serverId => $composableBuilder(
    column: $table.serverId,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<DateTime> get createdAt => $composableBuilder(
    column: $table.createdAt,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<DateTime> get updatedAt => $composableBuilder(
    column: $table.updatedAt,
    builder: (column) => ColumnOrderings(column),
  );

  $$SuratKategoriTableOrderingComposer get kategoriId {
    final $$SuratKategoriTableOrderingComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.kategoriId,
      referencedTable: $db.suratKategori,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$SuratKategoriTableOrderingComposer(
            $db: $db,
            $table: $db.suratKategori,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$SuratPengajuanTableAnnotationComposer
    extends Composer<_$AppDatabase, $SuratPengajuanTable> {
  $$SuratPengajuanTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<int> get id =>
      $composableBuilder(column: $table.id, builder: (column) => column);

  GeneratedColumn<String> get kodePengajuan => $composableBuilder(
    column: $table.kodePengajuan,
    builder: (column) => column,
  );

  GeneratedColumn<String> get status =>
      $composableBuilder(column: $table.status, builder: (column) => column);

  GeneratedColumn<String> get dataPengajuan => $composableBuilder(
    column: $table.dataPengajuan,
    builder: (column) => column,
  );

  GeneratedColumn<String> get keterangan => $composableBuilder(
    column: $table.keterangan,
    builder: (column) => column,
  );

  GeneratedColumn<String> get nomorSurat => $composableBuilder(
    column: $table.nomorSurat,
    builder: (column) => column,
  );

  GeneratedColumn<String> get fileSurat =>
      $composableBuilder(column: $table.fileSurat, builder: (column) => column);

  GeneratedColumn<String> get hashVerifikasi => $composableBuilder(
    column: $table.hashVerifikasi,
    builder: (column) => column,
  );

  GeneratedColumn<String> get catatanPenolakan => $composableBuilder(
    column: $table.catatanPenolakan,
    builder: (column) => column,
  );

  GeneratedColumn<String> get serverId =>
      $composableBuilder(column: $table.serverId, builder: (column) => column);

  GeneratedColumn<DateTime> get createdAt =>
      $composableBuilder(column: $table.createdAt, builder: (column) => column);

  GeneratedColumn<DateTime> get updatedAt =>
      $composableBuilder(column: $table.updatedAt, builder: (column) => column);

  $$SuratKategoriTableAnnotationComposer get kategoriId {
    final $$SuratKategoriTableAnnotationComposer composer = $composerBuilder(
      composer: this,
      getCurrentColumn: (t) => t.kategoriId,
      referencedTable: $db.suratKategori,
      getReferencedColumn: (t) => t.id,
      builder:
          (
            joinBuilder, {
            $addJoinBuilderToRootComposer,
            $removeJoinBuilderFromRootComposer,
          }) => $$SuratKategoriTableAnnotationComposer(
            $db: $db,
            $table: $db.suratKategori,
            $addJoinBuilderToRootComposer: $addJoinBuilderToRootComposer,
            joinBuilder: joinBuilder,
            $removeJoinBuilderFromRootComposer:
                $removeJoinBuilderFromRootComposer,
          ),
    );
    return composer;
  }
}

class $$SuratPengajuanTableTableManager
    extends
        RootTableManager<
          _$AppDatabase,
          $SuratPengajuanTable,
          SuratPengajuanData,
          $$SuratPengajuanTableFilterComposer,
          $$SuratPengajuanTableOrderingComposer,
          $$SuratPengajuanTableAnnotationComposer,
          $$SuratPengajuanTableCreateCompanionBuilder,
          $$SuratPengajuanTableUpdateCompanionBuilder,
          (SuratPengajuanData, $$SuratPengajuanTableReferences),
          SuratPengajuanData,
          PrefetchHooks Function({bool kategoriId})
        > {
  $$SuratPengajuanTableTableManager(
    _$AppDatabase db,
    $SuratPengajuanTable table,
  ) : super(
        TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$SuratPengajuanTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$SuratPengajuanTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$SuratPengajuanTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback:
              ({
                Value<int> id = const Value.absent(),
                Value<int> kategoriId = const Value.absent(),
                Value<String> kodePengajuan = const Value.absent(),
                Value<String> status = const Value.absent(),
                Value<String> dataPengajuan = const Value.absent(),
                Value<String?> keterangan = const Value.absent(),
                Value<String?> nomorSurat = const Value.absent(),
                Value<String?> fileSurat = const Value.absent(),
                Value<String?> hashVerifikasi = const Value.absent(),
                Value<String?> catatanPenolakan = const Value.absent(),
                Value<String?> serverId = const Value.absent(),
                Value<DateTime> createdAt = const Value.absent(),
                Value<DateTime> updatedAt = const Value.absent(),
              }) => SuratPengajuanCompanion(
                id: id,
                kategoriId: kategoriId,
                kodePengajuan: kodePengajuan,
                status: status,
                dataPengajuan: dataPengajuan,
                keterangan: keterangan,
                nomorSurat: nomorSurat,
                fileSurat: fileSurat,
                hashVerifikasi: hashVerifikasi,
                catatanPenolakan: catatanPenolakan,
                serverId: serverId,
                createdAt: createdAt,
                updatedAt: updatedAt,
              ),
          createCompanionCallback:
              ({
                Value<int> id = const Value.absent(),
                required int kategoriId,
                required String kodePengajuan,
                Value<String> status = const Value.absent(),
                required String dataPengajuan,
                Value<String?> keterangan = const Value.absent(),
                Value<String?> nomorSurat = const Value.absent(),
                Value<String?> fileSurat = const Value.absent(),
                Value<String?> hashVerifikasi = const Value.absent(),
                Value<String?> catatanPenolakan = const Value.absent(),
                Value<String?> serverId = const Value.absent(),
                Value<DateTime> createdAt = const Value.absent(),
                Value<DateTime> updatedAt = const Value.absent(),
              }) => SuratPengajuanCompanion.insert(
                id: id,
                kategoriId: kategoriId,
                kodePengajuan: kodePengajuan,
                status: status,
                dataPengajuan: dataPengajuan,
                keterangan: keterangan,
                nomorSurat: nomorSurat,
                fileSurat: fileSurat,
                hashVerifikasi: hashVerifikasi,
                catatanPenolakan: catatanPenolakan,
                serverId: serverId,
                createdAt: createdAt,
                updatedAt: updatedAt,
              ),
          withReferenceMapper: (p0) => p0
              .map(
                (e) => (
                  e.readTable(table),
                  $$SuratPengajuanTableReferences(db, table, e),
                ),
              )
              .toList(),
          prefetchHooksCallback: ({kategoriId = false}) {
            return PrefetchHooks(
              db: db,
              explicitlyWatchedTables: [],
              addJoins:
                  <
                    T extends TableManagerState<
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic,
                      dynamic
                    >
                  >(state) {
                    if (kategoriId) {
                      state =
                          state.withJoin(
                                currentTable: table,
                                currentColumn: table.kategoriId,
                                referencedTable: $$SuratPengajuanTableReferences
                                    ._kategoriIdTable(db),
                                referencedColumn:
                                    $$SuratPengajuanTableReferences
                                        ._kategoriIdTable(db)
                                        .id,
                              )
                              as T;
                    }

                    return state;
                  },
              getPrefetchedDataCallback: (items) async {
                return [];
              },
            );
          },
        ),
      );
}

typedef $$SuratPengajuanTableProcessedTableManager =
    ProcessedTableManager<
      _$AppDatabase,
      $SuratPengajuanTable,
      SuratPengajuanData,
      $$SuratPengajuanTableFilterComposer,
      $$SuratPengajuanTableOrderingComposer,
      $$SuratPengajuanTableAnnotationComposer,
      $$SuratPengajuanTableCreateCompanionBuilder,
      $$SuratPengajuanTableUpdateCompanionBuilder,
      (SuratPengajuanData, $$SuratPengajuanTableReferences),
      SuratPengajuanData,
      PrefetchHooks Function({bool kategoriId})
    >;
typedef $$MutasiPendudukTableCreateCompanionBuilder =
    MutasiPendudukCompanion Function({
      Value<int> id,
      required String jenisMutasi,
      required DateTime tanggalMutasi,
      required String alamatAsal,
      required String alasan,
      Value<String> status,
      Value<String?> serverId,
      Value<DateTime> createdAt,
    });
typedef $$MutasiPendudukTableUpdateCompanionBuilder =
    MutasiPendudukCompanion Function({
      Value<int> id,
      Value<String> jenisMutasi,
      Value<DateTime> tanggalMutasi,
      Value<String> alamatAsal,
      Value<String> alasan,
      Value<String> status,
      Value<String?> serverId,
      Value<DateTime> createdAt,
    });

class $$MutasiPendudukTableFilterComposer
    extends Composer<_$AppDatabase, $MutasiPendudukTable> {
  $$MutasiPendudukTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<int> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get jenisMutasi => $composableBuilder(
    column: $table.jenisMutasi,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<DateTime> get tanggalMutasi => $composableBuilder(
    column: $table.tanggalMutasi,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get alamatAsal => $composableBuilder(
    column: $table.alamatAsal,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get alasan => $composableBuilder(
    column: $table.alasan,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get status => $composableBuilder(
    column: $table.status,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get serverId => $composableBuilder(
    column: $table.serverId,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<DateTime> get createdAt => $composableBuilder(
    column: $table.createdAt,
    builder: (column) => ColumnFilters(column),
  );
}

class $$MutasiPendudukTableOrderingComposer
    extends Composer<_$AppDatabase, $MutasiPendudukTable> {
  $$MutasiPendudukTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<int> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get jenisMutasi => $composableBuilder(
    column: $table.jenisMutasi,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<DateTime> get tanggalMutasi => $composableBuilder(
    column: $table.tanggalMutasi,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get alamatAsal => $composableBuilder(
    column: $table.alamatAsal,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get alasan => $composableBuilder(
    column: $table.alasan,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get status => $composableBuilder(
    column: $table.status,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get serverId => $composableBuilder(
    column: $table.serverId,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<DateTime> get createdAt => $composableBuilder(
    column: $table.createdAt,
    builder: (column) => ColumnOrderings(column),
  );
}

class $$MutasiPendudukTableAnnotationComposer
    extends Composer<_$AppDatabase, $MutasiPendudukTable> {
  $$MutasiPendudukTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<int> get id =>
      $composableBuilder(column: $table.id, builder: (column) => column);

  GeneratedColumn<String> get jenisMutasi => $composableBuilder(
    column: $table.jenisMutasi,
    builder: (column) => column,
  );

  GeneratedColumn<DateTime> get tanggalMutasi => $composableBuilder(
    column: $table.tanggalMutasi,
    builder: (column) => column,
  );

  GeneratedColumn<String> get alamatAsal => $composableBuilder(
    column: $table.alamatAsal,
    builder: (column) => column,
  );

  GeneratedColumn<String> get alasan =>
      $composableBuilder(column: $table.alasan, builder: (column) => column);

  GeneratedColumn<String> get status =>
      $composableBuilder(column: $table.status, builder: (column) => column);

  GeneratedColumn<String> get serverId =>
      $composableBuilder(column: $table.serverId, builder: (column) => column);

  GeneratedColumn<DateTime> get createdAt =>
      $composableBuilder(column: $table.createdAt, builder: (column) => column);
}

class $$MutasiPendudukTableTableManager
    extends
        RootTableManager<
          _$AppDatabase,
          $MutasiPendudukTable,
          MutasiPendudukData,
          $$MutasiPendudukTableFilterComposer,
          $$MutasiPendudukTableOrderingComposer,
          $$MutasiPendudukTableAnnotationComposer,
          $$MutasiPendudukTableCreateCompanionBuilder,
          $$MutasiPendudukTableUpdateCompanionBuilder,
          (
            MutasiPendudukData,
            BaseReferences<
              _$AppDatabase,
              $MutasiPendudukTable,
              MutasiPendudukData
            >,
          ),
          MutasiPendudukData,
          PrefetchHooks Function()
        > {
  $$MutasiPendudukTableTableManager(
    _$AppDatabase db,
    $MutasiPendudukTable table,
  ) : super(
        TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$MutasiPendudukTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$MutasiPendudukTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$MutasiPendudukTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback:
              ({
                Value<int> id = const Value.absent(),
                Value<String> jenisMutasi = const Value.absent(),
                Value<DateTime> tanggalMutasi = const Value.absent(),
                Value<String> alamatAsal = const Value.absent(),
                Value<String> alasan = const Value.absent(),
                Value<String> status = const Value.absent(),
                Value<String?> serverId = const Value.absent(),
                Value<DateTime> createdAt = const Value.absent(),
              }) => MutasiPendudukCompanion(
                id: id,
                jenisMutasi: jenisMutasi,
                tanggalMutasi: tanggalMutasi,
                alamatAsal: alamatAsal,
                alasan: alasan,
                status: status,
                serverId: serverId,
                createdAt: createdAt,
              ),
          createCompanionCallback:
              ({
                Value<int> id = const Value.absent(),
                required String jenisMutasi,
                required DateTime tanggalMutasi,
                required String alamatAsal,
                required String alasan,
                Value<String> status = const Value.absent(),
                Value<String?> serverId = const Value.absent(),
                Value<DateTime> createdAt = const Value.absent(),
              }) => MutasiPendudukCompanion.insert(
                id: id,
                jenisMutasi: jenisMutasi,
                tanggalMutasi: tanggalMutasi,
                alamatAsal: alamatAsal,
                alasan: alasan,
                status: status,
                serverId: serverId,
                createdAt: createdAt,
              ),
          withReferenceMapper: (p0) => p0
              .map((e) => (e.readTable(table), BaseReferences(db, table, e)))
              .toList(),
          prefetchHooksCallback: null,
        ),
      );
}

typedef $$MutasiPendudukTableProcessedTableManager =
    ProcessedTableManager<
      _$AppDatabase,
      $MutasiPendudukTable,
      MutasiPendudukData,
      $$MutasiPendudukTableFilterComposer,
      $$MutasiPendudukTableOrderingComposer,
      $$MutasiPendudukTableAnnotationComposer,
      $$MutasiPendudukTableCreateCompanionBuilder,
      $$MutasiPendudukTableUpdateCompanionBuilder,
      (
        MutasiPendudukData,
        BaseReferences<_$AppDatabase, $MutasiPendudukTable, MutasiPendudukData>,
      ),
      MutasiPendudukData,
      PrefetchHooks Function()
    >;
typedef $$InformasiPublikTableCreateCompanionBuilder =
    InformasiPublikCompanion Function({
      Value<int> id,
      required String judul,
      required String slug,
      required String konten,
      Value<String?> gambar,
      Value<String?> penulis,
      Value<String?> kategori,
      Value<bool> isPublished,
      Value<DateTime> createdAt,
    });
typedef $$InformasiPublikTableUpdateCompanionBuilder =
    InformasiPublikCompanion Function({
      Value<int> id,
      Value<String> judul,
      Value<String> slug,
      Value<String> konten,
      Value<String?> gambar,
      Value<String?> penulis,
      Value<String?> kategori,
      Value<bool> isPublished,
      Value<DateTime> createdAt,
    });

class $$InformasiPublikTableFilterComposer
    extends Composer<_$AppDatabase, $InformasiPublikTable> {
  $$InformasiPublikTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<int> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get judul => $composableBuilder(
    column: $table.judul,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get slug => $composableBuilder(
    column: $table.slug,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get konten => $composableBuilder(
    column: $table.konten,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get gambar => $composableBuilder(
    column: $table.gambar,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get penulis => $composableBuilder(
    column: $table.penulis,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get kategori => $composableBuilder(
    column: $table.kategori,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<bool> get isPublished => $composableBuilder(
    column: $table.isPublished,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<DateTime> get createdAt => $composableBuilder(
    column: $table.createdAt,
    builder: (column) => ColumnFilters(column),
  );
}

class $$InformasiPublikTableOrderingComposer
    extends Composer<_$AppDatabase, $InformasiPublikTable> {
  $$InformasiPublikTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<int> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get judul => $composableBuilder(
    column: $table.judul,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get slug => $composableBuilder(
    column: $table.slug,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get konten => $composableBuilder(
    column: $table.konten,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get gambar => $composableBuilder(
    column: $table.gambar,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get penulis => $composableBuilder(
    column: $table.penulis,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get kategori => $composableBuilder(
    column: $table.kategori,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<bool> get isPublished => $composableBuilder(
    column: $table.isPublished,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<DateTime> get createdAt => $composableBuilder(
    column: $table.createdAt,
    builder: (column) => ColumnOrderings(column),
  );
}

class $$InformasiPublikTableAnnotationComposer
    extends Composer<_$AppDatabase, $InformasiPublikTable> {
  $$InformasiPublikTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<int> get id =>
      $composableBuilder(column: $table.id, builder: (column) => column);

  GeneratedColumn<String> get judul =>
      $composableBuilder(column: $table.judul, builder: (column) => column);

  GeneratedColumn<String> get slug =>
      $composableBuilder(column: $table.slug, builder: (column) => column);

  GeneratedColumn<String> get konten =>
      $composableBuilder(column: $table.konten, builder: (column) => column);

  GeneratedColumn<String> get gambar =>
      $composableBuilder(column: $table.gambar, builder: (column) => column);

  GeneratedColumn<String> get penulis =>
      $composableBuilder(column: $table.penulis, builder: (column) => column);

  GeneratedColumn<String> get kategori =>
      $composableBuilder(column: $table.kategori, builder: (column) => column);

  GeneratedColumn<bool> get isPublished => $composableBuilder(
    column: $table.isPublished,
    builder: (column) => column,
  );

  GeneratedColumn<DateTime> get createdAt =>
      $composableBuilder(column: $table.createdAt, builder: (column) => column);
}

class $$InformasiPublikTableTableManager
    extends
        RootTableManager<
          _$AppDatabase,
          $InformasiPublikTable,
          InformasiPublikData,
          $$InformasiPublikTableFilterComposer,
          $$InformasiPublikTableOrderingComposer,
          $$InformasiPublikTableAnnotationComposer,
          $$InformasiPublikTableCreateCompanionBuilder,
          $$InformasiPublikTableUpdateCompanionBuilder,
          (
            InformasiPublikData,
            BaseReferences<
              _$AppDatabase,
              $InformasiPublikTable,
              InformasiPublikData
            >,
          ),
          InformasiPublikData,
          PrefetchHooks Function()
        > {
  $$InformasiPublikTableTableManager(
    _$AppDatabase db,
    $InformasiPublikTable table,
  ) : super(
        TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$InformasiPublikTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$InformasiPublikTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$InformasiPublikTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback:
              ({
                Value<int> id = const Value.absent(),
                Value<String> judul = const Value.absent(),
                Value<String> slug = const Value.absent(),
                Value<String> konten = const Value.absent(),
                Value<String?> gambar = const Value.absent(),
                Value<String?> penulis = const Value.absent(),
                Value<String?> kategori = const Value.absent(),
                Value<bool> isPublished = const Value.absent(),
                Value<DateTime> createdAt = const Value.absent(),
              }) => InformasiPublikCompanion(
                id: id,
                judul: judul,
                slug: slug,
                konten: konten,
                gambar: gambar,
                penulis: penulis,
                kategori: kategori,
                isPublished: isPublished,
                createdAt: createdAt,
              ),
          createCompanionCallback:
              ({
                Value<int> id = const Value.absent(),
                required String judul,
                required String slug,
                required String konten,
                Value<String?> gambar = const Value.absent(),
                Value<String?> penulis = const Value.absent(),
                Value<String?> kategori = const Value.absent(),
                Value<bool> isPublished = const Value.absent(),
                Value<DateTime> createdAt = const Value.absent(),
              }) => InformasiPublikCompanion.insert(
                id: id,
                judul: judul,
                slug: slug,
                konten: konten,
                gambar: gambar,
                penulis: penulis,
                kategori: kategori,
                isPublished: isPublished,
                createdAt: createdAt,
              ),
          withReferenceMapper: (p0) => p0
              .map((e) => (e.readTable(table), BaseReferences(db, table, e)))
              .toList(),
          prefetchHooksCallback: null,
        ),
      );
}

typedef $$InformasiPublikTableProcessedTableManager =
    ProcessedTableManager<
      _$AppDatabase,
      $InformasiPublikTable,
      InformasiPublikData,
      $$InformasiPublikTableFilterComposer,
      $$InformasiPublikTableOrderingComposer,
      $$InformasiPublikTableAnnotationComposer,
      $$InformasiPublikTableCreateCompanionBuilder,
      $$InformasiPublikTableUpdateCompanionBuilder,
      (
        InformasiPublikData,
        BaseReferences<
          _$AppDatabase,
          $InformasiPublikTable,
          InformasiPublikData
        >,
      ),
      InformasiPublikData,
      PrefetchHooks Function()
    >;
typedef $$PendudukProfileTableCreateCompanionBuilder =
    PendudukProfileCompanion Function({
      Value<int> id,
      required String nik,
      required String namaLengkap,
      Value<String?> tempatLahir,
      Value<DateTime?> tanggalLahir,
      required String jenisKelamin,
      Value<String?> agama,
      required String noKk,
      Value<String?> fotoProfil,
      Value<String?> telegramChatId,
    });
typedef $$PendudukProfileTableUpdateCompanionBuilder =
    PendudukProfileCompanion Function({
      Value<int> id,
      Value<String> nik,
      Value<String> namaLengkap,
      Value<String?> tempatLahir,
      Value<DateTime?> tanggalLahir,
      Value<String> jenisKelamin,
      Value<String?> agama,
      Value<String> noKk,
      Value<String?> fotoProfil,
      Value<String?> telegramChatId,
    });

class $$PendudukProfileTableFilterComposer
    extends Composer<_$AppDatabase, $PendudukProfileTable> {
  $$PendudukProfileTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<int> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get nik => $composableBuilder(
    column: $table.nik,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get namaLengkap => $composableBuilder(
    column: $table.namaLengkap,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get tempatLahir => $composableBuilder(
    column: $table.tempatLahir,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<DateTime> get tanggalLahir => $composableBuilder(
    column: $table.tanggalLahir,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get jenisKelamin => $composableBuilder(
    column: $table.jenisKelamin,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get agama => $composableBuilder(
    column: $table.agama,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get noKk => $composableBuilder(
    column: $table.noKk,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get fotoProfil => $composableBuilder(
    column: $table.fotoProfil,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get telegramChatId => $composableBuilder(
    column: $table.telegramChatId,
    builder: (column) => ColumnFilters(column),
  );
}

class $$PendudukProfileTableOrderingComposer
    extends Composer<_$AppDatabase, $PendudukProfileTable> {
  $$PendudukProfileTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<int> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get nik => $composableBuilder(
    column: $table.nik,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get namaLengkap => $composableBuilder(
    column: $table.namaLengkap,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get tempatLahir => $composableBuilder(
    column: $table.tempatLahir,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<DateTime> get tanggalLahir => $composableBuilder(
    column: $table.tanggalLahir,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get jenisKelamin => $composableBuilder(
    column: $table.jenisKelamin,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get agama => $composableBuilder(
    column: $table.agama,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get noKk => $composableBuilder(
    column: $table.noKk,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get fotoProfil => $composableBuilder(
    column: $table.fotoProfil,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get telegramChatId => $composableBuilder(
    column: $table.telegramChatId,
    builder: (column) => ColumnOrderings(column),
  );
}

class $$PendudukProfileTableAnnotationComposer
    extends Composer<_$AppDatabase, $PendudukProfileTable> {
  $$PendudukProfileTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<int> get id =>
      $composableBuilder(column: $table.id, builder: (column) => column);

  GeneratedColumn<String> get nik =>
      $composableBuilder(column: $table.nik, builder: (column) => column);

  GeneratedColumn<String> get namaLengkap => $composableBuilder(
    column: $table.namaLengkap,
    builder: (column) => column,
  );

  GeneratedColumn<String> get tempatLahir => $composableBuilder(
    column: $table.tempatLahir,
    builder: (column) => column,
  );

  GeneratedColumn<DateTime> get tanggalLahir => $composableBuilder(
    column: $table.tanggalLahir,
    builder: (column) => column,
  );

  GeneratedColumn<String> get jenisKelamin => $composableBuilder(
    column: $table.jenisKelamin,
    builder: (column) => column,
  );

  GeneratedColumn<String> get agama =>
      $composableBuilder(column: $table.agama, builder: (column) => column);

  GeneratedColumn<String> get noKk =>
      $composableBuilder(column: $table.noKk, builder: (column) => column);

  GeneratedColumn<String> get fotoProfil => $composableBuilder(
    column: $table.fotoProfil,
    builder: (column) => column,
  );

  GeneratedColumn<String> get telegramChatId => $composableBuilder(
    column: $table.telegramChatId,
    builder: (column) => column,
  );
}

class $$PendudukProfileTableTableManager
    extends
        RootTableManager<
          _$AppDatabase,
          $PendudukProfileTable,
          PendudukProfileData,
          $$PendudukProfileTableFilterComposer,
          $$PendudukProfileTableOrderingComposer,
          $$PendudukProfileTableAnnotationComposer,
          $$PendudukProfileTableCreateCompanionBuilder,
          $$PendudukProfileTableUpdateCompanionBuilder,
          (
            PendudukProfileData,
            BaseReferences<
              _$AppDatabase,
              $PendudukProfileTable,
              PendudukProfileData
            >,
          ),
          PendudukProfileData,
          PrefetchHooks Function()
        > {
  $$PendudukProfileTableTableManager(
    _$AppDatabase db,
    $PendudukProfileTable table,
  ) : super(
        TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$PendudukProfileTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$PendudukProfileTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$PendudukProfileTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback:
              ({
                Value<int> id = const Value.absent(),
                Value<String> nik = const Value.absent(),
                Value<String> namaLengkap = const Value.absent(),
                Value<String?> tempatLahir = const Value.absent(),
                Value<DateTime?> tanggalLahir = const Value.absent(),
                Value<String> jenisKelamin = const Value.absent(),
                Value<String?> agama = const Value.absent(),
                Value<String> noKk = const Value.absent(),
                Value<String?> fotoProfil = const Value.absent(),
                Value<String?> telegramChatId = const Value.absent(),
              }) => PendudukProfileCompanion(
                id: id,
                nik: nik,
                namaLengkap: namaLengkap,
                tempatLahir: tempatLahir,
                tanggalLahir: tanggalLahir,
                jenisKelamin: jenisKelamin,
                agama: agama,
                noKk: noKk,
                fotoProfil: fotoProfil,
                telegramChatId: telegramChatId,
              ),
          createCompanionCallback:
              ({
                Value<int> id = const Value.absent(),
                required String nik,
                required String namaLengkap,
                Value<String?> tempatLahir = const Value.absent(),
                Value<DateTime?> tanggalLahir = const Value.absent(),
                required String jenisKelamin,
                Value<String?> agama = const Value.absent(),
                required String noKk,
                Value<String?> fotoProfil = const Value.absent(),
                Value<String?> telegramChatId = const Value.absent(),
              }) => PendudukProfileCompanion.insert(
                id: id,
                nik: nik,
                namaLengkap: namaLengkap,
                tempatLahir: tempatLahir,
                tanggalLahir: tanggalLahir,
                jenisKelamin: jenisKelamin,
                agama: agama,
                noKk: noKk,
                fotoProfil: fotoProfil,
                telegramChatId: telegramChatId,
              ),
          withReferenceMapper: (p0) => p0
              .map((e) => (e.readTable(table), BaseReferences(db, table, e)))
              .toList(),
          prefetchHooksCallback: null,
        ),
      );
}

typedef $$PendudukProfileTableProcessedTableManager =
    ProcessedTableManager<
      _$AppDatabase,
      $PendudukProfileTable,
      PendudukProfileData,
      $$PendudukProfileTableFilterComposer,
      $$PendudukProfileTableOrderingComposer,
      $$PendudukProfileTableAnnotationComposer,
      $$PendudukProfileTableCreateCompanionBuilder,
      $$PendudukProfileTableUpdateCompanionBuilder,
      (
        PendudukProfileData,
        BaseReferences<
          _$AppDatabase,
          $PendudukProfileTable,
          PendudukProfileData
        >,
      ),
      PendudukProfileData,
      PrefetchHooks Function()
    >;
typedef $$FasilitasDesaTableCreateCompanionBuilder =
    FasilitasDesaCompanion Function({
      Value<int> id,
      required String nama,
      required String jenisFasilitas,
      Value<String?> alamat,
      Value<String?> kondisi,
    });
typedef $$FasilitasDesaTableUpdateCompanionBuilder =
    FasilitasDesaCompanion Function({
      Value<int> id,
      Value<String> nama,
      Value<String> jenisFasilitas,
      Value<String?> alamat,
      Value<String?> kondisi,
    });

class $$FasilitasDesaTableFilterComposer
    extends Composer<_$AppDatabase, $FasilitasDesaTable> {
  $$FasilitasDesaTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<int> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get nama => $composableBuilder(
    column: $table.nama,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get jenisFasilitas => $composableBuilder(
    column: $table.jenisFasilitas,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get alamat => $composableBuilder(
    column: $table.alamat,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get kondisi => $composableBuilder(
    column: $table.kondisi,
    builder: (column) => ColumnFilters(column),
  );
}

class $$FasilitasDesaTableOrderingComposer
    extends Composer<_$AppDatabase, $FasilitasDesaTable> {
  $$FasilitasDesaTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<int> get id => $composableBuilder(
    column: $table.id,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get nama => $composableBuilder(
    column: $table.nama,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get jenisFasilitas => $composableBuilder(
    column: $table.jenisFasilitas,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get alamat => $composableBuilder(
    column: $table.alamat,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get kondisi => $composableBuilder(
    column: $table.kondisi,
    builder: (column) => ColumnOrderings(column),
  );
}

class $$FasilitasDesaTableAnnotationComposer
    extends Composer<_$AppDatabase, $FasilitasDesaTable> {
  $$FasilitasDesaTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<int> get id =>
      $composableBuilder(column: $table.id, builder: (column) => column);

  GeneratedColumn<String> get nama =>
      $composableBuilder(column: $table.nama, builder: (column) => column);

  GeneratedColumn<String> get jenisFasilitas => $composableBuilder(
    column: $table.jenisFasilitas,
    builder: (column) => column,
  );

  GeneratedColumn<String> get alamat =>
      $composableBuilder(column: $table.alamat, builder: (column) => column);

  GeneratedColumn<String> get kondisi =>
      $composableBuilder(column: $table.kondisi, builder: (column) => column);
}

class $$FasilitasDesaTableTableManager
    extends
        RootTableManager<
          _$AppDatabase,
          $FasilitasDesaTable,
          FasilitasDesaData,
          $$FasilitasDesaTableFilterComposer,
          $$FasilitasDesaTableOrderingComposer,
          $$FasilitasDesaTableAnnotationComposer,
          $$FasilitasDesaTableCreateCompanionBuilder,
          $$FasilitasDesaTableUpdateCompanionBuilder,
          (
            FasilitasDesaData,
            BaseReferences<
              _$AppDatabase,
              $FasilitasDesaTable,
              FasilitasDesaData
            >,
          ),
          FasilitasDesaData,
          PrefetchHooks Function()
        > {
  $$FasilitasDesaTableTableManager(_$AppDatabase db, $FasilitasDesaTable table)
    : super(
        TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$FasilitasDesaTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$FasilitasDesaTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$FasilitasDesaTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback:
              ({
                Value<int> id = const Value.absent(),
                Value<String> nama = const Value.absent(),
                Value<String> jenisFasilitas = const Value.absent(),
                Value<String?> alamat = const Value.absent(),
                Value<String?> kondisi = const Value.absent(),
              }) => FasilitasDesaCompanion(
                id: id,
                nama: nama,
                jenisFasilitas: jenisFasilitas,
                alamat: alamat,
                kondisi: kondisi,
              ),
          createCompanionCallback:
              ({
                Value<int> id = const Value.absent(),
                required String nama,
                required String jenisFasilitas,
                Value<String?> alamat = const Value.absent(),
                Value<String?> kondisi = const Value.absent(),
              }) => FasilitasDesaCompanion.insert(
                id: id,
                nama: nama,
                jenisFasilitas: jenisFasilitas,
                alamat: alamat,
                kondisi: kondisi,
              ),
          withReferenceMapper: (p0) => p0
              .map((e) => (e.readTable(table), BaseReferences(db, table, e)))
              .toList(),
          prefetchHooksCallback: null,
        ),
      );
}

typedef $$FasilitasDesaTableProcessedTableManager =
    ProcessedTableManager<
      _$AppDatabase,
      $FasilitasDesaTable,
      FasilitasDesaData,
      $$FasilitasDesaTableFilterComposer,
      $$FasilitasDesaTableOrderingComposer,
      $$FasilitasDesaTableAnnotationComposer,
      $$FasilitasDesaTableCreateCompanionBuilder,
      $$FasilitasDesaTableUpdateCompanionBuilder,
      (
        FasilitasDesaData,
        BaseReferences<_$AppDatabase, $FasilitasDesaTable, FasilitasDesaData>,
      ),
      FasilitasDesaData,
      PrefetchHooks Function()
    >;
typedef $$SyncQueueTableCreateCompanionBuilder =
    SyncQueueCompanion Function({
      required String clientId,
      required String type,
      required String action,
      required String data,
      required DateTime createdAt,
      Value<String> status,
      Value<String?> error,
      Value<int?> serverId,
      Value<int> rowid,
    });
typedef $$SyncQueueTableUpdateCompanionBuilder =
    SyncQueueCompanion Function({
      Value<String> clientId,
      Value<String> type,
      Value<String> action,
      Value<String> data,
      Value<DateTime> createdAt,
      Value<String> status,
      Value<String?> error,
      Value<int?> serverId,
      Value<int> rowid,
    });

class $$SyncQueueTableFilterComposer
    extends Composer<_$AppDatabase, $SyncQueueTable> {
  $$SyncQueueTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<String> get clientId => $composableBuilder(
    column: $table.clientId,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get type => $composableBuilder(
    column: $table.type,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get action => $composableBuilder(
    column: $table.action,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get data => $composableBuilder(
    column: $table.data,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<DateTime> get createdAt => $composableBuilder(
    column: $table.createdAt,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get status => $composableBuilder(
    column: $table.status,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get error => $composableBuilder(
    column: $table.error,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<int> get serverId => $composableBuilder(
    column: $table.serverId,
    builder: (column) => ColumnFilters(column),
  );
}

class $$SyncQueueTableOrderingComposer
    extends Composer<_$AppDatabase, $SyncQueueTable> {
  $$SyncQueueTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<String> get clientId => $composableBuilder(
    column: $table.clientId,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get type => $composableBuilder(
    column: $table.type,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get action => $composableBuilder(
    column: $table.action,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get data => $composableBuilder(
    column: $table.data,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<DateTime> get createdAt => $composableBuilder(
    column: $table.createdAt,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get status => $composableBuilder(
    column: $table.status,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get error => $composableBuilder(
    column: $table.error,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<int> get serverId => $composableBuilder(
    column: $table.serverId,
    builder: (column) => ColumnOrderings(column),
  );
}

class $$SyncQueueTableAnnotationComposer
    extends Composer<_$AppDatabase, $SyncQueueTable> {
  $$SyncQueueTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<String> get clientId =>
      $composableBuilder(column: $table.clientId, builder: (column) => column);

  GeneratedColumn<String> get type =>
      $composableBuilder(column: $table.type, builder: (column) => column);

  GeneratedColumn<String> get action =>
      $composableBuilder(column: $table.action, builder: (column) => column);

  GeneratedColumn<String> get data =>
      $composableBuilder(column: $table.data, builder: (column) => column);

  GeneratedColumn<DateTime> get createdAt =>
      $composableBuilder(column: $table.createdAt, builder: (column) => column);

  GeneratedColumn<String> get status =>
      $composableBuilder(column: $table.status, builder: (column) => column);

  GeneratedColumn<String> get error =>
      $composableBuilder(column: $table.error, builder: (column) => column);

  GeneratedColumn<int> get serverId =>
      $composableBuilder(column: $table.serverId, builder: (column) => column);
}

class $$SyncQueueTableTableManager
    extends
        RootTableManager<
          _$AppDatabase,
          $SyncQueueTable,
          SyncQueueData,
          $$SyncQueueTableFilterComposer,
          $$SyncQueueTableOrderingComposer,
          $$SyncQueueTableAnnotationComposer,
          $$SyncQueueTableCreateCompanionBuilder,
          $$SyncQueueTableUpdateCompanionBuilder,
          (
            SyncQueueData,
            BaseReferences<_$AppDatabase, $SyncQueueTable, SyncQueueData>,
          ),
          SyncQueueData,
          PrefetchHooks Function()
        > {
  $$SyncQueueTableTableManager(_$AppDatabase db, $SyncQueueTable table)
    : super(
        TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$SyncQueueTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$SyncQueueTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$SyncQueueTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback:
              ({
                Value<String> clientId = const Value.absent(),
                Value<String> type = const Value.absent(),
                Value<String> action = const Value.absent(),
                Value<String> data = const Value.absent(),
                Value<DateTime> createdAt = const Value.absent(),
                Value<String> status = const Value.absent(),
                Value<String?> error = const Value.absent(),
                Value<int?> serverId = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => SyncQueueCompanion(
                clientId: clientId,
                type: type,
                action: action,
                data: data,
                createdAt: createdAt,
                status: status,
                error: error,
                serverId: serverId,
                rowid: rowid,
              ),
          createCompanionCallback:
              ({
                required String clientId,
                required String type,
                required String action,
                required String data,
                required DateTime createdAt,
                Value<String> status = const Value.absent(),
                Value<String?> error = const Value.absent(),
                Value<int?> serverId = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => SyncQueueCompanion.insert(
                clientId: clientId,
                type: type,
                action: action,
                data: data,
                createdAt: createdAt,
                status: status,
                error: error,
                serverId: serverId,
                rowid: rowid,
              ),
          withReferenceMapper: (p0) => p0
              .map((e) => (e.readTable(table), BaseReferences(db, table, e)))
              .toList(),
          prefetchHooksCallback: null,
        ),
      );
}

typedef $$SyncQueueTableProcessedTableManager =
    ProcessedTableManager<
      _$AppDatabase,
      $SyncQueueTable,
      SyncQueueData,
      $$SyncQueueTableFilterComposer,
      $$SyncQueueTableOrderingComposer,
      $$SyncQueueTableAnnotationComposer,
      $$SyncQueueTableCreateCompanionBuilder,
      $$SyncQueueTableUpdateCompanionBuilder,
      (
        SyncQueueData,
        BaseReferences<_$AppDatabase, $SyncQueueTable, SyncQueueData>,
      ),
      SyncQueueData,
      PrefetchHooks Function()
    >;
typedef $$SettingsTableCreateCompanionBuilder =
    SettingsCompanion Function({
      required String key,
      required String value,
      Value<int> rowid,
    });
typedef $$SettingsTableUpdateCompanionBuilder =
    SettingsCompanion Function({
      Value<String> key,
      Value<String> value,
      Value<int> rowid,
    });

class $$SettingsTableFilterComposer
    extends Composer<_$AppDatabase, $SettingsTable> {
  $$SettingsTableFilterComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnFilters<String> get key => $composableBuilder(
    column: $table.key,
    builder: (column) => ColumnFilters(column),
  );

  ColumnFilters<String> get value => $composableBuilder(
    column: $table.value,
    builder: (column) => ColumnFilters(column),
  );
}

class $$SettingsTableOrderingComposer
    extends Composer<_$AppDatabase, $SettingsTable> {
  $$SettingsTableOrderingComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  ColumnOrderings<String> get key => $composableBuilder(
    column: $table.key,
    builder: (column) => ColumnOrderings(column),
  );

  ColumnOrderings<String> get value => $composableBuilder(
    column: $table.value,
    builder: (column) => ColumnOrderings(column),
  );
}

class $$SettingsTableAnnotationComposer
    extends Composer<_$AppDatabase, $SettingsTable> {
  $$SettingsTableAnnotationComposer({
    required super.$db,
    required super.$table,
    super.joinBuilder,
    super.$addJoinBuilderToRootComposer,
    super.$removeJoinBuilderFromRootComposer,
  });
  GeneratedColumn<String> get key =>
      $composableBuilder(column: $table.key, builder: (column) => column);

  GeneratedColumn<String> get value =>
      $composableBuilder(column: $table.value, builder: (column) => column);
}

class $$SettingsTableTableManager
    extends
        RootTableManager<
          _$AppDatabase,
          $SettingsTable,
          Setting,
          $$SettingsTableFilterComposer,
          $$SettingsTableOrderingComposer,
          $$SettingsTableAnnotationComposer,
          $$SettingsTableCreateCompanionBuilder,
          $$SettingsTableUpdateCompanionBuilder,
          (Setting, BaseReferences<_$AppDatabase, $SettingsTable, Setting>),
          Setting,
          PrefetchHooks Function()
        > {
  $$SettingsTableTableManager(_$AppDatabase db, $SettingsTable table)
    : super(
        TableManagerState(
          db: db,
          table: table,
          createFilteringComposer: () =>
              $$SettingsTableFilterComposer($db: db, $table: table),
          createOrderingComposer: () =>
              $$SettingsTableOrderingComposer($db: db, $table: table),
          createComputedFieldComposer: () =>
              $$SettingsTableAnnotationComposer($db: db, $table: table),
          updateCompanionCallback:
              ({
                Value<String> key = const Value.absent(),
                Value<String> value = const Value.absent(),
                Value<int> rowid = const Value.absent(),
              }) => SettingsCompanion(key: key, value: value, rowid: rowid),
          createCompanionCallback:
              ({
                required String key,
                required String value,
                Value<int> rowid = const Value.absent(),
              }) => SettingsCompanion.insert(
                key: key,
                value: value,
                rowid: rowid,
              ),
          withReferenceMapper: (p0) => p0
              .map((e) => (e.readTable(table), BaseReferences(db, table, e)))
              .toList(),
          prefetchHooksCallback: null,
        ),
      );
}

typedef $$SettingsTableProcessedTableManager =
    ProcessedTableManager<
      _$AppDatabase,
      $SettingsTable,
      Setting,
      $$SettingsTableFilterComposer,
      $$SettingsTableOrderingComposer,
      $$SettingsTableAnnotationComposer,
      $$SettingsTableCreateCompanionBuilder,
      $$SettingsTableUpdateCompanionBuilder,
      (Setting, BaseReferences<_$AppDatabase, $SettingsTable, Setting>),
      Setting,
      PrefetchHooks Function()
    >;

class $AppDatabaseManager {
  final _$AppDatabase _db;
  $AppDatabaseManager(this._db);
  $$SuratKategoriTableTableManager get suratKategori =>
      $$SuratKategoriTableTableManager(_db, _db.suratKategori);
  $$SuratPengajuanTableTableManager get suratPengajuan =>
      $$SuratPengajuanTableTableManager(_db, _db.suratPengajuan);
  $$MutasiPendudukTableTableManager get mutasiPenduduk =>
      $$MutasiPendudukTableTableManager(_db, _db.mutasiPenduduk);
  $$InformasiPublikTableTableManager get informasiPublik =>
      $$InformasiPublikTableTableManager(_db, _db.informasiPublik);
  $$PendudukProfileTableTableManager get pendudukProfile =>
      $$PendudukProfileTableTableManager(_db, _db.pendudukProfile);
  $$FasilitasDesaTableTableManager get fasilitasDesa =>
      $$FasilitasDesaTableTableManager(_db, _db.fasilitasDesa);
  $$SyncQueueTableTableManager get syncQueue =>
      $$SyncQueueTableTableManager(_db, _db.syncQueue);
  $$SettingsTableTableManager get settings =>
      $$SettingsTableTableManager(_db, _db.settings);
}
