@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Konfigurasi API Fonnte</h1>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                        role="alert">
                        <strong class="font-bold">Berhasil!</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <form action="{{ route('admin.chat.api.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="token" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Token API
                            Fonnte</label>
                        <input type="password" name="token" id="token" value="{{ $token ? $token->token : '' }}"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Masukkan Token API Fonnte">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Token akan disimpan secara terenkripsi. Dapatkan token di <a href="https://fonnte.com"
                                target="_blank" class="text-blue-500 hover:text-blue-600">Fonnte Dashboard</a>.
                        </p>
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit"
                            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm transition-colors">
                            Simpan Token
                        </button>

                        <button type="button" id="check-connection-btn"
                            class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg text-sm transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Cek Koneksi
                        </button>
                    </div>
                </form>

                <div id="connection-result" class="mt-6 hidden">
                    <!-- Result will be populated here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkBtn = document.getElementById('check-connection-btn');
            const resultDiv = document.getElementById('connection-result');
            const tokenInput = document.getElementById('token');

            checkBtn.addEventListener('click', function () {
                // Disable button
                checkBtn.disabled = true;
                checkBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memeriksa...
                `;
                resultDiv.classList.add('hidden');
                resultDiv.innerHTML = '';

                fetch("{{ route('admin.chat.api.check') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({})
                })
                    .then(response => response.json())
                    .then(data => {
                        resultDiv.classList.remove('hidden');
                        if (data.status === 'success') {
                            resultDiv.innerHTML = `
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                                <strong class="font-bold">Koneksi Berhasil!</strong>
                                <p class="block sm:inline">${data.message}</p>
                            </div>
                        `;
                        } else {
                            resultDiv.innerHTML = `
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                                <strong class="font-bold">Gagal!</strong>
                                <p class="block sm:inline">${data.message}</p>
                            </div>
                        `;
                        }

                        // Show notification in dashboard/layout if needed, though this local container is good.
                        // If dashboard global notification is strictly required, we can manipulate DOM or reload page with flash.
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        resultDiv.classList.remove('hidden');
                        resultDiv.innerHTML = `
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <strong class="font-bold">Error!</strong>
                            <p class="block sm:inline">Terjadi kesalahan saat memeriksa koneksi.</p>
                        </div>
                    `;
                    })
                    .finally(() => {
                        checkBtn.disabled = false;
                        checkBtn.innerHTML = `
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Cek Koneksi
                    `;
                    });
            });
        });
    </script>
@endsection