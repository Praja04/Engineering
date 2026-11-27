@extends('errors.minimal')

@section('title', '419 - Sesi Halaman Kedaluwarsa')
@section('code', '419')
@section('heading', 'Sesi Halaman Kedaluwarsa')
@section('message', 'Sesi keamanan atau token form (CSRF) Anda telah habis karena tidak ada aktivitas dalam waktu tertentu. Silakan muat ulang halaman untuk melanjutkan.')

@section('extra_actions')
    <button type="button" onclick="window.location.reload()" class="btn btn-warning btn-action shadow-sm">
        <i class="ri-refresh-line me-1"></i> Muat Ulang Halaman
    </button>
@endsection
