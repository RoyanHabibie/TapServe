@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    <h2>Selamat datang, {{ Auth::user()->name }}!</h2>
    <p>Ini adalah dashboard admin. Fitur lengkap akan segera hadir.</p>
@endsection
