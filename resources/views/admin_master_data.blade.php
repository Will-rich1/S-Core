<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Data Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold">Master Data Mahasiswa</h1>
                <p class="text-sm text-gray-600">Semua status mahasiswa tersedia di halaman ini (Memenuhi, Belum Memenuhi, Lulus, Cuti).</p>
            </div>
            <a href="{{ route('admin.dashboard', ['menu' => 'Students']) }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium">
                Kembali ke Admin
            </a>
        </div>

        <div class="bg-white rounded-xl border shadow-sm p-4 mb-4">
            <form method="GET" action="{{ route('admin.master-data') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <input
                    type="text"
                    name="search"
                    value="{{ $filters['search'] ?? '' }}"
                    placeholder="Cari nama atau NIM..."
                    class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                >

                <select name="major" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Jurusan</option>
                    <option value="STI" {{ ($filters['major'] ?? '') === 'STI' ? 'selected' : '' }}>STI</option>
                    <option value="BD" {{ ($filters['major'] ?? '') === 'BD' ? 'selected' : '' }}>BD</option>
                    <option value="KWU" {{ ($filters['major'] ?? '') === 'KWU' ? 'selected' : '' }}>KWU</option>
                </select>

                <select name="year" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Angkatan</option>
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ (string)($filters['year'] ?? '') === (string)$year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>

                <select name="status" class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="met" {{ ($filters['status'] ?? '') === 'met' ? 'selected' : '' }}>Memenuhi</option>
                    <option value="not_met" {{ ($filters['status'] ?? '') === 'not_met' ? 'selected' : '' }}>Belum Memenuhi</option>
                    <option value="graduated" {{ ($filters['status'] ?? '') === 'graduated' ? 'selected' : '' }}>Lulus</option>
                    <option value="on_leave" {{ ($filters['status'] ?? '') === 'on_leave' ? 'selected' : '' }}>Cuti</option>
                </select>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-3 py-2 text-sm font-medium">Terapkan</button>
                    <a href="{{ route('admin.master-data') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 rounded-lg px-3 py-2 text-sm font-medium text-center">Reset</a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b bg-gray-50 text-sm text-gray-600">
                Total data: <span class="font-semibold text-gray-800">{{ $rows->count() }}</span>
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
                            <th class="text-center px-4 py-3">Approved</th>
                            <th class="text-center px-4 py-3">Pending</th>
                            <th class="text-center px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            @php
                                $statusClass = match($row['finalStatus']) {
                                    'Memenuhi' => 'bg-green-100 text-green-700',
                                    'Belum Memenuhi' => 'bg-red-100 text-red-700',
                                    'Lulus' => 'bg-blue-100 text-blue-700',
                                    'Cuti' => 'bg-amber-100 text-amber-700',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                            @endphp
                            <tr class="border-b last:border-0 hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $row['id'] }}</td>
                                <td class="px-4 py-3 font-medium">{{ $row['name'] }}</td>
                                <td class="px-4 py-3 text-center">{{ $row['major'] }}</td>
                                <td class="px-4 py-3 text-center">{{ $row['year'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">{{ $row['semester'] ? 'Semester ' . $row['semester'] : '-' }}</td>
                                <td class="px-4 py-3 text-center">{{ $row['approvedPoints'] }}</td>
                                <td class="px-4 py-3 text-center">{{ $row['approvedCount'] }}</td>
                                <td class="px-4 py-3 text-center">{{ $row['pendingCount'] }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">{{ $row['finalStatus'] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-gray-500">Tidak ada data mahasiswa untuk filter saat ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
