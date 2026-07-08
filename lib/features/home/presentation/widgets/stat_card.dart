import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/api/api_client.dart';

final homeStatProvider = FutureProvider<Map<String, dynamic>>((ref) async {
  final dio = ref.read(dioProvider);
  final response = await dio.get('/statistik/demografi');
  final data = response.data as Map<String, dynamic>;
  return (data['data'] as Map<String, dynamic>?) ?? {};
});

class StatCard extends ConsumerWidget {
  const StatCard({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final stat = ref.watch(homeStatProvider);

    return stat.when(
      loading: () => Card(
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: List.generate(3, (_) => const _ShimmerStat()),
          ),
        ),
      ),
      error: (e, _) => Card(
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              _StatItem(icon: Icons.people, label: 'Penduduk', value: '-'),
              _StatItem(icon: Icons.description, label: 'Surat', value: '-'),
              _StatItem(icon: Icons.check_circle, label: 'Selesai', value: '-'),
            ],
          ),
        ),
      ),
      data: (data) {
        final totalPenduduk = data['total_penduduk'] as int? ?? 0;
        return Card(
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceAround,
              children: [
                _StatItem(icon: Icons.people, label: 'Penduduk', value: totalPenduduk.toString()),
                _StatItem(icon: Icons.description, label: 'Surat', value: '-'),
                _StatItem(icon: Icons.check_circle, label: 'Selesai', value: '-'),
              ],
            ),
          ),
        );
      },
    );
  }
}

class _StatItem extends StatelessWidget {
  final IconData icon;
  final String label;
  final String value;
  const _StatItem({required this.icon, required this.label, required this.value});

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

class _ShimmerStat extends StatelessWidget {
  const _ShimmerStat();

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Container(width: 24, height: 24, decoration: BoxDecoration(color: Colors.grey[300], shape: BoxShape.circle)),
        const SizedBox(height: 4),
        Container(width: 30, height: 16, color: Colors.grey[300]),
        const SizedBox(height: 2),
        Container(width: 50, height: 12, color: Colors.grey[300]),
      ],
    );
  }
}
