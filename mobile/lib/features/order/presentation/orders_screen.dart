import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/format.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/app_nav_bar.dart';
import '../../../core/widgets/async_view.dart';
import '../data/order_repository.dart';
import '../data/order_status.dart';

class OrdersScreen extends ConsumerWidget {
  const OrdersScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final orders = ref.watch(ordersProvider);
    return DefaultTabController(
      length: 4,
      child: Scaffold(
        appBar: AppBar(
          title: const Text('Pesanan'),
          bottom: const TabBar(tabs: [
            Tab(text: 'Semua'),
            Tab(text: 'Menunggu'),
            Tab(text: 'Dikerjakan'),
            Tab(text: 'Selesai'),
          ]),
        ),
        bottomNavigationBar: const AppNavBar(selected: 1),
        body: AsyncView(
          value: orders,
          onRetry: () => ref.invalidate(ordersProvider),
          builder: (all) => TabBarView(
            children: List.generate(4, (tab) {
              final items =
                  all.where((o) => matchesTab(tab, o.status)).toList();
              if (items.isEmpty) {
                return const Center(child: Text('Belum ada pesanan'));
              }
              return RefreshIndicator(
                onRefresh: () async => ref.invalidate(ordersProvider),
                child: ListView.separated(
                  itemCount: items.length,
                  separatorBuilder: (_, _) => const Divider(height: 1),
                  itemBuilder: (_, i) => _OrderTile(order: items[i]),
                ),
              );
            }),
          ),
        ),
      ),
    );
  }
}

class _OrderTile extends StatelessWidget {
  final Order order;
  const _OrderTile({required this.order});

  @override
  Widget build(BuildContext context) {
    final cancelled = order.status == 'cancelled';
    return ListTile(
      title: Text('#${order.orderNo}'),
      subtitle: Text(order.packageName),
      trailing: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        crossAxisAlignment: CrossAxisAlignment.end,
        children: [
          Text(rupiah(order.total),
              style: const TextStyle(fontWeight: FontWeight.w600)),
          Text(
            labelOf(order.status),
            style: TextStyle(
              fontSize: 12,
              color: cancelled ? AppColors.danger : AppColors.textMuted,
            ),
          ),
        ],
      ),
      onTap: () {
        if (cancelled) return;
        context.push(order.status == 'pending_payment'
            ? '/orders/${order.orderNo}/pay'
            : '/orders/${order.orderNo}/wait');
      },
    );
  }
}
