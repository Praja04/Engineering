@extends('layouts.app')

@section('title', 'Form Scoring - ' . $machineProcess->processParameter->name)

@section('styles')
<style>
    .section-card {
        border-left: 4px solid #405189;
        margin-bottom: 1.5rem;
    }

    .part-item {

        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        transition: all 0.3s ease;
    }

    .part-item:hover {
        background: rgba(68, 69, 69, 0.1);
        transform: translateX(5px);
    }

    .result-btn-group {
        display: flex;
        gap: 0.5rem;
    }

    .result-btn {
        flex: 1;
        padding: 0.5rem;
        border: 2px solid #dee2e6;
        background: white;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 600;
    }

    .result-btn:hover {
        border-color: #405189;
    }

    .result-btn.active-ok {
        background: #198754;
        color: white;
        border-color: #198754;
    }

    .result-btn.active-not-ok {
        background: #dc3545;
        color: white;
        border-color: #dc3545;
    }

    .critical-badge {
        background: #dc3545;
        color: white;
        padding: 0.125rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .non-critical-badge {
        background: #e7eb0aff;
        color: black;
        padding: 0.125rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .progress-bar-custom {
        height: 8px;
        border-radius: 10px;
        background: #e9ecef;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #198754 0%, #20c997 100%);
        border-radius: 10px;
        transition: width 0.3s ease;
    }

    .sticky-progress {
        position: sticky;
        top: 70px;
        z-index: 100;
    }

    .machine-info-header {
        background: linear-gradient(135deg, #405189 0%, #5664d2 100%);
        color: white;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">
                        <i class="ri-file-list-3-line align-middle me-2"></i>
                        Form Scoring
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('scoring.index') }}">Scoring Mesin</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('scoring.machine.processes', $machineProcess->machine->id) }}">
                                    {{ $machineProcess->machine->name }}
                                </a>
                            </li>
                            <li class="breadcrumb-item active">{{ $machineProcess->processParameter->name }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <form id="scoringForm" action="{{ route('scoring.store', $machineProcess->id) }}" method="POST">
            @csrf

            <!-- Machine Info Header -->
            <div class="machine-info-header" data-aos="fade-up">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="text-white mb-2">
                            <i class="ri-settings-3-fill me-2"></i>
                            {{ $machineProcess->machine->name }}
                        </h4>
                        <h5 class="text-white opacity-90 mb-3">{{ $machineProcess->processParameter->name }}</h5>
                        <div class="d-flex gap-3">
                            <span class="badge">
                                <i class="ri-folder-line me-1"></i>
                                {{ $sections->count() }} Section
                            </span>
                            <span class="badge">
                                <i class="ri-checkbox-multiple-line me-1"></i>
                                {{ $sections->sum(function($s) { return $s->parts->count(); }) }} Parts
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('scoring.machine.processes', $machineProcess->machine->id) }}" class="btn btn-light">
                        <i class="ri-arrow-left-line me-1"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Scoring Form -->
                <div class="col-lg-8">
                    @foreach($sections as $section)
                    <div class="card section-card" data-aos="fade-up" data-aos-delay="{{ 100 * $loop->index }}">
                        <div class="card-header bg-soft-primary">
                            <h5 class="mb-0">
                                <i class="ri-folder-3-line me-2"></i>
                                {{ $section->name }}
                                <span class="badge bg-primary ms-2">{{ $section->parts->count() }} Parts</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            @foreach($section->parts as $part)
                            @php
                            $existingDetail = $existingDetails[$part->id] ?? null;
                            @endphp
                            <div class="part-item" data-part-id="{{ $part->id }}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">
                                            {{ $part->name }}
                                            @if($part->critical == 'Y')
                                            <span class="critical-badge ms-2">
                                                <i class="ri-alert-fill"></i> CRITICAL
                                            </span>
                                            @else
                                            <span class="non-critical-badge ms-2">
                                                <i class="ri-alert-fill"></i>Non CRITICAL
                                            </span>
                                            @endif
                                        </h6>
                                        @if($part->standar)
                                        <p class="text-muted mb-0 small">
                                            <i class="ri-bookmark-line me-1"></i>
                                            <strong>Standar:</strong>{{ $part->standar }}
                                        </p>
                                        @endif
                                    </div>
                                </div>

                                <input type="hidden" name="parts[{{ $loop->parent->index }}_{{ $loop->index }}][part_id]" value="{{ $part->id }}">

                                <div class="result-btn-group mt-2">
                                    <button type="button" class="result-btn result-ok {{ $existingDetail && $existingDetail['result'] == 'OK' ? 'active-ok' : '' }}" data-result="OK" onclick="selectResult(this, '{{ $loop->parent->index }}_{{ $loop->index }}')">
                                        <i class="ri-checkbox-circle-line me-1"></i> OK
                                    </button>
                                    <button type="button" class="result-btn result-not-ok {{ $existingDetail && $existingDetail['result'] == 'NOT OK' ? 'active-not-ok' : '' }}" data-result="NOT OK" onclick="selectResult(this, '{{ $loop->parent->index }}_{{ $loop->index }}')">
                                        <i class="ri-close-circle-line me-1"></i> NOT OK
                                    </button>
                                </div>

                                <input type="hidden" name="parts[{{ $loop->parent->index }}_{{ $loop->index }}][result]" class="result-input" value="{{ $existingDetail['result'] ?? '' }}" required>

                                <div class="mt-2">
                                    <textarea name="parts[{{ $loop->parent->index }}_{{ $loop->index }}][notes]" class="form-control form-control-sm" rows="2" placeholder="Catatan (opsional)">{{ $existingDetail['notes'] ?? '' }}</textarea>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    <!-- Notes Section -->
                    <div class="card" data-aos="fade-up">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="ri-file-text-line me-2"></i>
                                Catatan Umum
                            </h5>
                        </div>
                        <div class="card-body">
                            <textarea name="notes" class="form-control" rows="4" placeholder="Tambahkan catatan umum untuk scoring ini...">{{ $existingScoring->notes ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Progress Sidebar -->
                <div class="col-lg-4">
                    <div class="sticky-progress" data-aos="fade-up">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="mb-3">
                                    <i class="ri-pie-chart-line me-2"></i>
                                    Progress Scoring
                                </h5>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Progress</span>
                                        <span class="fw-bold" id="progressText">0%</span>
                                    </div>
                                    <div class="progress-bar-custom">
                                        <div class="progress-fill" id="progressBar" style="width: 0%"></div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">Total Parts</span>
                                        <span class="fw-bold" id="totalParts">{{ $sections->sum(function($s) { return $s->parts->count(); }) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">Sudah Scoring</span>
                                        <span class="fw-bold text-primary" id="scoredParts">0</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-success">
                                            <i class="ri-checkbox-circle-fill me-1"></i> OK
                                        </span>
                                        <span class="fw-bold text-success" id="okCount">0</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-danger">
                                            <i class="ri-close-circle-fill me-1"></i> NOT OK
                                        </span>
                                        <span class="fw-bold text-danger" id="notOkCount">0</span>
                                    </div>
                                </div>

                                <div class="alert alert-info small mb-3">
                                    <i class="ri-information-line me-1"></i>
                                    Pastikan semua parts sudah di-scoring sebelum submit.
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" name="status" value="draft" class="btn btn-secondary">
                                        <i class="ri-save-line me-1"></i> Simpan Draft
                                    </button>
                                    <button type="submit" name="status" value="completed" class="btn btn-success" id="btnComplete">
                                        <i class="ri-checkbox-circle-line me-1"></i> Selesai & Submit
                                    </button>
                                </div>
                            </div>
                        </div>

                        @if($existingScoring)
                        <div class="card border-0 shadow-sm mt-3">
                            <div class="card-body">
                                <div class="alert alert-warning mb-0">
                                    <i class="ri-alert-line me-1"></i>
                                    <strong>Draft Ditemukan!</strong><br>
                                    <small>Anda memiliki draft scoring yang belum selesai. Data akan di-update jika submit.</small>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </form>

    </div>
</div>
@endsection

@section('scripts')
<script>
    const totalParts = "{{$sections-> sum(fn($s) => $s -> parts -> count())}}";
    let scoredParts = 0;
    let okCount = 0;
    let notOkCount = 0;

    function selectResult(btn, index) {
        const partItem = btn.closest('.part-item');
        const resultInput = partItem.querySelector('.result-input');
        const allBtns = partItem.querySelectorAll('.result-btn');
        const result = btn.dataset.result;

        // Remove active class from all buttons
        allBtns.forEach(b => {
            b.classList.remove('active-ok', 'active-not-ok');
        });

        // Add active class to selected button
        if (result === 'OK') {
            btn.classList.add('active-ok');
        } else {
            btn.classList.add('active-not-ok');
        }

        // Set hidden input value
        resultInput.value = result;

        // Update progress
        updateProgress();
    }

    function updateProgress() {
        scoredParts = 0;
        okCount = 0;
        notOkCount = 0;

        document.querySelectorAll('.result-input').forEach(input => {
            if (input.value) {
                scoredParts++;
                if (input.value === 'OK') okCount++;
                else notOkCount++;
            }
        });

        const percentage = Math.round((scoredParts / totalParts) * 100);

        document.getElementById('scoredParts').textContent = scoredParts;
        document.getElementById('okCount').textContent = okCount;
        document.getElementById('notOkCount').textContent = notOkCount;
        document.getElementById('progressText').textContent = percentage + '%';
        document.getElementById('progressBar').style.width = percentage + '%';
        console.log(scoredParts);
        console.log(totalParts);

        // ✅ Disable tombol "Selesai & Submit" jika belum semua part diisi
        const btnComplete = document.getElementById('btnComplete');
        if (scoredParts == totalParts) {
            btnComplete.disabled = false;
            btnComplete.classList.remove('disabled');
            btnComplete.title = '';
        } else {
            btnComplete.disabled = true;
            btnComplete.classList.add('disabled');
            btnComplete.title = 'Lengkapi semua scoring terlebih dahulu';
        }
    }


    // Form validation
    document.getElementById('scoringForm').addEventListener('submit', function(e) {
        const status = e.submitter.value;

        console.log('=== Form Submitted ===');
        console.log('Status:', status);
        console.log('Scored Parts:', scoredParts);
        console.log('Total Parts:', totalParts);

        // Debug semua data input
        const formData = new FormData(this);
        console.log('Form data preview:');
        for (const [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }

        if (status === 'completed' && scoredParts < totalParts) {
            e.preventDefault();
            alert('Harap lengkapi semua scoring sebelum submit!');
            return false;
        }

        if (status === 'draft' && scoredParts === 0) {
            e.preventDefault();
            alert('Minimal scoring 1 part untuk menyimpan draft!');
            return false;
        }
    });


    // Initialize progress on load (for existing draft)
    document.addEventListener('DOMContentLoaded', function() {
        updateProgress(); // Panggil pertama kali untuk set awal
        AOS.init({
            duration: 800,
            once: true
        });

        // Tombol simpan draft AJAX
        const btnSaveDraft = document.getElementById('btnSaveDraft');
        btnSaveDraft.addEventListener('click', function() {
            console.log('🟢 Tombol Simpan Draft diklik');

            // Buat form data
            const form = document.getElementById('scoringForm');
            const formData = new FormData(form);
            formData.append('status', 'draft');

            // Validasi minimal 1 part diisi
            if (scoredParts === 0) {
                alert('Minimal scoring 1 part untuk menyimpan draft!');
                return;
            }

            // Disable tombol sementara
            btnSaveDraft.disabled = true;
            btnSaveDraft.innerHTML = '<i class="ri-loader-4-line me-1 spin"></i> Menyimpan...';

            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
                    }
                })
                .then(res => res.json())
                .then(data => {
                    console.log('✅ Draft tersimpan:', data);
                    alert('Draft berhasil disimpan!');
                })
                .catch(err => {
                    console.error('❌ Gagal menyimpan draft:', err);
                    alert('Terjadi kesalahan saat menyimpan draft.');
                })
                .finally(() => {
                    btnSaveDraft.disabled = false;
                    btnSaveDraft.innerHTML = '<i class="ri-save-line me-1"></i> Simpan Draft';
                });
        });
    });
</script>
@endsection