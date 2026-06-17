@extends('layouts.portal')

@section('content')
<section class="page-header portal-page-header">
    <h1>Buat Pesanan Baru</h1>
    <p>Silakan isi form dan gunakan peta di bawah ini untuk mengajukan penjemputan cucian.</p>
</section>

<!-- Load Leaflet JS & CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="portal-profile-card" style="max-width: 650px; background: #ffffff; border-radius: 14px; padding: 26px; border: 1px solid #e2e8f0;">
    <form method="POST" action="{{ route('portal.pickups.store') }}" class="customer-modal-form">
        @csrf

        <div class="modal-form-group">
            <label style="font-weight: 600; color: #334155; font-size: 13px;">Jenis Layanan Laundry <span style="color: red;">*</span></label>
            <select name="service_id" style="width: 100%; height: 44px; border: 1px solid #d5dde8; border-radius: 9px; padding: 0 12px; font-size: 14px; outline: none; background: #ffffff;" required>
                <option value="">Pilih layanan</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                        {{ $service->name }} - Rp {{ number_format($service->price, 0, ',', '.') }}
                    </option>
                @endforeach
            </select>
            @error('service_id') <small style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</small> @enderror
        </div>

        <div class="modal-form-group" style="margin-top: 16px;">
            <label style="font-weight: 600; color: #334155; font-size: 13px;">Lokasi Penjemputan <span style="color: red;">*</span></label>
            <p style="margin: 0 0 8px 0; color: #64748b; font-size: 12px;">Geser pin pada peta untuk menentukan lokasi dan kalkulasi biaya jemput otomatis.</p>
            
            <!-- Pencarian Peta -->
            <div style="display: flex; gap: 8px; margin-bottom: 8px;">
                <input type="text" id="search-map-input" placeholder="Cari nama jalan, desa, atau kota..." style="flex: 1; height: 42px; border: 1px solid #d5dde8; border-radius: 8px; padding: 0 12px; font-size: 13px; outline: none;">
                <button type="button" id="search-map-btn" style="background: #0f172a; color: white; border: none; border-radius: 8px; padding: 0 18px; font-weight: 700; cursor: pointer; font-size: 13px;">Cari Lokasi</button>
            </div>

            <!-- Wadah Peta -->
            <div id="map" style="height: 250px; width: 100%; border-radius: 10px; z-index: 1; border: 1px solid #cbd5e1; margin-bottom: 8px;"></div>
            
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">

            <!-- Alamat Utama -->
            <textarea name="address_main" id="address_main" readonly required placeholder="Geser pin di peta, alamat otomatis terisi..." style="width: 100%; min-height: 60px; background: #f1f5f9; border: 1px solid #d5dde8; border-radius: 8px; padding: 12px; resize: none; font-size: 13px; outline: none;"></textarea>
            @error('latitude') <small style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</small> @enderror
        </div>

        <div class="modal-form-group" style="margin-top: 16px;">
            <label style="font-weight: 600; color: #334155; font-size: 13px;">Detail Tambahan Patokan (Wajib) <span style="color: red;">*</span></label>
            <input type="text" name="address_detail" required placeholder="Contoh: Rumah tingkat pagar hitam depan warung ibu Yati" style="width: 100%; height: 42px; border: 1px solid #d5dde8; border-radius: 8px; padding: 0 12px; outline: none; font-size: 13px;" value="{{ old('address_detail') }}">
        </div>

        <div class="modal-form-group" style="margin-top: 16px;">
            <label style="font-weight: 600; color: #334155; font-size: 13px;">Catatan Tambahan Mengenai Cucian</label>
            <textarea name="note" placeholder="Contoh: Pakaian putih dipisah, ada selimut bed cover besar, dll." style="width: 100%; min-height: 70px; border: 1px solid #d5dde8; border-radius: 8px; padding: 12px; resize: none; font-size: 13px; outline: none;">{{ old('note') }}</textarea>
        </div>

        <div class="modal-form-group" style="margin-top: 16px;">
            <label style="font-weight: 600; color: #334155; font-size: 13px;">Rencana Jadwal Penjemputan</label>
            <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" style="width: 100%; height: 44px; border: 1px solid #d5dde8; border-radius: 8px; padding: 0 12px; font-size: 13px; outline: none;">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    var outletLat = -7.428940;
    var outletLng = 109.337930; 

    var map = L.map('map').setView([outletLat, outletLng], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    var marker = L.marker([outletLat, outletLng], {draggable: true}).addTo(map);

    function updateLocation(lat, lng) {
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        document.getElementById('address_main').value = 'Sedang mencari alamat otomatis...';

        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(response => response.json())
            .then(data => {
                if(data && data.display_name) {
                    document.getElementById('address_main').value = data.display_name;
                } else {
                    document.getElementById('address_main').value = "Alamat spesifik tidak ditemukan. Silakan tambahkan detail di kolom bawah.";
                }
            })
            .catch(error => {
                document.getElementById('address_main').value = "Gagal memuat alamat. Pastikan Anda memiliki koneksi internet.";
            });
    }

    updateLocation(outletLat, outletLng);

    marker.on('dragend', function (e) {
        var position = marker.getLatLng();
        updateLocation(position.lat, position.lng);
    });

    var searchInput = document.getElementById('search-map-input');
    var searchBtn = document.getElementById('search-map-btn');

    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault(); 
            searchBtn.click();
        }
    });

    searchBtn.addEventListener('click', function(e) {
        e.preventDefault();
        var query = searchInput.value;
        if (!query) return;

        searchBtn.innerText = 'Mencari...';

        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                searchBtn.innerText = 'Cari Lokasi';
                if (data && data.length > 0) {
                    var lat = data[0].lat;
                    var lon = data[0].lon;
                    
                    map.setView([lat, lon], 16);
                    marker.setLatLng([lat, lon]);
                    updateLocation(lat, lon);
                } else {
                    alert('Lokasi tidak ditemukan. Coba gunakan nama desa/kecamatan yang lebih spesifik.');
                }
            })
            .catch(err => {
                searchBtn.innerText = 'Cari Lokasi';
                alert('Terjadi kesalahan jaringan.');
            });
    });
});
</script>
@endsection