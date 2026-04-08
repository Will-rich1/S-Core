<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfect Data Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold">Perfect Data Mahasiswa</h1>
                <p class="text-sm text-gray-600">Mahasiswa yang memenuhi target Perfect: poin mencapai minimum Perfect dan kategori wajib terpenuhi.</p>
            </div>
            <a href="{{ route('admin.dashboard', ['menu' => 'Students']) }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium">
                Kembali ke Admin
            </a>
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

        <div class="bg-white rounded-xl border shadow-sm p-4 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <a href="{{ route('admin.master-data') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium">
                    Master Data
                </a>
                <form method="POST" action="{{ route('settings.perfect-points.update') }}" class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-2">
                    @csrf
                    <input
                        type="number"
                        name="perfect_min_points"
                        min="1"
                        max="1000"
                        value="{{ $perfectMinPoints }}"
                        class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        placeholder="Minimum poin Perfect"
                        required
                    >
                    <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium">
                        Simpan Minimum Perfect
                    </button>
                </form>
            </div>
            <p class="text-xs text-gray-500 mt-3">Aturan saat ini: Mahasiswa Perfect jika poin >= {{ $perfectMinPoints }} dan kategori wajib sudah terpenuhi.</p>
        </div>

        <div class="bg-white rounded-xl border shadow-sm p-4 mb-4">
            <form method="GET" action="{{ route('admin.perfect-data') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input
                    type="text"
                    name="search"
                    value="{{ $filters['search'] ?? '' }}"
                    placeholder="Cari nama atau NIM..."
                    class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >

                <select name="major" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Jurusan</option>
                    <option value="STI" {{ ($filters['major'] ?? '') === 'STI' ? 'selected' : '' }}>STI</option>
                    <option value="BD" {{ ($filters['major'] ?? '') === 'BD' ? 'selected' : '' }}>BD</option>
                    <option value="KWU" {{ ($filters['major'] ?? '') === 'KWU' ? 'selected' : '' }}>KWU</option>
                </select>

                <select name="year" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Angkatan</option>
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ (string)($filters['year'] ?? '') === (string)$year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg px-3 py-2 text-sm font-medium">Terapkan</button>
                    <a href="{{ route('admin.perfect-data') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 rounded-lg px-3 py-2 text-sm font-medium text-center">Reset</a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b bg-gray-50 text-sm text-gray-600">
                Total mahasiswa Perfect: <span class="font-semibold text-gray-800">{{ $rows->count() }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="text-left px-4 py-3">NIM</th>
                            <th class="text-left px-4 py-3">Nama</th>
                            <th class="text-center px-4 py-3">Jurusan</th>
                            <th class="text-center px-4 py-3">Angkatan</th>
                            <th class="text-center px-4 py-3">Semester</th>
                            <th class="text-center px-4 py-3">Poin</th>
                            <th class="text-center px-4 py-3">Kategori</th>
                            <th class="text-center px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            <tr class="border-b last:border-0 hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $row['id'] }}</td>
                                <td class="px-4 py-3 font-medium">{{ $row['name'] }}</td>
                                <td class="px-4 py-3 text-center">{{ $row['major'] }}</td>
                                <td class="px-4 py-3 text-center">{{ $row['year'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">{{ $row['semester'] ? 'Semester ' . $row['semester'] : '-' }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-emerald-700">{{ number_format($row['approvedPoints'], 2) }}</td>
                                <td class="px-4 py-3 text-center">{{ $row['categoryCount'] }} / {{ $row['requiredCategories'] }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Perfect</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">Belum ada mahasiswa yang memenuhi kriteria Perfect untuk filter saat ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
