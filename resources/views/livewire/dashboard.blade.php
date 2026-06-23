<div class="animate-fade-in">
    {{-- Page Header --}}
    <div class="page-header">
        <h1>Dashboard</h1>
        <p>Selamat datang kembali, <strong>{{ auth()->user()->name }}</strong>! Berikut ringkasan operasional hari ini.</p>
    </div>

    {{-- Stat Cards Row 1 --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px;">
        <div class="stat-card navy">
            <div class="stat-icon navy">🏠</div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['vc'] }}</div>
                <div class="stat-label">Kamar Tersedia (VC)</div>
            </div>
        </div>
        <div class="stat-card success">
            <div class="stat-icon success">🛏️</div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['oc'] }}</div>
                <div class="stat-label">Kamar Terisi (OC)</div>
            </div>
        </div>
        <div class="stat-card warning">
            <div class="stat-icon warning">📅</div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['check_in_today'] }}</div>
                <div class="stat-label">Check-in Hari Ini</div>
            </div>
        </div>
        <div class="stat-card gold">
            <div class="stat-icon gold">💰</div>
            <div class="stat-info">
                <div class="stat-value" style="font-size:18px;">Rp {{ number_format($stats['revenue_month'], 0, ',', '.') }}</div>
                <div class="stat-label">Revenue Bulan Ini</div>
            </div>
        </div>
    </div>

    {{-- Stat Cards Row 2 --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px;">
        <div class="stat-card navy">
            <div class="stat-icon navy">🚪</div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['check_out_today'] }}</div>
                <div class="stat-label">Check-out Hari Ini</div>
            </div>
        </div>
        <div class="stat-card warning">
            <div class="stat-icon warning">🧹</div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['vd'] }}</div>
                <div class="stat-label">Kamar Kotor (VD)</div>
            </div>
        </div>
        <div class="stat-card success">
            <div class="stat-icon success">🍽️</div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['open_fnb_orders'] }}</div>
                <div class="stat-label">Order F&B Aktif</div>
            </div>
        </div>
        <div class="stat-card gold">
            <div class="stat-icon gold">💵</div>
            <div class="stat-info">
                <div class="stat-value" style="font-size:18px;">Rp {{ number_format($stats['revenue_today'], 0, ',', '.') }}</div>
                <div class="stat-label">Revenue Hari Ini</div>
            </div>
        </div>
    </div>

    {{-- Bottom Grid --}}
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;">

        {{-- Recent Reservations --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">📅 Reservasi Terbaru</span>
                <a href="{{ route('reservations.index') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
            </div>
            <div style="overflow:hidden;border-radius:0 0 var(--radius-lg) var(--radius-lg);">
                @if($recentReservations->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">📭</div>
                        <div class="empty-state-title">Belum ada reservasi</div>
                    </div>
                @else
                <table>
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Tamu</th>
                            <th>Kamar</th>
                            <th>Check-in</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentReservations as $r)
                        <tr>
                            <td><span style="font-family:monospace;font-size:12px;font-weight:600;color:var(--navy-700);">{{ $r->booking_code }}</span></td>
                            <td>{{ $r->guest->name }}</td>
                            <td>Kamar {{ $r->room->room_number }}</td>
                            <td>{{ $r->check_in_date->format('d M Y') }}</td>
                            <td><span class="badge badge-{{ $r->status_badge['color'] }}">{{ $r->status_badge['label'] }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        {{-- Quick Actions + Alerts --}}
        <div style="display:flex;flex-direction:column;gap:16px;">

            {{-- Quick Actions --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title">⚡ Aksi Cepat</span>
                </div>
                <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
                    <a href="{{ route('reservations.create') }}" class="btn btn-primary w-full" id="btn-new-reservation">
                        📅 Reservasi Baru
                    </a>
                    <a href="{{ route('fnb.orders.create') }}" class="btn btn-gold w-full" id="btn-new-order">
                        🍽️ Order F&B Baru
                    </a>
                    <a href="{{ route('room-status') }}" class="btn btn-outline w-full" id="btn-room-board">
                        🏠 Room Status Board
                    </a>
                </div>
            </div>

            {{-- Pending HK Tasks --}}
            @if($pendingHkTasks->isNotEmpty())
            <div class="card">
                <div class="card-header">
                    <span class="card-title">🧹 HK Urgent</span>
                    <a href="{{ route('housekeeping.tasks') }}" class="btn btn-outline btn-sm">Lihat</a>
                </div>
                <div class="card-body" style="padding:12px;">
                    @foreach($pendingHkTasks as $task)
                    <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid var(--gray-100);">
                        <div style="font-size:20px;font-weight:800;color:var(--navy-900);">{{ $task->room->room_number }}</div>
                        <div>
                            <div style="font-size:12px;font-weight:600;color:var(--gray-700);">{{ $task->task_type_label }}</div>
                            <span class="badge badge-danger" style="font-size:10px;">URGENT</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Pending F&B --}}
            @if($pendingFnbOrders->isNotEmpty())
            <div class="card">
                <div class="card-header">
                    <span class="card-title">🍽️ F&B Pending</span>
                    <a href="{{ route('fnb.kitchen') }}" class="btn btn-outline btn-sm">KDS</a>
                </div>
                <div class="card-body" style="padding:12px;">
                    @foreach($pendingFnbOrders as $order)
                    <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid var(--gray-100);">
                        <span class="badge badge-warning">{{ $order->order_number }}</span>
                        <span style="font-size:12px;color:var(--gray-600);">
                            {{ $order->room ? 'Kamar '.$order->room->room_number : 'Walk-in' }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
