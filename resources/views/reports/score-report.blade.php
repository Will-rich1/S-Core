<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S-Core Report - {{ $student->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: white;
            padding: 25mm 20mm;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            background: white;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #1e40af;
        }

        .header-logo {
            width: auto;
            height: 115px;
            max-width: 100%;
            object-fit: contain;
            margin-bottom: 8px;
        }

        .header-subtitle {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }

        .header h1 {
            font-size: 28px;
            color: #1e40af;
            margin: 10px 0;
            font-weight: bold;
        }

        .header p {
            font-size: 12px;
            color: #666;
        }

        /* Student Info */
        .student-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
            padding: 15px;
            background: #f3f4f6;
            border-radius: 5px;
            page-break-inside: avoid;
        }

        .student-info-item {
            font-size: 12px;
        }

        .student-info-item label {
            font-weight: bold;
            color: #1e40af;
            display: block;
            margin-bottom: 3px;
        }

        .student-info-item value {
            color: #333;
        }

        /* Status Section */
        .status-section {
            margin-bottom: 25px;
            padding: 15px;
            background: {{ $isPassed ? '#ecfdf5' : '#fef2f2' }};
            border-left: 4px solid {{ $isPassed ? '#10b981' : '#ef4444' }};
            border-radius: 5px;
            page-break-inside: avoid;
        }

        .status-section h2 {
            font-size: 14px;
            color: {{ $isPassed ? '#10b981' : '#ef4444' }};
            margin-bottom: 10px;
        }

        .status-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .status-item {
            font-size: 12px;
        }

        .status-item-value {
            font-size: 18px;
            font-weight: bold;
        }

        .status-item-label {
            font-size: 11px;
            color: #666;
            margin-top: 3px;
        }

        /* Category Breakdown */
        .breakdown-section {
            margin-bottom: 20px;
        }

        .breakdown-section h3 {
            font-size: 13px;
            margin-bottom: 12px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 8px;
            color: #1e40af;
        }

        .category-item {
            margin-bottom: 15px;
            padding: 10px;
            background: #f9fafb;
            border-left: 3px solid #3b82f6;
            border-radius: 3px;
            page-break-inside: avoid;
        }

        .category-name {
            font-weight: bold;
            color: #1e40af;
            font-size: 12px;
        }

        .category-details {
            font-size: 11px;
            color: #666;
            margin-top: 3px;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: flex-end;
            page-break-inside: avoid;
        }

        .signature-column {
            width: 250px;
            margin-top: 18px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 12px;
            page-break-inside: avoid;
        }

        .verification-qr {
            width: 120px;
            height: 120px;
            border: 1px solid #dbeafe;
            border-radius: 8px;
            background: white;
            padding: 4px;
        }

        .signature-block {
            text-align: right;
        }

        .signature-date {
            font-size: 11px;
            margin: 0;
        }

        .signature-name {
            text-align: right;
            margin-top: 6px;
        }

        .signature-role {
            text-align: right;
            color: #666;
            font-size: 10px;
            line-height: 1.45;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
            text-align: center;
        }

        /* Status Badge */
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Table for detailed list */
        .submissions-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-top: 8px;
            page-break-inside: avoid;
        }

        .submissions-table th {
            background: #e5e7eb;
            padding: 6px;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }

        .submissions-table td {
            padding: 6px;
            border-bottom: 1px solid #eee;
        }

        .submissions-table tr:nth-child(even) {
            background: #f9fafb;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            @if (extension_loaded('gd'))
                <img class="header-logo" src="{{ public_path('images/logo.png') }}" alt="Logo ITBSS">
            @endif
            <div class="header-subtitle">SISTEM CORE ITBSS</div>
            <h1>S-CORE REPORT</h1>
        </div>  
        
        <!-- Student Information -->
        <div class="student-info">
            <div class="student-info-item">
                <label>Nama Mahasiswa:</label>
                <value>{{ $student->name }}</value>
            </div>
            <div class="student-info-item">
                <label>NIM:</label>
                <value>{{ $student->student_id }}</value>
            </div>
            <div class="student-info-item">
                <label>Program Studi:</label>
                <value>{{ $student->major ?? 'N/A' }}</value>
            </div>
            <div class="student-info-item">
                <label>Angkatan:</label>
                <value>{{ $student->year ?? 'N/A' }}</value>
            </div>
        </div>

        <!-- Status Section -->
        <div class="status-section">
            <h2>STATUS PENGAKUAN S-CORE</h2>
            <div class="status-grid">
                <div class="status-item">
                    <div class="status-item-value">{{ $totalPoints }}</div>
                    <div class="status-item-label">Total Poin S-Core</div>
                    <div class="status-item-label" style="color: {{ $isPassed ? '#10b981' : '#ef4444' }}; font-weight: bold;">Minimum: {{ $minPointsRequired }}</div>
                </div>
                <div class="status-item">
                    <div class="status-item-value">{{ $completedCategories }}/{{ $totalCategories }}</div>
                    <div class="status-item-label">Kategori Selesai</div>
                    <div class="status-item-label" style="color: {{ $isPassed ? '#10b981' : '#ef4444' }}; font-weight: bold;">Minimum: {{ $minCategoriesRequired }}</div>
                </div>
            </div>
            <div style="margin-top:12px;">
                <span class="badge {{ $isPassed ? 'badge-success' : 'badge-warning' }}">
                    {{ $isPassed ? 'Memenuhi' : 'Belum Memenuhi' }}
                </span>
            </div>
        </div>

        <!-- Category Breakdown -->
        <div class="breakdown-section">
            <h3>RINCIAN KATEGORI YANG DISELESAIKAN</h3>
            
            @if($categoryBreakdown && count($categoryBreakdown) > 0)
                @foreach($categoryBreakdown as $category)
                <div class="category-item">
                    <div class="category-name">{{ $category['categoryName'] }}</div>
                    <div class="category-details">
                        {{ $category['count'] }} aktivitas | 
                        <strong>{{ $category['points'] }} poin</strong>
                    </div>
                    
                    @if(isset($category['submissions']) && count($category['submissions']) > 0)
                    <table class="submissions-table">
                        <thead>
                            <tr>
                                <th>Aktivitas</th>
                                <th>Tanggal</th>
                                <th>Poin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category['submissions'] as $submission)
                            <tr>
                                <td>{{ $submission['title'] }}</td>
                                <td>{{ \Carbon\Carbon::parse($submission['date'])->format('d/m/Y') }}</td>
                                <td>{{ $submission['points'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
                @endforeach
            @else
                <p style="color: #666; font-size: 12px;">Tidak ada kategori yang diselesaikan.</p>
            @endif
        </div>

        <!-- Signature -->
<div class="signature-section" style="width: 100%; display: block; clear: both; margin-top: 30px;">
    <!-- Kotak pembungkus utama diatur melayang ke kanan (float: right) -->
    <div class="signature-column" style="float: right; text-align: right; width: 250px;">
        
        <!-- Tanggal Rata Kanan -->
        <p class="signature-date" style="margin-bottom: 10px; font-size: 11px;">Pontianak, {{ $generatedDate }}</p>
        
        <!-- Container QR Code diatur agar gambarnya menempel ke kanan -->
        <div style="display: flex; justify-content: flex-end; margin-bottom: 15px;">
            @if(!empty($verificationQrDataUri))
                <img class="verification-qr" src="{{ $verificationQrDataUri }}" alt="QR Verifikasi" style="display: inline-block; width: 120px; height: 120px;">
            @else
                <div class="verification-qr" style="display:inline-flex;align-items:center;justify-content:center;font-size:9px;text-align:center;color:#64748b;width:120px;height:120px;border:1px dashed #cbd5e1;">QR tidak tersedia</div>
            @endif
        </div>
        
        <!-- Blok Nama dan Jabatan Rata Kanan -->
        <div class="signature-block">
            <div class="signature-name">
                <p style="font-size: 11px; font-weight: bold; margin: 0 0 2px 0;">William Sandy</p>
            </div>
            <div class="signature-role" style="font-size: 10px; line-height: 1.4; color: #475569; margin: 0;">
                Wakil Rektor Bidang Akademik dan<br>
                Kemahasiswaan
            </div>
        </div>

    </div>
    <!-- Penutup float agar layout di bawahnya tidak rusak -->
    <div style="clear: both;"></div>
</div>

        
        
    </div>
</body>
</html>
