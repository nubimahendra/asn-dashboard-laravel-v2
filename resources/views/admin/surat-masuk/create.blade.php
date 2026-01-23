@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center gap-2 mb-6">
            <a href="{{ route('surat-masuk.index') }}"
                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Tambah Surat Masuk</h1>
        </div>

        <div class="max-w-3xl bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 border-t-4 border-blue-500">
            <form action="{{ route('surat-masuk.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Tanggal Terima & No Agenda in one row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tanggal_terima"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Terima</label>
                        <input type="date" id="tanggal_terima" name="tanggal_terima"
                            value="{{ old('tanggal_terima', date('Y-m-d')) }}" required
                            class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-gray-100 transition-all">
                        @error('tanggal_terima') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="nomor_agenda"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomor Agenda</label>
                        <input type="text" id="nomor_agenda" name="nomor_agenda" value="{{ old('nomor_agenda') }}" required
                            class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-gray-100 transition-all">
                        @error('nomor_agenda') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- No Surat & Pengirim -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nomor_surat"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomor Surat</label>
                        <input type="text" id="nomor_surat" name="nomor_surat" value="{{ old('nomor_surat') }}" required
                            class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-gray-100 transition-all">
                        @error('nomor_surat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="pengirim"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pengirim</label>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Ketik untuk mencari nama PD (Perangkat
                            Daerah)</p>
                        <input type="text" id="pengirim" name="pengirim" value="{{ old('pengirim') }}" list="pd-list"
                            required
                            class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-gray-100 transition-all"
                            autocomplete="off">
                        <datalist id="pd-list">
                            @foreach($listOpd as $opd)
                                <option value="{{ $opd }}">
                            @endforeach
                        </datalist>
                        @error('pengirim') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="perihal"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Perihal</label>
                    <input type="text" id="perihal" name="perihal" value="{{ old('perihal') }}" required
                        class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-gray-100 transition-all">
                    @error('perihal') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="disposisi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Disposisi
                        (Opsional)</label>
                    <textarea id="disposisi" name="disposisi" rows="3"
                        class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-gray-100 transition-all">{{ old('disposisi') }}</textarea>
                    @error('disposisi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="keterangan"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan
                        (Opsional)</label>
                    <textarea id="keterangan" name="keterangan" rows="2"
                        class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-gray-100 transition-all">{{ old('keterangan') }}</textarea>
                    @error('keterangan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('surat-masuk.index') }}"
                        class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 transition-all">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2 text-sm font-bold text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700 hover:-translate-y-0.5 focus:ring-2 focus:ring-offset-1 focus:ring-blue-500 transition-all transform">
                        Simpan Surat
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection