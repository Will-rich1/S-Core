<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Admin Tinjauan - S-Core ITBSS (v2.0)</title>
    <script src="https://cdn.tailwindcss.com?v=<?php echo time(); ?>"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6'
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js?v=<?php echo time(); ?>"></script>
    <style>
        /* Force narrow sidebar on small screens */
        @media (max-width: 666px) {
            .sidebar-container {
                width: 3.5rem !important;
            }
            .sidebar-container .sidebar-text {
                display: none !important;
            }
            .sidebar-container .p-4 {
                padding: 0.5rem !important;
            }
            .sidebar-container img {
                width: 2rem !important;
                height: 2rem !important;
            }
            .hamburger-btn {
                display: none !important;
            }
        }
        @keyframes slideUp {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
        }
        .animate-slide-up {
            animation: slideUp 0.25s ease-out;
        }
    </style>
</head>
<body>
    <div class="flex h-screen bg-gray-100" x-data="adminReviewData()" x-init="init()">
        <!-- Logout Confirmation Modal -->
        <div x-show="showLogoutModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
            <div class="bg-white rounded-lg p-4 sm:p-6 max-w-sm w-full mx-4">
                <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4">Konfirmasi Keluar</h3>
                <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6">Apakah Anda yakin ingin keluar?</p>
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 sm:justify-end">
                    <button @click="showLogoutModal = false" class="w-full sm:w-auto px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-xs sm:text-sm font-medium">Tidak</button>
                    <button @click="confirmLogout" class="w-full sm:w-auto px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded text-xs sm:text-sm font-medium">Ya</button>
                </div>
            </div>
        </div>

        <!-- Delete Category Confirmation Modal -->
        <div x-show="showDeleteCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center" style="display: none; z-index: 9999;">
            <div class="bg-white rounded-lg shadow-xl max-w-sm w-full mx-4 overflow-hidden animate-in fade-in duration-300" style="z-index: 10000;">
                <!-- Header with red accent -->
                <div class="bg-gradient-to-r from-red-500 to-red-600 px-4 sm:px-6 py-3 sm:py-4 flex items-center gap-2 sm:gap-3">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h3 class="text-base sm:text-lg font-semibold text-white">Hapus Kategori</h3>
                </div>
                
                <!-- Content -->
                <div class="px-4 sm:px-6 py-3 sm:py-4">
                    <p class="text-sm sm:text-base text-gray-700 mb-2">Apakah Anda yakin ingin menghapus kategori ini?</p>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-2 sm:p-3 mt-3 sm:mt-4">
                        <p class="text-xs sm:text-sm text-gray-600">Kategori:</p>
                        <p class="text-sm sm:text-base font-semibold text-red-700" x-text="deleteTargetCategory || 'N/A'"></p>
                    </div>
                    <p class="text-xs sm:text-sm text-yellow-600 mt-2 sm:mt-3 flex items-start gap-1.5 sm:gap-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="text-xs sm:text-sm">Semua subkategori dalam kategori ini juga akan dinonaktifkan.</span>
                    </p>
                    <p class="text-sm text-gray-500 mt-4">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                
                <!-- Footer with buttons -->
                <div class="bg-gray-50 px-6 py-4 flex gap-3 justify-end border-t">
                    <button @click="showDeleteCategoryModal = false; deleteCategoryIndex = null; deleteTargetCategory = null;" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg text-sm font-medium transition">
                        Batal
                    </button>
                    <button @click="confirmDeleteCategory()" class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- Delete Subcategory Confirmation Modal -->
        <div x-show="showDeleteSubcategoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center" style="display: none; z-index: 9999;">
            <div class="bg-white rounded-lg shadow-xl max-w-sm w-full mx-4 overflow-hidden animate-in fade-in duration-300" style="z-index: 10000;">
                <!-- Header with red accent -->
                <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4 flex items-center gap-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 0v2m0-6h.01M15 9a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-white">Hapus Subkategori</h3>
                </div>
                
                <!-- Content -->
                <div class="px-6 py-4">
                    <p class="text-gray-700 mb-2">Apakah Anda yakin ingin menghapus subkategori ini?</p>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 mt-4">
                        <p class="text-sm text-gray-600">Subkategori:</p>
                        <p class="font-semibold text-red-700" x-text="deleteTargetSubcategory || 'N/A'"></p>
                    </div>
                    <p class="text-sm text-gray-500 mt-4">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                
                <!-- Footer with buttons -->
                <div class="bg-gray-50 px-6 py-4 flex gap-3 justify-end border-t">
                    <button @click="showDeleteSubcategoryModal = false; deleteCategoryIndex = null; deleteSubcategoryIndex = null; deleteTargetSubcategory = null;" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg text-sm font-medium transition">
                        Batal
                    </button>
                    <button @click="confirmDeleteSubcategory()" class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- Restore Category Confirmation Modal -->
        <div x-show="showRestoreCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center" style="display: none; z-index: 9999;">
            <div class="bg-white rounded-lg shadow-xl max-w-sm w-full mx-4 overflow-hidden animate-in fade-in duration-300" style="z-index: 10000;">
                <!-- Header with green accent -->
                <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4 flex items-center gap-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-white">Pulihkan Kategori</h3>
                </div>
                
                <!-- Content -->
                <div class="px-6 py-4">
                    <p class="text-gray-700 mb-2">Apakah Anda yakin ingin memulihkan kategori ini?</p>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 mt-4">
                        <p class="text-sm text-gray-600">Kategori:</p>
                        <p class="font-semibold text-green-700" x-text="restoreTargetCategory || 'N/A'"></p>
                    </div>
                    <p class="text-sm text-gray-500 mt-4">Ini akan mengaktifkan kembali kategori dan membuatnya terlihat lagi.</p>
                </div>
                
                <!-- Footer with buttons -->
                <div class="bg-gray-50 px-6 py-4 flex gap-3 justify-end border-t">
                    <button @click="showRestoreCategoryModal = false; restoreCategoryIndex = null; restoreTargetCategory = null;" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg text-sm font-medium transition">
                        Batal
                    </button>
                    <button @click="confirmRestoreCategory()" class="px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm font-medium transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Restore
                    </button>
                </div>
            </div>
        </div>

        <!-- Restore Subcategory Confirmation Modal -->
        <div x-show="showRestoreSubcategoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center" style="display: none; z-index: 9999;">
            <div class="bg-white rounded-lg shadow-xl max-w-sm w-full mx-4 overflow-hidden animate-in fade-in duration-300" style="z-index: 10000;">
                <!-- Header with green accent -->
                <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4 flex items-center gap-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-white">Pulihkan Subkategori</h3>
                </div>
                
                <!-- Content -->
                <div class="px-6 py-4">
                    <p class="text-gray-700 mb-2">Apakah Anda yakin ingin memulihkan subkategori ini?</p>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 mt-4">
                        <p class="text-sm text-gray-600">Subkategori:</p>
                        <p class="font-semibold text-green-700" x-text="restoreTargetSubcategory || 'N/A'"></p>
                    </div>
                    <p class="text-sm text-gray-500 mt-4">Ini akan mengaktifkan kembali subkategori dan membuatnya terlihat lagi.</p>
                </div>
                
                <!-- Footer with buttons -->
                <div class="bg-gray-50 px-6 py-4 flex gap-3 justify-end border-t">
                    <button @click="showRestoreSubcategoryModal = false; restoreCategoryIndex = null; restoreSubcategoryIndex = null; restoreTargetSubcategory = null;" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg text-sm font-medium transition">
                        Batal
                    </button>
                    <button @click="confirmRestoreSubcategory()" class="px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm font-medium transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Restore
                    </button>
                </div>
            </div>
        </div>

        <!-- Detail Modal - Full Screen -->
        <div x-show="showDetailModal" class="fixed inset-0 bg-black bg-opacity-50 z-50" style="display: none;">
            <div class="h-full w-full bg-white flex flex-col">
                <!-- Header -->
                <div class="bg-white border-b px-4 sm:px-6 py-3 sm:py-4 flex justify-between items-center">
                    <h3 class="text-lg sm:text-xl font-semibold">Tinjau Pengajuan</h3>
                    <button @click="closeModal" class="text-gray-500 hover:text-gray-700 text-2xl leading-none w-8 h-8 flex items-center justify-center">×</button>
                </div>

                <!-- Content Area - 2 Columns (responsive) -->
                <div class="flex-1 overflow-y-auto lg:overflow-hidden flex flex-col lg:flex-row">
                    <template x-if="selectedSubmission">
                        <div class="flex flex-col lg:flex-row w-full lg:h-full">
                            <!-- Left Column - PDF Viewer -->
                            <div class="w-full lg:w-1/2 bg-gray-100 lg:border-r overflow-hidden p-4 sm:p-6 order-2 lg:order-1 h-[40vh] lg:h-auto flex-shrink-0">
                                <div class="bg-white rounded-lg shadow-sm h-full flex flex-col">
                                    <div class="bg-gray-800 text-white px-3 sm:px-4 py-2 sm:py-3 rounded-t-lg flex items-center justify-between">
                                        <span class="text-xs sm:text-sm font-medium">Sertifikat/Bukti</span>
                                        <span class="text-xs text-gray-300 truncate ml-2" x-text="selectedSubmission.certificate"></span>
                                    </div>
                                    <div class="flex-1 bg-gray-50 relative h-full overflow-hidden" x-data="{ loading: true }">
                                        <template x-if="selectedSubmission.file_url">
                                            <div class="relative w-full h-full">
                                                <!-- Loading spinner -->
                                                <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-gray-50 z-10">
                                                    <div class="text-center">
                                                        <svg class="animate-spin h-8 w-8 sm:h-12 sm:w-12 text-blue-600 mx-auto mb-2 sm:mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                        </svg>
                                                        <p class="text-xs sm:text-sm text-gray-600">Memuat PDF...</p>
                                                    </div>
                                                </div>
                                                <!-- PDF iframe -->
                                                <iframe
                                                    :src="selectedSubmission.file_url"
                                                    @load="loading = false"
                                                    class="w-full h-full"
                                                    style="border: none;"
                                                    type="application/pdf"
                                                ></iframe>
                                            </div>
                                        </template>
                                        <template x-if="!selectedSubmission.file_url">
                                            <div class="flex items-center justify-center h-full text-red-500 flex-col gap-2">
                                                <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                                <p class="text-xs sm:text-sm text-center px-4">File PDF tidak ditemukan di database.</p>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column - Form Details -->
                            <div class="w-full lg:w-1/2 flex flex-col order-1 lg:order-2 flex-shrink-0 min-h-0">
                                <!-- Scrollable Content -->
                                <div class="flex-1 lg:overflow-y-auto p-4 sm:p-6">
                                    <div class="space-y-3 sm:space-y-4">
                                        <!-- Student Information -->
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4">
                                            <h4 class="font-semibold text-sm sm:text-base text-blue-900 mb-2 sm:mb-3">Informasi Mahasiswa</h4>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 text-xs sm:text-sm">
                                                <div>
                                                    <span class="text-gray-600 block mb-1">NIM</span>
                                                    <span class="font-medium" x-text="selectedSubmission.studentId"></span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-600 block mb-1">Nama</span>
                                                    <span class="font-medium" x-text="selectedSubmission.studentName"></span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-600 block mb-1">Tanggal Pengajuan</span>
                                                    <span class="font-medium" x-text="selectedSubmission.submittedDate"></span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-600 block mb-1">Tanggal Kegiatan</span>
                                                    <span class="font-medium" x-text="selectedSubmission.activityDate"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Current Main Category -->
                                        <div>
                                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Kategori Utama Saat Ini</label>
                                            <div class="bg-gray-50 border rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm" x-text="selectedSubmission.mainCategory"></div>
                                        </div>

                                        <!-- Current Subcategory -->
                                        <div>
                                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Subkategori Saat Ini</label>
                                            <div class="bg-gray-50 border rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm" x-text="selectedSubmission.subcategory"></div>
                                        </div>

                                        <!-- Activity Title -->
                                        <div>
                                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Judul Kegiatan</label>
                                            <div class="bg-gray-50 border rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-medium" x-text="selectedSubmission.judul"></div>
                                        </div>

                                        <!-- Description -->
                                        <div>
                                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Deskripsi</label>
                                            <div class="bg-gray-50 border rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm whitespace-pre-wrap min-h-[80px] sm:min-h-[100px]" x-text="selectedSubmission.keterangan"></div>
                                        </div>

                                        <div class="bg-gray-50 border rounded-lg p-3 sm:p-4">
                                            <label class="inline-flex items-center gap-2 text-xs sm:text-sm font-medium text-gray-700">
                                                <input
                                                    type="checkbox"
                                                    x-model="isCategoryCorrectionEnabled"
                                                    @change="if(!isCategoryCorrectionEnabled){ categoryChanged = false; }"
                                                    class="rounded border-gray-300 text-amber-600 focus:ring-amber-500"
                                                >
                                                Perbaiki kategori/subkategori (opsional)
                                            </label>
                                            <p class="text-xs text-gray-500 mt-1">Aktifkan hanya jika kategori atau subkategori mahasiswa salah.</p>
                                        </div>

                                        <!-- Assign Category (Optional) -->
                                        <div x-show="isCategoryCorrectionEnabled" class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-3 sm:p-4" style="display: none;">
                                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">
                                                Tetapkan Kategori Utama
                                            </label>
                                            <select x-model="assignedMainCategory" @change="updateAssignedSubcategories" class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-2 sm:mb-3">
                                                <option value="">Pilih Kategori Utama</option>
                                                <template x-for="(catGroup, idx) in categoryGroups" :key="idx">
                                                    <option :value="idx" x-text="catGroup.name"></option>
                                                </template>
                                            </select>

                                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">
                                                Tetapkan Subkategori
                                            </label>
                                            <select x-model="assignedSubcategory" @change="categoryChanged = true" :disabled="assignedMainCategory === ''" class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Pilih Subkategori</option>
                                                <template x-for="(subcat, subIdx) in assignedAvailableSubcategories" :key="subIdx">
                                                    <option :value="subIdx" x-text="subcat.name + ' (' + subcat.points + ' points)'"></option>
                                                </template>
                                            </select>
                                            <p class="text-xs text-gray-500 mt-1.5 sm:mt-2" x-show="assignedSubcategory !== ''">
                                                <span class="font-medium">Poin kategori ini:</span> 
                                                <span class="text-blue-600 font-semibold" x-text="assignedAvailableSubcategories[assignedSubcategory]?.points || 0"></span> points
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="hidden lg:block border-t bg-white px-4 sm:px-6 py-3 sm:py-4 flex-shrink-0">
                                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 sm:justify-end">
                                        <button @click="closeModal" class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-2.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-xs sm:text-sm font-medium transition-colors order-4 sm:order-1">
                                            Batal
                                        </button>
                                        <button x-show="isCategoryCorrectionEnabled && categoryChanged && selectedSubmission?.status !== 'Waiting'" @click="updateCategoryOnly" class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors order-3 sm:order-2">
                                            Update Category
                                        </button>
                                        <button x-show="selectedSubmission?.status !== 'Rejected'" @click="showRejectModal = true" class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors order-2 sm:order-3">
                                            Reject
                                        </button>
                                        <button x-show="selectedSubmission?.status !== 'Approved'" @click="handleApprove" class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors order-1 sm:order-4">
                                            Approve
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="lg:hidden order-3 border-t bg-white px-4 sm:px-6 py-3 sm:py-4 flex-shrink-0">
                                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 sm:justify-end">
                                    <button x-show="selectedSubmission?.status !== 'Approved'" @click="handleApprove" class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors order-1 sm:order-4">
                                        Approve
                                    </button>
                                    <button x-show="selectedSubmission?.status !== 'Rejected'" @click="showRejectModal = true" class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors order-2 sm:order-3">
                                        Reject
                                    </button>
                                    <button x-show="isCategoryCorrectionEnabled && categoryChanged && selectedSubmission?.status !== 'Waiting'" @click="updateCategoryOnly" class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors order-3 sm:order-2">
                                        Update Category
                                    </button>
                                    <button @click="closeModal" class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-2.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-xs sm:text-sm font-medium transition-colors order-4 sm:order-1">
                                        Batal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div x-show="showRejectModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-[60]" style="display: none;">
            <div class="bg-white rounded-lg p-6 max-w-xl w-full mx-4 shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold text-red-600">Tolak Pengajuan</h3>
                    <button @click="showRejectModal = false" class="text-gray-500 hover:text-gray-700 text-2xl leading-none">×</button>
                </div>

                <template x-if="selectedSubmission">
                    <div>
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-4">
                                Anda akan menolak pengajuan dari <strong x-text="selectedSubmission.studentName"></strong> (<span x-text="selectedSubmission.studentId"></span>)
                            </p>
                            <p class="text-sm font-medium mb-2">Kegiatan: <span x-text="selectedSubmission.judul"></span></p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                            <select x-model="rejectReasonType" @change="if(rejectReasonType !== 'other') rejectReason = rejectReasonType" class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 mb-3">
                                <option value="">Pilih alasan penolakan</option>
                                <option value="Sertifikat tidak sesuai dengan deskripsi kegiatan. Harap kirim ulang dengan dokumentasi yang benar.">Sertifikat tidak sesuai deskripsi kegiatan</option>
                                <option value="Bukti/sertifikat tidak jelas atau tidak lengkap. Harap unggah versi yang lebih jelas.">Bukti tidak jelas atau tidak lengkap</option>
                                <option value="Tanggal kegiatan melebihi batas waktu yang diizinkan. Harap ajukan kegiatan dalam periode yang valid.">Tanggal kegiatan melebihi batas waktu</option>
                                <option value="Kategori yang dipilih salah. Harap kirim ulang dengan kategori yang benar.">Kategori yang dipilih salah</option>
                                <option value="Pengajuan duplikat terdeteksi. Kegiatan ini sudah pernah diajukan.">Pengajuan duplikat</option>
                                <option value="Kegiatan tidak memenuhi persyaratan S-Core. Harap merujuk pada panduan.">Tidak memenuhi persyaratan S-Core</option>
                                <option value="other">Lainnya (sebutkan di bawah)</option>
                            </select>
                            
                            <div x-show="rejectReasonType === 'other' || rejectReasonType === ''" class="mt-3">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span x-show="rejectReasonType === 'other'">Sebutkan Alasan / Catatan Tambahan</span>
                                    <span x-show="rejectReasonType === ''">Alasan Kustom</span>
                                </label>
                                <textarea x-model="rejectReason" rows="4" class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500" :placeholder="rejectReasonType === 'other' ? 'Sebutkan alasan penolakan atau berikan catatan tambahan untuk mahasiswa...' : 'Berikan alasan yang jelas agar mahasiswa dapat memahami dan mengajukan kembali dengan benar...'"></textarea>
                            </div>
                        </div>

                        <div class="flex gap-3 justify-end">
                            <button @click="showRejectModal = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm font-medium">Batal</button>
                            <button @click="handleRejectConfirm" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded text-sm font-medium">Konfirmasi Penolakan</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Auto-next toast -->
        <div
            x-show="showQueueToast"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2"
            class="fixed bottom-6 right-6 z-[120]"
            style="display: none;"
        >
            <div class="bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg text-sm font-medium">
                <span x-text="queueToastMessage"></span>
            </div>
        </div>

        <!-- view s-core mahasiswa modal -->
        <div x-show="showStudentDetailModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-[80]" style="display: none;">
            <div class="bg-white rounded-lg max-w-4xl w-full mx-4 shadow-2xl h-[80vh] flex flex-col">
                <div class="bg-white border-b px-6 py-4 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800" x-text="selectedStudent?.name"></h3>
                        <p class="text-sm text-gray-500">
                            ID: <span x-text="selectedStudent?.id"></span> | 
                            Jurusan: <span x-text="selectedStudent?.major"></span> | 
                            Semester: <span x-text="selectedStudent?.semester ? 'Semester ' + selectedStudent?.semester : '-' "></span> | 
                            Angkatan: <span x-text="selectedStudent?.year"></span>
                        </p>
                    </div>
                    <button @click="showStudentDetailModal = false" class="text-gray-500 hover:text-gray-700 text-2xl leading-none">×</button>
                </div>

                <div class="flex-1 overflow-y-auto p-6 bg-gray-50">
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="bg-white p-4 rounded border shadow-sm text-center">
                            <p class="text-xs text-gray-500">Total poin</p>
                            <p class="text-xl font-bold text-green-600" x-text="selectedStudent?.approvedPoints"></p>
                        </div>
                        <div class="bg-white p-4 rounded border shadow-sm text-center">
                            <p class="text-xs text-gray-500">Status</p>
                            <span :class="getStudentStatusClass(selectedStudent)" class="px-2 py-1 rounded-full text-xs font-bold mt-1 inline-block">
                                <span x-text="getStudentFinalStatus(selectedStudent)"></span>
                            </span>
                        </div>
                        <div class="bg-white p-4 rounded border shadow-sm text-center">
                            <p class="text-xs text-gray-500">Pengajuan</p>
                            <p class="text-xl font-bold text-blue-600" x-text="selectedStudent?.totalSubmissions"></p>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow border p-4 mb-6">
                        <p class="text-sm font-semibold text-gray-800 mb-3">Status Akademik Mahasiswa</p>
                        <div class="flex flex-wrap gap-4 items-center">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                <input
                                    type="checkbox"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    :checked="studentAcademicStatusDraft === 'on_leave'"
                                    @change="studentAcademicStatusDraft = $event.target.checked ? 'on_leave' : 'active'"
                                >
                                Cuti
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                <input
                                    type="checkbox"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    :checked="studentAcademicStatusDraft === 'graduated'"
                                    @change="studentAcademicStatusDraft = $event.target.checked ? 'graduated' : 'active'"
                                >
                                Lulus
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                <input
                                    type="checkbox"
                                    class="rounded border-gray-300 text-slate-600 focus:ring-slate-500"
                                    :checked="studentAcademicStatusDraft === 'non_active'"
                                    @change="studentAcademicStatusDraft = $event.target.checked ? 'non_active' : 'active'"
                                >
                                Non Aktif
                            </label>
                            <span class="text-xs text-gray-500">Jika semua checkbox tidak dicentang, status dianggap Aktif.</span>
                        </div>
                        <div class="mt-3 flex items-center gap-3">
                            <button
                                @click="saveStudentAcademicStatus"
                                :disabled="isUpdatingAcademicStatus"
                                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 disabled:bg-blue-300 text-white rounded text-xs font-medium"
                            >
                                <span x-text="isUpdatingAcademicStatus ? 'Menyimpan...' : 'Simpan Status Akademik'"></span>
                            </button>
                            <span class="text-xs text-gray-600">Status saat ini: <strong x-text="getStudentFinalStatus(selectedStudent)"></strong></span>
                        </div>
                    </div>

                    <h4 class="font-semibold text-gray-700 mb-3">Riwayat Pengajuan</h4>

                    <div class="bg-white rounded-lg shadow p-4 mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <select x-model="studentDetailStatusFilter" class="border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Status</option>
                                <option value="Approved">Disetujui</option>
                                <option value="Waiting">Menunggu</option>
                                <option value="Rejected">Ditolak</option>
                            </select>

                            <select x-model="studentDetailCategoryFilter" class="border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Kategori</option>
                                <template x-for="cat in selectedStudentCategories" :key="cat">
                                    <option :value="cat" x-text="cat"></option>
                                </template>
                            </select>

                            <input type="text" x-model="studentDetailSearchQuery" placeholder="Cari judul..." class="border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow overflow-hidden border">
                        <div class="overflow-x-auto">
                            <table class="min-w-[980px] w-full text-sm text-left">
                                <thead class="bg-gray-100 text-gray-600 text-xs uppercase">
                                    <tr>
                                        <th class="px-4 py-3">Kategori Utama</th>
                                        <th class="px-4 py-3">Subkategori</th>
                                        <th class="px-4 py-3">Judul Kegiatan</th>
                                        <th class="px-4 py-3">Deskripsi</th>
                                        <th class="px-4 py-3 text-center">Poin</th>
                                        <th class="px-4 py-3">Waktu Input</th>
                                        <th class="px-4 py-3 text-center">Status</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <template x-for="sub in filteredSelectedStudentSubmissions" :key="sub.id">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3" x-text="sub.mainCategory"></td>
                                            <td class="px-4 py-3" x-text="sub.subcategory"></td>
                                            <td class="px-4 py-3 font-medium text-gray-800" x-text="sub.title"></td>
                                            <td class="px-4 py-3 text-gray-600" x-text="sub.description"></td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="flex flex-col items-center gap-1">
                                                    <span x-text="sub.points || '-'"></span>
                                                    <span
                                                        x-show="sub.pointAdjustmentReason"
                                                        class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-700"
                                                        :title="sub.pointAdjustmentReason"
                                                        style="display: none;"
                                                    >
                                                        Ada alasan
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-xs text-gray-700" x-text="sub.waktu"></td>
                                            <td class="px-4 py-3 text-center">
                                                <span :class="{
                                                    'bg-green-100 text-green-700': sub.status === 'Approved',
                                                    'bg-yellow-100 text-yellow-700': sub.status === 'Waiting',
                                                    'bg-red-100 text-red-700': sub.status === 'Rejected'
                                                }" class="px-2 py-1 rounded-full text-xs font-semibold" x-text="translateStatus(sub.status)"></span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button @click="openStudentSubmissionPreview(sub)" class="text-blue-500 hover:text-blue-700 p-1" title="Preview">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </button>
                                                    <button @click="reduceSubmissionPoints(sub)" class="text-amber-600 hover:text-amber-700 p-1" title="Kurangi Poin">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 12H4" />
                                                        </svg>
                                                    </button>
                                                    <button @click="deleteStudentSubmission(sub)" class="text-red-600 hover:text-red-700 p-1" title="Hapus S-Core">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>

                                    <template x-if="filteredSelectedStudentSubmissions.length === 0">
                                        <tr>
                                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">Tidak ada aktivitas yang sesuai dengan filter Anda.</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="bg-white border-t px-6 py-4 flex justify-end gap-3 items-center">
                    <button @click="viewStudentReport()" class="px-4 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded text-sm font-medium">Lihat Report</button>
                    <button @click="downloadStudentReport()" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded text-sm font-medium">Unduh Laporan</button>
                    <button @click="resetStudentPassword()" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-sm font-medium">Reset Kata Sandi</button>
                    <button @click="showStudentDetailModal = false" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm font-medium">Tutup</button>
                </div>
            </div>
        </div>

        <!-- Adjust Points Modal -->
        <div x-show="showAdjustPointsModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-[105]" style="display: none;">
            <div @click.away="closeAdjustPointsModal()" class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-1 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                        Kurangi / Ubah Poin S-Core
                    </h3>
                    <p class="text-sm text-gray-600 mb-4" x-text="adjustPointForm.title ? 'Kegiatan: ' + adjustPointForm.title : ''"></p>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Poin Saat Ini</label>
                                <input type="text" :value="adjustPointForm.currentPoints" disabled class="w-full border rounded-lg px-3 py-2 text-sm bg-gray-100 text-gray-600" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Poin Baru <span class="text-red-500">*</span></label>
                                <input type="text" x-model="adjustPointForm.nextPoints" placeholder="Contoh: 7.5 atau 7,5" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Alasan Pengurangan Poin <span class="text-red-500">*</span></label>
                            <textarea x-model="adjustPointForm.reason" rows="4" placeholder="Tulis alasan pengurangan poin agar mahasiswa bisa melihatnya..." class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"></textarea>
                            <p class="text-xs text-gray-500 mt-1">Alasan ini akan terlihat oleh mahasiswa.</p>
                        </div>
                    </div>

                    <div class="flex gap-3 justify-end mt-6">
                        <button @click="closeAdjustPointsModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm font-medium">Batal</button>
                        <button @click="submitAdjustedPoints()" :disabled="isAdjustingPoints" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 disabled:bg-amber-300 text-white rounded-lg text-sm font-medium">
                            <span x-text="isAdjustingPoints ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reset Password Modal -->
        <div x-show="showResetPasswordModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-[110]" style="display: none;">
            <div @click.away="showResetPasswordModal = false" class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        Reset Kata Sandi
                    </h3>
                    
                    <div class="mb-6">
                        <p class="text-sm text-gray-600 mb-4">
                            Masukkan kata sandi baru untuk <strong x-text="selectedStudent?.name"></strong>
                        </p>
                        
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kata Sandi Baru <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input 
                                :type="showResetPasswordVisible ? 'text' : 'password'" 
                                x-model="resetPasswordInput" 
                                @input="resetPasswordError = ''"
                                @keydown.enter="confirmResetPassword()"
                                :class="resetPasswordError ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-yellow-500 focus:border-yellow-500'"
                                class="w-full border rounded-lg px-4 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2" 
                                placeholder="Masukkan kata sandi baru (wajib)"
                                autofocus
                            />
                            <button 
                                type="button" 
                                @click="showResetPasswordVisible = !showResetPasswordVisible" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                            >
                                <svg x-show="!showResetPasswordVisible" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showResetPasswordVisible" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        <p x-show="resetPasswordError" x-text="resetPasswordError" class="text-xs text-red-600 mt-1.5 font-medium" style="display: none;"></p>
                        <p x-show="!resetPasswordError" class="text-xs text-gray-500 mt-1" style="display: block;">Minimal 6 karakter jika diisi manual</p>
                    </div>

                    <div class="flex gap-3 justify-end">
                        <button 
                            @click="showResetPasswordModal = false; resetPasswordInput = ''; showResetPasswordVisible = false; resetPasswordError = '';" 
                            class="px-5 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm font-medium transition-colors"
                        >
                            Batal
                        </button>
                        <button 
                            @click="confirmResetPassword()" 
                            class="px-5 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-sm font-medium transition-colors"
                        >
                            OK
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- PIN Verification Modal -->
        <div x-show="showPinModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-[70]" style="display: none;">
            <div class="bg-white rounded-lg max-w-md w-full mx-4 shadow-2xl">
                <div class="bg-red-500 text-white p-6 rounded-t-lg">
                    <div class="flex items-center gap-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <div>
                            <h3 class="text-xl font-semibold">Verifikasi Keamanan Diperlukan</h3>
                            <p class="text-red-100 text-sm mt-1">Manajemen Kategori adalah operasi yang sensitif</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <div class="text-sm text-yellow-700">
                                <p class="font-semibold">Peringatan!</p>
                                <p>Perubahan kategori dapat mempengaruhi semua pengajuan mahasiswa. Silakan masukkan PIN untuk melanjutkan.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Masukkan Kode PIN</label>
                        <input 
                            type="password" 
                            x-model="pinInput" 
                            @keyup.enter="verifyPin"
                            placeholder="Masukkan PIN 6 digit" 
                            maxlength="6"
                            class="w-full border-2 rounded-lg px-4 py-3 text-center text-2xl tracking-widest font-mono focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                            :class="pinError ? 'border-red-500 bg-red-50' : 'border-gray-300'"
                        />
                        <p x-show="pinError" class="text-red-600 text-sm mt-2 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            PIN salah. Silakan coba lagi.
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <button @click="closePinModal" class="flex-1 px-4 py-2.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm font-medium">Batal</button>
                        <button @click="verifyPin" class="flex-1 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium">Verifikasi PIN</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirmation Modals -->
        <!-- Edit Confirmation Modal -->
        <div x-show="showEditConfirmModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-[70]" style="display: none;">
            <div class="bg-white rounded-lg max-w-md w-full mx-4 shadow-2xl">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Konfirmasi Edit Kategori</h3>
                            <p class="text-sm text-gray-600">Apakah Anda yakin ingin menyimpan perubahan ini?</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4" x-show="editingCategory">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kategori:</span>
                                <span class="font-semibold" x-text="editingCategory?.name"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Poin:</span>
                                <span class="font-semibold" x-text="editingCategory?.points"></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button @click="showEditConfirmModal = false" class="flex-1 px-4 py-2.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm font-medium">Batal</button>
                        <button @click="confirmSaveCategory" class="flex-1 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium">Simpan Perubahan</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div x-show="showDeleteConfirmModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-[70]" style="display: none;">
            <div class="bg-white rounded-lg max-w-md w-full mx-4 shadow-2xl">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Konfirmasi Hapus Kategori</h3>
                            <p class="text-sm text-gray-600">Tindakan ini tidak dapat dibatalkan!</p>
                        </div>
                    </div>

                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4" x-show="deletingCategory">
                        <p class="text-sm text-red-800 mb-2">
                            Anda akan menghapus: <strong x-text="deletingCategory?.name"></strong>
                        </p>
                        <p class="text-xs text-red-700" x-show="deletingCategory?.usageCount > 0">
                            ⚠️ Kategori ini saat ini digunakan dalam <strong x-text="deletingCategory?.usageCount"></strong> pengajuan. Menghapusnya dapat mempengaruhi data yang ada.
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <button @click="showDeleteConfirmModal = false; deletingCategory = null" class="flex-1 px-4 py-2.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm font-medium">Batal</button>
                        <button @click="confirmDeleteCategoryFinal" class="flex-1 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium">Hapus Kategori</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Generic Alert/Confirmation Modal -->
        <div x-show="showAlertModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-[80]" style="display: none;">
            <div class="bg-white rounded-lg max-w-md w-full mx-4 shadow-2xl">
                <div class="p-6">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0" :class="{
                            'bg-green-100': alertType === 'success',
                            'bg-blue-100': alertType === 'info',
                            'bg-yellow-100': alertType === 'warning',
                            'bg-red-100': alertType === 'error'
                        }">
                            <svg class="w-6 h-6" :class="{
                                'text-green-500': alertType === 'success',
                                'text-blue-500': alertType === 'info',
                                'text-yellow-500': alertType === 'warning',
                                'text-red-500': alertType === 'error'
                            }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path x-show="alertType === 'success'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                <path x-show="alertType === 'info'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                <path x-show="alertType === 'warning'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                <path x-show="alertType === 'error'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-800 mb-1" x-text="alertTitle"></h3>
                            <p class="text-sm text-gray-600 whitespace-pre-line" x-text="alertMessage"></p>
                        </div>
                    </div>
                    <div class="flex gap-3 justify-end">
                        <button x-show="alertHasCancel" @click="closeAlertModal(false)" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm font-medium">Batal</button>
                        <button @click="closeAlertModal(true)" class="px-4 py-2 rounded-lg text-sm font-medium text-white" :class="{
                            'bg-green-500 hover:bg-green-600': alertType === 'success',
                            'bg-blue-500 hover:bg-blue-600': alertType === 'info',
                            'bg-yellow-500 hover:bg-yellow-600': alertType === 'warning',
                            'bg-red-500 hover:bg-red-600': alertType === 'error'
                        }" x-text="alertHasCancel ? 'Konfirmasi' : 'OK'"></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approve Confirmation Modal -->
        <div x-show="showApproveModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-[70]" style="display: none;">
            <div class="bg-white rounded-lg max-w-md w-full mx-4 shadow-2xl">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Setujui Pengajuan</h3>
                            <p class="text-sm text-gray-600">Konfirmasi detail persetujuan</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4" x-show="selectedSubmission">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Mahasiswa:</span>
                                <span class="font-semibold" x-text="selectedSubmission?.studentName"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kategori Utama:</span>
                                <span class="font-semibold" x-text="approveModalMainCategory"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subkategori:</span>
                                <span class="font-semibold" x-text="approveModalSubcategory"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Poin:</span>
                                <span class="font-semibold text-green-600" x-text="approveModalPoints"></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button @click="showApproveModal = false" class="flex-1 px-4 py-2.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm font-medium">Batal</button>
                        <button @click="confirmApprove" class="flex-1 px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm font-medium">Setujui</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Management Modal -->
        <div x-show="showCategoryModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-[60]" style="display: none;">
            <div class="bg-white rounded-lg max-w-6xl w-full mx-4 shadow-2xl max-h-[90vh] flex flex-col">
                <div class="flex justify-between items-center p-4 sm:p-6 border-b">
                    <div class="flex items-center gap-2 min-w-0 flex-1">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <h3 class="text-base sm:text-xl font-semibold text-gray-800 break-words">Manajemen Kategori</h3>
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-semibold whitespace-nowrap">Terverifikasi</span>
                    </div>
                    <button @click="closeCategoryModal" class="text-gray-500 hover:text-gray-700 text-2xl leading-none flex-shrink-0 ml-2">×</button>
                </div>

                <div class="flex-1 overflow-y-auto p-4 sm:p-6">
                    <!-- Add New Main Category Section -->
                    <div class="bg-green-50 border-2 border-green-200 rounded-lg p-3 sm:p-4 mb-4">
                        <h4 class="font-semibold text-gray-800 mb-3 sm:mb-4 flex items-center gap-2 text-sm sm:text-base">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span class="break-words">Tambah Kategori Utama Baru</span>
                        </h4>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <input type="text" x-model="newMainCategory" placeholder="Nama Kategori Utama" class="flex-1 border rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-green-500" />
                            <label class="inline-flex items-center gap-2 text-xs sm:text-sm text-gray-700 px-3 py-2 border rounded-lg bg-white">
                                <input type="checkbox" x-model="newMainCategoryIsMandatory" class="rounded border-gray-300 text-green-600 focus:ring-green-500" />
                                <span>Wajib</span>
                            </label>
                            <button @click="addMainCategory" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-xs sm:text-sm font-medium whitespace-nowrap">
                                Add Main Category
                            </button>
                        </div>
                    </div>

                    <!-- Add New Subcategory Section -->
                    <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-3 sm:p-4 mb-6">
                        <h4 class="font-semibold text-gray-800 mb-3 sm:mb-4 flex items-center gap-2 text-sm sm:text-base">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span class="break-words">Tambah Subkategori Baru</span>
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                            <select x-model="newCategory.mainCategoryIndex" class="border rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih Kategori Utama</option>
                                <template x-for="(cat, idx) in categories" :key="cat.id">
                                    <option :value="idx" x-text="(idx + 1) + '. ' + cat.name"></option>
                                </template>
                            </select>
                            <input type="text" x-model="newCategory.name" placeholder="Nama Subkategori" class="border rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            <input type="number" x-model="newCategory.points" placeholder="Poin" class="border rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            <input type="text" x-model="newCategory.description" placeholder="Deskripsi" class="border rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            <label class="inline-flex items-center gap-2 text-xs sm:text-sm text-gray-700 px-3 py-2 border rounded-lg bg-white">
                                <input type="checkbox" x-model="newCategory.isMandatory" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                <span>Wajib</span>
                            </label>
                        </div>
                        <button @click="addSubcategory" class="mt-3 w-full sm:w-auto bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-xs sm:text-sm font-medium whitespace-nowrap">
                            Add Subcategory
                        </button>
                    </div>

                    <!-- Categories List -->
                    <div class="space-y-4">
                        <h4 class="font-semibold text-gray-800 mb-3 text-sm sm:text-base">Kategori & Subkategori yang Sudah Ada</h4>
                        
                        <template x-for="(cat, catIndex) in categories" :key="cat.id">
                            <div class="bg-gray-50 border-2 border-gray-300 rounded-lg p-4 mb-4">
                                
                                <div class="flex items-center justify-between mb-3">
                                        <template x-if="!cat.isEditing">
                                            <h5 class="font-bold text-lg text-gray-800">
                                                <span x-text="(catIndex + 1) + '. ' + cat.name"></span>
                                                <span x-show="cat.is_mandatory" class="ml-2 inline-block text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded">Wajib</span>
                                                <span x-show="cat.is_active == 0 || cat.is_active === false" class="ml-2 inline-block text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded">Tidak Aktif</span>
                                            </h5>
                                        </template>
                                    
                                    <template x-if="cat.isEditing">
                                        <div class="flex-1 flex items-center gap-3 mr-3">
                                            <input type="text" x-model="cat.name" class="flex-1 border-2 border-blue-500 rounded-lg px-3 py-1.5 text-lg font-bold focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                            <label class="inline-flex items-center gap-2 text-xs sm:text-sm text-gray-700 px-3 py-2 border rounded-lg bg-white">
                                                <input type="checkbox" x-model="cat.is_mandatory" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                                <span>Wajib</span>
                                            </label>
                                        </div>
                                    </template>

                                    <div class="flex gap-2">
                                        <template x-if="!cat.isEditing">
                                            <button @click="cat.isEditing = true" class="text-blue-500 hover:text-blue-700 p-1" title="Edit Kategori Utama">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                            </button>
                                        </template>
                                        
                                        <template x-if="cat.isEditing">
                                            <div class="flex gap-1">
                                                <button @click="saveMainCategory(catIndex)" class="text-green-500 hover:text-green-700 p-1"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></button>
                                                <button @click="cat.isEditing = false" class="text-gray-500 hover:text-gray-700 p-1"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                                            </div>
                                        </template>
                                        
                                        <template x-if="cat.is_active == 1 || typeof(cat.is_active) === 'undefined'">
                                            <button @click="deleteMainCategory(catIndex)" class="text-red-500 hover:text-red-700 p-1" title="Hapus Kategori Utama">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </template>
                                        <template x-if="cat.is_active == 0 || cat.is_active === false">
                                            <button @click="reactivateCategoryPrompt(catIndex)" class="text-green-600 hover:text-green-800 p-1" title="Pulihkan Kategori">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <div class="mb-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                    <div class="text-xs text-gray-600">
                                        Maksimum submit per semester:
                                        <span class="font-semibold text-gray-800" x-text="getCategorySemesterLimitLabel(cat)"></span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <select x-model="cat.max_submissions_per_semester" class="border rounded-lg px-2.5 py-1.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                                            <option value="none">Tidak Ada</option>
                                            <template x-for="limit in [1,2,3,4,5,6,7,8,9,10]" :key="'inline-limit-' + cat.id + '-' + limit">
                                                <option :value="String(limit)" x-text="limit"></option>
                                            </template>
                                        </select>
                                        <button
                                            @click="updateCategorySemesterLimit(catIndex)"
                                            :disabled="cat.isSavingLimit"
                                            class="px-3 py-1.5 rounded-lg text-xs sm:text-sm font-medium text-white bg-green-500 hover:bg-green-600 disabled:bg-gray-300 disabled:cursor-not-allowed"
                                        >
                                            <span x-show="!cat.isSavingLimit">Simpan Limit</span>
                                            <span x-show="cat.isSavingLimit">Menyimpan...</span>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="space-y-2 ml-2 sm:ml-4">
                                    <template x-for="(subcat, subIndex) in cat.subcategories" :key="subIndex">
                                        <div class="bg-white border border-gray-200 rounded-lg p-2 sm:p-3 hover:border-blue-300 transition-colors">
                                            <div class="flex items-start gap-2 sm:gap-3">
                                                
                                                <template x-if="subcat.isEditing">
                                                    <div class="flex-1 flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                                        <input type="text" x-model="subcat.name" placeholder="Name" class="w-full sm:flex-1 border rounded px-2 py-1 text-xs sm:text-sm">
                                                        <input type="number" x-model="subcat.points" placeholder="Pts" class="w-full sm:w-16 border rounded px-2 py-1 text-xs sm:text-sm">
                                                        <input type="text" x-model="subcat.description" placeholder="Desc" class="w-full sm:flex-1 border rounded px-2 py-1 text-xs sm:text-sm">
                                                        <label class="inline-flex items-center gap-1 text-xs text-gray-700 px-2 py-1 border rounded bg-white">
                                                            <input type="checkbox" x-model="subcat.is_mandatory" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                                            <span>Wajib</span>
                                                        </label>
                                                        <div class="flex gap-2 w-full sm:w-auto justify-end">
                                                            <button @click="saveSubcategory(catIndex, subIndex)" class="text-green-600 hover:text-green-800"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></button>
                                                            <button @click="cancelEditSubcategory(catIndex, subIndex)" class="text-gray-500 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                                                        </div>
                                                    </div>
                                                </template>
                                                
                                                <template x-if="!subcat.isEditing">
                                                    <div class="flex-1 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                                        <div class="flex-1 min-w-0">
                                                            <h6 class="font-medium text-gray-800 text-xs sm:text-sm break-words">
                                                                <span x-text="subcat.name"></span>
                                                                <span x-show="subcat.is_mandatory" class="ml-2 inline-block text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded">Wajib</span>
                                                                <span x-show="subcat.is_active == 0 || subcat.is_active === false" class="ml-2 inline-block text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded">Tidak Aktif</span>
                                                            </h6>
                                                            <p class="text-xs text-gray-500 mt-1 break-words">
                                                                Poin: <span class="font-semibold text-blue-600" x-text="subcat.points"></span> | 
                                                                <span x-text="subcat.description"></span>
                                                            </p>
                                                        </div>
                                                        <div class="flex items-center gap-2 flex-shrink-0">
                                                            <div class="flex items-center gap-2">
                                                                <button @click="editSubcategory(catIndex, subIndex)" class="text-blue-500 hover:text-blue-700 p-1" title="Edit Subkategori">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                                </button>
                                                                <!-- show delete only when active (1) or when property missing; hide for 0/'0'/false -->
                                                                <button x-show="subcat.is_active == 1 || typeof(subcat.is_active) === 'undefined'" @click="deleteSubcategoryPrompt(catIndex, subIndex)" class="flex items-center gap-1 text-red-500 hover:text-red-700 px-2 sm:px-3 py-1 rounded-md text-xs sm:text-sm bg-red-50 hover:bg-red-100" title="Hapus Subkategori" aria-label="Hapus Subkategori">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                                </button>
                                                            </div>
                                                            <template x-if="subcat.is_active == 0 || subcat.is_active === false">
                                                                <button @click="reactivateSubcategoryPrompt(catIndex, subIndex)" class="text-green-600 hover:text-green-800 p-1" title="Pulihkan Subkategori">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-6-6 6-6" /></svg>
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </template>

                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="border-t p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row gap-3 sm:justify-between sm:items-center">
                        <label class="flex items-center gap-2 text-xs sm:text-sm">
                            <input type="checkbox" class="mr-2" x-model="showInactiveCategories" @change="loadCategories()">
                            <span class="text-xs sm:text-sm text-gray-600">Tampilkan yang tidak aktif</span>
                        </label>
                        <div class="flex gap-3 justify-end">
                            <button @click="closeCategoryModal" class="w-full sm:w-auto px-4 sm:px-6 py-2.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-xs sm:text-sm font-medium">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>        <!-- Sidebar -->
        <div :class="isSidebarOpen ? 'w-64' : 'w-20'" class="sidebar-container bg-white shadow-lg transition-all duration-300 flex flex-col">
            <div class="p-4 border-b flex flex-col items-center">
                <img src="/images/logo.png" alt="Logo" class="w-12 h-12 object-contain">
                <div x-show="isSidebarOpen" class="sidebar-text mt-2 text-center">
                    <h2 class="text-sm font-bold text-gray-800">S-Core ITBSS</h2>
                    <p class="text-xs text-gray-500">Tinjauan & Manajemen Admin</p>
                </div>
            </div>

            <nav class="mt-4 flex-1">
                <button @click="activeMenu = 'Review Submissions'" class="w-full flex items-center py-3 text-left hover:bg-gray-100 transition-colors" :class="[
                    isSidebarOpen ? 'gap-3 px-4' : 'justify-center',
                    activeMenu === 'Review Submissions' ? 'bg-blue-50 border-l-4 border-blue-500' : ''
                ]">
                    <svg class="w-6 h-6" :class="activeMenu === 'Review Submissions' ? 'text-blue-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span x-show="isSidebarOpen" class="sidebar-text text-sm" :class="activeMenu === 'Review Submissions' ? 'text-blue-700 font-medium' : 'text-gray-700'">Tinjauan Pengajuan</span>
                </button>
                <!-- <button @click="activeMenu = 'Statistics'" class="w-full flex items-center py-3 text-left hover:bg-gray-100 transition-colors" :class="[
                    isSidebarOpen ? 'gap-3 px-4' : 'justify-center',
                    activeMenu === 'Statistics' ? 'bg-blue-50 border-l-4 border-blue-500' : ''
                ]">
                    <svg class="w-6 h-6" :class="activeMenu === 'Statistics' ? 'text-blue-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span x-show="isSidebarOpen" class="sidebar-text text-sm" :class="activeMenu === 'Statistics' ? 'text-blue-700 font-medium' : 'text-gray-700'">Statistics</span>
                </button> -->
                <button @click="activeMenu = 'Students'" class="w-full flex items-center py-3 text-left hover:bg-gray-100 transition-colors" :class="[
                    isSidebarOpen ? 'gap-3 px-4' : 'justify-center',
                    activeMenu === 'Students' ? 'bg-blue-50 border-l-4 border-blue-500' : ''
                ]">
                    <svg class="w-6 h-6" :class="activeMenu === 'Students' ? 'text-blue-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span x-show="isSidebarOpen" class="sidebar-text text-sm" :class="activeMenu === 'Students' ? 'text-blue-700 font-medium' : 'text-gray-700'">Mahasiswa</span>
                </button>
                <button @click="activeMenu = 'Bulk Score'" class="w-full flex items-center py-3 text-left hover:bg-gray-100 transition-colors" :class="[
                    isSidebarOpen ? 'gap-3 px-4' : 'justify-center',
                    activeMenu === 'Bulk Score' ? 'bg-blue-50 border-l-4 border-blue-500' : ''
                ]">
                    <svg class="w-6 h-6" :class="activeMenu === 'Bulk Score' ? 'text-blue-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span x-show="isSidebarOpen" class="sidebar-text text-sm" :class="activeMenu === 'Bulk Score' ? 'text-blue-700 font-medium' : 'text-gray-700'">Nilai Massal</span>
                </button>
                <button @click="activeMenu = 'Settings'" class="w-full flex items-center py-3 text-left hover:bg-gray-100 transition-colors" :class="[
                    isSidebarOpen ? 'gap-3 px-4' : 'justify-center',
                    activeMenu === 'Settings' ? 'bg-blue-50 border-l-4 border-blue-500' : ''
                ]">
                    <svg class="w-6 h-6" :class="activeMenu === 'Settings' ? 'text-blue-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span x-show="isSidebarOpen" class="sidebar-text text-sm" :class="activeMenu === 'Settings' ? 'text-blue-700 font-medium' : 'text-gray-700'">Pengaturan</span>
                </button>
                <button @click="activeMenu = 'Help'" class="w-full flex items-center py-3 text-left hover:bg-gray-100 transition-colors" :class="[
                    isSidebarOpen ? 'gap-3 px-4' : 'justify-center',
                    activeMenu === 'Help' ? 'bg-blue-50 border-l-4 border-blue-500' : ''
                ]">
                    <svg class="w-6 h-6" :class="activeMenu === 'Help' ? 'text-blue-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-show="isSidebarOpen" class="sidebar-text text-sm" :class="activeMenu === 'Help' ? 'text-blue-700 font-medium' : 'text-gray-700'">Bantuan</span>
                </button>
            </nav>

            <div class="border-t mt-auto">
                <button @click="showLogoutModal = true" class="w-full flex items-center py-3 text-left hover:bg-gray-100 transition-colors" :class="isSidebarOpen ? 'gap-3 px-4' : 'justify-center'">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span x-show="isSidebarOpen" class="sidebar-text text-sm text-red-500">Logout</span>
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <div class="bg-white shadow-sm p-4 flex justify-between items-center sticky top-0 z-10">
                <button @click="isSidebarOpen = !isSidebarOpen" class="hamburger-btn p-2 hover:bg-gray-100 rounded">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium">{{ Auth::user()->name }}</span>
                </div>
            </div>

            <div class="flex-1 overflow-auto p-6">
                <!-- Review Submissions Page -->
                <div x-show="activeMenu === 'Review Submissions'">
                    <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row gap-3 sm:gap-4 sm:justify-between sm:items-start">
                        <div>
                            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 mb-2">Tinjauan Pengajuan S-Core</h1>
                            <p class="text-sm sm:text-base text-gray-600">Tinjau dan setujui pengajuan kegiatan mahasiswa</p>
                        </div>
                        <button @click="fetchSubmissions()" class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 sm:px-4 sm:py-2 rounded-lg text-xs sm:text-sm font-medium flex items-center justify-center gap-2 transition-colors">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span class="hidden sm:inline">Muat Pengajuan Baru</span>
                            <span class="sm:hidden">Segarkan</span>
                        </button>
                    </div>

                    <!-- Filters -->
                    <div class="bg-white rounded-lg shadow p-4 mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                            <select x-model="statusFilter" @change="submissionsPagination.currentPage = 1" class="border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Status</option>
                                <option value="Waiting">Menunggu</option>
                                <option value="Approved">Disetujui</option>
                                <option value="Rejected">Ditolak</option>
                            </select>

                            <select x-model="categoryFilter" @change="submissionsPagination.currentPage = 1" class="border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Kategori</option>
                                <template x-for="cat in categories" :key="cat.id">
                                    <option :value="cat.name" x-text="cat.name.length > 30 ? cat.name.substring(0, 30) + '...' : cat.name"></option>
                                </template>
                            </select>

                            <select x-model="submissionYearFilter" @change="submissionsPagination.currentPage = 1" class="border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Angkatan</option>
                                <template x-for="year in availableReviewYears" :key="`review-year-${year}`">
                                    <option :value="String(year)" x-text="year"></option>
                                </template>
                            </select>

                            <input type="text" x-model="searchQuery" @input="submissionsPagination.currentPage = 1" autocomplete="off" autocapitalize="off" spellcheck="false" placeholder="Cari pengajuan..." class="border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                    </div>

                    <!-- Submissions Table - Desktop (>=1000px) -->
                    <div class="bg-white rounded-lg shadow overflow-hidden hidden min-[1000px]:block">
                        <div class="overflow-x-auto">
                            <table class="min-w-[900px] w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="text-left py-3 px-4 font-semibold text-sm">Mahasiswa</th>
                                        <th class="text-left py-3 px-4 font-semibold text-sm">Kategori Utama</th>
                                        <th class="text-left py-3 px-4 font-semibold text-sm">Subkategori</th>
                                        <th class="text-left py-3 px-4 font-semibold text-sm">Judul Kegiatan</th>
                                        <th class="text-center py-3 px-4 font-semibold text-sm">Poin</th>
                                        <th class="text-left py-3 px-4 font-semibold text-sm">Dikirim</th>
                                        <th class="text-center py-3 px-4 font-semibold text-sm">Status</th>
                                        <th class="text-center py-3 px-4 font-semibold text-sm">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="submission in paginatedSubmissions" :key="submission.id">
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-4">
                                            <div class="text-sm">
                                                <div class="font-medium" x-text="submission.studentName"></div>
                                                <div class="text-gray-500 text-xs" x-text="submission.studentId"></div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-sm max-w-xs">
                                            <div class="truncate" :title="submission.mainCategory" x-text="submission.mainCategory"></div>
                                        </td>
                                        <td class="py-3 px-4 text-sm max-w-xs">
                                            <div class="truncate" :title="submission.subcategory" x-text="submission.subcategory"></div>
                                        </td>
                                        <td class="py-3 px-4 text-sm" x-text="submission.judul"></td>
                                        <td class="text-center py-3 px-4 text-sm font-medium" x-text="submission.point || '-'"></td>
                                        <td class="py-3 px-4 text-xs text-gray-600" x-text="submission.waktu"></td>
                                        <td class="text-center py-3 px-4">
                                            <span :class="{
                                                'bg-green-100 text-green-700': submission.status === 'Approved',
                                                'bg-yellow-100 text-yellow-700': submission.status === 'Waiting',
                                                'bg-red-100 text-red-700': submission.status === 'Rejected'
                                            }" class="px-3 py-1 rounded-full text-xs font-semibold" x-text="translateStatus(submission.status)"></span>
                                        </td>
                                        <td class="text-center py-3 px-4">
                                            <button @click="viewDetail(submission)" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs font-medium">Tinjau</button>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="filteredSubmissions.length === 0">
                                    <tr>
                                        <td colspan="7" class="text-center py-8 text-gray-500">Tidak ada pengajuan yang sesuai dengan filter Anda</td>
                                    </tr>
                                </template>
                            </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Submissions List - Mobile (<1000px) -->
                    <div class="min-[1000px]:hidden space-y-2">
                        <template x-for="submission in paginatedSubmissions" :key="submission.id">
                            <div @click="openMobileDetail(submission)" class="bg-white rounded-lg shadow p-3 flex items-center justify-between cursor-pointer active:bg-gray-50 transition-colors">
                                <div class="flex-1 min-w-0 mr-3">
                                    <div class="font-medium text-sm truncate" x-text="submission.studentName"></div>
                                    <div class="text-xs text-gray-500 truncate" x-text="submission.judul"></div>
                                </div>
                                <span :class="{
                                    'bg-green-100 text-green-700': submission.status === 'Approved',
                                    'bg-yellow-100 text-yellow-700': submission.status === 'Waiting',
                                    'bg-red-100 text-red-700': submission.status === 'Rejected'
                                }" class="px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap flex-shrink-0" x-text="translateStatus(submission.status)"></span>
                            </div>
                        </template>
                        <template x-if="filteredSubmissions.length === 0">
                            <div class="bg-white rounded-lg shadow p-6 text-center text-gray-500 text-sm">Tidak ada pengajuan yang sesuai dengan filter Anda</div>
                        </template>
                    </div>

                    <div class="border-t px-4 py-3 mt-3 bg-white rounded-lg shadow flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <p class="text-sm text-gray-600" x-text="`Halaman ${submissionsPagination.currentPage} dari ${reviewLastPage}`"></p>
                        <div class="flex gap-2 justify-end">
                            <button
                                @click="goToSubmissionPage(submissionsPagination.currentPage - 1)"
                                :disabled="submissionsPagination.currentPage <= 1"
                                :class="submissionsPagination.currentPage <= 1 ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white hover:bg-gray-100 text-gray-700 border'"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                            >
                                Kiri
                            </button>
                            <button
                                @click="goToSubmissionPage(submissionsPagination.currentPage + 1)"
                                :disabled="submissionsPagination.currentPage >= reviewLastPage"
                                :class="submissionsPagination.currentPage >= reviewLastPage ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-blue-500 hover:bg-blue-600 text-white'"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                            >
                                Kanan
                            </button>
                        </div>
                    </div>

                    <!-- Mobile Detail Bottom Sheet -->
                    <div x-show="showMobileDetailModal" class="fixed inset-0 z-50 min-[1000px]:hidden" style="display: none;">
                        <div class="absolute inset-0 bg-black bg-opacity-50" @click="showMobileDetailModal = false; mobileDetailSubmission = null;"></div>
                        <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-2xl max-h-[80vh] overflow-y-auto animate-slide-up">
                            <div class="sticky top-0 bg-white border-b px-4 py-3 flex justify-between items-center rounded-t-2xl z-10">
                                <h3 class="font-semibold text-base">Detail Pengajuan</h3>
                                <button @click="showMobileDetailModal = false; mobileDetailSubmission = null;" class="text-gray-400 hover:text-gray-600 text-xl leading-none w-8 h-8 flex items-center justify-center">&times;</button>
                            </div>
                            <template x-if="mobileDetailSubmission">
                                <div class="p-4 space-y-3">
                                    <!-- Student Info -->
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                        <div class="grid grid-cols-2 gap-2 text-xs">
                                            <div>
                                                <span class="text-gray-500 block">Mahasiswa</span>
                                                <span class="font-medium" x-text="mobileDetailSubmission.studentName"></span>
                                            </div>
                                            <div>
                                                <span class="text-gray-500 block">NIM</span>
                                                <span class="font-medium" x-text="mobileDetailSubmission.studentId"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Submission Details -->
                                    <div class="space-y-2 text-xs">
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">Status</span>
                                            <span :class="{
                                                'bg-green-100 text-green-700': mobileDetailSubmission.status === 'Approved',
                                                'bg-yellow-100 text-yellow-700': mobileDetailSubmission.status === 'Waiting',
                                                'bg-red-100 text-red-700': mobileDetailSubmission.status === 'Rejected'
                                            }" class="px-2 py-0.5 rounded-full font-semibold" x-text="translateStatus(mobileDetailSubmission.status)"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">Kategori</span>
                                            <span class="font-medium text-right ml-4" x-text="mobileDetailSubmission.mainCategory"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">Subkategori</span>
                                            <span class="font-medium text-right ml-4" x-text="mobileDetailSubmission.subcategory"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">Kegiatan</span>
                                            <span class="font-medium text-right ml-4" x-text="mobileDetailSubmission.judul"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">Poin</span>
                                            <span class="font-medium" x-text="mobileDetailSubmission.point || '-'"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">Dikirim</span>
                                            <span class="font-medium" x-text="mobileDetailSubmission.waktu"></span>
                                        </div>
                                    </div>
                                    <!-- Description -->
                                    <div x-show="mobileDetailSubmission.keterangan" class="text-xs">
                                        <span class="text-gray-500 block mb-1">Deskripsi</span>
                                        <div class="bg-gray-50 border rounded p-2 whitespace-pre-wrap" x-text="mobileDetailSubmission.keterangan"></div>
                                    </div>
                                    <!-- Action Button -->
                                    <button @click="let s = mobileDetailSubmission; showMobileDetailModal = false; mobileDetailSubmission = null; viewDetail(s);" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2.5 rounded-lg text-sm font-medium transition-colors">
                                        Buka Tinjauan Lengkap
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Statistics Page -->
                <div x-show="activeMenu === 'Statistics'" style="display: none;">
                    <div class="mb-4 sm:mb-6">
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 mb-2">Statistik</h1>
                        <p class="text-sm sm:text-base text-gray-600">Gambaran statistik pengajuan S-Core</p>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
                    <div class="bg-white rounded-lg shadow p-3 sm:p-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <p class="text-xs sm:text-sm text-gray-600">Total Pengajuan</p>
                                <p class="text-xl sm:text-2xl font-bold text-gray-800" x-text="stats.total"></p>
                            </div>
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-3 sm:p-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <p class="text-xs sm:text-sm text-gray-600">Menunggu Tinjauan</p>
                                <p class="text-xl sm:text-2xl font-bold text-yellow-600" x-text="stats.waiting"></p>
                            </div>
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-3 sm:p-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <p class="text-xs sm:text-sm text-gray-600">Disetujui</p>
                                <p class="text-xl sm:text-2xl font-bold text-green-600" x-text="stats.approved"></p>
                            </div>
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-3 sm:p-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <p class="text-xs sm:text-sm text-gray-600">Ditolak</p>
                                <p class="text-xl sm:text-2xl font-bold text-red-600" x-text="stats.rejected"></p>
                            </div>
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4">Tren Pengajuan</h3>
                        <div class="flex items-center justify-center h-64 bg-gray-50 rounded border-2 border-dashed">
                            <p class="text-gray-500">Grafik akan ditampilkan di sini</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4">Distribusi Kategori</h3>
                        <div class="flex items-center justify-center h-64 bg-gray-50 rounded border-2 border-dashed">
                            <p class="text-gray-500">Grafik akan ditampilkan di sini</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Aktivitas Terbaru</h3>
                    <div class="space-y-3">
                        <template x-for="submission in submissions.slice(0, 5)" :key="submission.id">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <div>
                                    <p class="font-medium text-sm" x-text="submission.studentName"></p>
                                    <p class="text-xs text-gray-600" x-text="submission.judul"></p>
                                </div>
                                <span :class="{
                                    'bg-green-100 text-green-700': submission.status === 'Approved',
                                    'bg-yellow-100 text-yellow-700': submission.status === 'Waiting',
                                    'bg-red-100 text-red-700': submission.status === 'Rejected'
                                }" class="px-2 py-1 rounded-full text-xs font-semibold" x-text="translateStatus(submission.status)"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Students Page -->
            <div x-show="activeMenu === 'Students'" style="display: none;">
                <div class="mb-4 sm:mb-6">
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 mb-2">Manajemen Mahasiswa & Laporan</h1>
                    <p class="text-sm sm:text-base text-gray-600">Laporan kinerja mahasiswa yang komprehensif dan pelacakan S-Core</p>
                </div>

                <!-- Filter Bar -->
                <div class="bg-white rounded-lg shadow p-4 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                        <div class="md:col-span-3 flex gap-2">
                            <input type="text" x-model="studentSearchQuery" @input="onStudentSearchInput()" @keydown.enter.prevent="applyStudentFilters()" autocomplete="off" autocapitalize="off" spellcheck="false" placeholder="Cari berdasarkan nama atau NIM..." class="flex-1 border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            <button @click="applyStudentFilters()" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm font-medium">Cari</button>
                        </div>
                        
                        <select x-model="majorFilter" @change="applyStudentFilters()" class="border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 md:col-span-2">
                            <option value="">Semua Jurusan</option>
                            <option value="STI">STI</option>
                            <option value="BD">BD</option>
                            <option value="KWU">KWU</option>
                        </select>
                        
                        <div class="grid grid-cols-2 gap-2 md:col-span-3">
                            <select x-model="yearFilterMode" @change="onYearFilterModeChange()" class="border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="all">Semua Angkatan</option>
                                <option value="specific">Angkatan Tertentu</option>
                            </select>
                            <select 
                                x-model="yearFilter" 
                                @change="$nextTick(() => applyStudentFilters())"
                                :disabled="yearFilterMode === 'all'"
                                :class="yearFilterMode === 'all' ? 'bg-gray-100 cursor-not-allowed' : ''"
                                class="border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="" :selected="String(yearFilter ?? '').trim() === ''">Pilih angkatan</option>
                                <template x-for="year in availableStudentYears" :key="`year-option-${year}`">
                                    <option :value="String(year)" :selected="String(yearFilter ?? '').trim() === String(year).trim()" x-text="year"></option>
                                </template>
                            </select>
                        </div>
                        
                        <select x-model="statusPassFilter" class="border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 md:col-span-2">
                            <option value="">Semua Status</option>
                            <option value="met">Memenuhi</option>
                            <option value="not_met">Belum Memenuhi</option>
                        </select>
                        
                        <button @click="exportReport" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center justify-center gap-2 md:col-span-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Ekspor Laporan
                        </button>

                        <a href="{{ route('admin.master-data') }}" target="_blank" rel="noopener noreferrer" class="bg-slate-700 hover:bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center justify-center gap-2 md:col-span-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Master Data
                        </a>

                        <a href="{{ route('admin.perfect-data') }}" target="_blank" rel="noopener noreferrer" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center justify-center gap-2 md:col-span-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15l-2.4 1.8.9-3-2.4-1.8h3l.9-3 .9 3h3l-2.4 1.8.9 3z" />
                            </svg>
                            Honorary Graduate
                        </a>
                    </div>

                    <div class="mt-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-t pt-3">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <span>Tampilkan</span>
                            <select x-model.number="studentsPagination.perPage" @change="changeStudentsPerPage" class="border rounded-lg px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option :value="25">25</option>
                                <option :value="50">50</option>
                                <option :value="100">100</option>
                                <option :value="250">250</option>
                                <option :value="500">500</option>
                            </select>
                            <span>mahasiswa per halaman</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <button
                                @click="demoteAllStudentsSemester()"
                                :disabled="isDemotingSemester"
                                class="bg-rose-500 hover:bg-rose-600 disabled:bg-rose-300 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                                <span x-text="isDemotingSemester ? 'Memproses...' : 'Semester Turun'"></span>
                            </button>
                            <button
                                @click="promoteAllStudentsSemester()"
                                :disabled="isPromotingSemester"
                                class="bg-indigo-500 hover:bg-indigo-600 disabled:bg-indigo-300 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                </svg>
                                <span x-text="isPromotingSemester ? 'Memproses...' : 'Semester Naik'"></span>
                            </button>
                            <p class="text-sm text-gray-500" x-text="`Menampilkan ${studentsRange.from} - ${studentsRange.to} dari ${studentsRange.total} mahasiswa`"></p>
                        </div>
                    </div>
                    
                    <!-- Delete Selected Button -->
                    <div x-show="selectedStudents.length > 0" class="mt-3 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 bg-red-50 border border-red-200 rounded-lg p-3">
                        <span class="text-sm text-red-700 font-medium">
                            <span x-text="selectedStudents.length"></span> mahasiswa dipilih
                        </span>

                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <div class="flex flex-wrap items-center gap-4 bg-white border border-red-100 rounded-lg px-3 py-2">
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                    <input
                                        type="checkbox"
                                        class="rounded border-gray-300 text-amber-600 focus:ring-amber-500"
                                        :checked="bulkAcademicStatusDraft === 'on_leave'"
                                        @change="setBulkAcademicStatus('on_leave', $event.target.checked)"
                                    >
                                    Cuti
                                </label>
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                    <input
                                        type="checkbox"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        :checked="bulkAcademicStatusDraft === 'graduated'"
                                        @change="setBulkAcademicStatus('graduated', $event.target.checked)"
                                    >
                                    Lulus
                                </label>
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                    <input
                                        type="checkbox"
                                        class="rounded border-gray-300 text-slate-600 focus:ring-slate-500"
                                        :checked="bulkAcademicStatusDraft === 'non_active'"
                                        @change="setBulkAcademicStatus('non_active', $event.target.checked)"
                                    >
                                    Non Aktif
                                </label>
                                <button
                                    @click="applyBulkAcademicStatus()"
                                    :disabled="isUpdatingBulkAcademicStatus"
                                    class="bg-slate-700 hover:bg-slate-800 disabled:bg-slate-400 text-white px-3 py-1.5 rounded text-xs font-medium"
                                >
                                    <span x-text="isUpdatingBulkAcademicStatus ? 'Menyimpan...' : 'Terapkan Status'"></span>
                                </button>
                            </div>

                            <button @click="deleteSelectedStudents" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus Terpilih
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Total Mahasiswa</p>
                                <p class="text-2xl font-bold text-gray-800" x-text="studentsRange.total"></p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Memenuhi Syarat</p>
                                <p class="text-2xl font-bold text-green-600" x-text="studentStats.met"></p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Belum Memenuhi</p>
                                <p class="text-2xl font-bold text-red-600" x-text="studentStats.notMet"></p>
                            </div>
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Rata-rata Poin</p>
                                <p class="text-2xl font-bold text-purple-600" x-text="studentStats.average"></p>
                            </div>
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Students Table with Hover Details -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <!-- Desktop Table -->
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="min-w-[1000px] w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-center py-3 px-4 font-semibold text-sm w-12">
                                        <input type="checkbox" 
                                        @change="toggleSelectAll($event.target.checked)"
                                        :checked="selectedStudents.length > 0 && selectedStudents.length === paginatedStudentsList.length"
                                        class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                                </th>
                                <th class="text-left py-3 px-4 font-semibold text-sm">NIM</th>
                                <th class="text-left py-3 px-4 font-semibold text-sm">Nama</th>
                                    <th class="text-center py-3 px-4 font-semibold text-sm">Jurusan</th>
                                    <th class="text-center py-3 px-4 font-semibold text-sm">Semester</th>
                                    <th class="text-center py-3 px-4 font-semibold text-sm">Angkatan</th>
                                <th class="text-center py-3 px-4 font-semibold text-sm">Total Poin</th>
                                <th class="text-center py-3 px-4 font-semibold text-sm">Status</th>
                                <th class="text-center py-3 px-4 font-semibold text-sm">Tertunda</th>
                                <th class="text-center py-3 px-4 font-semibold text-sm">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <template x-for="student in paginatedStudentsList" :key="student.id">
                            <tr class="border-b hover:bg-blue-50 transition-colors group" x-data="{ showTooltip: false, tooltipX: 0, tooltipY: 0 }">
                                <td class="text-center py-3 px-4">
                                    <input type="checkbox" 
                                        :value="student.id"
                                        @change="toggleStudentSelection(student.id, $event.target.checked)"
                                        :checked="selectedStudents.includes(student.id)"
                                        class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                                </td>
                                <td class="py-3 px-4 text-sm" x-text="student.id"></td>
                                <td class="py-3 px-4 text-sm font-medium" x-text="student.name"></td>
                                <td class="text-center py-3 px-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold" 
                                        :class="{
                                            'bg-blue-100 text-blue-700': student.major === 'STI',
                                            'bg-green-100 text-green-700': student.major === 'BD',
                                            'bg-purple-100 text-purple-700': student.major === 'KWU'
                                        }"
                                        x-text="student.major">
                                    </span>
                                </td>

                                <td class="text-center py-3 px-4 text-sm font-medium" x-text="student.semester ? 'Semester ' + student.semester : '-'"></td>
                                <td class="text-center py-3 px-4 text-sm" x-text="student.year"></td>
                                
                                <td class="text-center py-3 px-4 text-sm">
                                    <span 
                                        class="font-bold cursor-help"
                                        :class="student.approvedPoints >= 20 ? 'text-green-600' : 'text-red-600'" 
                                        @mouseenter="(e) => { 
                                            showTooltip = true; 
                                            const rect = e.target.getBoundingClientRect();
                                            tooltipX = rect.left + (rect.width / 2);
                                            tooltipY = rect.top + window.scrollY - 8;
                                        }"
                                        @mouseleave="showTooltip = false"
                                        x-text="student.approvedPoints"
                                    ></span>
                                    
                                    <div 
                                        x-show="showTooltip" 
                                        class="fixed z-[100] bg-white border-2 border-blue-500 rounded-lg shadow-2xl p-4 w-96 pointer-events-none"
                                        style="display: none;"
                                        :style="`left: ${tooltipX}px; top: ${tooltipY}px; transform: translate(-50%, -100%);`"
                                    >
                                        <div class="mb-3 pb-3 border-b border-gray-200">
                                            <div class="flex items-center justify-between mb-2">
                                                <h4 class="font-bold text-gray-800" x-text="student.name"></h4>
                                                <span :class="getStudentStatusClass(student)" class="px-2 py-1 rounded-full text-xs font-semibold">
                                                    <span x-text="getStudentFinalStatus(student)"></span>
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-600" x-text="student.id"></p>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="flex justify-between items-center mb-2">
                                                <span class="text-sm font-semibold text-gray-700">Total Poin:</span>
                                                <span class="text-lg font-bold" :class="student.approvedPoints >= 20 ? 'text-green-600' : 'text-red-600'" x-text="student.approvedPoints"></span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="h-2 rounded-full transition-all" :class="student.approvedPoints >= 20 ? 'bg-green-500' : 'bg-red-500'" :style="`width: ${Math.min((student.approvedPoints / 20) * 100, 100)}%`"></div>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1" x-text="`${Math.max(scoreSettings.minPoints - student.approvedPoints, 0)} poin lagi untuk memenuhi`"></p>
                                        </div>
                                        
                                        <div class="space-y-2">
                                            <p class="text-xs font-semibold text-gray-700 mb-2">Poin per Kategori:</p>
                                            <template x-for="(points, category) in student.categoryBreakdown" :key="category">
                                                <div class="flex justify-between text-xs">
                                                    <span class="text-gray-600 truncate pr-2" :title="category" x-text="category.substring(0, 35) + (category.length > 35 ? '...' : '')"></span>
                                                    <span class="font-semibold text-blue-600" x-text="points + ' poin'"></span>
                                                </div>
                                            </template>
                                        </div>
                                        
                                        <div class="mt-3 pt-3 border-t border-gray-200 grid grid-cols-3 gap-2 text-center">
                                            <div>
                                                <p class="text-xs text-gray-600">Total</p>
                                                <p class="font-semibold text-sm" x-text="student.totalSubmissions"></p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-600">Disetujui</p>
                                                <p class="font-semibold text-sm text-green-600" x-text="student.approvedCount"></p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-600">Tertunda</p>
                                                <p class="font-semibold text-sm text-yellow-600" x-text="student.pending"></p>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-center py-3 px-4">
                                    <span :class="getStudentStatusClass(student)" class="px-3 py-1 rounded-full text-xs font-semibold">
                                        <span x-text="getStudentFinalStatus(student)"></span>
                                    </span>
                                </td>

                                <td class="text-center py-3 px-4 text-sm text-yellow-600 font-medium" x-text="student.pending"></td>
                                
                                <td class="text-center py-3 px-4">
                                    <div class="flex gap-2 justify-center">
                                        <button @click="openStudentScoreReport(student)" class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1 rounded text-xs font-medium">Lihat Report</button>
                                        <button @click="viewStudentDetail(student)" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs font-medium">Lihat Detail</button>
                                        <button @click="deleteStudent(student.id)" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-medium">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        
                        <template x-if="filteredStudentsList.length === 0">
                            <tr>
                                <td colspan="9" class="text-center py-8 text-gray-500">Tidak ada mahasiswa yang sesuai dengan filter Anda</td>
                            </tr>
                        </template>
                    </tbody>
                    </table>
                    </div>

                    <!-- Mobile Compact List -->
                    <div class="lg:hidden">
                        <div class="grid grid-cols-[36px_1fr_86px] gap-2 items-center px-3 py-2 border-b bg-gray-50 text-[11px] font-semibold uppercase tracking-wide text-gray-600">
                            <div class="flex justify-center">
                                <input type="checkbox"
                                    @change="toggleSelectAll($event.target.checked)"
                                    :checked="selectedStudents.length > 0 && selectedStudents.length === paginatedStudentsList.length"
                                    class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                            </div>
                            <span>Nama</span>
                            <span class="text-center">Status</span>
                        </div>

                        <template x-for="student in paginatedStudentsList" :key="'mobile-student-' + student.id">
                            <div
                                class="grid grid-cols-[36px_1fr_86px] gap-2 items-center px-3 py-2.5 border-b cursor-pointer transition-colors"
                                :class="selectedStudent?.id === student.id ? 'bg-blue-50' : 'hover:bg-gray-50'"
                                @click="openMobileStudentDetail(student)"
                            >
                                <div class="flex justify-center" @click.stop>
                                    <input
                                        type="checkbox"
                                        :value="student.id"
                                        @change="handleStudentCheckboxToggle(student, $event.target.checked)"
                                        :checked="selectedStudents.includes(student.id)"
                                        class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500"
                                    >
                                </div>

                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate" x-text="student.name"></p>
                                    <p class="text-[11px] text-gray-500 mt-0.5">NIM: <span x-text="student.id"></span></p>
                                </div>

                                <div class="text-center">
                                    <span
                                        :class="getStudentStatusClass(student)"
                                        class="px-2 py-0.5 rounded-full text-[10px] font-semibold inline-block"
                                        x-text="getStudentFinalStatus(student)"
                                    ></span>
                                </div>
                            </div>
                        </template>

                        <template x-if="filteredStudentsList.length === 0">
                            <div class="text-center py-8 text-gray-500 text-sm">Tidak ada mahasiswa yang sesuai dengan filter Anda</div>
                        </template>

                        <!-- Mobile Student Detail Bottom Sheet -->
                        <div x-show="showMobileStudentDetailModal" class="fixed inset-0 z-[70]" style="display: none;">
                            <div class="absolute inset-0 bg-black bg-opacity-50" @click="closeMobileStudentDetail()"></div>
                            <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-2xl max-h-[82vh] overflow-y-auto animate-slide-up">
                                <div class="sticky top-0 bg-white border-b px-4 py-3 flex justify-between items-center rounded-t-2xl z-10">
                                    <h3 class="font-semibold text-base">Detail Mahasiswa</h3>
                                    <button @click="closeMobileStudentDetail()" class="text-gray-400 hover:text-gray-600 text-xl leading-none w-8 h-8 flex items-center justify-center">&times;</button>
                                </div>

                                <template x-if="mobileStudentDetail">
                                    <div class="p-4 space-y-3">
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <p class="text-sm font-semibold text-gray-800 truncate" x-text="mobileStudentDetail.name"></p>
                                                    <p class="text-xs text-gray-500">NIM: <span x-text="mobileStudentDetail.id"></span></p>
                                                </div>
                                                <span
                                                    :class="getStudentStatusClass(mobileStudentDetail)"
                                                    class="px-2 py-0.5 rounded-full text-[10px] font-semibold shrink-0"
                                                    x-text="getStudentFinalStatus(mobileStudentDetail)"
                                                ></span>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2 text-xs">
                                            <div class="bg-gray-50 rounded p-2">
                                                <p class="text-gray-500">Jurusan</p>
                                                <p class="font-semibold text-gray-800 mt-0.5" x-text="mobileStudentDetail.major || '-'"></p>
                                            </div>
                                            <div class="bg-gray-50 rounded p-2">
                                                <p class="text-gray-500">Semester</p>
                                                <p class="font-semibold text-gray-800 mt-0.5" x-text="mobileStudentDetail.semester ? 'Semester ' + mobileStudentDetail.semester : '-'"></p>
                                            </div>
                                            <div class="bg-gray-50 rounded p-2">
                                                <p class="text-gray-500">Angkatan</p>
                                                <p class="font-semibold text-gray-800 mt-0.5" x-text="mobileStudentDetail.year || '-'"></p>
                                            </div>
                                            <div class="bg-gray-50 rounded p-2">
                                                <p class="text-gray-500">Total Poin</p>
                                                <p class="font-semibold text-gray-800 mt-0.5" x-text="mobileStudentDetail.approvedPoints ?? 0"></p>
                                            </div>
                                            <div class="bg-gray-50 rounded p-2">
                                                <p class="text-gray-500">Tertunda</p>
                                                <p class="font-semibold text-gray-800 mt-0.5" x-text="mobileStudentDetail.pending ?? 0"></p>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <button
                                                @click="openStudentScoreReport(mobileStudentDetail)"
                                                class="bg-emerald-500 hover:bg-emerald-600 text-white py-2.5 rounded text-sm font-medium"
                                            >
                                                Lihat Report
                                            </button>
                                            <button
                                                @click="let s = mobileStudentDetail; closeMobileStudentDetail(); viewStudentDetail(s);"
                                                class="bg-blue-500 hover:bg-blue-600 text-white py-2.5 rounded text-sm font-medium"
                                            >
                                                Lihat Detail
                                            </button>
                                            <button
                                                @click="let s = mobileStudentDetail; closeMobileStudentDetail(); deleteStudent(s.id);"
                                                class="bg-red-500 hover:bg-red-600 text-white py-2.5 rounded text-sm font-medium"
                                            >
                                                Hapus
                                            </button>
                                        </div>

                                        <div>
                                            <p class="text-xs font-semibold text-gray-700 mb-2">Riwayat Pengajuan</p>
                                            <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                                                <template x-for="sub in mobileStudentDetail.submissions_list" :key="'mobile-sub-' + sub.id">
                                                    <div class="border rounded p-2 bg-gray-50">
                                                        <p class="text-xs font-medium text-gray-800 truncate" x-text="sub.title"></p>
                                                        <p class="text-[11px] text-gray-500 mt-0.5" x-text="sub.date"></p>
                                                        <p class="text-[11px] text-gray-600 mt-0.5 truncate" x-text="sub.category + ' • ' + sub.subcategory"></p>
                                                        <div class="flex items-center justify-between mt-1">
                                                            <span
                                                                :class="{
                                                                    'bg-green-100 text-green-700': sub.status === 'Approved',
                                                                    'bg-yellow-100 text-yellow-700': sub.status === 'Waiting',
                                                                    'bg-red-100 text-red-700': sub.status === 'Rejected'
                                                                }"
                                                                class="px-2 py-0.5 rounded-full text-[10px] font-semibold"
                                                                x-text="translateStatus(sub.status)"
                                                            ></span>
                                                            <span class="text-[11px] font-semibold text-gray-700" x-text="sub.points || '-'"></span>
                                                        </div>
                                                    </div>
                                                </template>

                                                <template x-if="!mobileStudentDetail.submissions_list?.length">
                                                    <div class="text-xs text-gray-500 text-center py-3">Belum ada riwayat pengajuan.</div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t px-4 py-3 mt-3 bg-white rounded-lg shadow flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <p class="text-sm text-gray-600" x-text="`Halaman ${studentsPagination.currentPage} dari ${studentsLastPage || 1}`"></p>
                    <div class="flex gap-2 justify-end">
                        <button
                            @click="goToStudentsPage(studentsPagination.currentPage - 1)"
                            :disabled="studentsPagination.currentPage <= 1"
                            :class="studentsPagination.currentPage <= 1 ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white hover:bg-gray-100 text-gray-700 border'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                        >
                            Kiri
                        </button>
                        <button
                            @click="goToStudentsPage(studentsPagination.currentPage + 1)"
                            :disabled="studentsPagination.currentPage >= studentsLastPage"
                            :class="studentsPagination.currentPage >= studentsLastPage ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-blue-500 hover:bg-blue-600 text-white'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                        >
                            Kanan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Bulk Score Page -->
            <div x-show="activeMenu === 'Bulk Score'" style="display: none;">
                <div class="mb-4 sm:mb-6">
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 mb-2">Penugasan S-Core Massal</h1>
                    <p class="text-sm sm:text-base text-gray-600">Buat pengajuan S-Core untuk banyak mahasiswa sekaligus berdasarkan filter</p>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <form @submit.prevent="applyBulkScore()" class="space-y-6">
                        <!-- Filter Type Selection -->
                        <div class="border-b pb-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">1. Pilih Target Mahasiswa</h3>
                            <p class="text-sm text-gray-600 mb-3">Anda dapat memilih beberapa filter untuk menargetkan mahasiswa tertentu. Biarkan semua filter kosong untuk menargetkan semua mahasiswa.</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Major Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Jurusan</label>
                                    <select x-model="bulkScore.selectedMajor" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Semua Jurusan</option>
                                        <option value="STI">STI</option>
                                        <option value="BD">BD</option>
                                        <option value="KWU">KWU</option>
                                    </select>
                                </div>

                                <!-- Year Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Angkatan</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <select x-model="bulkScore.yearMode" @change="if(bulkScore.yearMode === 'all') bulkScore.selectedYear = ''" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="all">Semua Angkatan</option>
                                            <option value="specific">Angkatan Tertentu</option>
                                        </select>
                                        <select
                                            x-model="bulkScore.selectedYear"
                                            :disabled="bulkScore.yearMode === 'all'"
                                            :class="bulkScore.yearMode === 'all' ? 'bg-gray-100 cursor-not-allowed' : ''"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        >
                                            <option value="">Pilih angkatan</option>
                                            <template x-for="year in studentYears" :key="year">
                                                <option :value="year" x-text="year"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>

                                <!-- Shift Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Shift</label>
                                    <select x-model="bulkScore.selectedShift" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Semua Shift</option>
                                        <option value="pagi">Pagi</option>
                                        <option value="sore">Sore</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Activity Details Section -->
                        <div class="border-b pb-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">2. Detail Kegiatan</h3>
                            
                            <!-- Main Category -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori Utama <span class="text-red-500">*</span></label>
                                <select x-model="bulkScore.mainCategory" @change="bulkScore.subcategory = ''" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    <option value="">Pilih kategori utama...</option>
                                    <template x-for="(catGroup, idx) in categoryGroups" :key="idx">
                                        <option :value="catGroup.id" x-text="(idx + 1) + '. ' + catGroup.name"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Subcategory -->
                            <div class="mb-4" x-show="bulkScore.mainCategory !== ''">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Subkategori <span class="text-red-500">*</span></label>
                                <select x-model="bulkScore.subcategory" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    <option value="">Pilih subkategori...</option>
                                    <template x-for="(catGroup, idx) in categoryGroups" :key="idx">
                                        <template x-if="catGroup.id == bulkScore.mainCategory">
                                            <template x-for="sub in catGroup.subcategories" :key="sub.id">
                                                <option :value="sub.id" x-text="sub.name + ' (' + sub.points + ' poin)'"></option>
                                            </template>
                                        </template>
                                    </template>
                                </select>
                            </div>

                            <!-- Activity Title -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Judul Kegiatan <span class="text-red-500">*</span></label>
                                <input type="text" x-model="bulkScore.activityTitle" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan judul kegiatan" required>
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi <span class="text-red-500">*</span></label>
                                <textarea x-model="bulkScore.description" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan deskripsi" required></textarea>
                            </div>

                            <!-- Activity Date -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kegiatan <span class="text-red-500">*</span></label>
                                <input type="date" x-model="bulkScore.activityDate" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>

                            <!-- Certificate Upload -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sertifikat/Bukti <span class="text-gray-500">(Opsional)</span></label>
                                <div 
                                    class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer transition-colors"
                                    @dragover.prevent="dragActiveBulk = true"
                                    @dragleave.prevent="dragActiveBulk = false"
                                    @drop.prevent="handleBulkFileDrop"
                                    :class="dragActiveBulk ? 'bg-blue-50 border-blue-400' : 'hover:border-blue-400'"
                                    @click="$refs.bulkCertificate.click()"
                                >
                                    <svg class="w-10 h-10 text-gray-400 mb-2 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="text-gray-600 text-sm mb-1">Seret dan lepas file PDF di sini</p>
                                    <p class="text-gray-400 text-xs">atau</p>
                                    <p class="text-blue-500 text-sm font-medium mt-1">Pilih File</p>
                                </div>
                                <input type="file" x-ref="bulkCertificate" accept=".pdf,application/pdf" class="hidden" @change="handleBulkCertificateChange($event)" />
                                <p class="text-xs text-gray-500 mt-2">Pengumpulan bukti hanya melalui PDF (maks 10MB)</p>
                                <div x-show="bulkFileName" class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-between">
                                    <span class="text-sm text-gray-700" x-text="bulkFileName"></span>
                                    <button type="button" @click="bulkFileName = ''; $refs.bulkCertificate.value = ''" class="text-red-500 hover:text-red-700 text-sm">Hapus</button>
                                </div>
                            </div>
                        </div>

                        <!-- Auto Points Display -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <p class="text-sm text-gray-700 mb-1"><strong>Poin yang akan diberikan:</strong></p>
                            <template x-for="(catGroup, idx) in categoryGroups" :key="idx">
                                <template x-if="catGroup.id == bulkScore.mainCategory">
                                    <template x-for="sub in catGroup.subcategories" :key="sub.id">
                                        <template x-if="sub.id == bulkScore.subcategory">
                                            <div>
                                                <p class="text-2xl font-bold text-green-600" x-text="sub.points"></p>
                                                <p class="text-xs text-gray-500 mt-1">Poin akan ditetapkan dan disetujui secara otomatis</p>
                                            </div>
                                        </template>
                                    </template>
                                </template>
                            </template>
                            <template x-if="!bulkScore.subcategory">
                                <div>
                                    <p class="text-2xl font-bold text-gray-400">0</p>
                                    <p class="text-xs text-gray-500 mt-1">Pilih subkategori untuk melihat poin</p>
                                </div>
                            </template>
                        </div>

                        <!-- Preview --> 
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h3 class="font-semibold text-blue-900 mb-2">Pratinjau</h3>
                            <div class="text-sm text-blue-800 space-y-1">
                                <p><strong>Target Mahasiswa:</strong></p>
                                <ul class="list-disc ml-5">
                                    <li x-show="bulkScore.selectedMajor">Jurusan: <span class="font-bold" x-text="bulkScore.selectedMajor"></span></li>
                                    <li x-show="bulkScore.yearMode === 'specific' && bulkScore.selectedYear">Angkatan: <span class="font-bold" x-text="bulkScore.selectedYear"></span></li>
                                    <li x-show="bulkScore.yearMode === 'all'">Angkatan: <span class="font-bold">Semua Angkatan</span></li>
                                    <li x-show="bulkScore.selectedShift">Shift: <span class="font-bold" x-text="bulkScore.selectedShift === 'pagi' ? 'Pagi' : 'Sore'"></span></li>
                                    <li x-show="!bulkScore.selectedMajor && bulkScore.yearMode === 'all' && !bulkScore.selectedShift" class="text-gray-600">Semua mahasiswa (tanpa filter)</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-3 pt-4">
                            <button 
                                type="submit"
                                :disabled="bulkScore.isSubmitting"
                                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white rounded-lg text-sm font-medium transition">
                                <span x-show="!bulkScore.isSubmitting">Buat Pengajuan Massal</span>
                                <span x-show="bulkScore.isSubmitting">Memproses...</span>
                            </button>
                            <button 
                                type="button"
                                @click="resetBulkScoreForm()"
                                class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition">
                                Atur Ulang
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Settings Page -->
            <div x-show="activeMenu === 'Settings'" x-data="{ settingsTab: 'profile' }" style="display: none;">
                <div class="mb-4 sm:mb-6">
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 mb-2">Pengaturan</h1>
                    <p class="text-sm sm:text-base text-gray-600">Konfigurasikan pengaturan sistem S-Core dan kelola akun pengguna</p>
                </div>

                <!-- Tab Navigation -->
                <div class="mb-4 sm:mb-6 border-b border-gray-200">
                    <nav class="flex gap-2 sm:gap-4 overflow-x-auto">
                        <button @click="settingsTab = 'profile'" :class="settingsTab === 'profile' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-2 px-1 border-b-2 font-medium text-xs sm:text-sm transition-colors flex items-center gap-1.5 sm:gap-2 whitespace-nowrap">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="hidden sm:inline">Profil & Kata Sandi</span>
                            <span class="sm:hidden">Profil</span>
                        </button>
                        <button @click="settingsTab = 'students'" :class="settingsTab === 'students' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-2 px-1 border-b-2 font-medium text-xs sm:text-sm transition-colors flex items-center gap-1.5 sm:gap-2 whitespace-nowrap">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span class="hidden sm:inline">Akun Mahasiswa</span>
                            <span class="sm:hidden">Mahasiswa</span>
                        </button>
                        <button @click="settingsTab = 'admins'" :class="settingsTab === 'admins' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-2 px-1 border-b-2 font-medium text-xs sm:text-sm transition-colors flex items-center gap-1.5 sm:gap-2 whitespace-nowrap">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span class="hidden sm:inline">Akun Admin</span>
                            <span class="sm:hidden">Admin</span>
                        </button>
                        <button @click="settingsTab = 'categories'" :class="settingsTab === 'categories' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-2 px-1 border-b-2 font-medium text-xs sm:text-sm transition-colors flex items-center gap-1.5 sm:gap-2 whitespace-nowrap">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            Manajemen Kategori
                        </button>
                        <button @click="settingsTab = 'system'" :class="settingsTab === 'system' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-2 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2 whitespace-nowrap">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Info Sistem
                        </button>
                    </nav>
                </div>

                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-green-800 font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-red-800 font-medium">Harap perbaiki kesalahan berikut:</p>
                        </div>
                        <ul class="text-red-700 text-sm ml-7 list-disc">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Profile & Password Tab -->
                <div x-show="settingsTab === 'profile'" class="space-y-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Informasi Profil Admin
                        </h3>

                        <div class="space-y-4 mb-8">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                                <div class="bg-gray-50 border rounded-lg px-4 py-3 text-sm text-gray-700">{{ Auth::user()->name }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <div class="bg-gray-50 border rounded-lg px-4 py-3 text-sm text-gray-700">{{ Auth::user()->email }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Peran</label>
                                <div class="bg-gray-50 border rounded-lg px-4 py-3 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        Admin
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="border-t pt-6">
                            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                                Ubah Kata Sandi
                            </h3>

                            <form @submit.prevent="updatePassword" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Kata Sandi Saat Ini <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input :type="showCurrentPassword ? 'text' : 'password'" x-model="passwordData.currentPassword" class="w-full border rounded-lg px-4 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan kata sandi saat ini">
                                        <button type="button" @click="showCurrentPassword = !showCurrentPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                            <svg x-show="!showCurrentPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg x-show="showCurrentPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Kata Sandi Baru <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input :type="showNewPassword ? 'text' : 'password'" x-model="passwordData.newPassword" class="w-full border rounded-lg px-4 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan kata sandi baru (min 8 karakter)">
                                        <button type="button" @click="showNewPassword = !showNewPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                            <svg x-show="!showNewPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg x-show="showNewPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Kata Sandi Baru <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input :type="showConfirmPassword ? 'text' : 'password'" x-model="passwordData.confirmPassword" class="w-full border rounded-lg px-4 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Konfirmasi kata sandi baru">
                                        <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                            <svg x-show="!showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg x-show="showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="flex justify-end gap-3 pt-2">
                                    <button type="button" @click="passwordData = {currentPassword: '', newPassword: '', confirmPassword: ''}" class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm font-medium transition-colors">
                                        Hapus
                                    </button>
                                    <button type="submit" :disabled="isSubmitting" class="px-6 py-2.5 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-400 text-white rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span x-text="isSubmitting ? 'Memperbarui...' : 'Perbarui Kata Sandi'"></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Student Accounts Tab -->
                <div x-show="settingsTab === 'students'" class="space-y-6">
                    <!-- CSV Upload Form -->
                    <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span class="break-words">Unggah Massal Akun Mahasiswa (CSV)</span>
                        </h3>

                        <div class="mb-4 bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 mb-2">
                                <p class="text-xs sm:text-sm text-blue-800"><strong>Persyaratan Format CSV:</strong></p>
                                <a href="/sample_students.csv" download class="text-xs bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded flex items-center justify-center gap-1 whitespace-nowrap w-full sm:w-auto">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Unduh Sampel
                                </a>
                            </div>
                            <p class="text-xs sm:text-sm text-blue-700 mb-2">File CSV harus memiliki kolom berikut (berurutan):</p>
                            <div class="bg-blue-100 rounded p-2 overflow-x-auto mb-2">
                                <code class="text-xs whitespace-nowrap">Nama,Email,Password,student_id,major,batch_year,OrKeSS (WAJIB),Retreat (WAJIB),Kegiatan Ilmiah dan Penalaran,"Performance, Pengembangan, dan Perlombaan",Kepengurusan Organisasi/Kepanitiaan,Kegiatan Sosial</code>
                            </div>
                            <p class="text-xs text-blue-600 mb-1"><strong>Contoh:</strong></p>
                            <div class="bg-blue-100 rounded p-2 overflow-x-auto">
                                <code class="text-xs whitespace-nowrap">John Doe,john.doe@itbss.ac.id,password123,2021001,STI,2021,3,1,5,2,4,3</code>
                            </div>
                            <div class="mt-3 pt-3 border-t border-blue-200">
                                <p class="text-xs text-blue-600"><strong>Catatan:</strong></p>
                                <ul class="text-xs text-blue-600 list-disc ml-4 mt-1 space-y-0.5">
                                    <li>Baris header bersifat opsional dan akan dideteksi otomatis</li>
                                    <li>6 kolom pertama (Nama - batch_year) bersifat <strong>wajib</strong></li>
                                    <li>Kolom nilai (7-12) bersifat <strong>opsional</strong>; gunakan 0 atau biarkan kosong untuk melewati</li>
                                    <li>Jurusan harus salah satu dari: STI, BD, atau KWU</li>
                                    <li>Email harus unik dan menggunakan domain @itbss.ac.id</li>
                                    <li>Nilai OrKeSS & Retreat langsung masuk ke subkategori masing-masing</li>
                                    <li>Nilai lainnya masuk ke subkategori "Migrasi" pada setiap kategori</li>
                                </ul>
                            </div>
                        </div>

                        <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Pilih File CSV <span class="text-red-500">*</span></label>
                                <input type="file" name="csv_file" accept=".csv" required class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="w-full sm:w-auto bg-green-500 hover:bg-green-600 text-white px-4 sm:px-6 py-2 rounded-lg text-xs sm:text-sm font-medium flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="whitespace-nowrap">Unggah & Impor Mahasiswa</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Manual Add Student Form -->
                    <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            <span class="break-words">Tambah Akun Mahasiswa Satu per Satu</span>
                        </h3>

                        <form action="{{ route('students.store') }}" method="POST" class="space-y-4">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Name -->
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Nama <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan nama lengkap">
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" required class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan alamat email">
                                </div>

                                <!-- Password -->
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Kata Sandi <span class="text-red-500">*</span></label>
                                    <input type="password" name="password" required class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan kata sandi (min 6 karakter)">
                                </div>

                                <!-- Student ID -->
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">NIM <span class="text-red-500">*</span></label>
                                    <input type="text" name="student_id" required class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan NIM">
                                </div>

                                <!-- Major -->
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Jurusan <span class="text-red-500">*</span></label>
                                    <select name="major" required class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Pilih Jurusan</option>
                                        <option value="STI">STI</option>
                                        <option value="BD">BD</option>
                                        <option value="KWU">KWU</option>
                                    </select>
                                </div>

                                <!-- Batch Year -->
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Tahun Angkatan <span class="text-red-500">*</span></label>
                                    <input type="number" name="batch_year" required class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="contoh: 2022">
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end pt-4">
                                <button type="submit" class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 text-white px-4 sm:px-6 py-2 rounded-lg text-xs sm:text-sm font-medium flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    <span class="whitespace-nowrap">Tambah Mahasiswa</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Admin Accounts Tab -->
                <div x-show="settingsTab === 'admins'" class="space-y-6" x-data="{
                    adminName: '',
                    adminEmail: '',
                    adminPassword: '',
                    adminPasswordConfirm: '',
                    passwordMatch: true,
                    passwordStrength: '',
                    checkPasswordMatch() {
                        this.passwordMatch = this.adminPassword === this.adminPasswordConfirm || this.adminPasswordConfirm === '';
                    },
                    checkPasswordStrength() {
                        const pwd = this.adminPassword;
                        if (pwd.length === 0) {
                            this.passwordStrength = '';
                        } else if (pwd.length < 6) {
                            this.passwordStrength = 'weak';
                        } else if (pwd.length < 10) {
                            this.passwordStrength = 'medium';
                        } else {
                            this.passwordStrength = 'strong';
                        }
                    }
                }">
                    <!-- Add Admin Form -->
                    <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            <span class="break-words">Tambah Akun Admin Baru</span>
                        </h3>

                        <div class="mb-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3 sm:p-4">
                            <p class="text-xs sm:text-sm text-yellow-800"><strong>Penting:</strong> Akun admin memiliki akses penuh ke sistem. Pastikan Anda mempercayai orang tersebut sebelum membuat akun admin.</p>
                        </div>

                        <form action="{{ route('admins.store') }}" method="POST" class="space-y-4">
                            @csrf

                            <div class="grid grid-cols-1 gap-4">
                                <!-- Name -->
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" x-model="adminName" required class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Masukkan nama lengkap admin">
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Alamat Email <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" x-model="adminEmail" required class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Masukkan alamat email">
                                </div>

                                <!-- Password -->
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Kata Sandi <span class="text-red-500">*</span></label>
                                    <input type="password" name="password" x-model="adminPassword" @input="checkPasswordStrength(); checkPasswordMatch()" required minlength="6" class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Masukkan kata sandi (minimal 6 karakter)">
                                    
                                    <!-- Password Strength Indicator -->
                                    <div x-show="adminPassword.length > 0" class="mt-2">
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                                <div class="h-full transition-all" :class="{
                                                    'bg-red-500 w-1/3': passwordStrength === 'weak',
                                                    'bg-yellow-500 w-2/3': passwordStrength === 'medium',
                                                    'bg-green-500 w-full': passwordStrength === 'strong'
                                                }"></div>
                                            </div>
                                            <span class="text-xs font-medium" :class="{
                                                'text-red-600': passwordStrength === 'weak',
                                                'text-yellow-600': passwordStrength === 'medium',
                                                'text-green-600': passwordStrength === 'strong'
                                            }" x-text="passwordStrength === 'weak' ? 'Lemah' : passwordStrength === 'medium' ? 'Sedang' : passwordStrength === 'strong' ? 'Kuat' : ''"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Konfirmasi Kata Sandi <span class="text-red-500">*</span></label>
                                    <input type="password" name="password_confirmation" x-model="adminPasswordConfirm" @input="checkPasswordMatch()" required class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" :class="!passwordMatch ? 'border-red-500' : ''" placeholder="Masukkan ulang kata sandi">
                                    
                                    <!-- Password Match Indicator -->
                                    <div x-show="adminPasswordConfirm.length > 0">
                                        <p x-show="passwordMatch" class="text-xs text-green-600 mt-1 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Kata sandi cocok
                                        </p>
                                        <p x-show="!passwordMatch" class="text-xs text-red-600 mt-1 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Kata sandi tidak cocok
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end pt-4">
                                <button type="submit" :disabled="!passwordMatch || adminPassword.length < 6" :class="!passwordMatch || adminPassword.length < 6 ? 'bg-gray-400 cursor-not-allowed' : 'bg-purple-500 hover:bg-purple-600'" class="w-full sm:w-auto text-white px-4 sm:px-6 py-2 rounded-lg text-xs sm:text-sm font-medium flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    <span class="whitespace-nowrap">Buat Akun Admin</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Category Management Tab -->
                <div x-show="settingsTab === 'categories'" class="space-y-6">
                    <!-- S-Core Settings -->
                    <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4">Pengaturan Kelayakan S-Core</h3>
                        <p class="text-xs sm:text-sm text-gray-600 mb-4 sm:mb-6">Atur persyaratan minimum agar mahasiswa dianggap LULUS</p>
                        
                        <form @submit.prevent="updateScoreSettings()" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                                        Minimum Poin yang Dibutuhkan
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="number" 
                                        x-model.number="scoreSettings.minPoints" 
                                        min="1" 
                                        max="1000"
                                        class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="contoh: 20"
                                        required
                                    />
                                </div>
                                
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                                        Minimum Kategori yang Dibutuhkan
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="number" 
                                        x-model.number="scoreSettings.minCategories" 
                                        min="1" 
                                        max="10"
                                        class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="contoh: 5"
                                        required
                                    />
                                </div>

                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                                        Minimum Poin Perfect
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        x-model.number="scoreSettings.perfectMinPoints"
                                        min="1"
                                        max="1000"
                                        class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="contoh: 40"
                                        required
                                    />
                                </div>
                            </div>

                            <div class="border border-gray-200 rounded-lg p-4 space-y-3">
                                <div class="flex flex-col gap-2">
                                    <p class="text-sm font-semibold text-gray-800">Aturan Batas Tanggal Pengumpulan</p>

                                    <label class="inline-flex w-fit items-center gap-2 text-xs sm:text-sm font-semibold text-rose-700 bg-rose-50 border border-rose-200 rounded-lg px-3 py-2">
                                        <input
                                            type="checkbox"
                                            class="rounded border-gray-300 text-rose-600 focus:ring-rose-500"
                                            x-model="scoreSettings.maintenanceMode"
                                        />
                                        Mode Maintenance Mahasiswa
                                    </label>
                                </div>

                                <label class="flex items-start gap-3 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        :checked="scoreSettings.submissionDateRuleMode === 'rolling_days'"
                                        @change="scoreSettings.submissionDateRuleMode = 'rolling_days'"
                                    />
                                    <div class="flex-1">
                                        <p class="text-xs sm:text-sm font-medium text-gray-700">Pakai batas dari jumlah hari terakhir</p>
                                        <div class="mt-2 flex items-center gap-2">
                                            <input
                                                type="number"
                                                x-model.number="scoreSettings.submissionDateRangeDays"
                                                min="1"
                                                max="3650"
                                                :disabled="scoreSettings.submissionDateRuleMode !== 'rolling_days'"
                                                class="w-32 border border-gray-300 rounded-lg px-3 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
                                                placeholder="30"
                                            />
                                            <span class="text-xs sm:text-sm text-gray-600">hari</span>
                                        </div>
                                    </div>
                                </label>

                                <label class="flex items-start gap-3 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        :checked="scoreSettings.submissionDateRuleMode === 'fixed_start_date'"
                                        @change="scoreSettings.submissionDateRuleMode = 'fixed_start_date'"
                                    />
                                    <div class="flex-1">
                                        <p class="text-xs sm:text-sm font-medium text-gray-700">Pakai batas mulai dari tanggal tertentu</p>
                                        <div class="mt-2">
                                            <input
                                                type="date"
                                                x-model="scoreSettings.submissionStartDate"
                                                :disabled="scoreSettings.submissionDateRuleMode !== 'fixed_start_date'"
                                                class="w-full md:w-64 border border-gray-300 rounded-lg px-3 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
                                            />
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4 mt-4">
                                <p class="text-xs sm:text-sm text-blue-800">
                                    <span class="font-semibold">Info:</span> Mahasiswa akan ditandai LULUS hanya jika memiliki setidaknya 
                                    <span class="font-bold" x-text="scoreSettings.minPoints"></span> points AND completed 
                                    <span class="font-bold" x-text="scoreSettings.minCategories"></span> kategori. Daftar Perfect menggunakan minimum
                                    <span class="font-bold" x-text="scoreSettings.perfectMinPoints"></span> poin.
                                </p>
                                <p class="text-xs sm:text-sm text-blue-800 mt-2">
                                    Batas tanggal pengumpulan:
                                    <span class="font-bold" x-show="scoreSettings.submissionDateRuleMode === 'rolling_days'">
                                        maksimal <span x-text="scoreSettings.submissionDateRangeDays || 30"></span> hari dari hari ini
                                    </span>
                                    <span class="font-bold" x-show="scoreSettings.submissionDateRuleMode === 'fixed_start_date'">
                                        mulai dari <span x-text="scoreSettings.submissionStartDate || '-'" ></span>
                                    </span>
                                </p>
                                <p class="text-xs sm:text-sm mt-2" :class="scoreSettings.maintenanceMode ? 'text-rose-700' : 'text-green-700'">
                                    Maintenance mahasiswa:
                                    <span class="font-bold" x-text="scoreSettings.maintenanceMode ? 'AKTIF (mahasiswa akan melihat halaman maintenance saat login)' : 'NONAKTIF'"></span>
                                </p>
                            </div>

                            <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6">
                                <button 
                                    type="button"
                                    @click="loadScoreSettings()" 
                                    class="w-full sm:w-auto px-4 sm:px-6 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-xs sm:text-sm font-medium transition-colors"
                                >
                                    Atur Ulang
                                </button>
                                <button 
                                    type="submit"
                                    class="w-full sm:w-auto px-4 sm:px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors"
                                >
                                    Simpan Pengaturan
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <span class="break-words">Kelola Kategori</span>
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-600 mb-3 sm:mb-4">Kelola kategori S-Core, ubah nama, tambah/hapus kategori, dan atur nilai poin defaultnya</p>
                        <div class="flex flex-wrap gap-2 mb-3 sm:mb-4">
                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">Total Kategori: <span class="font-bold" x-text="categories.length"></span></span>
                        </div>
                        <button @click="requestCategoryManagement" class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-xs sm:text-sm font-medium flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span class="whitespace-nowrap">Kelola Kategori</span>
                        </button>
                    </div>

                    <!-- Security PIN Management -->
                    <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span class="break-words">PIN Keamanan</span>
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-600 mb-4 sm:mb-6">Ubah PIN keamanan yang diperlukan untuk mengelola kategori. PIN harus terdiri dari 4-6 digit.</p>
                        
                        <form @submit.prevent="updateSecurityPin()" class="space-y-4">
                            <div class="grid grid-cols-1 gap-4">
                                <!-- Current PIN -->
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                                        PIN Saat Ini
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input 
                                            :type="showCurrentPin ? 'text' : 'password'" 
                                            x-model="pinData.currentPin" 
                                            maxlength="6"
                                            pattern="[0-9]{4,6}"
                                            class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 pr-10 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            placeholder="Masukkan PIN saat ini"
                                            required
                                        />
                                        <button 
                                            type="button" 
                                            @click="showCurrentPin = !showCurrentPin" 
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                                        >
                                            <svg x-show="!showCurrentPin" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg x-show="showCurrentPin" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- New PIN -->
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                                        PIN Baru
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input 
                                            :type="showNewPin ? 'text' : 'password'" 
                                            x-model="pinData.newPin" 
                                            maxlength="6"
                                            pattern="[0-9]{4,6}"
                                            class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 pr-10 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            placeholder="Masukkan PIN baru (4-6 digit)"
                                            required
                                        />
                                        <button 
                                            type="button" 
                                            @click="showNewPin = !showNewPin" 
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                                        >
                                            <svg x-show="!showNewPin" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg x-show="showNewPin" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Confirm New PIN -->
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                                        Konfirmasi PIN Baru
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input 
                                            :type="showConfirmPin ? 'text' : 'password'" 
                                            x-model="pinData.confirmPin" 
                                            maxlength="6"
                                            pattern="[0-9]{4,6}"
                                            class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 pr-10 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            placeholder="Masukkan ulang PIN baru"
                                            required
                                        />
                                        <button 
                                            type="button" 
                                            @click="showConfirmPin = !showConfirmPin" 
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                                        >
                                            <svg x-show="!showConfirmPin" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg x-show="showConfirmPin" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 sm:p-4 mt-4">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <div class="text-xs sm:text-sm text-yellow-800">
                                        <p class="font-semibold">Penting:</p>
                                        <p>PIN ini diperlukan untuk mengelola kategori (tambah, edit, hapus). Pastikan Anda mengingat PIN baru Anda.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6">
                                <button 
                                    type="button"
                                    @click="resetPinForm()" 
                                    class="w-full sm:w-auto px-4 sm:px-6 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-xs sm:text-sm font-medium transition-colors"
                                >
                                    Hapus
                                </button>
                                <button 
                                    type="submit"
                                    :disabled="isPinSubmitting"
                                    :class="isPinSubmitting ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-600'"
                                    class="w-full sm:w-auto px-4 sm:px-6 py-2 bg-blue-500 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors flex items-center justify-center gap-2"
                                >
                                    <svg x-show="!isPinSubmitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <svg x-show="isPinSubmitting" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24" style="display: none;">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="whitespace-nowrap" x-text="isPinSubmitting ? 'Memperbarui...' : 'Perbarui PIN'"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- System Info Tab -->
                <div x-show="settingsTab === 'system'" class="space-y-6">
                    <!-- System Information -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4">Informasi Sistem</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Versi:</span>
                                <span class="font-medium">2.0.0</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Terakhir Diperbarui:</span>
                                <span class="font-medium">27 Maret 2026</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Management moved to Settings -->
            <!-- This section has been integrated into Settings > Student Accounts and Settings > Admin Accounts -->
            <div x-show="false" style="display: none;">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">User Management</h1>
                    <p class="text-gray-600">Manage student and admin accounts</p>
                </div>

                <!-- Tab Navigation -->
                <div class="mb-6 border-b border-gray-200">
                    <nav class="flex gap-4">
                        <button @click="userTab = 'students'" :class="userTab === 'students' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-2 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Student Accounts
                        </button>
                        <button @click="userTab = 'admins'" :class="userTab === 'admins' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-2 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            Admin Accounts
                        </button>
                    </nav>
                </div>

                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-green-800 font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-red-800 font-medium">Please fix the following errors:</p>
                        </div>
                        <ul class="text-red-700 text-sm ml-7 list-disc">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Student Accounts Tab -->
                <div x-show="userTab === 'students'" class="space-y-6">
                    <!-- CSV Upload Form -->
                    <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span class="break-words">Unggah Massal Akun Mahasiswa (CSV)</span>
                        </h3>

                        <div class="mb-4 bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 mb-2">
                                <p class="text-xs sm:text-sm text-blue-800"><strong>CSV Format Requirements:</strong></p>
                                <a href="/sample_students.csv" download class="text-xs bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded flex items-center justify-center gap-1 whitespace-nowrap w-full sm:w-auto">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Download Sample
                                </a>
                            </div>
                            <p class="text-xs sm:text-sm text-blue-700 mb-2">The CSV file must have the following columns (in order):</p>
                            <div class="bg-blue-100 rounded p-2 overflow-x-auto mb-2">
                                <code class="text-xs whitespace-nowrap">Nama,Email,Password,student_id,major,batch_year,OrKeSS (WAJIB),Retreat (WAJIB),Kegiatan Ilmiah dan Penalaran,"Performance, Pengembangan, dan Perlombaan",Kepengurusan Organisasi/Kepanitiaan,Kegiatan Sosial</code>
                            </div>
                            <p class="text-xs text-blue-600 mb-1"><strong>Example:</strong></p>
                            <div class="bg-blue-100 rounded p-2 overflow-x-auto">
                                <code class="text-xs whitespace-nowrap">John Doe,john.doe@itbss.ac.id,password123,2021001,STI,2021,3,1,5,2,4,3</code>
                            </div>
                            <div class="mt-3 pt-3 border-t border-blue-200">
                                <p class="text-xs text-blue-600"><strong>Notes:</strong></p>
                                <ul class="text-xs text-blue-600 list-disc ml-4 mt-1 space-y-0.5">
                                    <li>Header row is optional (auto-detected)</li>
                                    <li>First 6 columns (Nama - batch_year) are <strong>required</strong></li>
                                    <li>Score columns (7-12) are <strong>optional</strong> — use 0 or leave empty to skip</li>
                                    <li>Major must be: STI, BD, or KWU</li>
                                    <li>Email must be unique and use @itbss.ac.id domain</li>
                                    <li>OrKeSS & Retreat scores go directly to their subcategory</li>
                                    <li>Other scores go to "Migrasi" subcategory of each category</li>
                                </ul>
                            </div>
                        </div>

                        <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Select CSV File <span class="text-red-500">*</span></label>
                                <input type="file" name="csv_file" accept=".csv" required class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="w-full sm:w-auto bg-green-500 hover:bg-green-600 text-white px-4 sm:px-6 py-2 rounded-lg text-xs sm:text-sm font-medium flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="whitespace-nowrap">Upload & Import Students</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Manual Add Student Form -->
                    <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            <span class="break-words">Add Single Student Account</span>
                        </h3>

                        <form action="{{ route('students.store') }}" method="POST" class="space-y-4">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Name -->
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter full name">
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" required class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter email address">
                                </div>

                                <!-- Password -->
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                                    <input type="password" name="password" required class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter password (min 6 characters)">
                                </div>

                                <!-- Student ID -->
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Student ID <span class="text-red-500">*</span></label>
                                    <input type="text" name="student_id" required class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter student ID">
                                </div>

                                <!-- Major -->
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Major <span class="text-red-500">*</span></label>
                                    <select name="major" required class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Major</option>
                                        <option value="STI">STI</option>
                                        <option value="BD">BD</option>
                                        <option value="KWU">KWU</option>
                                    </select>
                                </div>

                                <!-- Batch Year -->
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Batch Year <span class="text-red-500">*</span></label>
                                    <input type="number" name="batch_year" required class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., 2022">
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end pt-4">
                                <button type="submit" class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 text-white px-4 sm:px-6 py-2 rounded-lg text-xs sm:text-sm font-medium flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    <span class="whitespace-nowrap">Add Student</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Admin Accounts Tab -->
                <div x-show="userTab === 'admins'" class="space-y-6" x-data="{
                    adminName: '',
                    adminEmail: '',
                    adminPassword: '',
                    adminPasswordConfirm: '',
                    passwordMatch: true,
                    passwordStrength: '',
                    checkPasswordMatch() {
                        this.passwordMatch = this.adminPassword === this.adminPasswordConfirm || this.adminPasswordConfirm === '';
                    },
                    checkPasswordStrength() {
                        const pwd = this.adminPassword;
                        if (pwd.length === 0) {
                            this.passwordStrength = '';
                        } else if (pwd.length < 6) {
                            this.passwordStrength = 'weak';
                        } else if (pwd.length < 10) {
                            this.passwordStrength = 'medium';
                        } else {
                            this.passwordStrength = 'strong';
                        }
                    }
                }">
                    <!-- Add Admin Form -->
                    <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            <span class="break-words">Tambah Akun Admin Baru</span>
                        </h3>

                        <div class="mb-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3 sm:p-4">
                            <p class="text-xs sm:text-sm text-yellow-800"><strong>⚠️ Important:</strong> Admin accounts have full access to the system. Please ensure you trust the person before creating an admin account.</p>
                        </div>

                        <form action="{{ route('admins.store') }}" method="POST" class="space-y-4">
                            @csrf

                            <div class="grid grid-cols-1 gap-4">
                                <!-- Name -->
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" x-model="adminName" required class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Enter admin full name">
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" x-model="adminEmail" required class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Enter email address">
                                </div>

                                <!-- Password -->
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                                    <input type="password" name="password" x-model="adminPassword" @input="checkPasswordStrength(); checkPasswordMatch()" required minlength="6" class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Enter password (minimum 6 characters)">
                                    
                                    <!-- Password Strength Indicator -->
                                    <div x-show="adminPassword.length > 0" class="mt-2">
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                                <div class="h-full transition-all" :class="{
                                                    'bg-red-500 w-1/3': passwordStrength === 'weak',
                                                    'bg-yellow-500 w-2/3': passwordStrength === 'medium',
                                                    'bg-green-500 w-full': passwordStrength === 'strong'
                                                }"></div>
                                            </div>
                                            <span class="text-xs font-medium" :class="{
                                                'text-red-600': passwordStrength === 'weak',
                                                'text-yellow-600': passwordStrength === 'medium',
                                                'text-green-600': passwordStrength === 'strong'
                                            }" x-text="passwordStrength === 'weak' ? 'Weak' : passwordStrength === 'medium' ? 'Medium' : passwordStrength === 'strong' ? 'Strong' : ''"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Confirm Password <span class="text-red-500">*</span></label>
                                    <input type="password" name="password_confirmation" x-model="adminPasswordConfirm" @input="checkPasswordMatch()" required class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" :class="!passwordMatch ? 'border-red-500' : ''" placeholder="Re-enter password">
                                    
                                    <!-- Password Match Indicator -->
                                    <div x-show="adminPasswordConfirm.length > 0">
                                        <p x-show="passwordMatch" class="text-xs text-green-600 mt-1 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Passwords match
                                        </p>
                                        <p x-show="!passwordMatch" class="text-xs text-red-600 mt-1 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Passwords do not match
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end pt-4">
                                <button type="submit" :disabled="!passwordMatch || adminPassword.length < 6" :class="!passwordMatch || adminPassword.length < 6 ? 'bg-gray-400 cursor-not-allowed' : 'bg-purple-500 hover:bg-purple-600'" class="w-full sm:w-auto text-white px-4 sm:px-6 py-2 rounded-lg text-xs sm:text-sm font-medium flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    <span class="whitespace-nowrap">Create Admin Account</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Help Page -->
            <div x-show="activeMenu === 'Help'" style="display: none;">
                <div class="mb-4 sm:mb-6">
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 mb-2">Bantuan & Dokumentasi</h1>
                    <p class="text-sm sm:text-base text-gray-600">Dapatkan bantuan untuk menggunakan sistem S-Core</p>
                </div>

                <div class="space-y-4 sm:space-y-6">
                    <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Pertanyaan yang Sering Diajukan
                        </h3>
                        <div class="space-y-3 sm:space-y-4">
                            <div class="border-b pb-3 sm:pb-4">
                                <h4 class="font-semibold text-sm sm:text-base text-gray-800 mb-2 flex items-start gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold flex-shrink-0">1</span>
                                    <span>Bagaimana cara meninjau pengajuan?</span>
                                </h4>
                                <p class="text-xs sm:text-sm text-gray-600 ml-7 mb-2">Klik tombol "Tinjau" pada halaman Tinjauan Pengajuan untuk melihat detail pengajuan. Anda dapat menetapkan kategori yang benar, meninjau sertifikat/bukti langsung dari Google Drive, lalu menyetujui atau menolak pengajuan tersebut. Saat menyetujui, pastikan Anda memilih kategori yang sesuai dan memeriksa poin yang disarankan.</p>
                                <div class="mt-2 ml-7 bg-green-50 border border-green-200 rounded p-2">
                                    <p class="text-xs text-green-800"><strong>Integrasi Google Drive:</strong> Semua sertifikat disimpan di Google Drive. Anda dapat meninjau PDF langsung di browser tanpa mengunduhnya.</p>
                                </div>
                            </div>

                            <div class="border-b pb-4">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">2</span>
                                    Apa aturan pembagian poin?
                                </h4>
                                <p class="text-sm text-gray-600 ml-7">Poin disarankan secara otomatis berdasarkan kategori kegiatan. Setiap kategori memiliki nilai poin default yang dapat diatur di Pengaturan > Manajemen Kategori. Sistem akan menampilkan saran poin ini saat proses peninjauan, tetapi Anda tetap dapat memeriksa dan menyesuaikannya bila diperlukan.</p>
                            </div>

                            <div class="border-b pb-4">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">3</span>
                                    Bagaimana cara mengelola kategori dan poin?
                                </h4>
                                <p class="text-sm text-gray-600 ml-7 mb-2">Buka Pengaturan > Manajemen Kategori lalu klik "Kelola Kategori". Anda dapat:</p>
                                <ul class="text-sm text-gray-600 ml-7 space-y-1 list-disc list-inside">
                                    <li><strong>Menambah kategori baru:</strong> Masukkan nama kategori dan poin default, lalu klik "Tambah Kategori"</li>
                                    <li><strong>Mengedit/Mengganti nama kategori:</strong> Klik ikon edit, ubah nama atau poin, lalu klik "Simpan"</li>
                                    <li><strong>Memperbarui poin default:</strong> Edit kategori lalu ubah nilai poinnya</li>
                                    <li><strong>Menghapus kategori:</strong> Klik ikon hapus. Sistem akan memberi peringatan jika kategori sedang digunakan</li>
                                </ul>
                                <div class="mt-2 ml-7 bg-yellow-50 border border-yellow-200 rounded p-2">
                                    <p class="text-xs text-yellow-800"><strong>Penting:</strong> Berhati-hatilah saat menghapus kategori yang sedang digunakan pada pengajuan. Sistem akan menampilkan peringatan beserta jumlah pengajuan yang terdampak.</p>
                                </div>
                            </div>

                            <div class="border-b pb-4">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">4</span>
                                    Bagaimana cara mengubah kategori pengajuan mahasiswa?
                                </h4>
                                <p class="text-sm text-gray-600 ml-7">Saat meninjau pengajuan, gunakan dropdown "Tetapkan Kategori" untuk memilih kategori yang benar. Jika Anda mengubah kategori dari pilihan awal mahasiswa, akan muncul kolom opsional untuk memberikan alasan perubahan. Ini membantu mahasiswa memahami mengapa pengajuannya dikategorikan ulang.</p>
                            </div>

                            <div class="border-b pb-4">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">5</span>
                                    Alasan penolakan apa saja yang tersedia?
                                </h4>
                                <p class="text-sm text-gray-600 ml-7 mb-2">Saat menolak pengajuan, Anda dapat memilih alasan yang sudah disediakan atau menulis pesan khusus:</p>
                                <ul class="text-sm text-gray-600 ml-7 space-y-1 list-disc list-inside">
                                    <li>Sertifikat tidak sesuai dengan deskripsi kegiatan</li>
                                    <li>Bukti tidak jelas atau tidak lengkap</li>
                                    <li>Tanggal kegiatan melewati batas waktu yang diizinkan</li>
                                    <li>Kategori yang dipilih salah</li>
                                    <li>Pengajuan duplikat</li>
                                    <li>Tidak memenuhi persyaratan S-Core</li>
                                    <li>Lainnya (alasan kustom) - Pilih ini untuk menulis penjelasan Anda sendiri secara rinci</li>
                                </ul>
                            </div>

                            <div class="border-b pb-4">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">6</span>
                                    Bagaimana cara melihat informasi mahasiswa secara rinci?
                                </h4>
                                <p class="text-sm text-gray-600 ml-7">Pada halaman Mahasiswa, arahkan kursor ke total poin mahasiswa untuk melihat rincian lengkap yang mencakup:</p>
                                <ul class="text-sm text-gray-600 ml-7 space-y-1 list-disc list-inside">
                                    <li>Total poin dan progres terhadap syarat 1000 poin</li>
                                    <li>Status lulus/belum lulus</li>
                                    <li>Rincian poin per kategori</li>
                                    <li>Total pengajuan, jumlah disetujui, dan pengajuan tertunda</li>
                                </ul>
                                <p class="text-sm text-gray-600 ml-7 mt-2">Anda juga dapat memfilter mahasiswa berdasarkan angkatan dan status lulus/gagal menggunakan filter di bagian atas.</p>
                            </div>

                            <div class="border-b pb-4">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">7</span>
                                    Bagaimana cara mengekspor statistik dan laporan?
                                </h4>
                                <p class="text-sm text-gray-600 ml-7">Buka halaman Statistik untuk melihat metrik sistem secara keseluruhan, atau kunjungi halaman Mahasiswa dan gunakan tombol "Ekspor Laporan" untuk mengunduh laporan lengkap. Hasil ekspor akan mencakup informasi mahasiswa, total poin, status lulus/gagal, dan pengajuan tertunda. Anda dapat memfilter data terlebih dahulu sebelum mengekspor.</p>
                            </div>

                            <div class="border-b pb-4">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">8</span>
                                    Bagaimana cara menambahkan pengguna baru ke sistem?
                                </h4>
                                <p class="text-sm text-gray-600 ml-7">Buka menu pengelolaan pengguna lalu isi formulir penambahan pengguna baru. Masukkan informasi yang diperlukan seperti nama, email, kata sandi, peran, dan detail mahasiswa bila diperlukan. Pengguna akan otomatis ditambahkan ke database saat Anda menekan tombol tambah. Kata sandi akan di-hash secara aman sebelum disimpan.</p>
                            </div>

                            <div class="pb-4">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">9</span>
                                    Bagaimana cara menggunakan fitur Nilai Massal?
                                </h4>
                                <p class="text-sm text-gray-600 ml-7 mb-2">Fitur Nilai Massal memungkinkan Anda memberikan poin S-Core kepada banyak mahasiswa sekaligus untuk kegiatan kelompok. Cara menggunakannya:</p>
                                <ol class="text-sm text-gray-600 ml-7 space-y-1 list-decimal list-inside">
                                    <li>Buka menu <strong>Nilai Massal</strong> pada sidebar</li>
                                    <li>Pilih target mahasiswa menggunakan filter:
                                        <ul class="ml-5 mt-1 space-y-1 list-disc list-inside">
                                            <li><strong>Jurusan:</strong> Pilih program studi yang dituju</li>
                                            <li><strong>Angkatan:</strong> Pilih tahun angkatan</li>
                                            <li><strong>Shift:</strong> Kelas pagi atau sore</li>
                                        </ul>
                                    </li>
                                    <li>Pilih <strong>Kategori Utama</strong> dan <strong>Subkategori</strong> untuk kegiatan tersebut</li>
                                    <li>Isi data berikut:
                                        <ul class="ml-5 mt-1 space-y-1 list-disc list-inside">
                                            <li><strong>Judul Kegiatan:</strong> Nama kegiatan kelompok</li>
                                            <li><strong>Deskripsi:</strong> Rincian kegiatan</li>
                                            <li><strong>Tanggal Kegiatan:</strong> Waktu kegiatan berlangsung</li>
                                            <li><strong>Sertifikat/Bukti (Opsional):</strong> Unggah sertifikat bersama jika tersedia</li>
                                        </ul>
                                    </li>
                                    <li>Klik <strong>Buat Pengajuan Massal</strong></li>
                                </ol>
                                <div class="mt-2 ml-7 bg-blue-50 border border-blue-200 rounded p-2">
                                    <p class="text-xs text-blue-800 mb-2"><strong>Fitur Utama:</strong></p>
                                    <ul class="text-xs text-blue-800 space-y-1 list-disc list-inside">
                                        <li>Pengajuan akan <strong>disetujui otomatis</strong> dan langsung terlihat oleh mahasiswa</li>
                                        <li>Semua mahasiswa menerima poin yang sama berdasarkan kategori yang dipilih</li>
                                        <li>Cocok untuk acara kelompok, seminar, workshop, atau kompetisi</li>
                                        <li>Jika sertifikat diunggah, semua mahasiswa akan menggunakan file sertifikat yang sama</li>
                                        <li>Sistem menampilkan pratinjau jumlah mahasiswa yang akan menerima nilai sebelum pengiriman</li>
                                    </ul>
                                </div>
                                <div class="mt-2 ml-7 bg-yellow-50 border border-yellow-200 rounded p-2">
                                    <p class="text-xs text-yellow-800"><strong>Penting:</strong> Periksa kembali filter Anda sebelum mengirim, karena nilai massal langsung disetujui dan tidak mudah dibatalkan. Pastikan mahasiswa yang dipilih benar-benar mengikuti kegiatan tersebut.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4">Hubungi Dukungan</h3>
                        <p class="text-sm text-gray-600 mb-4">Butuh bantuan tambahan? Hubungi tim dukungan kami.</p>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>support@itbss.ac.id</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span>+62 21 1234 5678</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function adminReviewData() {
    return {
        activeMenu: 'Review Submissions',
        isSidebarOpen: false,
        
        // --- DATA DARI CONTROLLER ---
        submissions: @json($submissions),
        stats: @json($stats),
        
        // DATA KATEGORI (Data Mentah)
        categories: @json($categories), 
        
        // DATA GROUP (Kita kosongkan dulu, nanti diisi otomatis oleh init())
        categoryGroups: [], 
        
        students: @json($students),     
        studentYears: @json($availableStudentYears),
        studentsFilters: @json($studentsFilters ?? []),
        studentsPagination: @json($studentsPagination),
        studentStats: @json($studentStats), 
        
        // Bulk Score Data
        bulkScore: {
            selectedMajor: '',
            yearMode: 'all', // 'all' or 'specific'
            selectedYear: '',
            selectedShift: '',
            mainCategory: '',
            subcategory: '',
            activityTitle: '',
            description: '',
            activityDate: '',
            isSubmitting: false
        },
        
        // --- VARIABLE UI ---
        showLogoutModal: false,
        showDetailModal: false,
        showRejectModal: false,
        showCategoryModal: false,
        showPinModal: false,
        showEditConfirmModal: false,
        showDeleteConfirmModal: false,
        showApproveModal: false,
        showAlertModal: false,
        showQueueToast: false,
        queueToastMessage: '',
        showMobileDetailModal: false,
        mobileDetailSubmission: null,
        showMobileStudentDetailModal: false,
        mobileStudentDetail: null,
        
        alertType: 'info',
        alertTitle: '',
        alertMessage: '',
        alertHasCancel: false,
        alertCallback: null,
        
        approveModalMainCategory: '',
        approveModalSubcategory: '',
        approveModalPoints: 0,
        
        dragActiveBulk: false,
        bulkFileName: '',
        
        pinInput: '',
        pinError: false,
        isPinVerified: false,
        
        editingCategory: null,
        deletingCategory: null,
        selectedSubmission: null,
        
        // Delete Confirmation Modal
        showDeleteSubcategoryModal: false,
        showDeleteCategoryModal: false,
        deleteTargetCategory: null,
        deleteTargetSubcategory: null,
        deleteCategoryIndex: null,
        deleteSubcategoryIndex: null,
        
        // Restore Confirmation Modal
        showRestoreCategoryModal: false,
        showRestoreSubcategoryModal: false,
        restoreTargetCategory: null,
        restoreTargetSubcategory: null,
        restoreCategoryIndex: null,
        restoreSubcategoryIndex: null,
        
        rejectReason: '',
        rejectReasonType: '',
        categoryChangeReason: '',
        isCategoryCorrectionEnabled: false,
        categoryChanged: false,
        
        assignedMainCategory: '',
        assignedSubcategory: '',
        assignedAvailableSubcategories: [],
        
        newMainCategory: '',
        newMainCategoryIsMandatory: false,
        newMainCategoryMaxPerSemester: 'none',
        newCategory: { mainCategoryIndex: '', name: '', points: '', description: '', isMandatory: false },
        showInactiveCategories: false,
        
        // --- VARIABLE FILTER ---
        statusFilter: 'Waiting', 
        categoryFilter: '',      
        submissionYearFilter: '',
        searchQuery: '',         
        submissionsPagination: {
            currentPage: 1,
            perPage: 25,
        },
        
        // Filter Student Management
        studentSearchQuery: @json($studentsFilters['studentSearch'] ?? ''),
        majorFilter: @json($studentsFilters['majorFilter'] ?? ''),
        yearFilterMode: @json($studentsFilters['yearFilterMode'] ?? 'all'), // 'all' or 'specific'
        yearFilter: @json($studentsFilters['yearFilter'] ?? ''),
        statusPassFilter: '',    
        
        showStudentDetailModal: false, 
        selectedStudent: null,
        studentAcademicStatusDraft: 'active',
        isUpdatingAcademicStatus: false,
        studentDetailSearchQuery: '',
        studentDetailStatusFilter: '',
        studentDetailCategoryFilter: '',
        
        // Selected Students for Deletion
        selectedStudents: [],
        
        // Reset Password Modal
        showResetPasswordModal: false,
        resetPasswordInput: '',
        showResetPasswordVisible: false,
        resetPasswordError: '',
        studentSearchDebounceTimer: null,
        isPromotingSemester: false,
        isDemotingSemester: false,
        bulkAcademicStatusDraft: 'active',
        isUpdatingBulkAcademicStatus: false,

        showAdjustPointsModal: false,
        isAdjustingPoints: false,
        adjustPointForm: {
            submissionId: null,
            title: '',
            currentPoints: 0,
            nextPoints: '',
            reason: ''
        },
        
        // Tabs
        userTab: 'students',
        settingsTab: 'profile',
        
        // Password Change
        passwordData: {
            currentPassword: '',
            newPassword: '',
            confirmPassword: ''
        },
        showCurrentPassword: false,
        showNewPassword: false,
        showConfirmPassword: false,
        isSubmitting: false,
        
        // Security PIN Change
        pinData: {
            currentPin: '',
            newPin: '',
            confirmPin: ''
        },
        showCurrentPin: false,
        showNewPin: false,
        showConfirmPin: false,
        isPinSubmitting: false,
        
        // S-Core Settings
        scoreSettings: {
            minPoints: @json($scoreSettings['minPoints'] ?? 20),
            minCategories: @json($scoreSettings['minCategories'] ?? 5),
            perfectMinPoints: @json($scoreSettings['perfectMinPoints'] ?? 40),
            submissionDateRuleMode: @json($scoreSettings['submissionDateRuleMode'] ?? 'rolling_days'),
            submissionDateRangeDays: @json($scoreSettings['submissionDateRangeDays'] ?? 30),
            submissionStartDate: @json($scoreSettings['submissionStartDate'] ?? null),
            maintenanceMode: @json($scoreSettings['maintenanceMode'] ?? false)
        },

        // ============================================================
        //  FUNGSI INIT (PENTING: JANGAN DIHAPUS)
        //  Fungsi ini berjalan otomatis saat halaman dimuat
        // ============================================================
        async init() {
            const queryParams = new URLSearchParams(window.location.search);
            const menuFromQuery = queryParams.get('menu');
            const validMenus = ['Review Submissions', 'Students', 'Bulk Score', 'Settings', 'Help', 'Statistics'];
            if (menuFromQuery && validMenus.includes(menuFromQuery)) {
                this.activeMenu = menuFromQuery;
            }

            // Normalize selected year type so <select x-model> always binds correctly.
            this.yearFilter = String(this.yearFilter ?? '').trim();
            this.yearFilterMode = (this.yearFilterMode === 'specific') ? 'specific' : 'all';

            // If a year is already present, force specific mode so the select value stays visible.
            if ((this.yearFilter || '').trim() !== '') {
                this.yearFilterMode = 'specific';
            }

            // Clean legacy server-side students query params to avoid stale filter states after refresh.
            const legacyStudentParams = ['students_page', 'students_per_page', 'student_search', 'major_filter', 'year_mode', 'year_filter'];
            let hasLegacyStudentParams = false;
            legacyStudentParams.forEach((key) => {
                if (queryParams.has(key)) {
                    queryParams.delete(key);
                    hasLegacyStudentParams = true;
                }
            });
            if (hasLegacyStudentParams) {
                const nextQuery = queryParams.toString();
                const nextUrl = `${window.location.pathname}${nextQuery ? '?' + nextQuery : ''}`;
                window.history.replaceState({}, '', nextUrl);
            }

            // LOAD CATEGORIES dari API (Real-time)
            await this.loadCategories();
            // LOAD S-CORE SETTINGS
            await this.loadScoreSettings();
        },

        fetchSubmissions() {
            // Force full reload with cache-busting query so data benar-benar fresh
            const url = new URL(window.location.href);
            url.searchParams.set('menu', 'Review Submissions');
            url.searchParams.set('_refresh', Date.now().toString());
            window.location.href = url.toString();
        },

        normalizeCategorySemesterLimitValue(value) {
            if (value === null || typeof value === 'undefined' || value === '' || value === 'none') {
                return 'none';
            }
            return String(value);
        },

        parseCategorySemesterLimitValue(value) {
            if (value === null || typeof value === 'undefined' || value === '' || value === 'none') {
                return null;
            }

            const parsed = parseInt(value, 10);
            return Number.isNaN(parsed) ? null : parsed;
        },

        getCategorySemesterLimitLabel(category) {
            const value = this.normalizeCategorySemesterLimitValue(category.max_submissions_per_semester);
            return value === 'none' ? 'Tidak Ada' : value;
        },

        async loadCategories() {
            try {
                const url = '/api/categories' + (this.showInactiveCategories ? '?include_inactive=1' : '');
                const response = await fetch(url);
                if (!response.ok) throw new Error('Failed to fetch categories');
                
                let fetchedCategories = await response.json();
                
                // Transform dan tambahkan isEditing flag
                fetchedCategories = fetchedCategories.map(cat => ({
                    ...cat,
                    is_mandatory: !!cat.is_mandatory,
                    max_submissions_per_semester: this.normalizeCategorySemesterLimitValue(cat.max_submissions_per_semester),
                    isEditing: false,
                    isSavingLimit: false,
                    subcategories: (cat.subcategories || []).map(sub => ({
                        ...sub,
                        is_mandatory: !!sub.is_mandatory,
                        isEditing: false
                    }))
                }));
                
                // Update categories dan categoryGroups
                this.categories = fetchedCategories;
                this.categoryGroups = fetchedCategories;
                
            } catch (error) {
                console.error('Error loading categories:', error);
                // Fallback: gunakan data awal jika API gagal
                this.categories = this.categories.map(cat => ({
                    ...cat,
                    is_mandatory: !!cat.is_mandatory,
                    max_submissions_per_semester: this.normalizeCategorySemesterLimitValue(cat.max_submissions_per_semester),
                    isEditing: false,
                    isSavingLimit: false,
                    subcategories: (cat.subcategories || []).map(sub => ({
                        ...sub,
                        is_mandatory: !!sub.is_mandatory,
                        isEditing: false
                    }))
                }));
                this.categoryGroups = this.categories;
            }
        },

        // --- S-CORE SETTINGS FUNCTIONS ---
        async loadScoreSettings() {
            try {
                const response = await fetch('/api/settings/score');
                if (!response.ok) throw new Error('Failed to load settings');
                
                const data = await response.json();
                this.scoreSettings.minPoints = data.minPoints;
                this.scoreSettings.minCategories = data.minCategories;
                this.scoreSettings.perfectMinPoints = data.perfectMinPoints ?? this.scoreSettings.perfectMinPoints;
                this.scoreSettings.submissionDateRuleMode = data.submissionDateRuleMode || 'rolling_days';
                this.scoreSettings.submissionDateRangeDays = data.submissionDateRangeDays || 30;
                this.scoreSettings.submissionStartDate = data.submissionStartDate || null;
                this.scoreSettings.maintenanceMode = !!data.maintenanceMode;
            } catch (error) {
                console.error('Error loading score settings:', error);
                this.showAlert('error', 'Gagal', 'Tidak dapat memuat pengaturan S-Core');
            }
        },

        async updateScoreSettings() {
            if (!this.scoreSettings.minPoints || !this.scoreSettings.minCategories || !this.scoreSettings.perfectMinPoints) {
                this.showAlert('warning', 'Tidak Lengkap', 'Harap isi semua kolom');
                return;
            }

            if (this.scoreSettings.submissionDateRuleMode === 'rolling_days' && !this.scoreSettings.submissionDateRangeDays) {
                this.showAlert('warning', 'Tidak Lengkap', 'Isi jumlah hari untuk batas tanggal pengumpulan.');
                return;
            }

            if (this.scoreSettings.submissionDateRuleMode === 'fixed_start_date' && !this.scoreSettings.submissionStartDate) {
                this.showAlert('warning', 'Tidak Lengkap', 'Pilih tanggal mulai untuk batas tanggal pengumpulan.');
                return;
            }

            try {
                const response = await fetch('/admin/settings/score', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        minPoints: parseInt(this.scoreSettings.minPoints),
                        minCategories: parseInt(this.scoreSettings.minCategories),
                        perfectMinPoints: parseInt(this.scoreSettings.perfectMinPoints),
                        submissionDateRuleMode: this.scoreSettings.submissionDateRuleMode,
                        submissionDateRangeDays: this.scoreSettings.submissionDateRuleMode === 'rolling_days'
                            ? parseInt(this.scoreSettings.submissionDateRangeDays)
                            : null,
                        submissionStartDate: this.scoreSettings.submissionDateRuleMode === 'fixed_start_date'
                            ? this.scoreSettings.submissionStartDate
                            : null,
                        maintenanceMode: !!this.scoreSettings.maintenanceMode
                    })
                });

                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || 'Gagal memperbarui pengaturan');
                }

                this.showAlert('success', 'Berhasil', 'Pengaturan S-Core berhasil diperbarui. Halaman akan dimuat ulang untuk menerapkan perubahan...');
                
                // Reload page after 1 second so all data is fresh with new settings
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } catch (error) {
                console.error('Error updating score settings:', error);
                this.showAlert('error', 'Gagal', error.message || 'Tidak dapat memperbarui pengaturan');
            }
        },

        // --- PASSWORD CHANGE FUNCTION ---
        async updatePassword() {
            // Validation
            if (!this.passwordData.currentPassword || !this.passwordData.newPassword || !this.passwordData.confirmPassword) {
                this.showAlert('warning', 'Tidak Lengkap', 'Semua kolom wajib diisi');
                return;
            }

            if (this.passwordData.newPassword.length < 8) {
                this.showAlert('warning', 'Kata Sandi Tidak Valid', 'Kata sandi baru minimal harus 8 karakter');
                return;
            }

            if (this.passwordData.newPassword !== this.passwordData.confirmPassword) {
                this.showAlert('error', 'Kata Sandi Tidak Cocok', 'Kata sandi baru dan konfirmasi kata sandi tidak cocok');
                return;
            }

            this.isSubmitting = true;

            try {
                const response = await fetch('{{ route("profile.update-password") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        current_password: this.passwordData.currentPassword,
                        new_password: this.passwordData.newPassword,
                        new_password_confirmation: this.passwordData.confirmPassword
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Gagal memperbarui kata sandi');
                }

                this.showAlert('success', 'Berhasil', 'Kata sandi berhasil diperbarui');
                
                // Reset form
                this.passwordData = {
                    currentPassword: '',
                    newPassword: '',
                    confirmPassword: ''
                };
                this.showCurrentPassword = false;
                this.showNewPassword = false;
                this.showConfirmPassword = false;

            } catch (error) {
                console.error('Error updating password:', error);
                this.showAlert('error', 'Gagal', error.message || 'Tidak dapat memperbarui kata sandi. Periksa kata sandi saat ini.');
            } finally {
                this.isSubmitting = false;
            }
        },

        // --- SECURITY PIN CHANGE FUNCTIONS ---
        async updateSecurityPin() {
            const currentPin = (this.pinData.currentPin || '').trim();
            const newPin = (this.pinData.newPin || '').trim();
            const confirmPin = (this.pinData.confirmPin || '').trim();

            // Validation
            if (!currentPin || !newPin || !confirmPin) {
                this.showAlert('warning', 'Tidak Lengkap', 'Semua kolom wajib diisi');
                return;
            }

            // Validate PIN format (4-6 digits)
            const pinRegex = /^[0-9]{4,6}$/;
            if (!pinRegex.test(newPin)) {
                this.showAlert('warning', 'Format Tidak Valid', 'PIN baru harus terdiri dari 4-6 digit');
                return;
            }

            if (newPin !== confirmPin) {
                this.showAlert('warning', 'Tidak Cocok', 'PIN baru dan konfirmasinya tidak cocok');
                return;
            }

            if (newPin === currentPin) {
                this.showAlert('warning', 'PIN Sama', 'PIN baru harus berbeda dari PIN saat ini');
                return;
            }

            this.isPinSubmitting = true;

            try {
                const response = await fetch('/admin/settings/security-pin', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        current_pin: currentPin,
                        new_pin: newPin,
                        new_pin_confirmation: confirmPin
                    })
                });

                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || 'Gagal memperbarui PIN');
                }

                this.showAlert('success', 'Berhasil', 'PIN keamanan berhasil diperbarui');
                
                // Reset form
                this.resetPinForm();

            } catch (error) {
                console.error('Error updating PIN:', error);
                this.showAlert('error', 'Gagal', error.message || 'Tidak dapat memperbarui PIN. Periksa PIN saat ini.');
            } finally {
                this.isPinSubmitting = false;
            }
        },

        resetPinForm() {
            this.pinData = {
                currentPin: '',
                newPin: '',
                confirmPin: ''
            };
            this.showCurrentPin = false;
            this.showNewPin = false;
            this.showConfirmPin = false;
        },

        // Reactivate main category - Open Modal
        reactivateCategoryPrompt(catIndex) {
            const cat = this.categories[catIndex];
            this.restoreTargetCategory = cat.name;
            this.restoreCategoryIndex = catIndex;
            this.showRestoreCategoryModal = true;
        },
        
        // Confirm Reactivate Category
        confirmRestoreCategory() {
            const catIndex = this.restoreCategoryIndex;
            const cat = this.categories[catIndex];
            this.showRestoreCategoryModal = false;

            fetch(`/admin/categories/${cat.id}/reactivate`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(async res => {
                const ct = res.headers.get('content-type') || '';
                let data;
                if (ct.includes('application/json')) {
                    data = await res.json();
                } else {
                    const text = await res.text();
                    data = { __raw: text };
                }
                if (!res.ok) {
                    const msg = data && data.message ? data.message : (data.__raw || 'Server error');
                    if (data && data.__raw && /<\/?html|<!doctype/i.test(data.__raw)) {
                        console.error('Server returned HTML error on reactivateCategory:', data.__raw);
                        throw new Error('Server error (see console)');
                    }
                    throw new Error(msg);
                }
                return data;
            })
            .then(json => {
                this.showAlert('success', 'Berhasil', json.message || 'Kategori berhasil dipulihkan');
                this.loadCategories();
            })
            .catch(err => this.showAlert('error', 'Gagal', err.message))
            .finally(() => {
                this.restoreCategoryIndex = null;
                this.restoreTargetCategory = null;
            });
        },

        // Reactivate subcategory - Open Modal
        reactivateSubcategoryPrompt(catIndex, subIndex) {
            const sub = this.categories[catIndex].subcategories[subIndex];
            this.restoreTargetSubcategory = sub.name;
            this.restoreCategoryIndex = catIndex;
            this.restoreSubcategoryIndex = subIndex;
            this.showRestoreSubcategoryModal = true;
        },
        
        // Confirm Reactivate Subcategory
        confirmRestoreSubcategory() {
            const catIndex = this.restoreCategoryIndex;
            const subIndex = this.restoreSubcategoryIndex;
            const sub = this.categories[catIndex].subcategories[subIndex];
            this.showRestoreSubcategoryModal = false;

            fetch(`/admin/subcategories/${sub.id}/reactivate`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(async res => {
                const ct = res.headers.get('content-type') || '';
                let data;
                if (ct.includes('application/json')) {
                    data = await res.json();
                } else {
                    const text = await res.text();
                    data = { __raw: text };
                }
                if (!res.ok) {
                    const msg = data && data.message ? data.message : (data.__raw || 'Server error');
                    if (data && data.__raw && /<\/?html|<!doctype/i.test(data.__raw)) {
                        console.error('Server returned HTML error on reactivateSubcategory:', data.__raw);
                        throw new Error('Server error (see console)');
                    }
                    throw new Error(msg);
                }
                return data;
            })
            .then(json => {
                this.showAlert('success', 'Berhasil', json.message || 'Subkategori berhasil dipulihkan');
                this.loadCategories();
            })
            .catch(err => this.showAlert('error', 'Gagal', err.message))
            .finally(() => {
                this.restoreCategoryIndex = null;
                this.restoreSubcategoryIndex = null;
                this.restoreTargetSubcategory = null;
            });
        },
        // ============================================================
        
        // --- HELPER METHODS ---
        getStudentMinCategories(student) {
            const defaultMinCategories = parseInt(this.scoreSettings.minCategories || 0, 10);
            const yearValue = (student?.year ?? '').toString().trim();
            const yearDigits = yearValue.replace(/\D/g, '');
            let entryYear = null;

            if (yearDigits.length >= 4) {
                entryYear = parseInt(yearDigits.slice(0, 4), 10);
            } else if (yearDigits.length === 2) {
                entryYear = 2000 + parseInt(yearDigits, 10);
            } else {
                const nimDigits = (student?.id ?? '').toString().replace(/\D/g, '');
                if (nimDigits.length >= 2) {
                    entryYear = 2000 + parseInt(nimDigits.slice(0, 2), 10);
                }
            }

            if (entryYear === 2022) {
                return 4;
            }

            return defaultMinCategories;
        },

        isStudentPassed(student) {
            // Student passes if they have >= min points AND >= min categories (2022 override: 4 categories)
            const hasMinPoints = student.approvedPoints >= this.scoreSettings.minPoints;
            const studentMinCategories = this.getStudentMinCategories(student);
            const hasMinCategories = Object.keys(student.categoryBreakdown).length >= studentMinCategories;
            return hasMinPoints && hasMinCategories;
        },

        getStudentFinalStatus(student) {
            if (!student) return '-';

            const academicStatus = student.academicStatus || 'active';
            if (academicStatus === 'on_leave') return 'Cuti';
            if (academicStatus === 'graduated') return 'Lulus';
            if (academicStatus === 'non_active') return 'Non Aktif';

            return this.isStudentPassed(student) ? 'Memenuhi' : 'Belum Memenuhi';
        },

        getStudentStatusClass(student) {
            const label = this.getStudentFinalStatus(student);
            if (label === 'Memenuhi') return 'bg-green-100 text-green-700';
            if (label === 'Belum Memenuhi') return 'bg-red-100 text-red-700';
            if (label === 'Lulus') return 'bg-blue-100 text-blue-700';
            if (label === 'Cuti') return 'bg-amber-100 text-amber-700';
            if (label === 'Non Aktif') return 'bg-slate-200 text-slate-700';
            return 'bg-gray-100 text-gray-700';
        },

        translateStatus(status) {
            if (status === 'Approved') return 'Disetujui';
            if (status === 'Waiting') return 'Menunggu';
            if (status === 'Rejected') return 'Ditolak';
            return status;
        },

        // --- COMPUTED PROPERTIES ---

        get uniqueCategories() {
            // Mengambil semua nama kategori untuk dropdown filter
            return this.categories.map(c => c.name).sort();
        },

        get availableReviewYears() {
            return [...new Set(
                this.submissions
                    .filter(s => (s.academicStatus || 'active') === 'active')
                    .map(s => String(s.year || '').trim())
                    .filter(Boolean)
            )]
                .map(year => Number(year))
                .filter(year => Number.isFinite(year))
                .sort((a, b) => b - a)
                .map(year => String(year));
        },

        // Filter Logic Review Tab
        get filteredSubmissions() {
            return this.submissions.filter(submission => {
                const matchesSearch = this.searchQuery === '' || 
                    (submission.judul && submission.judul.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
                    (submission.studentName && submission.studentName.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
                    (submission.studentId && submission.studentId.includes(this.searchQuery));
                
                const matchesStatus = this.statusFilter === '' || submission.status === this.statusFilter;
                const matchesCategory = this.categoryFilter === '' || submission.mainCategory === this.categoryFilter;
                const matchesYear = this.submissionYearFilter === '' || String(submission.year || '').trim() === String(this.submissionYearFilter).trim();
                const isVisibleAcademicStatus = (submission.academicStatus || 'active') === 'active';
                
                return matchesSearch && matchesStatus && matchesCategory && matchesYear && isVisibleAcademicStatus;
            });
        },

        get reviewLastPage() {
            const totalItems = this.filteredSubmissions.length;
            return Math.max(1, Math.ceil(totalItems / this.submissionsPagination.perPage));
        },

        get paginatedSubmissions() {
            const page = Math.min(this.submissionsPagination.currentPage, this.reviewLastPage);
            const start = (page - 1) * this.submissionsPagination.perPage;
            const end = start + this.submissionsPagination.perPage;
            return this.filteredSubmissions.slice(start, end);
        },

        // Filter Logic Student Tab
        get filteredStudentsList() {
            return this.students.filter(student => {
                const finalStatus = this.getStudentFinalStatus(student);
                if (finalStatus === 'Lulus' || finalStatus === 'Cuti') {
                    return false;
                }

                const searchLower = this.studentSearchQuery.toLowerCase();
                const matchesSearch = student.name.toLowerCase().includes(searchLower) || 
                                      student.id.toString().includes(searchLower);

                const matchesMajor = this.majorFilter === '' || student.major === this.majorFilter;
                // Only filter by year if yearFilterMode is 'specific' and yearFilter has value
                const matchesYear = this.yearFilterMode === 'all' || this.yearFilter === '' || student.year == this.yearFilter;

                let matchesStatus = true;
                if (this.statusPassFilter === 'met') {
                    matchesStatus = finalStatus === 'Memenuhi';
                } else if (this.statusPassFilter === 'not_met') {
                    matchesStatus = finalStatus === 'Belum Memenuhi';
                }

                return matchesSearch && matchesMajor && matchesYear && matchesStatus;
            });
        },

        get studentsLastPage() {
            const total = this.filteredStudentsList.length;
            const perPage = Math.max(1, Number(this.studentsPagination.perPage || 25));
            return Math.max(1, Math.ceil(total / perPage));
        },

        get studentsRange() {
            const total = this.filteredStudentsList.length;
            const perPage = Math.max(1, Number(this.studentsPagination.perPage || 25));
            const currentPage = Math.max(1, Math.min(Number(this.studentsPagination.currentPage || 1), this.studentsLastPage));

            if (total === 0) {
                return { from: 0, to: 0, total: 0 };
            }

            const from = ((currentPage - 1) * perPage) + 1;
            const to = Math.min(currentPage * perPage, total);
            return { from, to, total };
        },

        get paginatedStudentsList() {
            const perPage = Math.max(1, Number(this.studentsPagination.perPage || 25));
            const total = this.filteredStudentsList.length;
            const lastPage = Math.max(1, Math.ceil(total / perPage));
            const currentPage = Math.max(1, Math.min(Number(this.studentsPagination.currentPage || 1), lastPage));
            const start = (currentPage - 1) * perPage;
            const end = start + perPage;

            return this.filteredStudentsList.slice(start, end);
        },

        get availableStudentYears() {
            const sourceYears = (this.studentYears && this.studentYears.length > 0)
                ? this.studentYears
                : this.students
                    .filter(student => {
                        const status = this.getStudentFinalStatus(student);
                        return status !== 'Lulus' && status !== 'Cuti';
                    })
                    .map(student => student.year)
                    .filter(year => year !== null && year !== undefined && year !== '');

            return [...new Set(sourceYears)]
                .map(year => Number(year))
                .filter(year => Number.isFinite(year))
                .sort((a, b) => b - a)
                .map(year => String(year));
        },

        get selectedStudentCategories() {
            if (!this.selectedStudent || !this.selectedStudent.submissions_list) {
                return [];
            }

            return [...new Set(this.selectedStudent.submissions_list.map(sub => sub.mainCategory).filter(Boolean))];
        },

        get filteredSelectedStudentSubmissions() {
            if (!this.selectedStudent || !this.selectedStudent.submissions_list) {
                return [];
            }

            return this.selectedStudent.submissions_list.filter(sub => {
                const search = (this.studentDetailSearchQuery || '').toLowerCase();
                const matchesSearch = search === '' ||
                    (sub.title && sub.title.toLowerCase().includes(search)) ||
                    (sub.description && sub.description.toLowerCase().includes(search));

                const matchesStatus = this.studentDetailStatusFilter === '' || sub.status === this.studentDetailStatusFilter;
                const matchesCategory = this.studentDetailCategoryFilter === '' || sub.mainCategory === this.studentDetailCategoryFilter;

                return matchesSearch && matchesStatus && matchesCategory;
            });
        },

        // --- FUNGSI UTAMA (Review, Approve, Reject) ---

        openMobileDetail(submission) {
            this.mobileDetailSubmission = submission;
            this.showMobileDetailModal = true;
        },

        openMobileStudentDetail(student) {
            this.selectedStudent = student;
            this.mobileStudentDetail = student;
            this.showMobileStudentDetailModal = true;
        },

        closeMobileStudentDetail() {
            this.showMobileStudentDetailModal = false;
            this.mobileStudentDetail = null;
        },

        viewDetail(submission) {
            this.selectedSubmission = submission;
            this.isCategoryCorrectionEnabled = false;
            this.categoryChanged = false;
            this.assignedMainCategory = '';
            this.assignedSubcategory = '';
            this.assignedAvailableSubcategories = [];

            // Auto-fill jika kategori sudah ada
            if (submission.mainCategory) {
                const mainIndex = this.categories.findIndex(c => c.name === submission.mainCategory);
                if (mainIndex !== -1) {
                    this.assignedMainCategory = mainIndex;
                    this.assignedAvailableSubcategories = this.categories[mainIndex].subcategories;
                    
                    const subIndex = this.assignedAvailableSubcategories.findIndex(s => s.name === submission.subcategory);
                    if (subIndex !== -1) {
                        this.assignedSubcategory = subIndex;
                    }
                }
            }
            this.showDetailModal = true;
        },

        updateAssignedSubcategories() {
            if (this.assignedMainCategory !== '') {
                this.assignedAvailableSubcategories = this.categories[this.assignedMainCategory].subcategories;
                this.assignedSubcategory = '';
                this.categoryChanged = true;
            } else {
                this.assignedAvailableSubcategories = [];
                this.assignedSubcategory = '';
                this.categoryChanged = false;
            }
        },

        applyLocalSubmissionReviewResult(submissionId, updates = {}) {
            this.submissions = this.submissions.map((submission) => {
                if (submission.id !== submissionId) {
                    return submission;
                }

                return {
                    ...submission,
                    ...updates,
                };
            });

            if (this.selectedSubmission && this.selectedSubmission.id === submissionId) {
                this.selectedSubmission = {
                    ...this.selectedSubmission,
                    ...updates,
                };
            }
        },

        showAutoNextToast(message) {
            this.queueToastMessage = message;
            this.showQueueToast = true;

            setTimeout(() => {
                this.showQueueToast = false;
            }, 1400);
        },

        getNextWaitingSubmission(currentSubmissionId = null) {
            const waitingQueue = this.filteredSubmissions
                .filter((submission) => submission.status === 'Waiting' && submission.id !== currentSubmissionId);

            return waitingQueue.length > 0 ? waitingQueue[0] : null;
        },

        moveToNextWaitingSubmission(currentSubmissionId = null) {
            const nextSubmission = this.getNextWaitingSubmission(currentSubmissionId);

            if (nextSubmission) {
                this.showAutoNextToast('Berhasil disimpan. Lanjut ke submission Waiting berikutnya.');
                this.viewDetail(nextSubmission);
                return;
            }

            this.closeModal();
            this.showAlert('success', 'Selesai', 'Tidak ada lagi pengajuan Waiting pada daftar ini.');
        },

        handleApprove() {
            if (this.isCategoryCorrectionEnabled && (this.assignedMainCategory === '' || this.assignedSubcategory === '')) {
                this.showAlert('warning', 'Tidak Lengkap', 'Harap periksa atau pilih Kategori dan Subkategori yang benar.');
                return;
            }

            if (this.isCategoryCorrectionEnabled) {
                const mainCat = this.categories[this.assignedMainCategory];
                const subCat = mainCat.subcategories[this.assignedSubcategory];

                this.approveModalMainCategory = mainCat.name;
                this.approveModalSubcategory = subCat.name;
                this.approveModalPoints = subCat.points;
            } else {
                this.approveModalMainCategory = this.selectedSubmission?.mainCategory || '-';
                this.approveModalSubcategory = this.selectedSubmission?.subcategory || '-';
                const defaultPoints = this.selectedSubmission?.suggestedPoint ?? this.selectedSubmission?.point ?? 0;
                this.approveModalPoints = Number(defaultPoints) || 0;
            }
            
            this.showApproveModal = true;
        },

        async updateCategoryOnly() {
            if (!this.isCategoryCorrectionEnabled) {
                this.showAlert('warning', 'Mode Perbaikan Nonaktif', 'Aktifkan checkbox Perbaiki terlebih dahulu.');
                return;
            }

            if (this.assignedMainCategory === '' || this.assignedSubcategory === '') {
                this.showAlert('warning', 'Tidak Lengkap', 'Harap pilih Kategori Utama dan Subkategori.');
                return;
            }

            const mainCat = this.categories[this.assignedMainCategory];
            const subCat = mainCat.subcategories[this.assignedSubcategory];
            const url = `/admin/submissions/${this.selectedSubmission.id}/update-category`;
            
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        assigned_subcategory_id: subCat.id,
                        category_change_reason: this.categoryChangeReason || ''
                    })
                });

                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || 'Gagal memperbarui kategori');
                }

                this.showAlert('success', 'Diperbarui', 'Kategori berhasil diperbarui. Memuat ulang...');
                setTimeout(() => window.location.reload(), 1500);
                this.closeModal();
            } catch (error) {
                console.error(error);
                this.showAlert('error', 'Kesalahan', error.message || 'Gagal memperbarui kategori');
            }
        },

        confirmApprove() {
            const currentSubmissionId = this.selectedSubmission?.id;
            const url = `/admin/submissions/${this.selectedSubmission.id}/approve`;

            let payload = {
                points: Number(this.approveModalPoints) || 0
            };

            const approvedCategoryName = this.approveModalMainCategory;
            const approvedSubcategoryName = this.approveModalSubcategory;
            const approvedPoints = Number(this.approveModalPoints) || 0;

            if (this.isCategoryCorrectionEnabled) {
                const mainCat = this.categories[this.assignedMainCategory];
                const subCat = mainCat.subcategories[this.assignedSubcategory];
                payload.assigned_subcategory_id = subCat.id;
                payload.points = Number(subCat.points) || 0;
            }
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            })
            .then(async response => {
                if (response.ok) {
                    this.showApproveModal = false;
                    this.applyLocalSubmissionReviewResult(currentSubmissionId, {
                        status: 'Approved',
                        mainCategory: approvedCategoryName,
                        subcategory: approvedSubcategoryName,
                        point: approvedPoints,
                    });
                    this.moveToNextWaitingSubmission(currentSubmissionId);
                } else {
                    const data = await response.json();
                    throw new Error(data.message || 'Gagal menyetujui pengajuan');
                }
            })
            .catch(error => {
                console.error(error);
                this.showAlert('error', 'Kesalahan', error.message);
            });
        },

        viewStudentDetail(student) {
            if (!student || !student.id) {
                this.showAlert('warning', 'Data Tidak Lengkap', 'Data mahasiswa tidak valid.');
                return;
            }

            const detailUrl = `/admin/students/${student.id}/detail`;
            window.open(detailUrl, '_blank', 'noopener,noreferrer');
        },

        async saveStudentAcademicStatus() {
            if (!this.selectedStudent || !this.selectedStudent.id) {
                this.showAlert('warning', 'Data Tidak Lengkap', 'Mahasiswa tidak ditemukan.');
                return;
            }

            if (this.isUpdatingAcademicStatus) {
                return;
            }

            this.isUpdatingAcademicStatus = true;

            try {
                const response = await fetch(`/admin/students/${this.selectedStudent.id}/academic-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        academic_status: this.studentAcademicStatusDraft
                    })
                });

                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Gagal memperbarui status akademik.');
                }

                const newStatus = data.academic_status || this.studentAcademicStatusDraft;
                this.selectedStudent.academicStatus = newStatus;

                this.students = this.students.map((student) => {
                    if (student.id === this.selectedStudent.id) {
                        return {
                            ...student,
                            academicStatus: newStatus,
                        };
                    }

                    return student;
                });

                if (this.mobileStudentDetail && this.mobileStudentDetail.id === this.selectedStudent.id) {
                    this.mobileStudentDetail.academicStatus = newStatus;
                }

                this.showAlert('success', 'Berhasil', 'Status akademik mahasiswa berhasil diperbarui.');
            } catch (error) {
                console.error(error);
                this.showAlert('error', 'Gagal', error.message || 'Terjadi kesalahan saat memperbarui status akademik.');
            } finally {
                this.isUpdatingAcademicStatus = false;
            }
        },

        openStudentScoreReport(student) {
            if (!student || !student.id) {
                this.showAlert('warning', 'Data Tidak Lengkap', 'ID mahasiswa tidak ditemukan untuk membuka report.');
                return;
            }

            const reportViewUrl = `/student/${student.id}/report/view`;
            window.open(reportViewUrl, '_blank', 'noopener,noreferrer');
        },

        viewStudentReport() {
            if (!this.selectedStudent || !this.selectedStudent.id) return;
            this.openStudentScoreReport(this.selectedStudent);
        },

        openStudentSubmissionPreview(submission) {
            if (!this.selectedStudent || !submission) {
                return;
            }

            this.showStudentDetailModal = false;

            this.viewDetail({
                id: submission.id,
                studentId: this.selectedStudent.id,
                studentName: this.selectedStudent.name,
                mainCategory: submission.mainCategory,
                subcategory: submission.subcategory,
                judul: submission.title,
                keterangan: submission.description,
                point: submission.points,
                suggestedPoint: submission.points || 0,
                waktu: submission.waktu,
                activityDate: submission.activityDate,
                submittedDate: submission.submittedDate,
                status: submission.status,
                certificate: submission.certificate,
                file_url: submission.file_url,
                certificate_path: submission.certificate_path,
            });
        },

        async reduceSubmissionPoints(submission) {
            if (!submission || !submission.id) {
                this.showAlert('warning', 'Data Tidak Lengkap', 'Submission tidak valid.');
                return;
            }

            if (submission.status !== 'Approved') {
                this.showAlert('warning', 'Tidak Bisa Diubah', 'Poin hanya bisa diubah untuk status Disetujui.');
                return;
            }

            const currentPoints = Number(submission.points ?? 0);

            this.adjustPointForm = {
                submissionId: submission.id,
                title: submission.title || '-',
                currentPoints: currentPoints,
                nextPoints: String(currentPoints),
                reason: submission.pointAdjustmentReason || ''
            };
            this.showAdjustPointsModal = true;
        },

        closeAdjustPointsModal() {
            this.showAdjustPointsModal = false;
            this.isAdjustingPoints = false;
            this.adjustPointForm = {
                submissionId: null,
                title: '',
                currentPoints: 0,
                nextPoints: '',
                reason: ''
            };
        },

        async submitAdjustedPoints() {
            if (!this.adjustPointForm.submissionId) {
                this.showAlert('warning', 'Data Tidak Lengkap', 'Submission tidak valid.');
                return;
            }

            const normalizedInput = String(this.adjustPointForm.nextPoints || '').trim().replace(',', '.');
            if (normalizedInput === '') {
                this.showAlert('warning', 'Input Kosong', 'Poin tidak boleh kosong.');
                return;
            }

            const nextPoints = Number(normalizedInput);
            if (!Number.isFinite(nextPoints) || nextPoints < 0) {
                this.showAlert('warning', 'Input Tidak Valid', 'Masukkan angka valid minimal 0.');
                return;
            }

            const reason = String(this.adjustPointForm.reason || '').trim();
            if (reason.length < 3) {
                this.showAlert('warning', 'Alasan Kurang Jelas', 'Alasan minimal 3 karakter.');
                return;
            }

            this.isAdjustingPoints = true;

            try {
                const response = await fetch(`/admin/submissions/${this.adjustPointForm.submissionId}/adjust-points`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        points_awarded: nextPoints,
                        points_adjustment_reason: reason
                    })
                });

                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Gagal mengubah poin S-Core.');
                }

                this.closeAdjustPointsModal();
                this.showAlert('success', 'Poin Diperbarui', 'Poin S-Core berhasil diperbarui. Memuat ulang...');
                setTimeout(() => window.location.reload(), 1000);
            } catch (error) {
                console.error(error);
                this.showAlert('error', 'Gagal', error.message || 'Terjadi kesalahan saat mengubah poin.');
            } finally {
                this.isAdjustingPoints = false;
            }
        },

        async deleteStudentSubmission(submission) {
            if (!submission || !submission.id) {
                this.showAlert('warning', 'Data Tidak Lengkap', 'Submission tidak valid.');
                return;
            }

            this.showAlert(
                'warning',
                'Konfirmasi Hapus S-Core',
                `Yakin ingin menghapus kegiatan "${submission.title || '-'}"?\n\nTindakan ini tidak dapat dibatalkan.`,
                true,
                async () => {
                    try {
                        const response = await fetch(`/admin/submissions/${submission.id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        const data = await response.json();
                        if (!response.ok) {
                            throw new Error(data.message || 'Gagal menghapus data S-Core.');
                        }

                        this.showAlert('success', 'Berhasil Dihapus', 'Data S-Core berhasil dihapus. Memuat ulang...');
                        setTimeout(() => window.location.reload(), 1000);
                    } catch (error) {
                        console.error(error);
                        this.showAlert('error', 'Gagal', error.message || 'Terjadi kesalahan saat menghapus data.');
                    }
                }
            );
        },

        // Admin: reset selected student's password - Open modal
        resetStudentPassword() {
            if (!this.selectedStudent || !this.selectedStudent.id) return;
            this.resetPasswordInput = '';
            this.resetPasswordError = '';
            this.showResetPasswordModal = true;
        },

        // Confirm and execute password reset
        async confirmResetPassword() {
            if (!this.selectedStudent || !this.selectedStudent.id) return;
            
            const id = this.selectedStudent.id;
            const pw = this.resetPasswordInput.trim();
            
            // Validate password is not empty
            if (pw === '') {
                this.resetPasswordError = 'Password tidak boleh dikosongkan. Harap isi password minimal 6 karakter.';
                return;
            }
            
            // Validate minimum length
            if (pw.length < 6) {
                this.resetPasswordError = 'Password harus minimal 6 karakter.';
                return;
            }
            
            // Close modal
            this.showResetPasswordModal = false;

            try {
                const url = `/admin/users/${id}/reset-password`;
                console.log('resetPassword URL:', url);
                const response = await fetch(url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ password: pw })
                });

                const ct = response.headers.get('content-type') || '';
                let data;
                if (ct.includes('application/json')) {
                    data = await response.json();
                } else {
                    const text = await response.text();
                    data = { __raw: text };
                }

                if (!response.ok) {
                    const msg = data && data.message ? data.message : (data.__raw || 'Server error');
                    if (data && data.__raw && /<\/?html|<!doctype/i.test(data.__raw)) {
                        console.error('Server returned HTML on resetStudentPassword:', data.__raw);
                        throw new Error('Server error (see console)');
                    }
                    throw new Error(msg);
                }

                const message = data.message || 'Kata sandi berhasil direset';
                this.showAlert('success', 'Reset Kata Sandi', message);
                this.resetPasswordInput = '';
                this.showStudentDetailModal = false;
            } catch (err) {
                console.error(err);
                this.showAlert('error', 'Gagal', err.message || 'Gagal mereset kata sandi');
            }
        },

        handleRejectConfirm() {
            if (!this.rejectReason || this.rejectReason.trim() === '') {
                this.showAlert('warning', 'Alasan Belum Diisi', 'Harap berikan alasan penolakan.');
                return;
            }

            const currentSubmissionId = this.selectedSubmission?.id;

            const url = `/admin/submissions/${this.selectedSubmission.id}/reject`;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    rejectReason: this.rejectReason
                })
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Gagal menolak pengajuan');
                return data;
            })
            .then(data => {
                this.showRejectModal = false;
                this.applyLocalSubmissionReviewResult(currentSubmissionId, {
                    status: 'Rejected',
                });
                this.moveToNextWaitingSubmission(currentSubmissionId);
            })
            .catch(error => {
                console.error('Reject Error:', error);
                this.showAlert('error', 'Penolakan Gagal', error.message);
            });
        },

        downloadStudentReport() {
            if (!this.selectedStudent || !this.selectedStudent.id) return;
            
            const studentId = this.selectedStudent.id;
            const reportUrl = `/student/${studentId}/report`;
            
            // Create a temporary link and click it to download the report
            const link = document.createElement('a');
            link.href = reportUrl;
            link.click();
        },

        exportReport() {
            const data = this.filteredStudentsList;
            if(data.length === 0) { 
                this.showAlert('warning', 'Tidak Ada Data', 'Tidak ada mahasiswa yang dapat diekspor dengan filter saat ini.'); 
                return; 
            }
            
            let csvContent = "data:text/csv;charset=utf-8,";
            csvContent += "NIM,Nama,Jurusan,Angkatan,Total Poin,Status,Pengajuan Tertunda\n"; 
            
            data.forEach(row => {
                const status = this.getStudentFinalStatus(row);
                csvContent += `${row.id},"${row.name}",${row.major},${row.year},${row.approvedPoints},${status},${row.pending}\n`;
            });

            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "Laporan_Mahasiswa_S-Core.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },

        changeStudentsPerPage() {
            this.studentsPagination.currentPage = 1;
            this.selectedStudents = [];
        },

        goToSubmissionPage(targetPage) {
            const requestedPage = Number(targetPage);
            if (!Number.isFinite(requestedPage)) {
                return;
            }
            const page = Math.max(1, Math.min(requestedPage, this.reviewLastPage));
            this.submissionsPagination.currentPage = page;
        },

        onYearFilterModeChange() {
            if (this.yearFilterMode === 'all') {
                this.yearFilter = '';
                this.applyStudentFilters();
            }
        },

        onStudentSearchInput() {
            if (this.studentSearchDebounceTimer) {
                clearTimeout(this.studentSearchDebounceTimer);
            }

            // Debounce to avoid page reload on every keystroke.
            this.studentSearchDebounceTimer = setTimeout(() => {
                this.applyStudentFilters();
            }, 450);
        },

        applyStudentFilters() {
            const normalizedYear = String(this.yearFilter ?? '').trim();
            const effectiveYearMode = normalizedYear !== '' ? 'specific' : (this.yearFilterMode || 'all');

            this.yearFilter = normalizedYear;
            this.yearFilterMode = effectiveYearMode;
            this.studentsPagination.currentPage = 1;
            this.selectedStudents = [];
        },

        goToStudentsPage(targetPage, forceReload = false) {
            const maxPage = this.studentsLastPage || 1;
            const page = Math.max(1, Math.min(targetPage, maxPage));

            if (!forceReload && page === this.studentsPagination.currentPage) {
                return;
            }

            this.studentsPagination.currentPage = page;
        },

        // Toggle select all students
        toggleSelectAll(checked) {
            if (checked) {
                this.selectedStudents = this.paginatedStudentsList.map(s => s.id);
                if (!this.selectedStudent && this.paginatedStudentsList.length > 0) {
                    this.selectedStudent = this.paginatedStudentsList[0];
                }
            } else {
                this.selectedStudents = [];
                this.selectedStudent = null;
            }
        },

        handleStudentCheckboxToggle(student, checked) {
            this.toggleStudentSelection(student.id, checked);

            if (checked) {
                this.selectedStudent = student;
            } else if (this.selectedStudent && this.selectedStudent.id === student.id) {
                this.selectedStudent = null;
            }
        },

        // Toggle individual student selection
        toggleStudentSelection(studentId, checked) {
            if (checked) {
                if (!this.selectedStudents.includes(studentId)) {
                    this.selectedStudents.push(studentId);
                }
            } else {
                this.selectedStudents = this.selectedStudents.filter(id => id !== studentId);
            }
        },

        // Delete single student
        deleteStudent(studentId) {
            this.showAlert('warning', 'Konfirmasi Hapus', 'Apakah Anda yakin ingin menghapus mahasiswa ini? Tindakan ini tidak dapat dibatalkan.', true, () => {
                this.performDeleteStudents([studentId]);
            });
        },

        // Delete selected students
        deleteSelectedStudents() {
            if (this.selectedStudents.length === 0) {
                this.showAlert('warning', 'Tidak Ada Pilihan', 'Pilih setidaknya satu mahasiswa untuk dihapus.');
                return;
            }

            this.showAlert('warning', 'Konfirmasi Hapus Massal', `Apakah Anda yakin ingin menghapus ${this.selectedStudents.length} mahasiswa? Tindakan ini tidak dapat dibatalkan.`, true, () => {
                this.performDeleteStudents([...this.selectedStudents]);
            });
        },

        setBulkAcademicStatus(status, checked) {
            if (checked) {
                this.bulkAcademicStatusDraft = status;
                return;
            }

            if (this.bulkAcademicStatusDraft === status) {
                this.bulkAcademicStatusDraft = 'active';
            }
        },

        async applyBulkAcademicStatus() {
            if (this.selectedStudents.length === 0) {
                this.showAlert('warning', 'Tidak Ada Pilihan', 'Pilih minimal satu mahasiswa dahulu.');
                return;
            }

            if (this.isUpdatingBulkAcademicStatus) {
                return;
            }

            const targetLabel = this.bulkAcademicStatusDraft === 'on_leave'
                ? 'Cuti'
                : this.bulkAcademicStatusDraft === 'graduated'
                    ? 'Lulus'
                    : this.bulkAcademicStatusDraft === 'non_active'
                        ? 'Non Aktif'
                        : 'Aktif';

            this.isUpdatingBulkAcademicStatus = true;

            try {
                const response = await fetch('/admin/students/academic-status/bulk', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        student_ids: this.selectedStudents,
                        academic_status: this.bulkAcademicStatusDraft
                    })
                });

                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Gagal memperbarui status mahasiswa terpilih.');
                }

                this.students = this.students.map((student) => {
                    if (!this.selectedStudents.includes(student.id)) {
                        return student;
                    }

                    return {
                        ...student,
                        academicStatus: this.bulkAcademicStatusDraft,
                    };
                });

                if (this.selectedStudent && this.selectedStudents.includes(this.selectedStudent.id)) {
                    this.selectedStudent.academicStatus = this.bulkAcademicStatusDraft;
                }

                if (this.mobileStudentDetail && this.selectedStudents.includes(this.mobileStudentDetail.id)) {
                    this.mobileStudentDetail.academicStatus = this.bulkAcademicStatusDraft;
                }

                this.showAlert('success', 'Berhasil', `Status ${targetLabel} berhasil diterapkan ke mahasiswa terpilih.`);
            } catch (error) {
                console.error(error);
                this.showAlert('error', 'Gagal', error.message || 'Terjadi kesalahan saat update status massal.');
            } finally {
                this.isUpdatingBulkAcademicStatus = false;
            }
        },

        promoteAllStudentsSemester() {
            this.showAlert(
                'warning',
                'Konfirmasi Semester Naik',
                'Apakah Anda yakin ingin menaikkan semester semua mahasiswa?\n\nSetiap mahasiswa akan bertambah +1 semester.',
                true,
                () => this.performPromoteSemester()
            );
        },

        demoteAllStudentsSemester() {
            this.showAlert(
                'warning',
                'Konfirmasi Semester Turun',
                'Apakah Anda yakin ingin menurunkan semester mahasiswa -1?\n\nSemester akan dikurangi satu tingkat untuk data yang pernah dinaikkan.',
                true,
                () => this.performDemoteSemester()
            );
        },

        async performPromoteSemester() {
            if (this.isPromotingSemester) {
                return;
            }

            this.isPromotingSemester = true;
            try {
                const response = await fetch('/admin/students/promote-semester', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Gagal menaikkan semester.');
                }

                this.showAlert('success', 'Berhasil', 'Semester semua mahasiswa berhasil dinaikkan. Memuat ulang...');
                setTimeout(() => window.location.reload(), 1200);
            } catch (error) {
                console.error('Error promoting semester:', error);
                this.showAlert('error', 'Gagal', error.message || 'Terjadi kesalahan saat menaikkan semester.');
            } finally {
                this.isPromotingSemester = false;
            }
        },

        async performDemoteSemester() {
            if (this.isDemotingSemester) {
                return;
            }

            this.isDemotingSemester = true;
            try {
                const response = await fetch('/admin/students/demote-semester', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Gagal menurunkan semester.');
                }

                this.showAlert('success', 'Berhasil', 'Semester mahasiswa berhasil diturunkan. Memuat ulang...');
                setTimeout(() => window.location.reload(), 1200);
            } catch (error) {
                console.error('Error demoting semester:', error);
                this.showAlert('error', 'Gagal', error.message || 'Terjadi kesalahan saat menurunkan semester.');
            } finally {
                this.isDemotingSemester = false;
            }
        },

        // Perform actual deletion
        async performDeleteStudents(studentIds) {
            try {
                const response = await fetch('/admin/students/delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ student_ids: studentIds })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Gagal menghapus mahasiswa');
                }

                this.showAlert('success', 'Berhasil', `Berhasil menghapus ${studentIds.length} mahasiswa. Memuat ulang...`);
                this.selectedStudents = [];
                setTimeout(() => window.location.reload(), 1500);
            } catch (error) {
                console.error('Error deleting students:', error);
                this.showAlert('error', 'Gagal', error.message || 'Tidak dapat menghapus mahasiswa');
            }
        },

        confirmLogout() {
            fetch('{{ route("logout") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (response.ok || response.redirected) {
                    window.location.href = '/login';
                } else {
                    console.error('Logout failed:', response.status);
                    window.location.href = '/login';
                }
            })
            .catch(error => {
                console.error('Logout error:', error);
                window.location.href = '/login';
            });
        },

        closeModal() {
            this.showDetailModal = false;
            this.showRejectModal = false;
            this.showApproveModal = false;
            this.showMobileDetailModal = false;
            this.mobileDetailSubmission = null;
            this.showMobileStudentDetailModal = false;
            this.mobileStudentDetail = null;
            this.selectedSubmission = null;
            this.rejectReason = '';
            this.rejectReasonType = '';
        },
        
        showAlert(type, title, message, hasCancel = false, callback = null) {
            this.alertType = type;
            this.alertTitle = title;
            this.alertMessage = message;
            this.alertHasCancel = hasCancel;
            this.alertCallback = callback;
            this.showAlertModal = true;
        },
        
        closeAlertModal(confirmed) {
            this.showAlertModal = false;
            if (confirmed && this.alertCallback) {
                this.alertCallback();
            }
            this.alertCallback = null;
        },

        // --- MANAJEMEN KATEGORI (CATEGORY MANAGEMENT) ---
        requestCategoryManagement() { this.showPinModal = true; this.pinInput = ''; this.pinError = false; },
        closePinModal() { this.showPinModal = false; },
        
        async verifyPin() {
            const pin = (this.pinInput || '').trim();

            if (!pin) {
                this.pinError = true;
                return;
            }

            try {
                const response = await fetch('/api/verify-security-pin', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        pin
                    })
                });

                const data = await response.json();

                if (response.ok && data.valid) {
                    this.isPinVerified = true;
                    this.showPinModal = false;
                    this.showCategoryModal = true;
                    this.pinError = false;
                } else {
                    this.pinError = true;
                }
            } catch (error) {
                console.error('Error verifying PIN:', error);
                this.pinError = true;
            }
        },
        closeCategoryModal() { this.showCategoryModal = false; },

        // 1. ADD MAIN CATEGORY
        addMainCategory() {
            if (!this.newMainCategory.trim()) return;

            fetch('{{ route("categories.store") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    name: this.newMainCategory,
                    is_mandatory: !!this.newMainCategoryIsMandatory,
                    max_submissions_per_semester: this.parseCategorySemesterLimitValue(this.newMainCategoryMaxPerSemester)
                })
            })
            .then(async res => {
                const json = await res.json();
                if (!res.ok) throw new Error(json.message);
                return json;
            })
            .then(json => {
                // Reload categories dari API untuk ensure sinkronisasi
                this.newMainCategory = '';
                this.newMainCategoryIsMandatory = false;
                this.newMainCategoryMaxPerSemester = 'none';
                this.showAlert('success', 'Tersimpan', 'Kategori berhasil ditambahkan. Memuat ulang...');
                this.loadCategories();
            })
            .catch(err => {
                this.showAlert('error', 'Kesalahan', err.message || 'Gagal menambahkan kategori');
            });
        },

        // 2. EDIT/SAVE MAIN CATEGORY
        saveMainCategory(index) {
            const cat = this.categories[index];
            fetch(`/admin/categories/${cat.id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    name: cat.name,
                    is_mandatory: !!cat.is_mandatory,
                    max_submissions_per_semester: this.parseCategorySemesterLimitValue(cat.max_submissions_per_semester)
                })
            })
            .then(res => res.ok ? res.json() : Promise.reject(res))
            .then(() => {
                cat.isEditing = false;
                this.showAlert('success', 'Diperbarui', 'Kategori berhasil diperbarui.');
                // Reload categories dari API untuk ensure sinkronisasi
                this.loadCategories();
            })
            .catch(() => this.showAlert('error', 'Kesalahan', 'Gagal memperbarui kategori'));
        },

        updateCategorySemesterLimit(index) {
            const cat = this.categories[index];
            cat.isSavingLimit = true;

            fetch(`/admin/categories/${cat.id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    name: cat.name,
                    max_submissions_per_semester: this.parseCategorySemesterLimitValue(cat.max_submissions_per_semester)
                })
            })
            .then(res => res.ok ? res.json() : Promise.reject(res))
            .then(() => {
                this.showAlert('success', 'Diperbarui', `Maksimum submit per semester untuk ${cat.name} berhasil diperbarui.`);
                this.loadCategories();
            })
            .catch(() => this.showAlert('error', 'Kesalahan', 'Gagal memperbarui maksimum submit per semester'))
            .finally(() => {
                cat.isSavingLimit = false;
            });
        },

        // 3. DELETE MAIN CATEGORY - Open Modal
        deleteMainCategory(index) {
            const cat = this.categories[index];
            this.deleteTargetCategory = cat.name;
            this.deleteCategoryIndex = index;
            this.showDeleteCategoryModal = true;
        },
        
        // Confirm Delete Category
        confirmDeleteCategory() {
            const index = this.deleteCategoryIndex;
            const cat = this.categories[index];
            this.showDeleteCategoryModal = false;

            fetch(`/admin/categories/${cat.id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(async res => {
                const ct = res.headers.get('content-type') || '';
                let data;
                if (ct.includes('application/json')) {
                    data = await res.json();
                } else {
                    const text = await res.text();
                    data = { __raw: text };
                }

                if (!res.ok) {
                    const msg = data && data.message ? data.message : (data.__raw || 'Server error');
                    if (data && data.__raw && /<\/?html|<!doctype/i.test(data.__raw)) {
                        console.error('Server returned HTML error on deleteMainCategory:', data.__raw);
                        throw new Error('Server error (see console)');
                    }
                    throw new Error(msg);
                }

                return data;
            })
            .then(json => {
                const msg = (json && json.message) ? json.message : 'Kategori telah dihapus atau dinonaktifkan. Memuat ulang kategori...';
                this.showAlert('success', 'Berhasil', msg);
                // Reload categories dari API untuk sinkronisasi dengan server
                this.loadCategories();
            })
            .catch(err => this.showAlert('error', 'Gagal', err.message))
            .finally(() => {
                this.deleteCategoryIndex = null;
                this.deleteTargetCategory = null;
            });
        },

        // 4. ADD SUBCATEGORY
        addSubcategory() {
            if (this.newCategory.mainCategoryIndex === '' || !this.newCategory.name) {
                this.showAlert('warning', 'Info Tidak Lengkap', 'Pilih kategori dan masukkan nama'); return;
            }

            const catIndex = this.newCategory.mainCategoryIndex;
            const catId = this.categories[catIndex].id;

            fetch('{{ route("subcategories.store") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    category_id: catId,
                    name: this.newCategory.name,
                    points: this.newCategory.points || 0,
                    description: this.newCategory.description,
                    is_mandatory: !!this.newCategory.isMandatory
                })
            })
            .then(res => res.ok ? res.json() : Promise.reject(res))
            .then(() => {
                // Reset form
                this.newCategory = { mainCategoryIndex: '', name: '', points: '', description: '', isMandatory: false };
                this.showAlert('success', 'Tersimpan', 'Subkategori berhasil ditambahkan. Memuat ulang...');
                // Reload categories dari API untuk sinkronisasi
                this.loadCategories();
            })
            .catch(() => this.showAlert('error', 'Kesalahan', 'Gagal menambahkan subkategori'));
        },

        // 5. EDIT SUBCATEGORY
        editSubcategory(catIndex, subIndex) {
            this.categories[catIndex].subcategories[subIndex].isEditing = true;
        },
        
        cancelEditSubcategory(catIndex, subIndex) {
            this.categories[catIndex].subcategories[subIndex].isEditing = false;
        },

        // 6. SAVE SUBCATEGORY
        saveSubcategory(catIndex, subIndex) {
            const sub = this.categories[catIndex].subcategories[subIndex];
            
            fetch(`/admin/subcategories/${sub.id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    name: sub.name,
                    points: sub.points,
                    description: sub.description,
                    is_mandatory: !!sub.is_mandatory
                })
            })
            .then(res => res.ok ? res.json() : Promise.reject(res))
            .then(() => {
                sub.isEditing = false;
                this.showAlert('success', 'Diperbarui', 'Subkategori berhasil diperbarui.');
                // Reload categories dari API untuk sinkronisasi
                this.loadCategories();
            })
            .catch(() => this.showAlert('error', 'Kesalahan', 'Gagal memperbarui subkategori'));
        },

        // 7. DELETE SUBCATEGORY - Open Modal
        deleteSubcategoryPrompt(catIndex, subIndex) {
            const sub = this.categories[catIndex].subcategories[subIndex];
            this.deleteTargetSubcategory = sub.name;
            this.deleteCategoryIndex = catIndex;
            this.deleteSubcategoryIndex = subIndex;
            this.showDeleteSubcategoryModal = true;
        },
        
        // Confirm Delete Subcategory
        confirmDeleteSubcategory() {
            const catIndex = this.deleteCategoryIndex;
            const subIndex = this.deleteSubcategoryIndex;
            const sub = this.categories[catIndex].subcategories[subIndex];
            this.showDeleteSubcategoryModal = false;

            fetch(`/admin/subcategories/${sub.id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(async res => {
                const contentType = (res.headers.get('content-type') || '').toLowerCase();
                const bodyText = await res.text();

                // If response is JSON, parse it; otherwise keep raw text
                let parsed = null;
                if (contentType.includes('application/json')) {
                    try { parsed = JSON.parse(bodyText); } catch (e) { parsed = null; }
                }

                if (!res.ok) {
                    const msg = parsed && parsed.message ? parsed.message : (bodyText || 'Server error');
                    throw new Error(msg);
                }

                return parsed || { message: bodyText };
            })
            .then(json => {
                const msg = (json && json.message) ? json.message : 'Subkategori telah dihapus atau dinonaktifkan. Memuat ulang...';
                this.showAlert('success', 'Berhasil', msg);
                // Reload categories dari API untuk sinkronisasi
                this.loadCategories();
            })
            .catch(err => this.showAlert('error', 'Gagal', err.message || 'Terjadi kesalahan yang tidak diketahui'))
            .finally(() => {
                this.deleteCategoryIndex = null;
                this.deleteSubcategoryIndex = null;
                this.deleteTargetSubcategory = null;
            });
        },

        handleBulkFileDrop(e) {
            this.dragActiveBulk = false;
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
                if (!isPdf) {
                    this.showAlert('error', 'File Tidak Valid', 'Pengumpulan bukti hanya melalui PDF.');
                    return;
                }
                // Validate file size (10MB max)
                if (file.size > 10 * 1024 * 1024) {
                    this.showAlert('error', 'File Terlalu Besar', 'Ukuran file maksimum adalah 10MB');
                    return;
                }
                // Set file name and sync to input
                this.bulkFileName = file.name;
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                if (this.$refs.bulkCertificate) {
                    this.$refs.bulkCertificate.files = dataTransfer.files;
                }
            }
        },

        handleBulkCertificateChange(e) {
            const file = e.target.files && e.target.files[0] ? e.target.files[0] : null;
            if (!file) {
                this.bulkFileName = '';
                return;
            }

            const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
            if (!isPdf) {
                this.showAlert('error', 'File Tidak Valid', 'Pengumpulan bukti hanya melalui PDF.');
                this.bulkFileName = '';
                e.target.value = '';
                return;
            }

            if (file.size > 10 * 1024 * 1024) {
                this.showAlert('error', 'File Terlalu Besar', 'Ukuran file maksimum adalah 10MB');
                this.bulkFileName = '';
                e.target.value = '';
                return;
            }

            this.bulkFileName = file.name;
        },

        // Bulk Score Management
        async applyBulkScore() {
            if (!this.bulkScore.mainCategory || !this.bulkScore.subcategory) {
                this.showAlert('error', 'Kesalahan', 'Harap pilih kategori dan subkategori');
                return;
            }
            
            if (!this.bulkScore.activityTitle || !this.bulkScore.description || !this.bulkScore.activityDate) {
                this.showAlert('error', 'Kesalahan', 'Harap isi semua kolom wajib');
                return;
            }
            
            this.bulkScore.isSubmitting = true;
            
            try {
                const formData = new FormData();
                formData.append('selectedMajor', this.bulkScore.selectedMajor);
                // Only send year if specific year is selected
                if (this.bulkScore.yearMode === 'specific' && this.bulkScore.selectedYear) {
                    formData.append('selectedYear', this.bulkScore.selectedYear);
                } else {
                    formData.append('selectedYear', '');
                }
                formData.append('selectedShift', this.bulkScore.selectedShift);
                formData.append('mainCategory', this.bulkScore.mainCategory);
                formData.append('subcategory', this.bulkScore.subcategory);
                formData.append('activityTitle', this.bulkScore.activityTitle);
                formData.append('description', this.bulkScore.description);
                formData.append('activityDate', this.bulkScore.activityDate);
                
                if (this.$refs.bulkCertificate && this.$refs.bulkCertificate.files.length > 0) {
                    formData.append('certificate', this.$refs.bulkCertificate.files[0]);
                }
                
                const response = await fetch('/admin/bulk-score', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });
                
                if (!response.ok) {
                    // Handle non-2xx HTTP responses
                    let errorMessage = `Server error: ${response.status} ${response.statusText}`;
                    try {
                        const errorData = await response.json();
                        if (errorData.message) {
                            errorMessage = errorData.message;
                        }
                    } catch (e) {
                        // Response is not JSON, use status message
                    }
                    throw new Error(errorMessage);
                }
                
                const result = await response.json();
                
                if (result.success) {
                    this.showAlert('success', 'Berhasil', result.message);
                    this.resetBulkScoreForm();
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    this.showAlert('error', 'Kesalahan', result.message || 'Gagal membuat pengajuan');
                }
            } catch (error) {
                console.error('Bulk score error:', error);
                const errorMsg = error.message || 'Gagal terhubung ke server. Pastikan server berjalan lalu coba lagi.';
                this.showAlert('error', 'Kesalahan', errorMsg);
            } finally {
                this.bulkScore.isSubmitting = false;
            }
        },
        
        resetBulkScoreForm() {
            this.bulkScore.selectedMajor = '';
            this.bulkScore.yearMode = 'all';
            this.bulkScore.selectedYear = '';
            this.bulkScore.selectedShift = '';
            this.bulkScore.mainCategory = '';
            this.bulkScore.subcategory = '';
            this.bulkScore.activityTitle = '';
            this.bulkScore.description = '';
            this.bulkScore.activityDate = '';
            if (this.$refs.bulkCertificate) {
                this.$refs.bulkCertificate.value = '';
            }
        }
    }
}
</script>
</body>
</html>










