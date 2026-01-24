@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Manajemen FAQ (Auto Reply)</h1>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: FAQ List -->
                <div class="lg:col-span-2 space-y-4">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div
                            class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-900/50">
                            <h2 class="font-semibold text-gray-700 dark:text-gray-200">Daftar Pertanyaan</h2>
                            <span
                                class="text-xs text-gray-500 bg-white dark:bg-gray-800 px-2 py-1 rounded border dark:border-gray-700">Total:
                                {{ $faqs->total() }}</span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-4 py-3">Pertanyaan</th>
                                        <th scope="col" class="px-4 py-3">Keywords</th>
                                        <th scope="col" class="px-4 py-3 text-center">Status</th>
                                        <th scope="col" class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($faqs as $faq)
                                        <tr
                                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                                <div class="truncate w-48" title="{{ $faq->question }}">{{ $faq->question }}
                                                </div>
                                                <div class="text-xs text-blue-500 mt-1">{{ $faq->category }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex flex-wrap gap-1 w-32">
                                                    @foreach(explode(',', $faq->keywords) as $keyword)
                                                        <span
                                                            class="bg-blue-100 text-blue-800 text-[10px] px-1.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                                            {{ trim($keyword) }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @if($faq->is_active)
                                                    <div class="h-2.5 w-2.5 rounded-full bg-green-500 mx-auto" title="Aktif"></div>
                                                @else
                                                    <div class="h-2.5 w-2.5 rounded-full bg-red-500 mx-auto" title="Nonaktif"></div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="flex justify-center gap-2">
                                                    <button
                                                        onclick="editFaq({{ $faq->id }}, '{{ addslashes($faq->question) }}', '{{ addslashes($faq->keywords) }}', '{{ addslashes($faq->answer) }}', '{{ $faq->category }}', {{ $faq->is_active }})"
                                                        class="p-1.5 bg-yellow-100 text-yellow-600 rounded hover:bg-yellow-200 transition-colors"
                                                        title="Edit">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                    <form action="{{ route('admin.chat.faqs.destroy', $faq->id) }}"
                                                        method="POST" onsubmit="return confirm('Hapus item ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="p-1.5 bg-red-100 text-red-600 rounded hover:bg-red-200 transition-colors"
                                                            title="Hapus">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                </path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">
                                                Belum ada data FAQ. Silakan isi form di bawah.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                            {{ $faqs->links() }}
                        </div>
                    </div>
                </div>

                <!-- Right Column: Form -->
                <div class="lg:col-span-1">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 sticky top-4">
                        <div
                            class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex justify-between items-center">
                            <h2 class="font-bold text-gray-800 dark:text-white" id="form-title">Tambah Baru</h2>
                            <button onclick="resetForm()" class="text-xs text-blue-600 hover:underline" id="btn-reset"
                                style="display: none;">
                                + Buat Baru
                            </button>
                        </div>

                        <form id="faq-form" action="{{ route('admin.chat.faqs.store') }}" method="POST"
                            class="p-6 space-y-4">
                            @csrf
                            <div id="method-spoof"></div> <!-- Place for @method('PUT') -->

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pertanyaan /
                                    Referensi</label>
                                <input type="text" name="question" id="question" required
                                    placeholder="Contoh: Info Syarat Cuti"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Keywords <span class="text-xs text-gray-400 font-normal">(Pisahkan koma)</span>
                                </label>
                                <input type="text" name="keywords" id="keywords" required placeholder="cuti, syarat, libur"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jawaban
                                    Bot</label>
                                <textarea name="answer" id="answer" rows="5" required
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori</label>
                                    <select name="category" id="category"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="umum">Umum</option>
                                        <option value="kepegawaian">Kepegawaian</option>
                                        <option value="teknis">Teknis</option>
                                    </select>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                    <select name="is_active" id="is_active"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="1">Aktif</option>
                                        <option value="0">Nonaktif</option>
                                    </select>
                                </div>
                            </div>

                            <div class="pt-2 flex gap-3">
                                <button type="submit" id="btn-submit"
                                    class="flex-1 inline-flex justify-center items-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm transition-all">
                                    Simpan Data
                                </button>
                                <button type="button" onclick="resetForm()"
                                    class="flex-1 inline-flex justify-center items-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm transition-all">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const baseUrl = "{{ route('admin.chat.faqs.index') }}";
        const form = document.getElementById('faq-form');
        const methodSpoof = document.getElementById('method-spoof');
        const title = document.getElementById('form-title');
        const submitBtn = document.getElementById('btn-submit');
        const resetBtn = document.getElementById('btn-reset');

        function editFaq(id, question, keywords, answer, category, isActive) {
            // Change Form Mode to Edit
            form.action = `${baseUrl}/${id}`;
            methodSpoof.innerHTML = '<input type="hidden" name="_method" value="PUT">';

            // Populate Data
            document.getElementById('question').value = question;
            document.getElementById('keywords').value = keywords;
            document.getElementById('answer').value = answer;

            // Select Options
            setSelectedValue('category', category);
            setSelectedValue('is_active', isActive ? "1" : "0");

            // Update UI
            title.innerText = "Edit FAQ";
            title.classList.add('text-yellow-600');
            submitBtn.innerHTML = "Update Data";
            submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            submitBtn.classList.add('bg-yellow-600', 'hover:bg-yellow-700');
            resetBtn.style.display = 'block';

            // Scroll to form on mobile
            if (window.innerWidth < 1024) {
                form.scrollIntoView({ behavior: 'smooth' });
            }
        }

        function resetForm() {
            form.reset();
            form.action = "{{ route('admin.chat.faqs.store') }}";
            methodSpoof.innerHTML = ''; // Remove PUT method

            title.innerText = "Tambah Baru";
            title.classList.remove('text-yellow-600');

            submitBtn.innerHTML = "Simpan Data";
            submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
            submitBtn.classList.remove('bg-yellow-600', 'hover:bg-yellow-700');

            resetBtn.style.display = 'none';
        }

        function setSelectedValue(id, value) {
            const select = document.getElementById(id);
            for (let i = 0; i < select.options.length; i++) {
                if (select.options[i].value == value) {
                    select.options[i].selected = true;
                    break;
                }
            }
        }
    </script>
@endsection