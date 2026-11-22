const statusLabel = {
  'pending_payment': 'Menunggu Pembayaran',
  'queued': 'Menunggu Antrian',
  'in_progress': 'Dikerjakan',
  'completed': 'Selesai',
  'cancelled': 'Dibatalkan',
};

String labelOf(String status) => statusLabel[status] ?? status;

bool matchesTab(int tab, String status) => switch (tab) {
      1 => status == 'pending_payment' || status == 'queued',
      2 => status == 'in_progress',
      3 => status == 'completed',
      _ => true,
    };

int stepOf(String status) => switch (status) {
      'queued' => 1,
      'in_progress' => 2,
      'completed' => 3,
      _ => 0,
    };
