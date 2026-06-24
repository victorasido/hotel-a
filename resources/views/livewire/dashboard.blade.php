<div class="animate-fade-in">
    {{-- Page Header --}}
    <div class="page-header" style="margin-bottom: 16px;">
        <h1>Dashboard</h1>
        <p>Selamat datang kembali, <strong>{{ auth()->user()->name }}</strong>! Berikut ringkasan operasional hari ini.</p>
    </div>

    {{-- Tab Navigation — Room Status hanya untuk Admin & Front Office --}}
    <div class="page-tabs">
        <button wire:click="setTab('summary')" class="page-tab {{ $activeTab === 'summary' ? 'active' : '' }}" id="tab-btn-summary">
            📊 Ringkasan
        </button>
        @hasanyrole('Super Admin|Front Office')
        <button wire:click="setTab('room-status')" class="page-tab {{ $activeTab === 'room-status' ? 'active' : '' }}" id="tab-btn-room-status">
            🏠 Status Kamar
        </button>
        @endhasanyrole
    </div>

    {{-- Tab: Room Status --}}
    @if($activeTab === 'room-status')
    <div class="tab-content" wire:key="dashboard-tab-room-status">
        <livewire:room-status-board key="dashboard-room-status-board" />
    </div>
    @endif

    {{-- Tab: Summary --}}
    @if($activeTab === 'summary')
    <div class="tab-content" wire:key="dashboard-tab-summary">

    {{-- Role: Super Admin & Front Office (Rooms Stats) --}}
    @hasanyrole('Super Admin|Front Office')
    <h3 style="margin-bottom: 12px; font-size: 16px; color: var(--navy-800);">Status Kamar & Reservasi</h3>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px;">
        <div class="stat-card navy">
            <div class="stat-icon navy">🏠</div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['vc'] ?? 0 }}</div>
                <div class="stat-label">Kamar Tersedia (VC)</div>
            </div>
        </div>
        <div class="stat-card success">
            <div class="stat-icon success">🛏️</div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['oc'] ?? 0 }}</div>
                <div class="stat-label">Kamar Terisi (OC)</div>
            </div>
        </div>
        <div class="stat-card warning">
            <div class="stat-icon warning">📅</div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['check_in_today'] ?? 0 }}</div>
                <div class="stat-label">Check-in Hari Ini</div>
            </div>
        </div>
        <div class="stat-card navy">
            <div class="stat-icon navy">🚪</div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['check_out_today'] ?? 0 }}</div>
                <div class="stat-label">Check-out Hari Ini</div>
            </div>
        </div>
    </div>
    @endhasanyrole

    {{-- Role: Super Admin (Revenue) --}}
    @role('Super Admin')
    <h3 style="margin-bottom: 12px; font-size: 16px; color: var(--navy-800);">Keuangan</h3>
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-bottom:28px;">
        <div class="stat-card gold">
            <div class="stat-icon gold">💵</div>
            <div class="stat-info">
                <div class="stat-value" style="font-size:18px;">Rp {{ number_format($stats['revenue_today'] ?? 0, 0, ',', '.') }}</div>
                <div class="stat-label">Revenue Hari Ini</div>
            </div>
        </div>
        <div class="stat-card gold">
            <div class="stat-icon gold">💰</div>
            <div class="stat-info">
                <div class="stat-value" style="font-size:18px;">Rp {{ number_format($stats['revenue_month'] ?? 0, 0, ',', '.') }}</div>
                <div class="stat-label">Revenue Bulan Ini</div>
            </div>
        </div>
    </div>
    @endrole

    {{-- Role: Super Admin, FnB, Housekeeping --}}
    @if(auth()->user()->hasAnyRole(['Super Admin', 'FnB', 'Housekeeping']))
    <h3 style="margin-bottom: 12px; font-size: 16px; color: var(--navy-800);">Operasional Lainnya</h3>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:28px;">
        
        @hasanyrole('Super Admin|Housekeeping')
        <div class="stat-card warning">
            <div class="stat-icon warning">🧹</div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['vd'] ?? 0 }}</div>
                <div class="stat-label">Kamar Kotor (VD)</div>
            </div>
        </div>
        <div class="stat-card danger">
            <div class="stat-icon danger" style="color:var(--danger-600);background:var(--danger-50);">🚨</div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['hk_pending'] ?? 0 }}</div>
                <div class="stat-label">Task HK Pending</div>
            </div>
        </div>
        @endhasanyrole

        @hasanyrole('Super Admin|FnB')
        <div class="stat-card success">
            <div class="stat-icon success">🍽️</div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['open_fnb_orders'] ?? 0 }}</div>
                <div class="stat-label">Order F&B Aktif</div>
            </div>
        </div>
        @endhasanyrole

    </div>
    @endif

    {{-- Bottom Grid --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(300px, 1fr));gap:20px;">

        @hasanyrole('Super Admin|Front Office')
        {{-- Recent Reservations --}}
        <div class="card" style="grid-column: span 2;">
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
        @endhasanyrole

        <div style="display:flex;flex-direction:column;gap:20px;">
            @hasanyrole('Super Admin|Front Office')
            <div class="card">
                <div class="card-header">
                    <span class="card-title">⚡ Aksi Cepat</span>
                </div>
                <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
                    <a href="{{ route('reservations.create') }}" class="btn btn-primary w-full">📅 Reservasi Baru</a>
                    <a href="{{ route('fnb.portal') }}" class="btn btn-gold w-full">🍽️ Order F&B Baru</a>
                    <a href="{{ route('dashboard') }}" wire:click="setTab('room-status')" class="btn btn-outline w-full">🏠 Room Status Board</a>
                </div>
            </div>
            @endhasanyrole

            @hasanyrole('Super Admin|Housekeeping')
            @if($pendingHkTasks->isNotEmpty())
            <div class="card">
                <div class="card-header">
                    <span class="card-title">🧹 HK Urgent</span>
                    <a href="{{ route('housekeeping.tasks') }}" class="btn btn-outline btn-sm">Task Board</a>
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
            @endhasanyrole

            @hasanyrole('Super Admin|FnB')
            @if($pendingFnbOrders->isNotEmpty())
            <div class="card">
                <div class="card-header">
                    <span class="card-title">🍽️ F&B Pending</span>
                    <a href="{{ route('fnb.portal') }}" class="btn btn-outline btn-sm">KDS</a>
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
            @endhasanyrole
        </div>
        
    </div>
    </div> {{-- /tab-content summary --}}
    @endif

</div>
