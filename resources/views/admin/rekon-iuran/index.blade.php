@extends('layouts.mari')

@section('header')
<div class="flex justify-between items-center">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Rekonsiliasi Data Iuran Manual') }}
    </h2>
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Filter Bar -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm mb-6 border border-gray-100 dark:border-gray-700">
            <form method="GET" action="{{ route('mari.rekon-iuran.index') }}" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">OPD</label>
                    <select name="opd" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200" onchange="this.form.submit()">
                        <option value="">Semua OPD</option>
                        @foreach($listOpd as $opd)
                            <option value="{{ $opd }}" {{ $filterOpd == $opd ? 'selected' : '' }}>{{ $opd }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Unit Kerja (Unor)</label>
                    <input type="text" name="unor" value="{{ $filterUnor }}" list="listUnor" placeholder="Ketik nama unit kerja..." class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200" onblur="this.form.submit()" onkeypress="if(event.keyCode==13) this.form.submit();">
                    <datalist id="listUnor">
                        @foreach($listUnor as $u)
                            <option value="{{ $u->nama_opd }}">{{ $u->nama_lengkap }}</option>
                        @endforeach
                    </datalist>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Golongan</label>
                    <select name="golongan" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200" onchange="this.form.submit()">
                        <option value="">Semua Golongan</option>
                        @foreach($listGolongan as $gol)
                            <option value="{{ $gol }}" {{ $filterGolongan == $gol ? 'selected' : '' }}>{{ $gol }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Jabatan</label>
                    <input type="text" name="jabatan" value="{{ $filterJabatan }}" placeholder="Cari jabatan..." class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200" onblur="this.form.submit()">
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Cari Nama/NIP</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ $search }}" class="w-full text-sm pl-8 rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200" placeholder="Ketik lalu Enter..." onkeypress="if(event.keyCode==13) this.form.submit();">
                        <svg class="w-4 h-4 text-gray-500 absolute top-2.5 left-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
                <div class="flex items-center gap-3 mb-2">
                    <input type="hidden" name="pns" value="0">
                    <input type="hidden" name="pppk" value="0">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="pns" value="1" class="sr-only peer" {{ $pns ? 'checked' : '' }} onchange="this.form.submit()">
                        <div class="relative w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">PNS</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="pppk" value="1" class="sr-only peer" {{ $pppk ? 'checked' : '' }} onchange="this.form.submit()">
                        <div class="relative w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-600"></div>
                        <span class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">PPPK</span>
                    </label>
                </div>
                <div class="mb-1">
                    <a href="{{ route('mari.rekon-iuran.index') }}" class="text-sm bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg transition-colors">Reset</a>
                </div>
            </form>
        </div>

        <!-- Action Bar -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm mb-6 border border-gray-100 dark:border-gray-700 flex flex-wrap justify-between items-center gap-4">
            <div class="flex items-center">
                <input type="checkbox" id="selectAll" class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                <label for="selectAll" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300"><span id="selectedCount">0</span> pegawai dipilih</label>
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="openBulkModal()" class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors font-medium flex items-center gap-2 disabled:opacity-50" id="btnBulkOverride" disabled>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    Ubah Terpilih
                </button>
                <button type="button" onclick="confirmSyncReset()" class="text-sm bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition-colors font-medium flex items-center gap-2 disabled:opacity-50" id="btnSyncReset" disabled>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Sync Reset BKN
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-3 w-10 text-center"></th>
                            <th scope="col" class="px-4 py-3">Nama / NIP</th>
                            <th scope="col" class="px-4 py-3">Unit Kerja</th>
                            <th scope="col" class="px-4 py-3">Jns Jabatan</th>
                            <th scope="col" class="px-4 py-3 text-center">Gol/Esel<br><span class="text-[10px] font-normal">(Asli BKN)</span></th>
                            <th scope="col" class="px-4 py-3 text-center">Efektif<br><span class="text-[10px] font-normal">(Untuk Iuran)</span></th>
                            <th scope="col" class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pegawaiList as $pegawai)
                            @php
                                $override = $pegawai->iuranOverride;
                                $isStruktural = $pegawai->jenis_jabatan_id == 1;
                                
                                // Asli
                                $golAsli = $pegawai->golongan_pppk;
                                $eselAsli = $isStruktural ? ($eselonMappings[$pegawai->jabatan_id] ?? 'IV/b') : '-';
                                
                                // Efektif
                                $golEfektif = $override && $override->override_golongan_key ? $override->override_golongan_key : $golAsli;
                                $eselEfektif = $isStruktural ? ($override && $override->override_eselon_key ? $override->override_eselon_key : $eselAsli) : '-';
                                
                                $hasOverride = $override != null;
                            @endphp
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-4 py-4 text-center">
                                    <input type="checkbox" value="{{ $pegawai->id }}" class="row-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 cursor-pointer" data-nama="{{ $pegawai->nama }}">
                                </td>
                                <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $pegawai->nama }}<br>
                                    <span class="text-xs text-gray-500">{{ $pegawai->nip_baru }}</span>
                                </th>
                                <td class="px-4 py-4">
                                    {{ $pegawai->unor->nama_lengkap ?? '-' }}
                                </td>
                                <td class="px-4 py-4">
                                    {{ $pegawai->jenisJabatan->nama ?? '-' }}<br>
                                    <span class="text-xs text-gray-500 truncate block max-w-[150px]" title="{{ $pegawai->jabatan->nama ?? '' }}">{{ $pegawai->jabatan->nama ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <div class="text-xs">Gol: <span class="font-semibold">{{ $golAsli }}</span></div>
                                    @if($isStruktural)
                                    <div class="text-xs">Esel: <span class="font-semibold">{{ $eselAsli }}</span></div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if($hasOverride)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 mb-1" title="Data Override">
                                            ⚠️ Override
                                        </span><br>
                                    @endif
                                    <div class="text-xs">Gol: <span class="font-bold {{ $override && $override->override_golongan_key ? 'text-orange-600' : 'text-emerald-600' }}">{{ $golEfektif }}</span></div>
                                    @if($isStruktural)
                                    <div class="text-xs">Esel: <span class="font-bold {{ $override && $override->override_eselon_key ? 'text-orange-600' : 'text-emerald-600' }}">{{ $eselEfektif }}</span></div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" onclick="openSingleModal({{ $pegawai->id }}, '{{ addslashes($pegawai->nama) }}', '{{ $golAsli }}', '{{ $eselAsli }}', '{{ $override->override_golongan_key ?? '' }}', '{{ $override->override_eselon_key ?? '' }}', {{ $isStruktural ? 'true' : 'false' }})" class="text-blue-600 hover:text-blue-900 dark:hover:text-blue-400" title="Edit Override">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        @if($hasOverride)
                                        <button type="button" onclick="deleteOverride({{ $pegawai->id }})" class="text-red-600 hover:text-red-900 dark:hover:text-red-400" title="Hapus Override">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    Tidak ada data pegawai yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pegawaiList->hasPages())
                <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $pegawaiList->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Override -->
<div id="overrideModal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center bg-gray-900/50">
    <div class="relative w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white" id="modalTitle">
                    Ubah Golongan/Eselon
                </h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4" id="modalSubtitle">Untuk X pegawai terpilih</p>
                
                <form id="overrideForm">
                    <input type="hidden" id="formType" value="bulk">
                    <input type="hidden" id="singlePegawaiId" value="">
                    
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Override Golongan</label>
                        <select id="inputGolongan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                            <option value="">-- Kosongkan untuk pakai Asli (BKN) --</option>
                            <optgroup label="═══ Golongan PNS ═══">
                                @foreach(['I/a', 'I/b', 'I/c', 'I/d', 'II/a', 'II/b', 'II/c', 'II/d', 'II/e', 'III/a', 'III/b', 'III/c', 'III/d', 'III/e', 'IV/a', 'IV/b', 'IV/c', 'IV/d', 'IV/e'] as $key)
                                    @if(isset($golonganKeys[$key]))
                                        <option value="{{ $key }}">{{ $golonganKeys[$key] }}</option>
                                    @endif
                                @endforeach
                            </optgroup>
                            <optgroup label="═══ Golongan PPPK ═══">
                                @foreach(['I', 'V', 'VII', 'IX', 'X', 'XI'] as $key)
                                    @if(isset($golonganKeys[$key]))
                                        <option value="{{ $key }}">{{ $golonganKeys[$key] }}</option>
                                    @endif
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                    
                    <div id="eselonContainer">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white mt-4">Override Eselon (Jika Struktural)</label>
                        <select id="inputEselon" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                            <option value="">-- Kosongkan untuk pakai Asli (BKN) --</option>
                            @foreach($eselonKeys as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white mt-4">Alasan Perubahan</label>
                        <input type="text" id="inputAlasan" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="Contoh: Koreksi batch BKN, dll">
                    </div>
                </form>
            </div>
            <!-- Modal footer -->
            <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600 justify-end">
                <button type="button" onclick="closeModal()" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Batal</button>
                <button type="button" onclick="submitOverride()" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Simpan Override</button>
            </div>
        </div>
    </div>
</div>

<script>
    function updateSelectedCount() {
        const count = document.querySelectorAll('.row-checkbox:checked').length;
        document.getElementById('selectedCount').innerText = count;
        
        const disabled = count === 0;
        document.getElementById('btnBulkOverride').disabled = disabled;
        document.getElementById('btnSyncReset').disabled = disabled;
    }

    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateSelectedCount();
    });

    document.querySelectorAll('.row-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            const allChecked = document.querySelectorAll('.row-checkbox:checked').length === document.querySelectorAll('.row-checkbox').length;
            document.getElementById('selectAll').checked = allChecked;
            updateSelectedCount();
        });
    });

    function openBulkModal() {
        const count = document.querySelectorAll('.row-checkbox:checked').length;
        if(count === 0) return;
        
        document.getElementById('formType').value = 'bulk';
        document.getElementById('modalTitle').innerText = 'Bulk Override';
        document.getElementById('modalSubtitle').innerText = `Untuk ${count} pegawai terpilih`;
        document.getElementById('inputGolongan').value = '';
        document.getElementById('inputEselon').value = '';
        document.getElementById('inputAlasan').value = '';
        document.getElementById('eselonContainer').style.display = 'block';
        
        document.getElementById('overrideModal').classList.remove('hidden');
    }

    function openSingleModal(id, nama, golAsli, eselAsli, golOv, eselOv, isStruktural) {
        document.getElementById('formType').value = 'single';
        document.getElementById('singlePegawaiId').value = id;
        document.getElementById('modalTitle').innerText = 'Override Iuran';
        document.getElementById('modalSubtitle').innerText = nama;
        document.getElementById('inputGolongan').value = golOv;
        document.getElementById('inputEselon').value = eselOv;
        document.getElementById('inputAlasan').value = '';
        
        document.getElementById('eselonContainer').style.display = isStruktural ? 'block' : 'none';
        
        document.getElementById('overrideModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('overrideModal').classList.add('hidden');
    }

    function getSelectedIds() {
        return Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
    }

    function submitOverride() {
        const type = document.getElementById('formType').value;
        const url = type === 'bulk' ? '{{ route("mari.rekon-iuran.bulk-override") }}' : '{{ route("mari.rekon-iuran.single-override") }}';
        
        const payload = {
            override_golongan_key: document.getElementById('inputGolongan').value,
            override_eselon_key: document.getElementById('inputEselon').value,
            alasan: document.getElementById('inputAlasan').value,
            _token: '{{ csrf_token() }}',
            _method: 'PUT'
        };

        if(payload.alasan.trim() === '') {
            alert('Alasan wajib diisi!');
            return;
        }

        if (type === 'bulk') {
            payload.pegawai_ids = getSelectedIds();
        } else {
            payload.pegawai_id = document.getElementById('singlePegawaiId').value;
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error request ke server');
        });
    }

    function deleteOverride(id) {
        if(!confirm('Anda yakin ingin menghapus override dan kembali ke data asli BKN?')) return;
        
        fetch('{{ url("mari/rekon-iuran/override") }}/' + id, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                _token: '{{ csrf_token() }}',
                _method: 'DELETE'
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error request ke server');
        });
    }

    function confirmSyncReset() {
        const ids = getSelectedIds();
        if(ids.length === 0) return;
        
        if(!confirm(`Anda yakin ingin menghapus override untuk ${ids.length} pegawai terpilih dan kembali ke data asli BKN?`)) return;
        
        fetch('{{ route("mari.rekon-iuran.sync-reset") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                _token: '{{ csrf_token() }}',
                pegawai_ids: ids
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error request ke server');
        });
    }
</script>
@endsection
