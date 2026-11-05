<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Laundry - T.A.M Laundry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;}
        .form-card { border-radius: 20px; border: none;}
        .input-readonly { background-color: #e9ecef !important; cursor: not-allowed; font-weight: bold;}
        .form-control, .form-select { border-radius: 10px; }
        .bg-custom { background: linear-gradient(90deg, #0d6efd 0%, #0dcaf0 100%); }
        #map-picker { height: 350px; width: 100%; border-radius: 8px; border: 2px solid #dee2e6; z-index: 1;}
    </style>
</head>
<body class="pt-4 pb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
                <div class="card form-card shadow-sm mb-4">
                    <div class="card-header bg-custom text-white text-center py-4" style="border-radius: 20px 20px 0 0;">
                        <h4 class="mb-0 fw-bold">🫧 Buat Pesanan Baru</h4>
                        <small class="opacity-75">T.A.M Laundry siap mencuci pakaian Anda!</small>
                    </div>
                    <div class="card-body p-4 p-md-5">

                        @if($errors->any())
                            <div class="alert alert-danger rounded-3">
                                <ul class="mb-0 small fw-bold">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('simpan.pesanan') }}" method="POST">
                            @csrf
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label class="fw-bold form-label small text-muted">Nama Anda</label>
                                    <input type="text" class="form-control input-readonly" value="{{ Auth::user()->name }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-bold form-label small text-muted">No. WhatsApp</label>
                                    <input type="text" class="form-control input-readonly" value="{{ Auth::user()->no_wa }}" readonly>
                                </div>
                            </div>
                            <hr class="my-4 text-muted">

                            <div class="mb-3">
                                <label class="fw-bold form-label">Jenis Layanan <span class="text-danger">*</span></label>
                                <select name="layanan" class="form-select form-select-lg fs-6" required>
                                    <option value="" disabled selected>-- Pilih jenis layanan --</option>
                                    <option value="Cuci Kering">Cuci Kering — Rp 5.000/kg</option>
                                    <option value="Cuci Setrika">Cuci Setrika — Rp 7.000/kg</option>
                                    <option value="Setrika Saja">Setrika Saja — Rp 4.000/kg</option>
                                    <option disabled>────── CUCI SATUAN ──────</option>
                                    <option value="Cuci Satuan (Karpet)">Karpet — Rp 50.000/pcs</option>
                                    <option value="Cuci Satuan (Selimut)">Selimut — Rp 20.000/pcs</option>
                                    <option value="Cuci Satuan (Mukena)">Mukena — Rp 10.000/pcs</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                                <select name="metode_pembayaran" class="form-select form-select-lg fs-6" required>
                                    <option value="" disabled selected>-- Pilih metode pembayaran --</option>
                                    <option value="Tunai">💵 Bayar Tunai (Ke Kurir / Outlet)</option>
                                    <option value="Online">💳 Bayar Online (QRIS / Virtual Account)</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="fw-bold form-label">Metode Pengiriman <span class="text-danger">*</span></label>
                                <select name="metode_kirim" id="metode_kirim" class="form-select form-select-lg fs-6" required onchange="toggleAlamat(this.value)">
                                    <option value="" disabled selected>-- Bagaimana Anda mengirim pakaian? --</option>
                                    <option value="Bawa Sendiri">Bawa Sendiri ke Outlet</option>
                                    <option value="Antar Jemput">🛵 Antar Jemput (+Rp 7.500)</option>
                                </select>
                            </div>

                            <div class="p-4 bg-light rounded-4 border mb-4" id="field_alamat" style="display:none;">
                                <h6 class="fw-bold mb-3 text-primary">📍 Detail Alamat Penjemputan</h6>
                                
                                <div class="mb-3">
                                    <label class="small fw-bold mb-2">Geser Pin Merah ke Lokasi Rumah Anda <span class="text-danger">*</span></label>
                                    <div id="map-picker"></div>
                                    <div class="form-text mt-1 text-muted small">
                                        Peta dapat digeser dan di-zoom. Pastikan pin merah berada tepat di rumah Anda. <br>
                                        <span class="text-success fw-bold">Alamat di bawah akan terisi otomatis saat pin digeser!</span>
                                    </div>
                                    <input type="hidden" name="lat_gps" id="lat_gps">
                                    <input type="hidden" name="lng_gps" id="lng_gps">
                                </div>

                                <!-- PERUBAHAN 1: MENGGUNAKAN SELECT DROPDOWN -->
                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <label class="small fw-bold">Provinsi <span class="text-danger">*</span></label>
                                        <select name="provinsi" id="provinsi" class="form-select text-truncate">
                                            <option value="">Pilih Provinsi</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="small fw-bold">Kota/Kabupaten <span class="text-danger">*</span></label>
                                        <select name="kota" id="kota" class="form-select text-truncate">
                                            <option value="">Pilih Kota/Kab</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row g-2 mb-3">
                                    <div class="col-7">
                                        <label class="small fw-bold">Kecamatan <span class="text-danger">*</span></label>
                                        <select name="kecamatan" id="kecamatan" class="form-select text-truncate">
                                            <option value="">Pilih Kecamatan</option>
                                        </select>
                                    </div>
                                    <div class="col-5">
                                        <label class="small fw-bold">Kode Pos</label>
                                        <input type="number" name="kode_pos" id="kode_pos" class="form-control" placeholder="40280">
                                    </div>
                                </div>

                                <!-- PERUBAHAN 2: TOMBOL GESER PETA MANUAL -->
                                <label class="small fw-bold">Detail Alamat Lengkap <span class="text-danger">*</span></label>
                                <textarea name="detail_alamat" id="detail_alamat" class="form-control" rows="3" placeholder="Nama Jalan, Gedung, No. Rumah, RT/RW, Patokan..."></textarea>
                                <button type="button" class="btn btn-sm btn-info w-100 mt-2 fw-bold text-white shadow-sm" onclick="cariDiPetaManual()">
                                    🎯 Geser Pin Peta ke Alamat di Atas
                                </button>
                            </div>

                            <div class="mb-4">
                                <label class="fw-bold form-label">Catatan Tambahan <span class="text-muted fw-normal">(Opsional)</span></label>
                                <textarea name="catatan" class="form-control" rows="2" placeholder="Contoh: Tolong jangan disetrika terlalu panas, kancing kemeja ada yang rapuh..."></textarea>
                            </div>

                            <div class="alert alert-primary rounded-4 small py-3 mb-4 border-0">
                                ℹ️ <strong>Berat/Jumlah pakaian tidak perlu diisi sekarang.</strong><br>
                                Kurir kami akan membawa pakaian Anda ke gerai, lalu Admin akan menghitung total tagihannya.
                            </div>

                            <button type="submit" class="btn btn-primary w-100 fw-bold py-3 fs-5 rounded-pill shadow-sm">
                                🚀 Kirim Pesanan Sekarang
                            </button>
                        </form>
                    </div>
                </div>

                <div class="text-center">
                    <a href="{{ route('customer.dashboard') }}" class="btn btn-outline-secondary fw-bold rounded-pill px-4 py-2">
                        ← Kembali ke Dasbor Saya
                    </a>
                </div>

            </div>
        </div>
    </div>

    <script>
        var mapPicker;
        var markerPicker;

        // FUNGSI LOAD API EMSIFA (PROVINSI)
        function loadProvinsi() {
            fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')
                .then(response => response.json())
                .then(provinces => {
                    let options = '<option value="">Pilih Provinsi</option>';
                    provinces.forEach(prov => {
                        options += `<option value="${prov.name}" data-id="${prov.id}">${prov.name}</option>`;
                    });
                    document.getElementById('provinsi').innerHTML = options;
                });
        }

        // TRIGGER KETIKA PROVINSI DIPILIH
        document.getElementById('provinsi').addEventListener('change', function() {
            let idProv = this.options[this.selectedIndex].getAttribute('data-id');
            if(idProv) {
                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${idProv}.json`)
                    .then(response => response.json())
                    .then(regencies => {
                        let options = '<option value="">Pilih Kota/Kab</option>';
                        regencies.forEach(kota => {
                            options += `<option value="${kota.name}" data-id="${kota.id}">${kota.name}</option>`;
                        });
                        document.getElementById('kota').innerHTML = options;
                        document.getElementById('kecamatan').innerHTML = '<option value="">Pilih Kecamatan</option>'; // reset kecamatan
                    });
            }
        });

        // TRIGGER KETIKA KOTA DIPILIH
        document.getElementById('kota').addEventListener('change', function() {
            let idKota = this.options[this.selectedIndex].getAttribute('data-id');
            if(idKota) {
                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${idKota}.json`)
                    .then(response => response.json())
                    .then(districts => {
                        let options = '<option value="">Pilih Kecamatan</option>';
                        districts.forEach(kec => {
                            options += `<option value="${kec.name}">${kec.name}</option>`;
                        });
                        document.getElementById('kecamatan').innerHTML = options;
                    });
            }
        });

        // FUNGSI GESER MAPS MANUAL
        function cariDiPetaManual() {
            let alamat = document.getElementById('detail_alamat').value;
            let kota = document.getElementById('kota').value;
            
            if(alamat.trim() === '') {
                alert('Silakan isi detail alamat lengkap terlebih dahulu!');
                return;
            }

            let query = alamat + (kota ? ", " + kota : "");
            
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}`)
                .then(response => response.json())
                .then(data => {
                    if(data.length > 0) {
                        let lat = data[0].lat;
                        let lon = data[0].lon;
                        
                        mapPicker.setView([lat, lon], 17);
                        markerPicker.setLatLng([lat, lon]);
                        
                        document.getElementById('lat_gps').value = lat;
                        document.getElementById('lng_gps').value = lon;
                        alert("✅ Titik berhasil digeser! Silakan cek peta.");
                    } else {
                        alert("❌ Alamat tidak ditemukan di sistem peta. Mohon geser pin merah secara manual.");
                    }
                });
        }

        // FUNGSI MENGAMBIL ALAMAT DARI KOORDINAT (REVERSE GEOCODING)
        function getAddressFromCoords(lat, lng) {
            document.getElementById('detail_alamat').placeholder = "⏳ Sedang melacak alamat otomatis...";
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.address) {
                        let addr = data.address;
                        // Karena kita pakai Dropdown, kita hanya isi textarea dan kode pos
                        document.getElementById('kode_pos').value = addr.postcode || '';
                        document.getElementById('detail_alamat').value = data.display_name || '';
                    }
                })
                .catch(err => {
                    document.getElementById('detail_alamat').placeholder = "Gagal melacak alamat. Silakan ketik manual.";
                });
        }

        function toggleAlamat(val) {
            var field = document.getElementById('field_alamat');
            var inputProvinsi = document.getElementById('provinsi');
            var inputKota = document.getElementById('kota');
            var inputKecamatan = document.getElementById('kecamatan');
            var inputDetail = document.getElementById('detail_alamat');

            if (val === 'Antar Jemput') {
                field.style.display = 'block';
                inputProvinsi.required = true;
                inputKota.required = true;
                inputKecamatan.required = true;
                inputDetail.required = true;
                
                // Load Dropdown Emsifa Pertama Kali
                if(inputProvinsi.options.length <= 1) {
                    loadProvinsi();
                }

                setTimeout(function() {
                    if (!mapPicker) {
                        var defaultLat = -6.9089;
                        var defaultLng = 107.6450;
                        mapPicker = L.map('map-picker').setView([defaultLat, defaultLng], 15);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap'
                        }).addTo(mapPicker);
                        markerPicker = L.marker([defaultLat, defaultLng], {draggable: true}).addTo(mapPicker);
                        
                        document.getElementById('lat_gps').value = defaultLat;
                        document.getElementById('lng_gps').value = defaultLng;

                        markerPicker.on('dragend', function (e) {
                            var position = markerPicker.getLatLng();
                            document.getElementById('lat_gps').value = position.lat;
                            document.getElementById('lng_gps').value = position.lng;
                            getAddressFromCoords(position.lat, position.lng);
                        });

                        mapPicker.on('click', function(e) {
                            markerPicker.setLatLng(e.latlng);
                            document.getElementById('lat_gps').value = e.latlng.lat;
                            document.getElementById('lng_gps').value = e.latlng.lng;
                            getAddressFromCoords(e.latlng.lat, e.latlng.lng);
                        });
                    }
                    mapPicker.invalidateSize();
                }, 300);

            } else {
                field.style.display = 'none';
                inputProvinsi.required = false;
                inputKota.required = false;
                inputKecamatan.required = false;
                inputDetail.required = false;
            }
        }
    </script>
</body>
</html>