import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/format.dart';
import '../../../core/widgets/async_view.dart';
import '../data/order_repository.dart';
import '../data/order_status.dart';

class OrderWaitScreen extends ConsumerStatefulWidget {
  final String orderNo;
  const OrderWaitScreen({super.key, required this.orderNo});

  @override
  ConsumerState<OrderWaitScreen> createState() => _OrderWaitScreenState();
}

class _OrderWaitScreenState extends ConsumerState<OrderWaitScreen> {
  static const steps = ['Dibayar', 'Antrian', 'Dikerjakan', 'Selesai'];
  Timer? _poll;

  @override
  void initState() {
    super.initState();
    _poll = Timer.periodic(const Duration(seconds: 30), (_) {
      ref.invalidate(orderDetailProvider(widget.orderNo));
      ref.invalidate(queueProvider(widget.orderNo));
    });
  }

  @override
  void dispose() {
    _poll?.cancel();
    super.dispose();
  }

  String _headline(Order o, QueueInfo? q) {
    switch (o.status) {
      case 'queued':
        return q?.position == null
            ? 'Menunggu antrian'
            : 'Posisi antrian ${q!.position}';
      case 'in_progress':
        return 'Sedang dikerjakan';
      case 'completed':
        return 'Pesanan selesai';
      case 'cancelled':
        return 'Pesanan dibatalkan';
      default:
        return labelOf(o.status);
    }
  }

  @override
  Widget build(BuildContext context) {
    final order = ref.watch(orderDetailProvider(widget.orderNo));
    final queue = ref.watch(queueProvider(widget.orderNo)).asData?.value;
    return Scaffold(
      appBar: AppBar(title: Text('#${widget.orderNo}')),
      body: AsyncView(
        value: order,
        onRetry: () => ref.invalidate(orderDetailProvider(widget.orderNo)),
        builder: (o) => SingleChildScrollView(
          padding: const EdgeInsets.all(24),
          child: Column(
            children: [
              const SizedBox(height: 16),
              Text(
                _headline(o, queue),
                textAlign: TextAlign.center,
                style: Theme.of(context)
                    .textTheme
                    .headlineSmall
                    ?.copyWith(fontWeight: FontWeight.w700),
              ),
              const SizedBox(height: 8),
              Text(o.packageName),
              if (o.status == 'queued' && queue?.estimateHours != null) ...[
                const SizedBox(height: 8),
                Text(estimasi(queue!.estimateHours!)),
              ],
              const SizedBox(height: 24),
              Stepper(
                physics: const NeverScrollableScrollPhysics(),
                currentStep: stepOf(o.status),
                controlsBuilder: (_, _) => const SizedBox.shrink(),
                steps: [
                  for (var i = 0; i < steps.length; i++)
                    Step(
                      title: Text(steps[i]),
                      content: const SizedBox.shrink(),
                      isActive: i <= stepOf(o.status),
                    ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}
