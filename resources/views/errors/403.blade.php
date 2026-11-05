@extends('errors.minimal')

@section('title', '403 - Akses Ditolak')
@section('code', '403')
@section('heading', 'Akses Ditolak (Forbidden)')
@section('message', __($exception->getMessage() ?: 'Maaf, akun Anda tidak memiliki hak akses atau izin (permission) yang diperlukan untuk membuka halaman ini. Silakan hubungi Administrator jika Anda memerlukan akses.'))
