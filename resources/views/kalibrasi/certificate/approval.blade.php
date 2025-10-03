@extends('layouts.app')

@section('styles')
    <style>

    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="conatiner-fluid">
            <div class="row" id="sertifikat-list-container">
                {{-- Kartu akan dimuat di sini oleh JavaScript --}}
                <div class="col-12 text-center" id="loading-spinner">
                    <p>Memuat data...</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const sertifikatId = '{{ $id ?? 'null' }}';

            const baseUrlApi = `{{ url('api/kalibrasi/certificate/approval/data') }}`;

            let finalApiUrl = baseUrlApi;

            if (sertifikatId !== 'null') {
                finalApiUrl = `${baseUrlApi}/${sertifikatId}`;
            }

            fetchSertifikat(finalApiUrl);

            function fetchSertifikat(url) {
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(res) {
                        var container = $('#sertifikat-list-container');
                        const data = res.data;
                        container.empty();

                        if ($.isArray(data)) {
                            // Tampilkan daftar kartu col-md-4
                            if (data.length > 0) {
                                displayCollectionCards(data, container);
                            } else {
                                displayEmptyState(container);
                            }

                            // Kasus 2: Data adalah Single Object (Asumsi: objek sertifikat tunggal)
                        } else if (typeof data === 'object' && data !== null) {
                            // Tampilkan kartu detail col-md-12
                            displaySingleCard(data, container);
                        } else {
                            displayEmptyState(container, 'Data tidak valid.');
                        }
                    },
                    error: function() {
                        $('#sertifikat-list-container').html(
                            '<div class="col-md-12"><div class="alert alert-danger">Gagal memuat data sertifikat.</div></div>'
                        );
                    }
                });
            }

            // 1. Tampilan untuk Collection (col-md-4)
            function displayCollectionCards(sertifikatArray, container) {
                const getStatusClass = (status) => {
                    if (status === 'pending') return 'badge-soft-warning ';
                    if (status === 'approved') return 'badge-soft-success';
                    return 'badge-soft-danger';
                };

                $.each(sertifikatArray, function(index, item) {
                    // Mendapatkan data dengan aman (jika properti bersarang mungkin null)
                    const kodeAlat = item.kalibrasi && item.kalibrasi.alat ? item.kalibrasi.alat.kode_alat :
                        'N/A';
                    const jenisKalibrasi = item.kalibrasi ? item.kalibrasi.jenis_kalibrasi : 'N/A';
                    const namaAlat = item.kalibrasi.alat ? item.kalibrasi.alat.nama_alat : 'N/A';
                    const lokasiKalibrasi = item.kalibrasi ? item.kalibrasi.lokasi_kalibrasi : 'N/A';
                    const username = item.user ? item.user.username : 'N/A';

                    // Format tanggal (Catatan: ini hanya untuk contoh, tanggal dari JSON mungkin perlu format lebih lanjut)
                    const tanggalPengajuan = new Date(item.created_at).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: '2-digit',
                        year: '2-digit'
                    }).replace(/\//g, '/');

                    const delay = (index * 200) % 1000;
                    const cardHtml = `
                         <div class="col-md-4 mb-4">
                            <div data-aos="fade-up" data-aos-delay="${delay}" data-aos-anchor-placement="top-bottom">
                                <div class="card shadow-lg border-0 rounded-4 h-100 d-flex flex-column justify-content-between">
                                    
                                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                                        
                                        <div class="d-flex justify-content-between align-items-start mb-2 border-bottom pb-2">
                                            <span class="badge badge-soft-primary fw-bold rounded-pill px-3 py-2 small">
                                                SERTIFIKAT #${item.id}
                                            </span>
                                            <span class="badge ${getStatusClass(item.status)} fw-bolder p-2 rounded-pill text-uppercase shadow-sm small">
                                                ${item.status}
                                            </span>
                                        </div>

                                        <h6 class="fw-bolder text-dark mb-2 text-truncate" title="Kode Alat">
                                            ${kodeAlat}
                                        </h6>

                                        <div class="row g-2 small mb-2 flex-grow-1 ">
                                            <div class="col-6 d-flex flex-column gap-2 ">
                                                <div class="d-flex align-items-center">
                                                    
                                                    <div>
                                                        <span class="d-block text-uppercase fw-semibold text-muted extra-small">Jenis Kalibrasi</span>
                                                        <span class="fw-bold text-dark">${jenisKalibrasi.charAt(0).toUpperCase() + jenisKalibrasi.slice(1) || 'N/A'}</span>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    
                                                    <div>
                                                        <span class="d-block text-uppercase fw-semibold text-muted extra-small">Nama Alat</span>
                                                        <span class="fw-bold text-dark">${namaAlat.charAt(0).toUpperCase() + namaAlat.slice(1) || 'N/A'}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6 d-flex flex-column gap-2 ">
                                                <div class="d-flex align-items-center">
                                                    
                                                    <div>
                                                        <span class="d-block text-uppercase fw-semibold text-muted extra-small">Lokasi Kalibrasi</span>
                                                        <span class="fw-bold text-dark">${lokasiKalibrasi.charAt(0).toUpperCase() + lokasiKalibrasi.slice(1) || 'N/A'}</span>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    
                                                    <div>
                                                        <span class="d-block text-uppercase fw-semibold text-muted extra-small">User Pemohon</span>
                                                        <span class="fw-bold text-dark">${username.charAt(0).toUpperCase() + username.slice(1) || 'N/A'}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 pt-2">
                                                <div class="d-flex align-items-center">
                                                    
                                                    <div>
                                                        <span class="d-block text-uppercase fw-semibold text-muted extra-small">Tanggal Pengajuan</span>
                                                        <span class="fw-bold text-dark">${tanggalPengajuan}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pt-0"> 
                                            <a href="#"
                                                class="btn-detail-sertifikat btn btn-sm btn-soft-info w-100 d-flex align-items-center justify-content-center py-2 fw-bolder rounded-3 mb-2"
                                                data-sertifikat-id="${item.id}" title="Lihat Detail Sertifikat">
                                                <i class="mdi mdi-magnify me-2"></i> Detail Sertifikat
                                            </a>

                                            <div class="mb-2 flex-grow-1">
                                                <textarea id="komentar_list_${item.id}" data-komentar-id="${item.id}" class="form-control form-control-sm komentar-textarea" rows="2" style="resize: none;"
                                                    placeholder="Tulis komentar untuk Approve/Reject (Opsional)..."></textarea>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button" 
                                                    class="btn-action btn btn-soft-success d-flex align-items-center justify-content-center fw-bolder flex-fill py-2 rounded-3 shadow-sm btn-sm"
                                                    data-id="${item.id}" data-status="approved" title="Approve Sertifikat">
                                                    <i class="mdi mdi-check-circle me-1"></i> Approve
                                                </button>
                                                <button type="button" 
                                                    class="btn-action btn btn-soft-danger d-flex align-items-center justify-content-center fw-bolder flex-fill py-2 rounded-3 shadow-sm btn-sm"
                                                    data-id="${item.id}" data-status="rejected" title="Reject Sertifikat">
                                                    <i class="mdi mdi-close-circle me-1"></i> Reject
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    container.append(cardHtml);
                });
                // setupActionButtons(); // Pasang kembali event listener setelah kartu dibuat
            }

            // 2. Tampilan untuk Single Object (col-md-12)
            function displaySingleCard(item, container) {
                // Penanganan data nested dan null (seperti yang dilakukan di fungsi sebelumnya)
                const kodeAlat = item.kalibrasi && item.kalibrasi.alat ? item.kalibrasi.alat.kode_alat : '—';
                const namaAlat = item.kalibrasi && item.kalibrasi.alat ? item.kalibrasi.alat.nama_alat : '—';
                const jenisKalibrasi = item.kalibrasi ? item.kalibrasi.jenis_kalibrasi : '—';
                const lokasiKalibrasi = item.kalibrasi ? item.kalibrasi.lokasi_kalibrasi : '—';

                // Format tanggal
                const dateObj = new Date(item.created_at);
                const tanggalKalibrasi =
                    `${dateObj.getDate().toString().padStart(2, '0')}/${(dateObj.getMonth() + 1).toString().padStart(2, '0')}/${dateObj.getFullYear()}`;

                // Logic untuk Status Badge
                let statusClass, iconClass;
                if (item.status.toLowerCase() === 'pending') {
                    statusClass = 'badge-soft-warning';
                    iconClass = 'mdi-progress-clock';
                } else if (item.status.toLowerCase() === 'approved') {
                    statusClass = 'badge-soft-success';
                    iconClass = 'mdi-check-all';
                } else {
                    statusClass = 'badge-soft-danger';
                    iconClass = 'mdi-close-circle';
                }

                // Gunakan template literal (backticks) untuk membuat HTML
                var singleCardHtml = `
                    <div class="col-md-12 mb-3">
                        <div data-aos="fade-up" data-aos-anchor-placement="top-bottom">
                            <div class="card shadow-sm border-0 rounded-3">
                                <div class="card-body p-3 p-md-4">
                                    <div class="row g-3 align-items-stretch">
                                        <div class="col-md-6 border-end pe-md-3 d-flex flex-column justify-content-between">
                                            <h5 class="fw-bolder text-dark pb-1 border-bottom border-primary border-2">
                                                <span class="text-muted fw-normal small d-block">Kode Alat:</span>
                                                <span class="text-primary">${kodeAlat}</span>
                                            </h5>
                                            <div class="row g-2 small">
                                                <div class="col-6">
                                                    <span class="text-uppercase fw-semibold text-muted d-block"> Jenis Kalibrasi</span>
                                                    <span class="fw-bold text-dark fs-6">${jenisKalibrasi.charAt(0).toUpperCase() + jenisKalibrasi.slice(1) || '—'}</span>
                                                </div>
                                                <div class="col-6">
                                                    <span class="text-uppercase fw-semibold text-muted d-block"> Nama Alat</span>
                                                    <span class="fw-bold text-dark fs-6">${namaAlat.charAt(0).toUpperCase() + namaAlat.slice(1) || '—'}</span>
                                                </div>

                                                <div class="col-6 pt-2 border-top">
                                                    <span class="text-uppercase fw-semibold text-muted d-block">Lokasi Kalibrasi</span>
                                                    <span class="fw-bold text-dark fs-6">${lokasiKalibrasi.charAt(0).toUpperCase() + lokasiKalibrasi.slice(1) || '—'}</span>
                                                </div>
                                                <div class="col-6 pt-2 border-top">
                                                    <span class="text-uppercase fw-semibold text-muted d-block">Tanggal Kalibrasi</span>
                                                    <span class="fw-bold text-dark fs-6">${tanggalKalibrasi}</span>
                                                </div>
                                            </div>
                                            <button type="button" data-alat-id="${item.kalibrasi && item.kalibrasi.alat ? item.kalibrasi.alat.id : '—'}"
                                                class="btn-detail-alat btn btn-outline-info btn-sm d-flex align-items-center justify-content-center fw-medium py-2 rounded-3 shadow-sm mt-3">
                                                <i class="mdi mdi-magnify me-1"></i> Detail Alat & Riwayat
                                            </button>
                                        </div>
                                        <div class="col-md-6 ps-md-3 d-flex flex-column justify-content-between">
                                            <div class="mb-3">
                                                <span class="text-uppercase fw-bold text-muted small d-block mb-1">Status Sertifikat</span>
                                                <span class="badge ${statusClass} fs-6 fw-bolder p-2 px-3 rounded-pill shadow-sm text-uppercase">
                                                    <i class="mdi ${iconClass} me-2"></i>
                                                    ${item.status}
                                                </span>
                                            </div>
                                            <div class="mb-3 flex-grow-1">
                                                <label for="komentar_${item.id}" class="form-label text-uppercase fw-bold text-muted small mb-1">Komentar / Catatan (Opsional)</label>
                                                <textarea id="komentar_${item.id}" data-komentar-id="${item.id}" class="form-control form-control-sm komentar-textarea" rows="3" style="resize: none;"
                                                    placeholder="Tulis komentar untuk proses Approve atau Reject..."></textarea>
                                            </div>
                                            <div class="d-flex gap-2 mt-auto">
                                                <button type="button" data-id="${item.id}" data-status="Approved" 
                                                    class="btn-action btn btn-success btn-sm d-flex align-items-center justify-content-center fw-bolder text-uppercase flex-fill py-2 rounded-3 shadow-sm">
                                                    <i class="mdi mdi-check-circle me-1"></i> Approve
                                                </button>
                                                <button type="button" data-id="${item.id}" data-status="Rejected" 
                                                    class="btn-action btn btn-danger btn-sm d-flex align-items-center justify-content-center fw-bolder text-uppercase flex-fill py-2 rounded-3 shadow-sm">
                                                    <i class="mdi mdi-close-circle me-1"></i> Reject
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                container.append(singleCardHtml);
                AOS.refresh();

                // Pasang event listener untuk tombol setelah kartu dibuat
                // setupActionButtons();
            }

            function displayEmptyState(container, message = 'Tidak ada data sertifikat yang tersedia.') {
                container.append(`
                    <div class="col-md-12">
                        <div class="alert alert-info text-center" role="alert">
                            <i class="mdi mdi-information-outline me-2"></i> ${message}
                        </div>
                    </div>
                `);
            }

            // ... (Fungsi setupActionButtons() Anda di sini) ...
        });
    </script>
@endsection
