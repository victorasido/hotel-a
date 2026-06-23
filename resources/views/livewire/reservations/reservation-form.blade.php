<div class="animate-fade-in">
    <div class="page-header flex items-center justify-between">
        <div>
            <h1>{{ $reservationId ? 'Edit Reservasi' : 'Reservasi Baru' }}</h1>
            <p>Isi form di bawah untuk membuat reservasi kamar.</p>
        </div>
        <a href="{{ route('reservations.index') }}" class="btn btn-outline">← Kembali</a>
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;align-items:start;">

        {{-- Main Form --}}
        <div style="display:flex;flex-direction:column;gap:16px;">

            {{-- Guest Selection --}}
            <div class="card">
                <div class="card-header"><span class="card-title">👤 Data Tamu</span></div>
                <div class="card-body">
                    @if(!$guest_id)
                    <div style="position:relative;">
                        <label class="form-label">Cari Tamu<span class="required">*</span></label>
                        <input type="text" class="form-control" wire:model.live="guestSearch" wire:input="searchGuest" placeholder="Ketik nama atau nomor KTP tamu...">
                        @error('guest_id') <div class="form-error">{{ $message }}</div> @enderror

                        @if($showGuestList && count($guestResults) > 0)
                        <div style="position:absolute;top:100%;left:0;right:0;background:white;border:1px solid var(--gray-200);border-radius:var(--radius-md);box-shadow:var(--shadow-md);z-index:10;margin-top:4px;">
                            @foreach($guestResults as $g)
                            <div wire:click="selectGuest({{ $g->id }})" style="padding:12px 16px;cursor:pointer;border-bottom:1px solid var(--gray-100);transition:background .15s;" onmouseover="this.style.background='var(--gray-50)'" onmouseout="this.style.background='white'">
                                <div style="font-weight:600;">{{ $g->name }}</div>
                                <div style="font-size:12px;color:var(--gray-400);">{{ $g->id_card_type }}: {{ $g->id_card_number ?? '—' }} | {{ $g->phone ?? '—' }}</div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <div class="mt-2" style="display:flex;gap:8px;align-items:center;">
                        <span style="font-size:12px;color:var(--gray-400);">Tamu baru?</span>
                        <button type="button" class="btn btn-outline btn-sm" wire:click="$toggle('showNewGuestForm')" id="btn-toggle-new-guest">
                            {{ $showNewGuestForm ? '— Tutup Form' : '+ Tamu Baru' }}
                        </button>
                    </div>

                    @if($showNewGuestForm)
                    <div style="background:var(--gray-50);border-radius:var(--radius-md);padding:16px;margin-top:12px;border:1px solid var(--gray-200);">
                        <div class="form-grid-2">
                            <div class="form-group" style="grid-column:1/-1;">
                                <label class="form-label">Nama Lengkap<span class="required">*</span></label>
                                <input type="text" class="form-control" wire:model="new_guest_name" placeholder="Nama tamu">
                                @error('new_guest_name') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Jenis ID</label>
                                <select class="form-select" wire:model="new_guest_id_card_type">
                                    <option value="KTP">KTP</option>
                                    <option value="Passport">Passport</option>
                                    <option value="SIM">SIM</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nomor ID</label>
                                <input type="text" class="form-control" wire:model="new_guest_id_card_number">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Telepon</label>
                                <input type="text" class="form-control" wire:model="new_guest_phone">
                            </div>
                        </div>
                        <button class="btn btn-success btn-sm" wire:click="createNewGuest" id="btn-create-guest">✅ Simpan & Gunakan</button>
                    </div>
                    @endif

                    @else
                    {{-- Selected guest chip --}}
                    <div style="display:flex;align-items:center;gap:12px;background:var(--navy-100);padding:12px 16px;border-radius:var(--radius-md);">
                        <div style="font-size:22px;">✅</div>
                        <div>
                            <div style="font-weight:700;color:var(--navy-800);">{{ $guestName }}</div>
                            <div style="font-size:12px;color:var(--navy-600);">Tamu terpilih</div>
                        </div>
                        <button class="btn btn-outline btn-sm" wire:click="$set('guest_id', null)" style="margin-left:auto;">Ganti</button>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Room & Date --}}
            <div class="card">
                <div class="card-header"><span class="card-title">🏠 Kamar & Tanggal</span></div>
                <div class="card-body">
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label">Check-in<span class="required">*</span></label>
                            <input type="date" class="form-control" wire:model.live="check_in_date">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Check-out<span class="required">*</span></label>
                            <input type="date" class="form-control" wire:model.live="check_out_date">
                            @error('check_out_date') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tipe Kamar</label>
                        <select class="form-select" wire:model.live="room_type_id">
                            <option value="">-- Pilih Tipe Kamar --</option>
                            @foreach($roomTypes as $rt)
                            <option value="{{ $rt->id }}">{{ $rt->name }} — Rp {{ number_format($rt->base_price, 0, ',', '.') }}/malam</option>
                            @endforeach
                        </select>
                    </div>
                    @if($room_type_id)
                    <div class="form-group">
                        <label class="form-label">Pilih Kamar<span class="required">*</span></label>
                        @if(count($availableRooms) > 0)
                        <select class="form-select" wire:model.live="room_id">
                            <option value="">-- Pilih Kamar Tersedia --</option>
                            @foreach($availableRooms as $room)
                            <option value="{{ $room->id }}">Kamar {{ $room->room_number }} (Lantai {{ $room->floor }})</option>
                            @endforeach
                        </select>
                        @error('room_id') <div class="form-error">{{ $message }}</div> @enderror
                        @else
                        <div style="padding:12px;background:var(--warning-light);border-radius:var(--radius-md);color:var(--warning-dark);font-size:13px;">
                            ⚠️ Tidak ada kamar tersedia untuk tanggal dan tipe yang dipilih.
                        </div>
                        @endif
                    </div>
                    @endif
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label">Jumlah Tamu (Pax)</label>
                            <input type="number" class="form-control" wire:model="pax" min="1">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Sumber Reservasi</label>
                            <select class="form-select" wire:model="source">
                                <option value="walk_in">Walk-in</option>
                                <option value="phone">Telepon</option>
                                <option value="online">Online</option>
                                <option value="travel_agent">Travel Agent</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div class="card">
                <div class="card-header"><span class="card-title">📝 Catatan</span></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Permintaan Khusus</label>
                        <textarea class="form-control" wire:model="special_request" rows="2" placeholder="Kamar di lantai tinggi, extra bed, dll..."></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Catatan Internal</label>
                        <textarea class="form-control" wire:model="notes" rows="2"></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar Summary --}}
        <div style="position:sticky;top:80px;">
            <div class="card">
                <div class="card-header" style="background:linear-gradient(135deg,var(--navy-900),var(--navy-700));border-radius:var(--radius-lg) var(--radius-lg) 0 0;">
                    <span class="card-title" style="color:var(--gold-300);">💰 Ringkasan Harga</span>
                </div>
                <div class="card-body">
                    <div style="display:flex;flex-direction:column;gap:12px;">
                        <div style="display:flex;justify-content:space-between;font-size:13px;">
                            <span style="color:var(--gray-500);">Harga/malam</span>
                            <span style="font-weight:600;">Rp {{ number_format($room_rate, 0, ',', '.') }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:13px;">
                            <span style="color:var(--gray-500);">Jumlah malam</span>
                            <span style="font-weight:600;">{{ $nights }} malam</span>
                        </div>
                        <div class="divider"></div>
                        <div style="display:flex;justify-content:space-between;">
                            <span style="font-weight:700;color:var(--navy-800);">Total</span>
                            <span style="font-size:20px;font-weight:800;color:var(--navy-900);">Rp {{ number_format($total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Deposit (Rp)</label>
                            <input type="number" class="form-control" wire:model="deposit" placeholder="0">
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary w-full" wire:click="save" id="btn-save-reservation">
                        📅 {{ $reservationId ? 'Update Reservasi' : 'Buat Reservasi' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
