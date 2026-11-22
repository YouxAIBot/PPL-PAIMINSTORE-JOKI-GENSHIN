import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/app_nav_bar.dart';
import '../../../core/widgets/async_view.dart';
import '../../auth/data/auth_repository.dart';
import '../data/profile_repository.dart';

class ProfileScreen extends ConsumerWidget {
  const ProfileScreen({super.key});

  Future<void> _logout(BuildContext context, WidgetRef ref) async {
    final ok = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        title: const Text('Keluar'),
        content: const Text('Yakin ingin keluar dari akun ini?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(false),
            child: const Text('Batal'),
          ),
          TextButton(
            onPressed: () => Navigator.of(context).pop(true),
            child: const Text('Keluar'),
          ),
        ],
      ),
    );
    if (ok != true) return;
    await ref.read(authRepositoryProvider).logout();
    if (context.mounted) context.go('/auth');
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final profile = ref.watch(profileProvider);
    return Scaffold(
      appBar: AppBar(title: const Text('Profil')),
      bottomNavigationBar: const AppNavBar(selected: 2),
      body: AsyncView(
        value: profile,
        onRetry: () => ref.invalidate(profileProvider),
        builder: (p) => ListView(
          padding: const EdgeInsets.all(16),
          children: [
            const SizedBox(height: 8),
            Center(
              child: CircleAvatar(
                radius: 36,
                backgroundColor: AppColors.accentSoft,
                child: Text(
                  p.name.isEmpty ? '?' : p.name[0].toUpperCase(),
                  style: const TextStyle(
                    fontSize: 28,
                    fontWeight: FontWeight.w700,
                    color: AppColors.accent,
                  ),
                ),
              ),
            ),
            const SizedBox(height: 12),
            Center(
              child: Text(
                p.name,
                style: Theme.of(context)
                    .textTheme
                    .titleLarge
                    ?.copyWith(fontWeight: FontWeight.w700),
              ),
            ),
            Center(child: Text(p.email)),
            if (!p.emailVerified)
              const Padding(
                padding: EdgeInsets.only(top: 4),
                child: Center(
                  child: Text(
                    'Email belum diverifikasi',
                    style: TextStyle(color: AppColors.warning, fontSize: 12),
                  ),
                ),
              ),
            const SizedBox(height: 24),
            const Divider(height: 1),
            ListTile(
              title: const Text('Ubah Profil'),
              trailing: const Icon(Icons.chevron_right),
              onTap: () => context.push('/profile/edit'),
            ),
            const Divider(height: 1),
            ListTile(
              title: const Text('Ganti Password'),
              trailing: const Icon(Icons.chevron_right),
              onTap: () => context.push('/profile/password'),
            ),
            const Divider(height: 1),
            ListTile(
              title: const Text(
                'Keluar',
                style: TextStyle(color: AppColors.danger),
              ),
              onTap: () => _logout(context, ref),
            ),
            const Divider(height: 1),
          ],
        ),
      ),
    );
  }
}
