import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../features/auth/data/auth_repository.dart';
import '../../features/auth/presentation/auth_screen.dart';
import '../../features/catalog/presentation/home_screen.dart';
import '../../features/catalog/presentation/package_detail_screen.dart';
import '../../features/catalog/presentation/package_list_screen.dart';
import '../../features/order/presentation/order_form_screen.dart';
import '../../features/order/presentation/order_wait_screen.dart';
import '../../features/order/presentation/orders_screen.dart';
import '../../features/payment/presentation/payment_summary_screen.dart';
import '../../features/profile/presentation/change_password_screen.dart';
import '../../features/profile/presentation/edit_profile_screen.dart';
import '../../features/profile/presentation/profile_screen.dart';

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
        builder: (_, s) => OrderFormScreen(
          packageId: int.parse(s.pathParameters['packageId']!),
        ),
      ),
      GoRoute(
        path: '/orders/:no/pay',
        builder: (_, s) =>
            PaymentSummaryScreen(orderNo: s.pathParameters['no']!),
      ),
      GoRoute(path: '/orders', builder: (_, _) => const OrdersScreen()),
      GoRoute(path: '/profile', builder: (_, _) => const ProfileScreen()),
      GoRoute(path: '/profile/edit', builder: (_, _) => const EditProfileScreen()),
      GoRoute(
        path: '/profile/password',
        builder: (_, _) => const ChangePasswordScreen(),
      ),
      GoRoute(
        path: '/orders/:no/wait',
        builder: (_, s) => OrderWaitScreen(orderNo: s.pathParameters['no']!),
      ),
    ],
  );
});