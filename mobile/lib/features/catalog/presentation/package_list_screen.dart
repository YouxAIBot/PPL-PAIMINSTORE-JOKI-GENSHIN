import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/format.dart';
import '../../../core/widgets/async_view.dart';
import '../data/catalog_providers.dart';

class PackageListScreen extends ConsumerWidget {
  final String slug;
  const PackageListScreen({super.key, required this.slug});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final pkgs = ref.watch(packagesProvider(slug));
    return Scaffold(
      appBar: AppBar(title: const Text('Pilih Paket')),
      body: AsyncView(
        value: pkgs,
        isEmpty: (l) => l.isEmpty,
        onRetry: () => ref.invalidate(packagesProvider(slug)),
        builder: (list) => ListView.separated(
          itemCount: list.length,
          separatorBuilder: (_, _) => const Divider(height: 1),
          itemBuilder: (_, i) => ListTile(
            title: Text(list[i].name),
            subtitle: Text(estimasi(list[i].estimatedHours)),
            trailing: Text(
              rupiah(list[i].price),
              style: const TextStyle(fontWeight: FontWeight.w600),
            ),
            onTap: () => context.push('/package/${list[i].id}'),
          ),
        ),
      ),
    );
  }
}