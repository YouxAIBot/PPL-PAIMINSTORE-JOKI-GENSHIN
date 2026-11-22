import 'package:flutter/material.dart';
import 'package:webview_flutter/webview_flutter.dart';

class SnapWebView extends StatefulWidget {
  final String url;
  const SnapWebView({super.key, required this.url});

  @override
  State<SnapWebView> createState() => _SnapWebViewState();
}

class _SnapWebViewState extends State<SnapWebView> {
  static const _fakeHtml = '''
<html><body style="font-family:sans-serif;padding:24px">
<h3>Midtrans Snap (simulasi)</h3>
<p>Halaman ini pengganti Snap asli selama Backend belum siap.</p>
<p><a href="https://paiminstore.local/payment/finish">Simulasi: bayar berhasil</a></p>
<p><a href="https://paiminstore.local/payment/error">Simulasi: pembayaran gagal</a></p>
</body></html>''';

  late final WebViewController _controller;

  @override
  void initState() {
    super.initState();
    _controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setNavigationDelegate(NavigationDelegate(
        onNavigationRequest: (req) {
          final u = req.url;
          if (u.contains('/payment/finish')) {
            _close(true);
            return NavigationDecision.prevent;
          }
          if (u.contains('/payment/error') || u.contains('/payment/unfinish')) {
            _close(false);
            return NavigationDecision.prevent;
          }
          return NavigationDecision.navigate;
        },
      ));
    if (widget.url.startsWith('fake://')) {
      _controller.loadHtmlString(_fakeHtml);
    } else {
      _controller.loadRequest(Uri.parse(widget.url));
    }
  }

  void _close(bool result) {
    if (mounted) Navigator.of(context).pop(result);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Pembayaran')),
      body: WebViewWidget(controller: _controller),
    );
  }
}
