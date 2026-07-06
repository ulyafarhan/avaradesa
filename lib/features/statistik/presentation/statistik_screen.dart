import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:fl_chart/fl_chart.dart';
import '../../../../core/api/api_client.dart';

final statistikDemografiProvider = FutureProvider<Map<String, dynamic>>((ref) async {
  final dio = ref.read(dioProvider);
  final response = await dio.get('/statistik/demografi');
  final data = response.data as Map<String, dynamic>;
  return (data['data'] as Map<String, dynamic>?) ?? {};
});

final statistikLayananProvider = FutureProvider<Map<String, dynamic>>((ref) async {
  final dio = ref.read(dioProvider);
  final response = await dio.get('/statistik/layanan');
  final data = response.data as Map<String, dynamic>;
  return (data['data'] as Map<String, dynamic>?) ?? {};
});

class StatistikScreen extends ConsumerWidget {
  const StatistikScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final demografi = ref.watch(statistikDemografiProvider);
    final layanan = ref.watch(statistikLayananProvider);

    return Scaffold(
      appBar: AppBar(title: const Text('Statistik Desa')),
      body: RefreshIndicator(
        onRefresh: () async {
          ref.invalidate(statistikDemografiProvider);
          ref.invalidate(statistikLayananProvider);
        },
        child: ListView(
          padding: const EdgeInsets.all(16),
          children: [
            Text('Demografi Penduduk', style: Theme.of(context).textTheme.titleLarge),
            const SizedBox(height: 16),
            demografi.when(
              loading: () => const SizedBox(height: 200, child: Center(child: CircularProgressIndicator())),
              error: (e, _) => Card(child: Padding(
                padding: const EdgeInsets.all(16),
                child: Text('Gagal memuat data demografi'),
              )),
              data: (data) => _DemografiSection(data: data),
            ),
            const SizedBox(height: 32),
            Text('Statistik Layanan', style: Theme.of(context).textTheme.titleLarge),
            const SizedBox(height: 16),
            layanan.when(
              loading: () => const SizedBox(height: 200, child: Center(child: CircularProgressIndicator())),
              error: (e, _) => Card(child: Padding(
                padding: const EdgeInsets.all(16),
                child: Text('Gagal memuat data layanan'),
              )),
              data: (data) => _LayananSection(data: data),
            ),
          ],
        ),
      ),
    );
  }
}

class _DemografiSection extends StatelessWidget {
  final Map<String, dynamic> data;
  const _DemografiSection({required this.data});

  @override
  Widget build(BuildContext context) {
    final jenisKelamin = data['jenis_kelamin'] as Map<String, dynamic>? ?? {};
    final totalPenduduk = data['total_penduduk'] as int? ?? 0;
    final laki = jenisKelamin['laki_laki'] as int? ?? 0;
    final perempuan = jenisKelamin['perempuan'] as int? ?? 0;

    return Column(
      children: [
        Card(
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceAround,
              children: [
                _StatItem(label: 'Total', value: totalPenduduk.toString(), icon: Icons.people),
                _StatItem(label: 'Laki-laki', value: laki.toString(), icon: Icons.male),
                _StatItem(label: 'Perempuan', value: perempuan.toString(), icon: Icons.female),
              ],
            ),
          ),
        ),
        const SizedBox(height: 16),
        Card(
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Perbandingan Jenis Kelamin', style: Theme.of(context).textTheme.titleSmall),
                const SizedBox(height: 16),
                SizedBox(
                  height: 200,
                  child: PieChart(
                    PieChartData(
                      sections: [
                        PieChartSectionData(
                          value: laki.toDouble(),
                          title: '$laki',
                          color: Colors.blue,
                          radius: 60,
                        ),
                        PieChartSectionData(
                          value: perempuan.toDouble(),
                          title: '$perempuan',
                          color: Colors.pink,
                          radius: 60,
                        ),
                      ],
                      sectionsSpace: 2,
                      centerSpaceRadius: 40,
                    ),
                  ),
                ),
                const SizedBox(height: 8),
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    _Legend(color: Colors.blue, label: 'Laki-laki'),
                    const SizedBox(width: 24),
                    _Legend(color: Colors.pink, label: 'Perempuan'),
                  ],
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }
}

class _LayananSection extends StatelessWidget {
  final Map<String, dynamic> data;
  const _LayananSection({required this.data});

  @override
  Widget build(BuildContext context) {
    final surat = data['surat'] as Map<String, dynamic>? ?? {};
    final totalSurat = surat['total'] as int? ?? 0;
    final disetujui = surat['disetujui'] as int? ?? 0;
    final ditolak = surat['ditolak'] as int? ?? 0;
    final menunggu = surat['menunggu'] as int? ?? 0;

    return Column(
      children: [
        Card(
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Pengajuan Surat', style: Theme.of(context).textTheme.titleSmall),
                const SizedBox(height: 16),
                SizedBox(
                  height: 200,
                  child: BarChart(
                    BarChartData(
                      alignment: BarChartAlignment.spaceAround,
                      maxY: (totalSurat > 0 ? totalSurat * 1.2 : 10).toDouble(),
                      barGroups: [
                        _makeBarGroup(0, 'Disetujui', disetujui.toDouble(), Colors.green),
                        _makeBarGroup(1, 'Menunggu', menunggu.toDouble(), Colors.amber),
                        _makeBarGroup(2, 'Ditolak', ditolak.toDouble(), Colors.red),
                      ],
                      barTouchData: BarTouchData(enabled: false),
                      titlesData: FlTitlesData(
                        show: true,
                        bottomTitles: AxisTitles(
                          sideTitles: SideTitles(
                            showTitles: true,
                            getTitlesWidget: (value, meta) {
                              final labels = ['Disetujui', 'Menunggu', 'Ditolak'];
                              return Padding(
                                padding: const EdgeInsets.only(top: 8),
                                child: Text(labels[value.toInt()], style: const TextStyle(fontSize: 12)),
                              );
                            },
                          ),
                        ),
                        leftTitles: AxisTitles(sideTitles: SideTitles(showTitles: false)),
                        topTitles: AxisTitles(sideTitles: SideTitles(showTitles: false)),
                        rightTitles: AxisTitles(sideTitles: SideTitles(showTitles: false)),
                      ),
                      gridData: FlGridData(show: false),
                      borderData: FlBorderData(show: false),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }

  BarChartGroupData _makeBarGroup(int x, String label, double value, Color color) {
    return BarChartGroupData(x: x, barRods: [
      BarChartRodData(toY: value, color: color, width: 24, borderRadius: const BorderRadius.vertical(top: Radius.circular(4))),
    ]);
  }
}

class _StatItem extends StatelessWidget {
  final String label;
  final String value;
  final IconData icon;
  const _StatItem({required this.label, required this.value, required this.icon});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Icon(icon, color: Theme.of(context).colorScheme.primary),
        const SizedBox(height: 4),
        Text(value, style: Theme.of(context).textTheme.titleLarge?.copyWith(fontWeight: FontWeight.bold)),
        Text(label, style: Theme.of(context).textTheme.bodySmall),
      ],
    );
  }
}

class _Legend extends StatelessWidget {
  final Color color;
  final String label;
  const _Legend({required this.color, required this.label});

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Container(width: 12, height: 12, decoration: BoxDecoration(color: color, shape: BoxShape.circle)),
        const SizedBox(width: 4),
        Text(label, style: Theme.of(context).textTheme.bodySmall),
      ],
    );
  }
}
