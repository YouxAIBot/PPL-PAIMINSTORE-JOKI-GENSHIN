@php
$map = [
    'pending_payment' => 'muted', 'pending' => 'muted',
    'queued' => 'blue', 'in_progress' => 'amber',
    'completed' => 'green', 'paid' => 'green',
    'cancelled' => 'red', 'failed' => 'red', 'refunded' => 'red',
    'banned' => 'red', 'active' => 'green',
];
@endphp
<span class="badge {{ $map[$status] ?? 'muted' }}">{{ str_replace('_', ' ', $status) }}</span>
