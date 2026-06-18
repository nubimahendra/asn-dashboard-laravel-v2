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

    <!-- Manajemen Data Usul SLKS -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 mb-8 overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    Manajemen Data Usul SLKS
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola data usulan SLKS sebelum dicetak atau diajukan.</p>
            </div>
            <a href="{{ route('siput.usul-slks.index') }}" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 transition-colors shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Baru
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                    <tr>
                        <th scope="col" class="px-4 py-3">No</th>
                        <th scope="col" class="px-4 py-3">NIP</th>
                        <th scope="col" class="px-4 py-3">Nama Pegawai</th>
                        <th scope="col" class="px-4 py-3">Pangkat</th>
                        <th scope="col" class="px-4 py-3">Jabatan</th>
                        <th scope="col" class="px-4 py-3 text-center">Usul SLKS</th>
                        <th scope="col" class="px-4 py-3 text-center">Tahunp</th>
                        <th scope="col" class="px-4 py-3 text-center">MS/TMS</th>
                        <th scope="col" class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $item)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-center">{{ $data->firstItem() + $index }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $item->nip }}</td>
                            <td class="px-4 py-3">{{ $item->nama }}</td>
                            <td class="px-4 py-3">{{ $item->pangkat }}</td>
                            <td class="px-4 py-3 truncate max-w-xs" title="{{ $item->jabatan }}">{{ $item->jabatan }}</td>
                            <td class="px-4 py-3 text-center font-semibold text-blue-600 dark:text-blue-400">{{ $item->usul_slks }}</td>
                            <td class="px-4 py-3 text-center">{{ $item->tahunp }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($item->ms_tms == 'MS')
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">MS</span>
                                @else
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300" title="{{ $item->ket_tms }}">TMS</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center flex items-center justify-center gap-2">
                                <a href="{{ route('siput.usul-slks.edit', $item->id) }}" class="inline-flex items-center justify-center text-white bg-amber-500 hover:bg-amber-600 focus:ring-4 focus:ring-amber-300 font-medium rounded-lg text-xs px-3 py-1.5 focus:outline-none transition-colors !text-white !bg-amber-500" title="Edit">
                                    Edit
                                </a>
                                <form action="{{ route('siput.usul-slks.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data usulan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-xs px-3 py-1.5 focus:outline-none transition-colors" title="Hapus">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 italic">
                                Belum ada data usulan SLKS.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Links -->
        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
            {{ $data->links() }}
        </div>
    </div>

    <!-- Tombol Cetak -->
    @if($data->count() > 0)
    <div class="flex justify-end mb-8">
        <a href="{{ route('siput.usul-slks.print') }}" target="_blank" class="text-white bg-emerald-600 hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-300 font-bold rounded-lg text-sm px-6 py-3 dark:bg-emerald-600 dark:hover:bg-emerald-700 focus:outline-none dark:focus:ring-emerald-800 transition-colors shadow-md flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            CETAK USULAN
        </a>
    </div>
    @endif

</div>
@endsection
