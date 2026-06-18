@extends('layouts.siput')

@section('content')
<div class="container mx-auto px-4 md:px-10 py-8">

    <!-- Notifikasi -->
    @if(session('success'))
        <div class="mb-6 p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 dark:bg-gray-800 dark:text-green-400 dark:border-green-800 flex items-center gap-2 shadow-sm" role="alert">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <div><span class="font-bold">Simpan perubahan berhasil!</span> {{ session('success') }}</div>
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

    <!-- Alert Frontend Validation -->
    <div id="frontend-validation-alert" class="hidden mb-6 p-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 border border-yellow-200 dark:bg-gray-800 dark:text-yellow-400 dark:border-yellow-800 shadow-sm" role="alert">
        <div class="flex items-center gap-2 mb-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <span class="font-bold">Peringatan Validasi:</span>
        </div>
        <ul id="frontend-validation-messages" class="list-disc list-inside space-y-1 ml-7"></ul>
    </div>

    <!-- Bagian 1: Form Input -->
    <div id="form-input-slks" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 mb-8 overflow-hidden">
        <form action="{{ isset($usulSlks) ? route('siput.usul-slks.update', $usulSlks->id) : route('siput.usul-slks.store') }}" method="POST" id="mainForm">
            @csrf
            @if(isset($usulSlks))
                @method('PUT')
            @endif
            
            <!-- Form Header -->
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 text-center bg-gray-50 dark:bg-gray-900/50 relative">
                @if(isset($usulSlks))
                    <a href="{{ route('siput.usul-slks.manage') }}" class="absolute left-6 top-6 text-gray-500 hover:text-gray-700 flex items-center gap-1 text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali
                    </a>
                @endif
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ isset($usulSlks) ? 'EDIT DATA USUL SLKS' : 'INPUT DATA USUL SLKS' }}</h2>
            </div>

            <!-- Form Body: Dua Kolom -->
            <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Kolom Kiri: Data Pegawai -->
                <div class="space-y-4">
                    <h3 class="text-md font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">Data Pegawai</h3>
                    
                    <div class="grid grid-cols-3 items-center gap-4 relative">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 col-span-1">NIP <span class="text-red-500">*</span></label>
                        <div class="col-span-2 relative">
                            <input type="text" name="nip" id="form_nip" value="{{ old('nip', $usulSlks->nip ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 pr-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Ketik NIP..." {{ isset($usulSlks) ? 'readonly' : 'required' }}>
                            <div id="nip-loading" class="absolute inset-y-0 right-0 flex items-center pr-3 hidden">
                                <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                        <p id="nip-message" class="text-xs text-red-500 hidden col-start-2 col-span-2 mt-1">NIP tidak ditemukan.</p>
                    </div>
                    
                    <div class="grid grid-cols-3 items-center gap-4">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 col-span-1">Nama <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" id="form_nama" value="{{ old('nama', $usulSlks->nama ?? '') }}" class="col-span-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" readonly required>
                    </div>
                    
                    <div class="grid grid-cols-3 items-center gap-4">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 col-span-1">Pangkat</label>
                        <input type="text" name="pangkat" id="form_pangkat" value="{{ old('pangkat', $usulSlks->pangkat ?? '') }}" class="col-span-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" readonly>
                    </div>
                    
                    <div class="grid grid-cols-3 items-center gap-4">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 col-span-1">Jabatan</label>
                        <input type="text" name="jabatan" id="form_jabatan" value="{{ old('jabatan', $usulSlks->jabatan ?? '') }}" class="col-span-2 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>

                    {{-- MK: display readonly + hidden inputs untuk submit --}}
                    <div class="grid grid-cols-3 items-center gap-4">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 col-span-1">Masa Kerja</label>
                        <div class="col-span-2">
                            <input type="text" id="form_mk_display"
                                value="{{ isset($usulSlks) && $usulSlks->masa_kerja_tahun !== null ? $usulSlks->masa_kerja_tahun . ' Thn ' . ($usulSlks->masa_kerja_bulan ?? 0) . ' Bln' : '' }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="(otomatis dari NIP)" readonly>
                            {{-- Hidden: nilai numerik yang dikirim ke server --}}
                            <input type="hidden" name="masa_kerja_tahun" id="form_mk_tahun"
                                value="{{ old('masa_kerja_tahun', $usulSlks->masa_kerja_tahun ?? '') }}">
                            <input type="hidden" name="masa_kerja_bulan" id="form_mk_bulan"
                                value="{{ old('masa_kerja_bulan', $usulSlks->masa_kerja_bulan ?? '') }}">
                        </div>
                    </div>

                    {{-- Kedudukan Hukum: display + hidden id --}}
                    <div class="grid grid-cols-3 items-center gap-4">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 col-span-1">Kedudukan Hukum</label>
                        <div class="col-span-2">
                            <input type="text" id="form_kedudukan_hukum_display"
                                value="{{ isset($usulSlks) && $usulSlks->kedudukan_hukum_id ? $usulSlks->kedudukan_hukum_id : '' }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="(otomatis dari NIP)" readonly>
                            {{-- Hidden: id kode kedudukan hukum --}}
                            <input type="hidden" name="kedudukan_hukum_id" id="form_kedudukan_hukum_id"
                                value="{{ old('kedudukan_hukum_id', $usulSlks->kedudukan_hukum_id ?? '') }}">
                            {{-- Hidden: jenis pegawai PNS/PPPK --}}
                            <input type="hidden" name="jenis_pegawai" id="form_jenis_pegawai"
                                value="{{ old('jenis_pegawai', $usulSlks->jenis_pegawai ?? '') }}">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-3 items-center gap-4">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 col-span-1">No SK Hukdis / TMT</label>
                        <div class="col-span-2 grid grid-cols-2 gap-2">
                            <input type="text" name="no_sk_hukdis" value="{{ old('no_sk_hukdis', $usulSlks->no_sk_hukdis ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Nomor SK">
                            <input type="date" name="tmt_hukdis" value="{{ old('tmt_hukdis', isset($usulSlks) && $usulSlks->tmt_hukdis ? $usulSlks->tmt_hukdis->format('Y-m-d') : '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-3 items-center gap-4">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 col-span-1">No SK CLTN / TMT</label>
                        <div class="col-span-2 grid grid-cols-2 gap-2">
                            <input type="text" name="no_sk_cltn" value="{{ old('no_sk_cltn', $usulSlks->no_sk_cltn ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Nomor SK">
                            <input type="date" name="tmt_cltn" value="{{ old('tmt_cltn', isset($usulSlks) && $usulSlks->tmt_cltn ? $usulSlks->tmt_cltn->format('Y-m-d') : '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-3 items-center gap-4">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 col-span-1">Kabkota</label>
                        <input type="text" name="kabkota" value="Kab. Blitar" class="col-span-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" readonly>
                    </div>
                    
                    <div class="grid grid-cols-3 items-center gap-4">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 col-span-1">Provinsi</label>
                        <input type="text" name="provinsi" value="Jawa Timur" class="col-span-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" readonly>
                    </div>
                    
                    <div class="grid grid-cols-3 items-center gap-4">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 col-span-1">Kd Wil</label>
                        <input type="text" name="kd_wil" value="12636" class="col-span-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" readonly>
                    </div>
                </div>
                
                <!-- Kolom Kanan: Riwayat & Usulan -->
                <div class="space-y-6">
                    
                    <!-- C. SLKS yang diusulkan -->
                    <div class="space-y-4">
                        <h3 class="text-md font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">SLKS yang diusulkan</h3>
                        
                        <div class="grid grid-cols-3 items-center gap-4">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 col-span-1">Slks Usul</label>
                            @php $usul_slks_val = old('usul_slks', $usulSlks->usul_slks ?? ''); @endphp
                            <select name="usul_slks" id="form_usul_slks" onchange="validateForm()" class="col-span-2 bg-blue-50 border border-blue-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-blue-900/30 dark:border-blue-700 dark:text-white font-medium">
                                <option value="">-- Pilih --</option>
                                <option value="10 Tahun" {{ $usul_slks_val == '10 Tahun' ? 'selected' : '' }}>10 Tahun</option>
                                <option value="20 Tahun" {{ $usul_slks_val == '20 Tahun' ? 'selected' : '' }}>20 Tahun</option>
                                <option value="30 Tahun" {{ $usul_slks_val == '30 Tahun' ? 'selected' : '' }}>30 Tahun</option>
                            </select>
                        </div>
                        
                        <div class="grid grid-cols-3 items-center gap-4">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 col-span-1">Bulan/Tahunp</label>
                            <div class="col-span-2 grid grid-cols-2 gap-2">
                                @php $bulanp = old('bulanp', $usulSlks->bulanp ?? date('m')); @endphp
                                <select name="bulanp" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @for($i=1; $i<=12; $i++)
                                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $bulanp == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                    @endfor
                                </select>
                                @php $tahunp = old('tahunp', $usulSlks->tahunp ?? date('Y')); @endphp
                                <select name="tahunp" id="form_tahunp" onchange="validateForm()" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @for($y=date('Y')-1; $y<=date('Y')+1; $y++)
                                        <option value="{{ $y }}" {{ $tahunp == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-3 items-center gap-4">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 col-span-1">Ms Tms</label>
                            @php $ms_tms = old('ms_tms', $usulSlks->ms_tms ?? 'MS'); @endphp
                            <select name="ms_tms" id="form_ms_tms" onchange="toggleKetTms()" class="col-span-2 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="MS" {{ $ms_tms == 'MS' ? 'selected' : '' }}>Memenuhi Syarat (MS)</option>
                                <option value="TMS" {{ $ms_tms == 'TMS' ? 'selected' : '' }}>Tidak Memenuhi Syarat (TMS)</option>
                            </select>
                        </div>
                        
                        <div id="ket_tms_container" class="grid grid-cols-3 items-start gap-4 {{ $ms_tms == 'TMS' ? '' : 'hidden' }}">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 col-span-1 mt-2">Ket Tms</label>
                            <textarea name="ket_tms" id="form_ket_tms" rows="2" class="col-span-2 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('ket_tms', $usulSlks->ket_tms ?? '') }}</textarea>
                        </div>
                    </div>

                    <!-- Riwayat SLKS (Dinamis) -->
                    <div id="riwayat-container" class="hidden">
                        <h3 class="text-md font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">Riwayat SLKS yang sudah diperoleh</h3>
                        <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-lg">
                            <table class="min-w-full text-sm divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-medium">Slks</th>
                                        <th class="px-4 py-2 text-left font-medium">Nokeppres</th>
                                        <th class="px-4 py-2 text-left font-medium">Tglkeppres</th>
                                    </tr>
                                </thead>
                                <tbody id="riwayat-body" class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700 text-gray-800 dark:text-gray-200">
                                    <!-- Diisi oleh JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                </div>
            </div>
            
            <!-- Footer Form -->
            <div class="p-6 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex justify-center items-center gap-4">
                @if(!isset($usulSlks))
                    <button type="button" onclick="clearForm()" class="text-gray-700 bg-gray-200 hover:bg-gray-300 focus:ring-4 focus:outline-none focus:ring-gray-300 font-bold rounded-lg text-sm px-8 py-3 text-center dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 dark:focus:ring-gray-800 transition-colors shadow-sm">
                        CLEAR
                    </button>
                @endif
                <button type="submit" id="submit-btn" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-bold rounded-lg text-sm px-8 py-3 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-colors shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ isset($usulSlks) ? 'UPDATE USULAN' : 'SIMPAN USULAN' }}
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    window.pegawaiRiwayat = [];
    window.masaKerjaTahun = 0;
    @if(isset($usulSlks) && $usulSlks->masa_kerja_tahun !== null)
        window.masaKerjaTahun = {{ $usulSlks->masa_kerja_tahun }};
    @endif
    window.currentUsulId = {{ isset($usulSlks) ? $usulSlks->id : 'null' }};

    document.addEventListener('DOMContentLoaded', function() {
        const nipInput = document.getElementById('form_nip');
        let typingTimer;
        const doneTypingInterval = 500; // 500ms

        @if(!isset($usulSlks))
            nipInput.addEventListener('input', function() {
                clearTimeout(typingTimer);
                
                // Allow user to type, only search if length is at least 18 (NIP standard)
                const nipValue = this.value.trim();
                
                if (nipValue.length >= 18) {
                    typingTimer = setTimeout(() => {
                        searchPegawai(nipValue);
                    }, doneTypingInterval);
                } else {
                    document.getElementById('nip-message').classList.add('hidden');
                }
            });
        @else
            // If editing, try to load riwayat if NIP is present
            if(nipInput.value) {
                loadRiwayat(nipInput.value);
            }
        @endif
    });

    function searchPegawai(nip) {
        document.getElementById('nip-loading').classList.remove('hidden');
        document.getElementById('nip-message').classList.add('hidden');
        
        fetch(`{{ route('siput.usul-slks.search') }}?nip=${nip}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('nip-loading').classList.add('hidden');
                
                if (data.found) {
                    document.getElementById('form_nama').value = data.nama;
                    document.getElementById('form_pangkat').value = data.pangkat;
                    document.getElementById('form_jabatan').value = data.jabatan;
                    
                    // Isi MK display dan hidden inputs
                    const mkTahun = data.mk_tahun ?? 0;
                    const mkBulan = data.mk_bulan ?? 0;
                    document.getElementById('form_mk_display').value =
                        mkTahun > 0 || mkBulan > 0 ? `${mkTahun} Thn ${mkBulan} Bln` : '';
                    document.getElementById('form_mk_tahun').value = mkTahun;
                    document.getElementById('form_mk_bulan').value = mkBulan;

                    // Isi Kedudukan Hukum
                    const kdhId   = data.kedudukan_hukum_id ?? '';
                    const kdhNama = data.kedudukan_hukum ?? '';
                    // Tampilkan: "01 - Aktif" jika ada nama, atau hanya kode
                    document.getElementById('form_kedudukan_hukum_display').value =
                        kdhId ? (kdhNama ? `${kdhId} - ${kdhNama}` : kdhId) : '';
                    document.getElementById('form_kedudukan_hukum_id').value = kdhId;

                    // Isi jenis pegawai
                    document.getElementById('form_jenis_pegawai').value = data.jenis_pegawai ?? '';

                    // Auto select SLKS Usul based on masa kerja
                    let usulSlks = '';
                    if (mkTahun >= 30) usulSlks = '30 Tahun';
                    else if (mkTahun >= 20) usulSlks = '20 Tahun';
                    else if (mkTahun >= 10) usulSlks = '10 Tahun';
                    document.getElementById('form_usul_slks').value = usulSlks;
                    
                    // Render Riwayat
                    window.pegawaiRiwayat = data.riwayat || [];
                    window.masaKerjaTahun = mkTahun;
                    renderRiwayat(data.riwayat);
                    
                    // Validate after loading
                    validateForm();
                } else {
                    document.getElementById('nip-message').classList.remove('hidden');
                    // Kosongkan semua field yang diisi otomatis
                    document.getElementById('form_nama').value = '';
                    document.getElementById('form_pangkat').value = '';
                    document.getElementById('form_jabatan').value = '';
                    document.getElementById('form_mk_display').value = '';
                    document.getElementById('form_mk_tahun').value = '';
                    document.getElementById('form_mk_bulan').value = '';
                    document.getElementById('form_kedudukan_hukum_display').value = '';
                    document.getElementById('form_kedudukan_hukum_id').value = '';
                    document.getElementById('form_jenis_pegawai').value = '';
                    document.getElementById('riwayat-container').classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('nip-loading').classList.add('hidden');
            });
    }

    function loadRiwayat(nip) {
        fetch(`{{ route('siput.usul-slks.search') }}?nip=${nip}`)
            .then(response => response.json())
            .then(data => {
                if (data.found) {
                    window.pegawaiRiwayat = data.riwayat || [];
                    window.masaKerjaTahun = data.mk_tahun ?? window.masaKerjaTahun;
                    renderRiwayat(data.riwayat);
                    validateForm();
                }
            });
    }

    function renderRiwayat(riwayatList) {
        const container = document.getElementById('riwayat-container');
        const tbody = document.getElementById('riwayat-body');
        
        if (riwayatList && riwayatList.length > 0) {
            tbody.innerHTML = '';
            riwayatList.forEach(item => {
                // Format tgl_slks
                let tglSlksStr = '-';
                if(item.tgl_slks) {
                    const d = new Date(item.tgl_slks);
                    tglSlksStr = `${d.getDate().toString().padStart(2, '0')}/${(d.getMonth()+1).toString().padStart(2, '0')}/${d.getFullYear()}`;
                }
                
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="px-2 py-1">${item.usul_slks || '-'}</td>
                    <td class="px-2 py-1">${item.no_slks || '-'}</td>
                    <td class="px-2 py-1">${tglSlksStr}</td>
                `;
                tbody.appendChild(tr);
            });
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
            tbody.innerHTML = '';
        }
    }

    function clearForm() {
        document.getElementById('mainForm').reset();
        
        // Explicitly clear specific fields and hidden sections
        document.getElementById('form_nip').value = '';
        document.getElementById('form_nama').value = '';
        document.getElementById('form_pangkat').value = '';
        document.getElementById('form_jabatan').value = '';
        document.getElementById('form_mk_display').value = '';
        document.getElementById('form_mk_tahun').value = '';
        document.getElementById('form_mk_bulan').value = '';
        document.getElementById('form_kedudukan_hukum_display').value = '';
        document.getElementById('form_kedudukan_hukum_id').value = '';
        document.getElementById('form_jenis_pegawai').value = '';
        document.getElementById('nip-message').classList.add('hidden');
        document.getElementById('riwayat-container').classList.add('hidden');
        
        // Reset MS/TMS container
        document.getElementById('form_ms_tms').value = 'MS';
        document.getElementById('ket_tms_container').classList.add('hidden');
        document.getElementById('form_ket_tms').value = '';
        
        // Reset Usul select
        document.getElementById('form_usul_slks').value = '';
    }

    function toggleKetTms() {
        const msTms = document.getElementById('form_ms_tms').value;
        const ketTmsContainer = document.getElementById('ket_tms_container');
        
        if (msTms === 'TMS') {
            ketTmsContainer.classList.remove('hidden');
        } else {
            ketTmsContainer.classList.add('hidden');
        }
    }

    function validateForm() {
        const alertBox = document.getElementById('frontend-validation-alert');
        const messagesUl = document.getElementById('frontend-validation-messages');
        const submitBtn = document.getElementById('submit-btn');
        const usulSlks = document.getElementById('form_usul_slks').value;
        const tahunp = document.getElementById('form_tahunp').value;
        
        let errors = [];
        
        if (!usulSlks || !document.getElementById('form_nip').value) {
            alertBox.classList.add('hidden');
            submitBtn.disabled = false;
            return;
        }

        // 1. Cek Duplikat: usul_slks dan tahunp yang sama
        const hasDuplicate = window.pegawaiRiwayat.some(item => {
            // Jika sedang edit dan ini adalah data yang sedang diedit (berdasarkan id jika ada, atau lewati)
            // Di sini kita tidak punya ID di json, tapi biasanya data update akan replace. 
            // Untuk amannya, kita cek jika NIP punya riwayat yang sama di tahun yang sama
            return item.usul_slks === usulSlks && item.tahunp == tahunp;
        });

        // Karena response searchPegawai juga memuat data yang sedang di-edit (jika dalam mode edit),
        // kita perlu handle ini: jika form sedang mode edit dan tahun/usul tidak berubah dari value awal,
        // maka abaikan duplikat diri sendiri.
        let isDuplicateError = false;
        if (hasDuplicate) {
            @if(isset($usulSlks))
                const originalUsul = "{{ $usulSlks->usul_slks }}";
                const originalTahun = "{{ $usulSlks->tahunp }}";
                if (usulSlks !== originalUsul || tahunp !== originalTahun) {
                    isDuplicateError = true;
                }
            @else
                isDuplicateError = true;
            @endif
        }

        if (isDuplicateError) {
            errors.push(`NIP ini sudah memiliki pengajuan <b>${usulSlks}</b> untuk tahun <b>${tahunp}</b>.`);
        }

        // 2. Cek Masa Kerja < Usulan SLKS
        const slksYearsStr = usulSlks.replace(/[^0-9]/g, '');
        if (slksYearsStr) {
            const slksYears = parseInt(slksYearsStr);
            if (window.masaKerjaTahun < slksYears) {
                errors.push(`Masa kerja (<b>${window.masaKerjaTahun} Tahun</b>) kurang dari usulan SLKS (<b>${slksYears} Tahun</b>).`);
            }
        }

        if (errors.length > 0) {
            messagesUl.innerHTML = errors.map(e => `<li>${e}</li>`).join('');
            alertBox.classList.remove('hidden');
            
            // Disable submit jika ada error duplikat, tapi jika hanya error masa kerja (TMS), kita blokir juga?
            // "tampilkan peringatan" -> kita disable saja biar aman, user harus ganti usul_slks
            submitBtn.disabled = true;
        } else {
            alertBox.classList.add('hidden');
            submitBtn.disabled = false;
        }
    }
</script>
@endsection
