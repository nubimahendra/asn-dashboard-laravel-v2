@extends('layouts.siput')

@section('content')
<div class="container mx-auto px-4 md:px-10 py-8">

    <!-- Notifikasi -->
    @if(session('success'))
        <div class="mb-6 p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 dark:bg-gray-800 dark:text-green-400 dark:border-green-800 flex items-center gap-2 shadow-sm" role="alert">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <div><span class="font-bold">Berhasil!</span> {{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 dark:bg-gray-800 dark:text-red-400 dark:border-red-800 flex items-center gap-2 shadow-sm" role="alert">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            <div><span class="font-bold">Gagal!</span> {{ session('error') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 dark:bg-gray-800 dark:text-red-400 dark:border-red-800 shadow-sm" role="alert">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span class="font-bold">Peringatan! Data tidak bisa disimpan karena:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 ml-7">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Bagian 1: Form Input -->
    <div id="form-input-slks" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 mb-8 overflow-hidden">
        <form id="mainForm" method="POST" action="">
            @csrf
            @method('PUT')
            
            <!-- Form Header -->
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 text-center bg-gray-50 dark:bg-gray-900/50 relative">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">APPROVE DATA USUL SLKS</h2>
            </div>

            <!-- Form Body: Dua Kolom -->
            <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Kolom Kiri: Data Pegawai -->
                <div class="space-y-4">
                    <h3 class="text-md font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">Pencarian & Data Pegawai</h3>
                    
                    <div class="grid grid-cols-3 items-center gap-4 relative">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 col-span-1">Cari NIP/Nama <span class="text-red-500">*</span></label>
                        <div class="col-span-2 relative">
                            <input type="text" id="form_nip" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 pr-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Ketik NIP/Nama..." required>
                            <div id="nip-loading" class="absolute inset-y-0 right-0 flex items-center pr-3 hidden">
                                <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                        <p id="nip-message" class="text-xs text-red-500 hidden col-start-2 col-span-2 mt-1">Data usulan tidak ditemukan.</p>
                    </div>
                    
                    <div class="grid grid-cols-3 items-center gap-4">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 col-span-1">Nama</label>
                        <input type="text" id="form_nama" class="col-span-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" readonly>
                    </div>
                    
                    <div class="grid grid-cols-3 items-center gap-4">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 col-span-1">Pangkat</label>
                        <input type="text" id="form_pangkat" class="col-span-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" readonly>
                    </div>
                    
                    <div class="grid grid-cols-3 items-center gap-4">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 col-span-1">Jabatan</label>
                        <input type="text" id="form_jabatan" class="col-span-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" readonly>
                    </div>

                    <div class="grid grid-cols-3 items-center gap-4">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 col-span-1">Masa Kerja</label>
                        <input type="text" id="form_mk_display" class="col-span-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" readonly>
                    </div>
                </div>
                
                <!-- Kolom Kanan: Approve Data -->
                <div class="space-y-4">
                    <h3 class="text-md font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">Input Keppres</h3>
                    
                    <div class="grid grid-cols-3 items-center gap-4">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 col-span-1">Usul SLKS</label>
                        <input type="text" id="form_usul_slks" class="col-span-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white font-bold text-blue-600 dark:text-blue-400" readonly>
                    </div>
                    
                    <div class="grid grid-cols-3 items-center gap-4">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 col-span-1">No Keppres</label>
                        <input type="text" name="no_kepres" id="form_no_kepres" class="col-span-2 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Nomor Keppres" disabled>
                    </div>
                    
                    <div class="grid grid-cols-3 items-center gap-4">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 col-span-1">Tanggal Keppres</label>
                        <input type="date" name="tanggal_kepres" id="form_tanggal_kepres" class="col-span-2 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" disabled>
                    </div>
                </div>
            </div>
            
            <!-- Footer Form -->
            <div class="p-6 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex justify-center items-center gap-4">
                <button type="button" onclick="clearForm()" class="text-gray-700 bg-gray-200 hover:bg-gray-300 focus:ring-4 focus:outline-none focus:ring-gray-300 font-bold rounded-lg text-sm px-8 py-3 text-center dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 dark:focus:ring-gray-800 transition-colors shadow-sm">
                    CLEAR
                </button>
                <button type="submit" id="submit-btn" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-bold rounded-lg text-sm px-8 py-3 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-colors shadow-md disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    SIMPAN APPROVE
                </button>
            </div>
        </form>
    </div>

    <!-- Bagian 2: Tabel Usulan -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-900/50">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white">Data Usulan SLKS</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap">Aksi</th>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap">NIP / Nama</th>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap">Usul SLKS</th>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap">No Keppres</th>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap">Tgl Keppres</th>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap">Tahun</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button type="button" onclick="loadData('{{ $item->nip }}')" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Pilih</button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $item->nip }}</div>
                                <div class="text-xs">{{ $item->nama }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-blue-600 dark:text-blue-400">
                                {{ $item->usul_slks ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white font-medium">
                                {{ $item->no_kepres ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $item->tanggal_kepres ? $item->tanggal_kepres->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $item->tahunp ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                Tidak ada data usulan SLKS.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
            {{ $data->links() }}
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nipInput = document.getElementById('form_nip');
        let typingTimer;
        const doneTypingInterval = 500;

        nipInput.addEventListener('input', function() {
            clearTimeout(typingTimer);
            
            const val = this.value.trim();
            if (val.length >= 3) {
                typingTimer = setTimeout(() => {
                    searchPegawai(val);
                }, doneTypingInterval);
            } else {
                document.getElementById('nip-message').classList.add('hidden');
                clearFormDetails();
            }
        });
    });

    function loadData(nip) {
        document.getElementById('form_nip').value = nip;
        searchPegawai(nip);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function searchPegawai(query) {
        document.getElementById('nip-loading').classList.remove('hidden');
        document.getElementById('nip-message').classList.add('hidden');
        
        fetch(`{{ route('siput.usul-slks.search-approve') }}?nip=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('nip-loading').classList.add('hidden');
                
                if (data.found) {
                    document.getElementById('form_nama').value = data.nama;
                    document.getElementById('form_pangkat').value = data.pangkat;
                    document.getElementById('form_jabatan').value = data.jabatan;
                    
                    const mkTahun = data.masa_kerja_tahun ?? 0;
                    const mkBulan = data.masa_kerja_bulan ?? 0;
                    document.getElementById('form_mk_display').value = mkTahun > 0 || mkBulan > 0 ? `${mkTahun} Thn ${mkBulan} Bln` : '';
                    
                    document.getElementById('form_usul_slks').value = data.usul_slks;
                    document.getElementById('form_no_kepres').value = data.no_kepres || '';
                    document.getElementById('form_tanggal_kepres').value = data.tanggal_kepres || '';
                    
                    // Set action URL and enable inputs
                    document.getElementById('mainForm').action = `{{ url('siput/usul-slks') }}/${data.id}/approve`;
                    document.getElementById('form_no_kepres').disabled = false;
                    document.getElementById('form_tanggal_kepres').disabled = false;
                    document.getElementById('submit-btn').disabled = false;
                } else {
                    document.getElementById('nip-message').classList.remove('hidden');
                    clearFormDetails();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('nip-loading').classList.add('hidden');
                clearFormDetails();
            });
    }

    function clearFormDetails() {
        document.getElementById('form_nama').value = '';
        document.getElementById('form_pangkat').value = '';
        document.getElementById('form_jabatan').value = '';
        document.getElementById('form_mk_display').value = '';
        document.getElementById('form_usul_slks').value = '';
        document.getElementById('form_no_kepres').value = '';
        document.getElementById('form_tanggal_kepres').value = '';
        
        document.getElementById('form_no_kepres').disabled = true;
        document.getElementById('form_tanggal_kepres').disabled = true;
        document.getElementById('submit-btn').disabled = true;
        document.getElementById('mainForm').action = '';
    }

    function clearForm() {
        document.getElementById('form_nip').value = '';
        document.getElementById('nip-message').classList.add('hidden');
        clearFormDetails();
    }
</script>
@endsection
