@extends('layouts.portal')

@section('content')
<section class="page-header portal-page-header">
    <h1>Buat Pesanan Baru</h1>
    <p>Silakan isi form di bawah ini untuk mengajukan penjemputan cucian oleh staff kami.</p>
</section>

<div class="portal-profile-card" style="max-width: 650px; background: #ffffff; border-radius: 14px; padding: 26px; border: 1px solid #e2e8f0;">
    <form method="POST" action="{{ route('portal.pickups.store') }}" class="customer-modal-form">
        @csrf

        <div class="modal-form-group">
            <label style="font-weight: 600; color: #334155; font-size: 13px;">Jenis Layanan Laundry</label>
            <select name="service_id" style="width: 100%; height: 44px; border: 1px solid #d5dde8; border-radius: 9px; padding: 0 12px; font-size: 14px; outline: none; background: #ffffff;">
                <option value="">Pilih layanan</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                        {{ $service->name }} - Rp {{ number_format($service->price, 0, ',', '.') }}
                    </option>
                @endforeach
            </select>
            @error('service_id') <small class="error-text" style="color: #dc2626; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</small> @enderror
        </div>

        <div class="modal-form-group" style="margin-top: 16px;">
            <label style="font-weight: 600; color: #334155; font-size: 13px;">Alamat Penjemputan Cucian</label>
            <textarea name="address" placeholder="Masukkan alamat lengkap penjemputan" style="min-height: 92px; resize: none;">{{ old('address', $customer->address ?? '') }}</textarea>
            @error('address') <small class="error-text" style="color: #dc2626; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</small> @enderror
        </div>

        <div class="modal-form-group" style="margin-top: 16px;">
            <label style="font-weight: 600; color: #334155; font-size: 13px;">Catatan Tambahan Mengenai Cucian</label>
            <textarea name="note" placeholder="Contoh: Pakaian putih dipisah, ada selimut bed cover besar, dll.">{{ old('note') }}</textarea>
            @error('note') <small class="error-text" style="color: #dc2626; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</small> @enderror
        </div>

        <div class="modal-form-group" style="margin-top: 16px;">
            <label style="font-weight: 600; color: #334155; font-size: 13px;">Rencana Jadwal Penjemputan</label>
            <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" style="height: 44px;">
            @error('scheduled_at') <small class="error-text" style="color: #dc2626; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</small> @enderror
        </div>

        <div style="display: flex; justify-content: flex-end; margin-top: 26px; gap: 12px;">
            <a href="{{ route('portal.dashboard') }}" class="modal-cancel-btn" style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center; padding: 0 20px; height: 42px; border-radius: 9px; border: 1px solid #d5dde8; color: #475569; font-weight: 600; font-size: 14px; background: #ffffff;">
                Batal
            </a>
            <button type="submit" class="modal-submit-btn" style="width: auto; padding: 0 24px; height: 42px; border-radius: 9px; background: #0ea5e9; color: #ffffff; font-weight: 700; border: 0; cursor: pointer;">
                Kirim Permintaan Jemput
            </button>
        </div>
    </form>
</div>
@endsection