@extends('errors.minimal')

@section('title', '401 - Akses Tidak Diizinkan')
@section('code', '401')
@section('heading', 'Akses Tidak Diizinkan')
@section('message', 'Sesi login Anda belum aktif atau telah berakhir. Silakan masuk kembali ke akun Anda untuk melanjutkan aktivitas.')

@section('extra_actions')
    <a href="{{ route('login') }}" class="btn btn-success btn-action shadow-sm">
        <i class="ri-login-box-line me-1"></i> Masuk / Login
    </a>
@endsection
