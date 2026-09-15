@extends('layouts.mari')

@section('content')
    <div class="container mx-auto px-10 py-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Pengaturan Invoice</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    Atur logo dan informasi rekening bank yang akan ditampilkan pada Invoice Golongan.
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-8">
            <form action="{{ route('mari.pengaturan.invoice.update') }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8">
                @csrf
                
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 dark:bg-green-900/30 dark:text-green-300 rounded-r-lg">
                        <div class="flex">
                            <svg class="h-5 w-5 text-green-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 dark:bg-red-900/30 dark:text-red-300 rounded-r-lg">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                    <!-- Logo Upload -->
                    <div class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-xl border border-gray-100 dark:border-gray-700">
                        <h4 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Logo KORPRI
                        </h4>
                        
                        <div class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-6">
                            <div class="shrink-0 relative group">
                                @if(!empty($invoiceSettings['logo']))
                                    <img class="h-32 w-32 object-contain rounded-xl border border-gray-200 dark:border-gray-600 p-2 bg-white shadow-sm" src="{{ Storage::url($invoiceSettings['logo']) }}" alt="Logo Invoice" />
                                @else
                                    <div class="h-32 w-32 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 flex flex-col items-center justify-center bg-white dark:bg-gray-800 text-gray-400">
                                        <svg class="h-10 w-10 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span class="text-xs font-medium">Belum ada logo</span>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex-1 w-full">
                                <label class="block">
                                    <span class="sr-only">Choose profile photo</span>
                                    <input type="file" name="invoice_logo" accept="image/png, image/jpeg, image/webp" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/30 dark:file:text-blue-400 transition-colors cursor-pointer" />
                                </label>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                    <strong>Ketentuan Logo:</strong><br>
                                    - Format yang diizinkan: JPG, PNG, WEBP.<br>
                                    - Ukuran file maksimal: 2MB.<br>
                                    - Disarankan menggunakan gambar dengan rasio 1:1 (persegi) dengan latar belakang transparan (PNG).
                                </p>
                                
                                @if(!empty($invoiceSettings['logo']))
                                <div class="mt-4 inline-block bg-red-50 dark:bg-red-900/20 px-3 py-2 rounded-lg border border-red-100 dark:border-red-800/30">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="remove_logo" value="1" class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-red-600 dark:text-red-400 font-medium">Hapus logo yang ada</span>
                                    </label>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Rekening Bank -->
                    <div class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-xl border border-gray-100 dark:border-gray-700 space-y-5">
                        <h4 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            Informasi Rekening Bank
                        </h4>
                        
                        <div>
                            <label for="invoice_bank_nama" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Bank</label>
                            <input type="text" id="invoice_bank_nama" name="invoice_bank_nama" value="{{ old('invoice_bank_nama', $invoiceSettings['bank_nama']) }}" placeholder="Contoh: JATIM" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition shadow-sm">
                        </div>

                        <div>
                            <label for="invoice_bank_rekening" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nomor Rekening</label>
                            <input type="text" id="invoice_bank_rekening" name="invoice_bank_rekening" value="{{ old('invoice_bank_rekening', $invoiceSettings['bank_rekening']) }}" placeholder="Contoh: 00000123" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition shadow-sm">
                        </div>

                        <div>
                            <label for="invoice_bank_atas_nama" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Atas Nama</label>
                            <input type="text" id="invoice_bank_atas_nama" name="invoice_bank_atas_nama" value="{{ old('invoice_bank_atas_nama', $invoiceSettings['bank_atas_nama']) }}" placeholder="Contoh: KORPRI KAB. BLITAR" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition shadow-sm">
                        </div>
                        
                        <div class="pt-2 border-t border-gray-200 dark:border-gray-700 mt-6">
                            <label for="invoice_batas_setor" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Batas Tanggal Setor (1-31)</label>
                            <div class="flex items-center">
                                <input type="number" id="invoice_batas_setor" name="invoice_batas_setor" value="{{ old('invoice_batas_setor', $invoiceSettings['batas_setor']) }}" min="1" max="31" class="w-24 px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition shadow-sm text-center font-bold">
                                <span class="ml-3 text-sm text-gray-500 dark:text-gray-400">Tanggal jatuh tempo per bulan</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-blue-600 border border-transparent rounded-lg font-bold text-sm text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
