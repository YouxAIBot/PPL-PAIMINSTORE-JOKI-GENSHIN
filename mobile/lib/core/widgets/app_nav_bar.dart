import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

class AppNavBar extends StatelessWidget {
  final int selected;
  const AppNavBar({super.key, required this.selected});

  @override
  Widget build(BuildContext context) {
    return NavigationBar(
      selectedIndex: selected,
      onDestinationSelected: (i) {
        if (i == selected) return;
        if (i == 0) context.go('/home');
        if (i == 1) context.go('/orders');
      },
      destinations: const [
        NavigationDestination(icon: Icon(Icons.home_outlined), label: 'Beranda'),
        NavigationDestination(icon: Icon(Icons.receipt_long_outlined), label: 'Pesanan'),
        NavigationDestination(icon: Icon(Icons.person_outline), label: 'Profil'),
      ],
    );
  }
}
