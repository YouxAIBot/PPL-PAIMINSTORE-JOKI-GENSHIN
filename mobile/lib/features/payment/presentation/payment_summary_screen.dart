import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/format.dart';
import '../../../core/widgets/async_view.dart';
import '../../order/data/order_repository.dart';
import '../data/payment_repository.dart';
import 'snap_webview.dart';

class PaymentSummaryScreen extends ConsumerStatefulWidget {
  final String orderNo;
  const PaymentSummaryScreen({super.key, required this.orderNo});

  @override
  ConsumerState<PaymentSummaryScreen> createState() =>
      _PaymentSummaryScreenState();
}

class _PaymentSummaryScreenState extends ConsumerState<PaymentSummaryScreen> {
  Timer? _timer;
  bool _busy = false;

  @override
  void initState() {
    super.initState();
    _timer = Timer.periodic(const Duration(seconds: 1), (_) {
      if (mounted) setState(() {});
    });
  }

  @override
  void dispose() {
    _timer?.cancel();
    super.dispose();
  }

  String _fmt(Duration d) {
    String two(int n) => n.toString().padLeft(2, '0');
    return '${two(d.inHours)}:${two(d.inMinutes % 60)}:${two(d.inSeconds % 60)}';
  }

  void _snack(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg)));
  }

  Future<void> _pay() async {
    setState(() => _busy = true);
    final repo = ref.read(paymentRepositoryProvider);
    try {
      final link = await repo.createGateway(widget.orderNo);
      if (!mounted) return;
      final finished = await Navigator.of(context).push<bool>(
        MaterialPageRoute(builder: (_) => SnapWebView(url: link.redirectUrl)),
      );
      if (finished != true) return;

      _snack('Memeriksa pembayaran...');
      final paid = await waitPaid(repo, widget.orderNo);
      if (!mounted) return;
      ref.invalidate(orderDetailProvider(widget.orderNo));
      if (paid) {
        context.go('/orders/${widget.orderNo}/wait');
      } else {
        _snack('Pembayaran belum terkonfirmasi. Cek lagi di menu Pesanan.');
      }
    } catch (_) {
      if (mounted) _snack('Gagal memulai pembayaran');
    } finally {
      if (mounted) setState(() => _busy = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final order = ref.watch(orderDetailProvider(widget.orderNo));
    return Scaffold(
      appBar: AppBar(title: const Text('Ringkasan Pembayaran')),
      body: AsyncView(
        value: order,
        onRetry: () => ref.invalidate(orderDetailProvider(widget.orderNo)),
        builder: (o) {
          final remaining = o.expiresAt?.difference(DateTime.now());
          final expired = remaining != null && remaining.isNegative;
          return Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('#${o.orderNo}',
                    style: const TextStyle(fontWeight: FontWeight.w700)),
                const SizedBox(height: 8),
                Text(o.packageName),
                const SizedBox(height: 4),
                Text(
                  rupiah(o.total),
                  style: Theme.of(context)
                      .textTheme
                      .headlineSmall
                      ?.copyWith(fontWeight: FontWeight.w700),
                ),
                const SizedBox(height: 12),
                if (remaining != null)
                  Text(expired
                      ? 'Waktu pembayaran habis'
                      : 'Bayar dalam ${_fmt(remaining)}'),
                const SizedBox(height: 24),
                const ListTile(
                  contentPadding: EdgeInsets.zero,
                  leading: Icon(Icons.check_circle),
                  title: Text('Pembayaran Online (Midtrans)'),
                ),
                const ListTile(
                  enabled: false,
                  contentPadding: EdgeInsets.zero,
                  leading: Icon(Icons.circle_outlined),
                  title: Text('Transfer Manual'),
                  trailing: Text('Segera'),
                ),
                const Spacer(),
                FilledButton(
                  onPressed: (_busy || expired) ? null : _pay,
                  child: _busy
                      ? const SizedBox(
                          height: 20,
                          width: 20,
                          child: CircularProgressIndicator(strokeWidth: 2),
                        )
                      : const Text('Bayar Sekarang'),
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}
