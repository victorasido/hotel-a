<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $folio->folio_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #1a202c; background: #fff; }

        .header { background: linear-gradient(135deg, #0a1628, #163060); color: white; padding: 30px; display: flex; justify-content: space-between; align-items: center; }
        .hotel-name { font-size: 22px; font-weight: bold; color: #e8b93a; }
        .hotel-sub { font-size: 10px; color: rgba(255,255,255,.6); margin-top: 2px; text-transform: uppercase; letter-spacing: .08em; }
        .invoice-label { text-align: right; }
        .invoice-label h2 { font-size: 20px; font-weight: bold; color: rgba(255,255,255,.9); }
        .invoice-label p { font-size: 11px; color: rgba(255,255,255,.5); }

        .content { padding: 24px 30px; }

        .info-grid { display: flex; justify-content: space-between; margin-bottom: 24px; }
        .info-box { flex: 1; }
        .info-box + .info-box { margin-left: 20px; }
        .info-label { font-size: 9px; text-transform: uppercase; letter-spacing: .08em; color: #9ca3af; margin-bottom: 4px; font-weight: bold; }
        .info-value { font-size: 13px; font-weight: 600; color: #111; }
        .info-sub { font-size: 11px; color: #6b7280; }

        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 16px 0; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead { background: #0a1628; }
        thead th { padding: 10px 12px; text-align: left; font-size: 10px; font-weight: bold; color: rgba(255,255,255,.8); text-transform: uppercase; letter-spacing: .06em; }
        tbody tr { border-bottom: 1px solid #f3f4f6; }
        tbody td { padding: 10px 12px; font-size: 11px; }
        tbody tr:nth-child(even) { background: #f9fafb; }

        .type-badge { display: inline-block; padding: 2px 7px; border-radius: 20px; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .type-room { background: #dbeafe; color: #1d4ed8; }
        .type-fnb { background: #dcfce7; color: #15803d; }
        .type-extra { background: #f3f4f6; color: #4b5563; }
        .type-discount { background: #fee2e2; color: #dc2626; }

        .totals-box { display: flex; justify-content: flex-end; }
        .totals-inner { width: 300px; }
        .total-row { display: flex; justify-content: space-between; padding: 5px 0; font-size: 12px; }
        .total-row.grand { border-top: 2px solid #0a1628; margin-top: 8px; padding-top: 10px; font-size: 16px; font-weight: bold; color: #0a1628; }

        .footer { margin-top: 32px; padding: 16px 30px; background: #f9fafb; border-top: 1px solid #e5e7eb; text-align: center; font-size: 10px; color: #9ca3af; }
        .footer strong { color: #6b7280; }

        .stamp-box { text-align: right; margin-top: 24px; }
        .stamp-box p { font-size: 11px; color: #6b7280; margin-bottom: 50px; }
        .stamp-label { font-size: 11px; font-weight: bold; color: #111; border-top: 1px solid #111; display: inline-block; padding-top: 4px; width: 150px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="hotel-name">🏨 Grand Nusantara Hotel</div>
            <div class="hotel-sub">Front Office Management System</div>
            <div class="hotel-sub" style="margin-top:4px;">Jl. Contoh No. 123, Kota Hotel | Telp. (021) 123-4567</div>
        </div>
        <div class="invoice-label">
            <h2>INVOICE</h2>
            <p>{{ $folio->folio_number }}</p>
            <p>Dicetak: {{ now()->format('d M Y H:i') }}</p>
        </div>
    </div>

    <div class="content">
        {{-- Guest & Stay Info --}}
        <div class="info-grid">
            <div class="info-box">
                <div class="info-label">Tagihan Kepada</div>
                <div class="info-value">{{ $folio->guest->name }}</div>
                <div class="info-sub">{{ $folio->guest->id_card_type }}: {{ $folio->guest->id_card_number ?? '—' }}</div>
                <div class="info-sub">{{ $folio->guest->phone ?? '' }}</div>
            </div>
            <div class="info-box">
                <div class="info-label">Informasi Menginap</div>
                <div class="info-value">Kamar {{ $folio->checkIn->reservation->room->room_number }}</div>
                <div class="info-sub">{{ $folio->checkIn->reservation->room->roomType->name }}</div>
                <div class="info-sub">Check-in: {{ $folio->checkIn->actual_check_in->format('d M Y') }}</div>
                <div class="info-sub">Check-out: {{ $folio->checkIn->reservation->check_out_date->format('d M Y') }}</div>
            </div>
            <div class="info-box">
                <div class="info-label">Nomor Reservasi</div>
                <div class="info-value" style="font-family:monospace;">{{ $folio->checkIn->reservation->booking_code }}</div>
                <div class="info-sub">Nomor Folio: {{ $folio->folio_number }}</div>
                <div class="info-sub">Status: {{ strtoupper($folio->status) }}</div>
            </div>
        </div>

        <hr class="divider">

        {{-- Items Table --}}
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tipe</th>
                    <th>Keterangan</th>
                    <th>Tanggal</th>
                    <th style="text-align:center;">Qty</th>
                    <th style="text-align:right;">Harga Satuan</th>
                    <th style="text-align:right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($folio->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><span class="type-badge type-{{ $item->type }}">{{ $item->type }}</span></td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->item_date->format('d M Y') }}</td>
                    <td style="text-align:center;">{{ $item->qty }}</td>
                    <td style="text-align:right;">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td style="text-align:right;font-weight:bold;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totals --}}
        <div class="totals-box">
            <div class="totals-inner">
                <div class="total-row">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($folio->items->sum('subtotal'), 0, ',', '.') }}</span>
                </div>
                @if($folio->discount > 0)
                <div class="total-row" style="color:#dc2626;">
                    <span>Diskon</span>
                    <span>- Rp {{ number_format($folio->discount, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($folio->tax > 0)
                <div class="total-row">
                    <span>Pajak</span>
                    <span>+ Rp {{ number_format($folio->tax, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="total-row grand">
                    <span>GRAND TOTAL</span>
                    <span>Rp {{ number_format($folio->grand_total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Signature --}}
        <div class="stamp-box">
            <p>Hormat kami,</p>
            <span class="stamp-label">Front Office</span>
        </div>
    </div>

    <div class="footer">
        <strong>Grand Nusantara Hotel</strong> — Terima kasih atas kunjungan Anda. Kami berharap dapat melayani Anda kembali.<br>
        Invoice ini digenerate secara otomatis oleh Sistem Front Office.
    </div>
</body>
</html>
