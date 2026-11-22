import 'package:flutter_test/flutter_test.dart';
import 'package:paiminstore_mobile/core/format.dart';

void main() {
  test('rupiah memakai pemisah titik', () {
    expect(rupiah(175000), contains('175.000'));
  });

  test('estimasi jam dan hari', () {
    expect(estimasi(12), 'Est. 12 jam');
    expect(estimasi(60), 'Est. ±3 hari');
  });
}