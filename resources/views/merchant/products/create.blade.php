@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
<div class="max-w-2xl mx-auto bg-white border border-gray-200 rounded-lg p-6">
    <h1 class="text-xl font-bold mb-4">Tambah Produk</h1>

    {{-- enctype wajib multipart agar file ikut terkirim; tanpa ini upload selalu kosong. --}}
    <form method="POST" action="{{ route('merchant.products.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @include('merchant.products._form')
    </form>
</div>
@endsection