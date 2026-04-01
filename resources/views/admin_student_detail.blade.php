<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Mahasiswa - {{ $student['name'] }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold">Detail Mahasiswa</h1>
                <p class="text-sm text-gray-600">Halaman detail terpisah untuk review lebih luas dan nyaman.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="/student/{{ $student['id'] }}/report/view" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-cyan-500 hover:bg-cyan-600 text-white text-sm font-medium">
                    Lihat Report
                </a>
                <a href="/student/{{ $student['id'] }}/report" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-green-500 hover:bg-green-600 text-white text-sm font-medium">
                    Unduh Laporan
                </a>
                <a href="{{ route('admin.dashboard', ['menu' => 'Students']) }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium">
                    Kembali ke Admin
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
            <div class="rounded-xl bg-white border p-4 shadow-sm lg:col-span-2">
                <h2 class="text-lg font-semibold mb-3">Informasi Mahasiswa</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-gray-500">Nama</p>
                        <p class="font-semibold">{{ $student['name'] }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">NIM</p>
                        <p class="font-semibold">{{ $student['id'] }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Jurusan</p>
                        <p class="font-semibold">{{ $student['major'] }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Semester</p>
                        <p class="font-semibold">{{ $student['semester'] ? 'Semester ' . $student['semester'] : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Angkatan</p>
                        <p class="font-semibold">{{ $student['year'] }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Status Akhir</p>
                        @php
                            $statusClass = match($student['finalStatus']) {
                                'Memenuhi' => 'bg-green-100 text-green-700',
                                'Belum Memenuhi' => 'bg-red-100 text-red-700',
                                'Lulus' => 'bg-blue-100 text-blue-700',
                                'Cuti' => 'bg-amber-100 text-amber-700',
                                default => 'bg-gray-100 text-gray-700'
                            };
                        @endphp
                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">{{ $student['finalStatus'] }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-white border p-4 shadow-sm">
                <h2 class="text-lg font-semibold mb-3">Ubah Status Akademik</h2>
                <form method="POST" action="{{ route('students.update-academic-status', ['studentId' => $student['id']]) }}" class="space-y-3">
                    @csrf
                    <label class="block text-sm text-gray-600" for="academic_status">Pilih Status</label>
                    <select id="academic_status" name="academic_status" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="active" {{ $student['academicStatus'] === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="on_leave" {{ $student['academicStatus'] === 'on_leave' ? 'selected' : '' }}>Cuti</option>
                        <option value="graduated" {{ $student['academicStatus'] === 'graduated' ? 'selected' : '' }}>Lulus</option>
                    </select>
                    <button type="submit" class="w-full px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium">
                        Simpan Status
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="rounded-xl bg-white border p-4 shadow-sm">
                <p class="text-sm text-gray-500">Total Poin</p>
                <p class="text-2xl font-bold text-green-600">{{ $student['approvedPoints'] }}</p>
            </div>
            <div class="rounded-xl bg-white border p-4 shadow-sm">
                <p class="text-sm text-gray-500">Approved</p>
                <p class="text-2xl font-bold text-blue-600">{{ $student['approvedCount'] }}</p>
            </div>
            <div class="rounded-xl bg-white border p-4 shadow-sm">
                <p class="text-sm text-gray-500">Pending</p>
                <p class="text-2xl font-bold text-amber-600">{{ $student['pendingCount'] }}</p>
            </div>
            <div class="rounded-xl bg-white border p-4 shadow-sm">
                <p class="text-sm text-gray-500">Total Pengajuan</p>
                <p class="text-2xl font-bold text-gray-800">{{ $student['totalSubmissions'] }}</p>
            </div>
        </div>

        <div class="rounded-xl bg-white border shadow-sm mb-6">
            <div class="px-4 py-3 border-b bg-gray-50">
                <h3 class="font-semibold">Ringkasan Kategori</h3>
                <p class="text-xs text-gray-500">Target minimal: {{ $minPoints }} poin dan {{ $minCategories }} kategori</p>
            </div>
            <div class="p-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2 pr-4">Kategori</th>
                            <th class="py-2 pr-4">Jumlah Kegiatan</th>
                            <th class="py-2">Total Poin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categoryBreakdown as $categoryName => $row)
                            <tr class="border-b last:border-0">
                                <td class="py-2 pr-4 font-medium">{{ $categoryName }}</td>
                                <td class="py-2 pr-4">{{ $row['count'] }}</td>
                                <td class="py-2">{{ $row['points'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-gray-500">Belum ada poin approved.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-xl bg-white border shadow-sm">
            <div class="px-4 py-3 border-b bg-gray-50">
                <h3 class="font-semibold">Riwayat Pengajuan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500">
                        <tr>
                            <th class="text-left py-3 px-4">Kategori Utama</th>
                            <th class="text-left py-3 px-4">Subkategori</th>
                            <th class="text-left py-3 px-4">Judul</th>
                            <th class="text-left py-3 px-4">Deskripsi</th>
                            <th class="text-center py-3 px-4">Poin</th>
                            <th class="text-left py-3 px-4">Tanggal Kegiatan</th>
                            <th class="text-left py-3 px-4">Dikirim</th>
                            <th class="text-center py-3 px-4">Status</th>
                            <th class="text-center py-3 px-4">File</th>
                            <th class="text-left py-3 px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $sub)
                            @php
                                $submissionClass = match($sub['status']) {
                                    'Approved' => 'bg-green-100 text-green-700',
                                    'Waiting' => 'bg-yellow-100 text-yellow-700',
                                    'Rejected' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                                $submissionLabel = match($sub['status']) {
                                    'Approved' => 'Disetujui',
                                    'Waiting' => 'Menunggu',
                                    'Rejected' => 'Ditolak',
                                    default => $sub['status']
                                };
                            @endphp
                            <tr class="border-b last:border-0 align-top">
                                <td class="py-3 px-4">{{ $sub['mainCategory'] }}</td>
                                <td class="py-3 px-4">{{ $sub['subcategory'] }}</td>
                                <td class="py-3 px-4 font-medium">{{ $sub['title'] }}</td>
                                <td class="py-3 px-4 text-gray-600">{{ $sub['description'] }}</td>
                                <td class="py-3 px-4 text-center">{{ $sub['points'] ?? '-' }}</td>
                                <td class="py-3 px-4">{{ $sub['activityDate'] }}</td>
                                <td class="py-3 px-4">{{ $sub['submittedAt'] }}</td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold {{ $submissionClass }}">{{ $submissionLabel }}</span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($sub['fileUrl'])
                                        <a href="{{ $sub['fileUrl'] }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-700 font-medium">Lihat</a>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2">
                                        @if($sub['status'] === 'Approved')
                                            <button
                                                type="button"
                                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-amber-100 text-amber-700 hover:bg-amber-200"
                                                title="Edit S-Core"
                                                data-edit-scope
                                                data-id="{{ $sub['id'] }}"
                                                data-title="{{ e($sub['title']) }}"
                                                data-category="{{ e($sub['mainCategory']) }}"
                                                data-subcategory="{{ e($sub['subcategory']) }}"
                                                data-status="{{ e($submissionLabel) }}"
                                                data-points="{{ $sub['points'] }}"
                                                data-reason="{{ e($sub['pointAdjustmentReason'] ?? '') }}"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L12 15l-4 1 1-4 8.586-8.586z" />
                                                </svg>
                                            </button>
                                        @endif

                                        <form method="POST" action="{{ route('admin.submissions.destroy', ['id' => $sub['id']]) }}" onsubmit="return confirm('Yakin ingin menghapus data S-Core ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-100 text-red-700 hover:bg-red-200" title="Hapus S-Core">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-6 text-center text-gray-500">Belum ada pengajuan untuk mahasiswa ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div id="editScopeModal" class="hidden fixed inset-0 bg-black/50 z-50 items-center justify-center p-4">
            <div class="bg-white w-full max-w-xl rounded-xl shadow-xl">
                <div class="px-5 py-4 border-b flex items-center justify-between">
                    <h4 class="text-lg font-semibold">Edit S-Core</h4>
                    <button type="button" id="closeEditScopeModal" class="text-gray-500 hover:text-gray-700 text-xl leading-none">&times;</button>
                </div>

                <form id="editScopeForm" method="POST" action="#" class="p-5 space-y-4">
                    @csrf
                    <input type="hidden" name="points_adjustment_reason" id="modalReasonCombined">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                        <div class="sm:col-span-2">
                            <p class="text-gray-500">Judul S-Core</p>
                            <p id="modalScopeTitle" class="font-semibold">-</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Kategori</p>
                            <p id="modalScopeCategory" class="font-medium">-</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Subkategori</p>
                            <p id="modalScopeSubcategory" class="font-medium">-</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Status</p>
                            <p id="modalScopeStatus" class="font-medium">-</p>
                        </div>
                        <div>
                            <label class="text-gray-500" for="modalCurrentPoints">Poin Saat Ini</label>
                            <input id="modalCurrentPoints" type="text" readonly class="mt-1 w-full border rounded-lg px-3 py-2 bg-gray-100 text-gray-600" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 mb-1" for="modalNextPoints">Poin Baru</label>
                        <input id="modalNextPoints" type="text" name="points_awarded" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="Contoh: 8 atau 7.5" required>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 mb-1" for="modalAdminReason">Alasan Admin untuk Mahasiswa</label>
                        <textarea id="modalAdminReason" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="Jelaskan alasan perubahan agar dipahami mahasiswa." required></textarea>
                        <p class="text-xs text-gray-500 mt-1">Alasan ini akan tersimpan dan terlihat oleh mahasiswa.</p>
                    </div>

                    <div class="pt-1 flex items-center justify-end gap-2">
                        <button type="button" id="cancelEditScopeModal" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-sm font-medium">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const editButtons = document.querySelectorAll('[data-edit-scope]');
        const modal = document.getElementById('editScopeModal');
        const closeModalBtn = document.getElementById('closeEditScopeModal');
        const cancelModalBtn = document.getElementById('cancelEditScopeModal');
        const form = document.getElementById('editScopeForm');

        const modalScopeTitle = document.getElementById('modalScopeTitle');
        const modalScopeCategory = document.getElementById('modalScopeCategory');
        const modalScopeSubcategory = document.getElementById('modalScopeSubcategory');
        const modalScopeStatus = document.getElementById('modalScopeStatus');
        const modalCurrentPoints = document.getElementById('modalCurrentPoints');
        const modalNextPoints = document.getElementById('modalNextPoints');
        const modalAdminReason = document.getElementById('modalAdminReason');
        const modalReasonCombined = document.getElementById('modalReasonCombined');

        const openModal = () => {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        };

        const closeModal = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        };

        editButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const id = button.dataset.id;
                form.action = `/admin/submissions/${id}/adjust-points`;

                modalScopeTitle.textContent = button.dataset.title || '-';
                modalScopeCategory.textContent = button.dataset.category || '-';
                modalScopeSubcategory.textContent = button.dataset.subcategory || '-';
                modalScopeStatus.textContent = button.dataset.status || '-';
                modalCurrentPoints.value = button.dataset.points || '0';
                modalNextPoints.value = button.dataset.points || '0';
                modalAdminReason.value = button.dataset.reason || '';
                modalReasonCombined.value = '';

                openModal();
            });
        });

        closeModalBtn.addEventListener('click', closeModal);
        cancelModalBtn.addEventListener('click', closeModal);

        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });

        form.addEventListener('submit', (event) => {
            const reason = (modalAdminReason.value || '').trim();
            if (reason.length < 3) {
                event.preventDefault();
                alert('Alasan admin untuk mahasiswa minimal 3 karakter.');
                return;
            }

            modalReasonCombined.value = reason;
        });
    </script>
</body>
</html>
