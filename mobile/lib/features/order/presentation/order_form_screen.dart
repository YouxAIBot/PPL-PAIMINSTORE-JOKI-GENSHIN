import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../data/order_repository.dart';

String? validateUid(String? v) =>
    (v == null || !RegExp(r'^\d{9,10}$').hasMatch(v.trim()))
        ? 'UID harus 9 atau 10 digit'
        : null;

class OrderFormScreen extends ConsumerStatefulWidget {
  final int packageId;
  const OrderFormScreen({super.key, required this.packageId});

  @override
  ConsumerState<OrderFormScreen> createState() => _OrderFormScreenState();
}

class _OrderFormScreenState extends ConsumerState<OrderFormScreen> {
  static const servers = {
    'asia': 'Asia',
    'america': 'America',
    'europe': 'Europe',
    'twhkmo': 'TW, HK, MO',
  };

  final _form = GlobalKey<FormState>();
  final _uid = TextEditingController();
  final _user = TextEditingController();
  final _pass = TextEditingController();
  final _notes = TextEditingController();
  String _server = 'asia';
  bool _agree = false;
  bool _loading = false;

  @override
  void dispose() {
    _uid.dispose();
    _user.dispose();
    _pass.dispose();
    _notes.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_form.currentState!.validate()) return;
    if (!_agree) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Setujui syarat terlebih dahulu')),
      );
      return;
    }
    setState(() => _loading = true);
    try {
      final order = await ref.read(orderRepositoryProvider).create(
        packageId: widget.packageId,
        uid: _uid.text.trim(),
        server: _server,
        username: _user.text.trim(),
        password: _pass.text,
        notes: _notes.text.trim().isEmpty ? null : _notes.text.trim(),
      );
      if (mounted) context.go('/orders/${order.orderNo}/pay');
    } catch (_) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Gagal membuat pesanan')),
        );
      }
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    String? required(String? v) =>
        (v == null || v.trim().isEmpty) ? 'Wajib diisi' : null;

    return Scaffold(
      appBar: AppBar(title: const Text('Form Pesanan')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Form(
          key: _form,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              TextFormField(
                controller: _uid,
                keyboardType: TextInputType.number,
                decoration: const InputDecoration(labelText: 'UID Genshin'),
                validator: validateUid,
              ),
              const SizedBox(height: 12),
              DropdownButtonFormField<String>(
                initialValue: _server,
                decoration: const InputDecoration(labelText: 'Server'),
                items: servers.entries
                    .map((e) => DropdownMenuItem(value: e.key, child: Text(e.value)))
                    .toList(),
                onChanged: (v) => setState(() => _server = v!),
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _user,
                decoration: const InputDecoration(
                    labelText: 'Email / Username HoYoverse'),
                validator: required,
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _pass,
                obscureText: true,
                decoration:
                const InputDecoration(labelText: 'Password HoYoverse'),
                validator: required,
              ),
              const SizedBox(height: 4),
              const Text(
                'Dienkripsi dan dihapus otomatis 7 hari setelah order selesai.',
                style: TextStyle(fontSize: 12),
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _notes,
                maxLines: 3,
                decoration:
                const InputDecoration(labelText: 'Catatan (opsional)'),
              ),
              CheckboxListTile(
                contentPadding: EdgeInsets.zero,
                controlAffinity: ListTileControlAffinity.leading,
                value: _agree,
                onChanged: (v) => setState(() => _agree = v ?? false),
                title: const Text(
                  'Saya paham risiko penggunaan jasa joki dan data akun saya benar.',
                  style: TextStyle(fontSize: 13),
                ),
              ),
              const SizedBox(height: 12),
              FilledButton(
                onPressed: _loading ? null : _submit,
                child: _loading
                    ? const SizedBox(
                  height: 20,
                  width: 20,
                  child: CircularProgressIndicator(strokeWidth: 2),
                )
                    : const Text('Lanjut ke Pembayaran'),
              ),
            ],
          ),
        ),
      ),
    );
  }
}