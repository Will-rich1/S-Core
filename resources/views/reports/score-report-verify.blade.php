<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi S-Core - {{ $student->name }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
            color: #1f2937;
        }

        .page {
            max-width: 920px;
            margin: 0 auto;
            padding: 28px 18px 40px;
        }

        .hero {
            background: white;
            border: 1px solid #dbeafe;
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
        }

        .top-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
            flex-wrap: wrap;
        }

        .title {
            margin: 0 0 8px;
            font-size: 30px;
            line-height: 1.1;
            color: #0f172a;
        }

        .subtitle {
            margin: 0;
            color: #64748b;
            font-size: 14px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 14px;
            white-space: nowrap;
        }

        .badge.success {
            background: #dcfce7;
            color: #166534;
        }

        .badge.warning {
            background: #fee2e2;
            color: #991b1b;
        }

        .grid {
            margin-top: 22px;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 16px 18px;
        }

        .label {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 6px;
        }

        .value {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
        }

        .stats {
            margin-top: 18px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .stat {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 18px;
            text-align: center;
        }

        .stat-number {
            font-size: 34px;
            font-weight: 800;
            color: #2563eb;
            line-height: 1;
            margin-bottom: 8px;
        }

        .stat-text {
            font-size: 13px;
            color: #64748b;
        }

        .footer {
            margin-top: 18px;
            font-size: 12px;
            color: #64748b;
            text-align: center;
        }

        .note {
            margin-top: 18px;
            padding: 14px 16px;
            border-radius: 14px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 13px;
            line-height: 1.55;
        }

        @media (max-width: 640px) {
            .grid,
            .stats {
                grid-template-columns: 1fr;
            }

            .hero {
                padding: 20px;
            }

            .title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="hero">
            <div class="top-row">
                <div>
                    <h1 class="title">Verifikasi S-Core</h1>
                    <p class="subtitle">Halaman ini menampilkan ringkasan report asli yang tersimpan di server.</p>
                </div>
                <div class="badge {{ $isPassed ? 'success' : 'warning' }}">
                    {{ $isPassed ? 'Memenuhi' : 'Belum Memenuhi' }}
                </div>
            </div>

            <div class="grid">
                <div class="card">
                    <div class="label">Nama</div>
                    <div class="value">{{ $student->name }}</div>
                </div>
                <div class="card">
                    <div class="label">NIM</div>
                    <div class="value">{{ $student->student_id }}</div>
                </div>
                <div class="card">
                    <div class="label">Program Studi</div>
                    <div class="value">{{ $student->major ?? 'N/A' }}</div>
                </div>
                <div class="card">
                    <div class="label">Angkatan</div>
                    <div class="value">{{ $student->year ?? 'N/A' }}</div>
                </div>
            </div>

            <div class="stats">
                <div class="stat">
                    <div class="stat-number">{{ $totalPoints }}</div>
                    <div class="stat-text">Total Poin</div>
                </div>
                <div class="stat">
                    <div class="stat-number">{{ $completedCategories }}</div>
                    <div class="stat-text">Kategori Terkumpul</div>
                </div>
                <div class="stat">
                    <div class="stat-number">{{ $totalCategories }}</div>
                    <div class="stat-text">Total Kategori</div>
                </div>
            </div>

            <div class="note">
                <strong>Catatan verifikasi:</strong> data ini diambil langsung dari server S-Core. Jika isi PDF berbeda dengan halaman ini, maka PDF tersebut sudah dimodifikasi.
            </div>

            <div class="footer">
                Digenerate pada {{ $generatedDate }} | Verifikasi resmi S-Core ITBSS
            </div>
        </div>
    </div>
</body>
</html>
