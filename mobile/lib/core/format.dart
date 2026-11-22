import 'package:intl/intl.dart';

String rupiah(int v) =>
    NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0).format(v);

String estimasi(int hours) =>
    hours < 24 ? 'Est. $hours jam' : 'Est. ±${(hours / 24).ceil()} hari';