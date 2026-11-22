import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/format.dart';
import '../../../core/widgets/async_view.dart';
import '../data/catalog_providers.dart';

class PackageDetailScreen extends ConsumerWidget {
  final int id;
  const PackageDetailScreen({super.key, required this.id});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final pkg = ref.watch(packageProvider(id));
    return Scaffold(
      appBar: AppBar(title: const Text('Detail Paket')),
      body: AsyncView(
        value: pkg,
        onRetry: () => ref.invalidate(packageProvider(id)),
        builder: (p) => Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                p.name,
                style: Theme.of(context)
                    .textTheme
                    .headlineSmall
                    ?.copyWith(fontWeight: FontWeight.w700),
              ),
              const SizedBox(height: 4),
              Text('${estimasi(p.estimatedHours)}  •  ${rupiah(p.price)}'),
              const SizedBox(height: 16),
              Text(p.description ?? 'Belum ada deskripsi.'),
              const Spacer(),
              FilledButton(
                onPressed: () => context.push('/order/new/${p.id}'),
                child: const Text('Pesan Paket Ini'),
              ),
            ],
          ),
        ),
      ),
    );
  }
}