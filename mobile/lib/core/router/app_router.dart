import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../features/auth/data/auth_repository.dart';
import '../../features/auth/presentation/auth_screen.dart';
import '../../features/catalog/presentation/home_screen.dart';
import '../../features/catalog/presentation/package_detail_screen.dart';
import '../../features/catalog/presentation/package_list_screen.dart';

final routerProvider = Provider<GoRouter>((ref) {
  return GoRouter(
    initialLocation: '/home',
    redirect: (context, state) async {
      final logged = await ref.read(authRepositoryProvider).hasSession();
      final atAuth = state.matchedLocation == '/auth';
      if (!logged && !atAuth) return '/auth';
      if (logged && atAuth) return '/home';
      return null;
    },
    routes: [
      GoRoute(path: '/auth', builder: (_, _) => const AuthScreen()),
      GoRoute(path: '/home', builder: (_, _) => const HomeScreen()),
      GoRoute(
        path: '/category/:slug',
        builder: (_, s) => PackageListScreen(slug: s.pathParameters['slug']!),
      ),
      GoRoute(
        path: '/package/:id',
        builder: (_, s) =>
            PackageDetailScreen(id: int.parse(s.pathParameters['id']!)),
      ),
      GoRoute(
        path: '/order/new/:packageId',
        builder: (_, s) => Scaffold(
          appBar: AppBar(title: const Text('Form Pesanan')),
          body: Center(
            child: Text('Form untuk paket ${s.pathParameters['packageId']} (Langkah 5)'),
          ),
        ),
      ),
    ],
  );
});