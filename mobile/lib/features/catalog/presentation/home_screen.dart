import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/widgets/app_nav_bar.dart';
import '../../../core/widgets/async_view.dart';
import '../data/catalog_providers.dart';

class HomeScreen extends ConsumerWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final cats = ref.watch(categoriesProvider);
    return Scaffold(
      appBar: AppBar(
        title: const Text('Paiminstore'),
      ),
      bottomNavigationBar: const AppNavBar(selected: 0),
      body: AsyncView(
        value: cats,
        isEmpty: (l) => l.isEmpty,
        onRetry: () => ref.invalidate(categoriesProvider),
        builder: (list) => ListView.separated(
          padding: const EdgeInsets.all(16),
          itemCount: list.length,
          separatorBuilder: (_, _) => const Divider(height: 1),
          itemBuilder: (_, i) => ListTile(
            title: Text(list[i].name),
            trailing: const Icon(Icons.chevron_right),
            onTap: () => context.push('/category/${list[i].slug}'),
          ),
        ),
      ),
    );
  }
}