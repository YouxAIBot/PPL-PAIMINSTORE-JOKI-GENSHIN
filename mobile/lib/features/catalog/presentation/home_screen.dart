import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/widgets/async_view.dart';
import '../../auth/data/auth_repository.dart';
import '../data/catalog_providers.dart';

class HomeScreen extends ConsumerWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final cats = ref.watch(categoriesProvider);
    return Scaffold(
      appBar: AppBar(
        title: const Text('Paiminstore'),
        actions: [
          IconButton(
            icon: const Icon(Icons.logout),
            tooltip: 'Keluar',
            onPressed: () async {
              await ref.read(authRepositoryProvider).logout();
              if (context.mounted) context.go('/auth');
            },
          ),
        ],
      ),
      bottomNavigationBar: NavigationBar(
        selectedIndex: 0,
        onDestinationSelected: (i) {
          // Pesanan dan Profil ditambahkan di langkah berikutnya
        },
        destinations: const [
          NavigationDestination(icon: Icon(Icons.home_outlined), label: 'Beranda'),
          NavigationDestination(icon: Icon(Icons.receipt_long_outlined), label: 'Pesanan'),
          NavigationDestination(icon: Icon(Icons.person_outline), label: 'Profil'),
        ],
      ),
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