<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Dashboard - S-Core ITBSS</title>
    <script src="https://cdn.tailwindcss.com?v={{ time() }}"></script>
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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js?v={{ time() }}"></script>
    <style>
        @keyframes slideUp {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
        }
        .animate-slide-up {
            animation: slideUp 0.25s ease-out;
        }
        /* Force narrow sidebar on small screens */
        @media (max-width: 666px) {
            .sidebar-container {
                width: 3.5rem !important; /* w-14 = 56px */
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
    </style>
</head>
<body>
    <div class="flex h-screen bg-gray-100" x-data="dashboardData()" x-init="refreshSubmissionCategories(); loadCategories()">
        <!-- Logout Confirmation Modal -->
        <div x-show="showLogoutModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999]" style="display: none;">
            <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4">
                <h3 class="text-lg font-semibold mb-4">Konfirmasi Keluar</h3>
                <p class="text-gray-600 mb-6">Apakah Anda yakin ingin keluar?</p>
                <div class="flex gap-3 justify-end">
                    <button @click="showLogoutModal = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm font-medium">Tidak</button>
                    <button @click="confirmLogout" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded text-sm font-medium">Ya</button>
                </div>
            </div>
        </div>

        <!-- Add New Activity Modal - Full Screen -->
        <div x-show="showAddModal" class="fixed inset-0 bg-black bg-opacity-50 z-[9999]" style="display: none;">
            <div class="h-full w-full bg-white flex flex-col">
                <div class="bg-white border-b px-4 sm:px-6 py-3 sm:py-4 flex justify-between items-center">
                    <h3 class="text-lg sm:text-xl font-semibold">Ajukan S-Core Baru</h3>
                    <button @click="closeModal" class="text-gray-500 hover:text-gray-700 text-2xl leading-none w-8 h-8 flex items-center justify-center">×</button>
                </div>

                <div class="flex-1 overflow-y-auto">
                    <div class="flex flex-col lg:flex-row w-full h-full">
                        <!-- Form Details Section - Appears First on Mobile -->
                        <div class="w-full lg:w-1/2 flex flex-col order-1 lg:order-2">
                            <div class="flex-1 overflow-y-auto p-4 sm:p-6">
                                <div class="space-y-3 sm:space-y-4">
                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Mahasiswa</label>
                                        <div class="bg-gray-50 border rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm text-gray-700">{{ Auth::user()->name }}</div>
                                    </div>

                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Kategori Utama <span class="text-red-500">*</span></label>
                                        <select x-model="formData.mainCategory" @change="updateAvailableSubcategoriesForSubmission()" class="w-full border rounded-lg px-3 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="">Pilih Kategori Utama</option>
                                            <template x-for="(catGroup, idx) in submissionCategoryGroups" :key="catGroup.id">
                                                <option :value="idx" x-text="(idx + 1) + '. ' + catGroup.name"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <div x-show="formData.mainCategory !== ''">
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Subkategori <span class="text-red-500">*</span></label>
                                        <select x-model="formData.subcategory" class="w-full border rounded-lg px-3 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="">Pilih Subkategori</option>
                                            <template x-for="(sub, idx) in availableSubcategories" :key="idx">
                                                <option :value="sub.name" x-text="sub.name + ' (' + sub.points + ' poin)'"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Judul Kegiatan <span class="text-red-500">*</span></label>
                                        <input type="text" x-model="formData.activityTitle" class="w-full border rounded-lg px-3 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan judul kegiatan" />
                                    </div>

                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Deskripsi <span class="text-red-500">*</span></label>
                                        <textarea x-model="formData.description" rows="3" class="w-full border rounded-lg px-3 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan deskripsi"></textarea>
                                    </div>

                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Tanggal Kegiatan <span class="text-red-500">*</span></label>
                                        <input type="date" x-model="formData.activityDate" :max="maxDate" class="w-full border rounded-lg px-3 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                        <p class="text-xs text-gray-500 mt-1">Maksimal 1 bulan dari tanggal kegiatan</p>
                                        <p x-show="dateValidationError" class="text-xs text-red-500 mt-1" x-text="dateValidationError"></p>
                                    </div>

                                    <!-- Upload Section - Appears After Date on Mobile, Hidden on Desktop (shown in left column) -->
                                    <div class="lg:hidden">
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Unggah Sertifikat/Bukti <span class="text-red-500">*</span></label>
                                        <div 
                                            class="border-2 border-dashed border-gray-300 rounded-lg flex flex-col items-center justify-center p-6 sm:p-8 hover:border-blue-400 transition-colors cursor-pointer"
                                            @dragover.prevent="dragActive = true"
                                            @dragleave.prevent="dragActive = false"
                                            @drop.prevent="handleFileDrop"
                                            :class="dragActive ? 'bg-blue-50 border-blue-400' : ''"
                                            @click="$refs.fileInput.click()"
                                        >
                                            <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-400 mb-3 sm:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            <p class="text-gray-600 text-xs sm:text-sm mb-2 text-center">Seret dan lepas file PDF Anda di sini</p>
                                            <p class="text-gray-400 text-xs mb-3 sm:mb-4">atau</p>
                                            <label class="cursor-pointer" @click.stop>
                                                <span class="bg-blue-500 hover:bg-blue-600 text-white px-4 sm:px-6 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-medium inline-block">Pilih File</span>
                                                <input type="file" accept=".pdf" class="hidden" x-ref="fileInput" @change="handleFileSelect" />
                                            </label>
                                            <p class="text-gray-400 text-xs mt-3 sm:mt-4">Hanya PDF - Maksimal 10MB</p>
                                        </div>
                                        <div x-show="formData.fileName" class="mt-3 sm:mt-4 p-2.5 sm:p-3 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span class="text-xs sm:text-sm text-gray-700 truncate" x-text="formData.fileName"></span>
                                            </div>
                                            <button @click="clearSelectedFile()" class="text-red-500 hover:text-red-700 flex-shrink-0">
                                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Fixed Action Buttons -->
                            <div class="border-t bg-white px-4 sm:px-6 py-3 sm:py-4">
                                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 justify-end">
                                    <button @click="closeModal" class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-2.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-xs sm:text-sm font-medium transition-colors">Batal</button>
                                    <button 
                                        @click="showSubmitConfirmation" 
                                        :disabled="isSubmitting"
                                        :class="isSubmitting ? 'bg-blue-400 cursor-not-allowed' : 'bg-blue-500 hover:bg-blue-600'"
                                        class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-2.5 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors flex items-center justify-center gap-2">
                                        
                                        <span x-show="!isSubmitting">Kirim untuk Ditinjau</span>
                                        
                                        <span x-show="isSubmitting" class="flex items-center gap-2">
                                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Memproses...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Upload Section - Left Column on Desktop (Hidden on Mobile, shown inline after date) -->
                        <div class="w-full lg:w-1/2 bg-gray-100 lg:border-r overflow-auto p-4 sm:p-6 order-2 lg:order-1 hidden lg:block">
                            <div class="bg-white rounded-lg shadow-sm h-full flex flex-col p-4 sm:p-6">
                                <h4 class="font-semibold text-gray-800 mb-3 sm:mb-4 text-sm sm:text-base">Unggah Sertifikat/Bukti</h4>
                                <div 
                                    class="flex-1 border-2 border-dashed border-gray-300 rounded-lg flex flex-col items-center justify-center p-6 sm:p-8 hover:border-blue-400 transition-colors cursor-pointer min-h-[300px]"
                                    @dragover.prevent="dragActive = true"
                                    @dragleave.prevent="dragActive = false"
                                    @drop.prevent="handleFileDrop"
                                    :class="dragActive ? 'bg-blue-50 border-blue-400' : ''"
                                    @click="$refs.fileInputDesktop.click()"
                                >
                                    <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-400 mb-3 sm:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="text-gray-600 text-xs sm:text-sm mb-2 text-center">Seret dan lepas file PDF Anda di sini</p>
                                    <p class="text-gray-400 text-xs mb-3 sm:mb-4">atau</p>
                                    <label class="cursor-pointer" @click.stop>
                                        <span class="bg-blue-500 hover:bg-blue-600 text-white px-4 sm:px-6 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-medium inline-block">Pilih File</span>
                                        <input type="file" accept=".pdf" class="hidden" x-ref="fileInputDesktop" @change="handleFileSelect" />
                                    </label>
                                    <p class="text-gray-400 text-xs mt-3 sm:mt-4">Hanya PDF - Maksimal 10MB</p>
                                </div>
                                <div x-show="formData.fileName" class="mt-3 sm:mt-4 p-2.5 sm:p-3 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-between">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span class="text-xs sm:text-sm text-gray-700 truncate" x-text="formData.fileName"></span>
                                    </div>
                                    <button @click="clearSelectedFile()" class="text-red-500 hover:text-red-700 flex-shrink-0 ml-2">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirmation Modal - Review Before Submit -->
        <div x-show="showConfirmSubmitModal" class="fixed inset-0 bg-black bg-opacity-50 z-[10000] flex items-center justify-center p-4" style="display: none;">
            <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-4 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-xl font-bold">Tinjau Pengajuan Anda</h3>
                    </div>
                    <button @click="showConfirmSubmitModal = false" class="text-white hover:text-gray-200 text-2xl leading-none w-8 h-8 flex items-center justify-center">×</button>
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-y-auto p-6">
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-r-lg">
                        <div class="flex gap-3">
                            <svg class="w-6 h-6 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <p class="font-semibold text-yellow-800 mb-1">Harap Periksa dengan Teliti</p>
                                <p class="text-sm text-yellow-700">Pastikan semua informasi sudah benar sebelum dikirim. Anda dapat memperbaiki pengajuan yang ditolak nanti.</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Student -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Mahasiswa</label>
                            <p class="text-sm font-medium text-gray-800">{{ Auth::user()->name }}</p>
                        </div>

                        <!-- Main Category -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Kategori Utama</label>
                            <p class="text-sm font-medium text-gray-800" x-text="formData.mainCategory !== '' && formData.mainCategory !== null && submissionCategoryGroups[formData.mainCategory] ? (formData.mainCategory + 1) + '. ' + submissionCategoryGroups[formData.mainCategory].name : '-'"></p>
                        </div>

                        <!-- Subcategory -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Subkategori</label>
                            <p class="text-sm font-medium text-gray-800" x-text="formData.subcategory || '-'"></p>
                            <template x-if="formData.subcategory">
                                <p class="text-xs text-blue-600 font-semibold mt-1">
                                    Poin: <span x-text="availableSubcategories.find(s => s.name === formData.subcategory)?.points || 0"></span>
                                </p>
                            </template>
                        </div>

                        <!-- Activity Title -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Judul Kegiatan</label>
                            <p class="text-sm font-medium text-gray-800" x-text="formData.activityTitle || '-'"></p>
                        </div>

                        <!-- Description -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Deskripsi</label>
                            <p class="text-sm text-gray-800 whitespace-pre-wrap" x-text="formData.description || '-'"></p>
                        </div>

                        <!-- Activity Date -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Tanggal Kegiatan</label>
                            <p class="text-sm font-medium text-gray-800" x-text="formData.activityDate || '-'"></p>
                        </div>

                        <!-- Certificate File -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">File Sertifikat/Bukti</label>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <p class="text-sm font-medium text-gray-800" x-text="formData.fileName || '-'"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="border-t bg-gray-50 px-6 py-4">
                    <div class="flex flex-col sm:flex-row gap-3 justify-end">
                        <button 
                            @click="showConfirmSubmitModal = false" 
                            class="w-full sm:w-auto px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition-colors order-2 sm:order-1">
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                                </svg>
                                Kembali Mengedit
                            </span>
                        </button>
                        <button 
                            @click="saveActivity" 
                            :disabled="isSubmitting"
                            :class="isSubmitting ? 'bg-green-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700'"
                            class="w-full sm:w-auto px-6 py-2.5 text-white rounded-lg text-sm font-bold transition-colors shadow-lg order-1 sm:order-2">
                            <span x-show="!isSubmitting" class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Confirm & Submit
                            </span>
                            <span x-show="isSubmitting" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Submitting...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Activity Modal - Full Screen -->
        <div x-show="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 z-[9999]" style="display: none;">
            <div class="h-full w-full bg-white flex flex-col">
                <div class="bg-white border-b px-6 py-4 flex justify-between items-center">
                    <h3 class="text-xl font-semibold">Edit S-Core Submission</h3>
                    <button @click="closeModal" class="text-gray-500 hover:text-gray-700 text-2xl leading-none w-8 h-8 flex items-center justify-center">×</button>
                </div>

                <div class="flex-1 overflow-hidden flex">
                    <template x-if="selectedActivity">
                        <div class="flex w-full h-full">
                            <!-- Left Column - PDF Viewer with Upload Option -->
                            <div class="w-1/2 bg-gray-100 border-r overflow-hidden p-6">
                                <div class="bg-white rounded-lg shadow-sm h-full flex flex-col">
                                    <div class="bg-gray-800 text-white px-4 py-3 rounded-t-lg flex items-center justify-between">
                                        <span class="text-sm font-medium">Certificate/Evidence</span>
                                        <span class="text-xs text-gray-300" x-text="selectedActivity ? (selectedActivity.certificate || 'document.pdf') : ''"></span>
                                    </div>

                                    <div class="flex-1 bg-gray-50 relative h-full overflow-hidden">
                                        <!-- Current PDF Display -->
                                        <template x-if="selectedActivity && selectedActivity.file_url && !editShowUploadBox && editPdfKey >= 0">
                                            <div class="relative w-full h-full" :key="editPdfKey">
                                                <!-- Loading spinner -->
                                                <div x-show="editPdfLoading" class="absolute inset-0 flex items-center justify-center bg-gray-50 z-10">
                                                    <div class="text-center">
                                                        <svg class="animate-spin h-12 w-12 text-blue-600 mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                        </svg>
                                                        <p class="text-sm text-gray-600">Loading PDF...</p>
                                                    </div>
                                                </div>
                                                <!-- PDF iframe -->
                                                <iframe
                                                    :src="selectedActivity.file_url + '?v=' + pdfTimestamp"
                                                    @load="editPdfLoading = false"
                                                    class="w-full h-full"
                                                    style="border: none;"
                                                    type="application/pdf"
                                                ></iframe>
                                                <!-- Change PDF Button Overlay -->
                                                <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2">
                                                    <button @click="editShowUploadBox = true" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-lg flex items-center gap-2">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                        </svg>
                                                        Ganti PDF
                                                    </button>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Upload Box (shown when changing PDF or no PDF exists) -->
                                        <template x-if="!selectedActivity || !selectedActivity.file_url || editShowUploadBox">
                                            <div class="p-6 h-full flex flex-col">
                                                <div class="flex justify-between items-center mb-4">
                                                    <h4 class="font-semibold text-gray-800">Perbarui Sertifikat/Bukti</h4>
                                                    <button x-show="selectedActivity && selectedActivity.file_url && editShowUploadBox" @click="editShowUploadBox = false; formData.fileName = ''" class="text-gray-500 hover:text-gray-700 text-sm">
                                                        Batal
                                                    </button>
                                                </div>
                                                <div class="flex-1 border-2 border-dashed border-gray-300 rounded-lg flex flex-col items-center justify-center p-8 hover:border-blue-400 transition-colors cursor-pointer">
                                                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                    </svg>
                                                    <p class="text-gray-600 text-sm mb-2">Seret dan lepas file PDF Anda di sini</p>
                                                    <p class="text-gray-400 text-xs mb-4">atau</p>
                                                    <label class="cursor-pointer" @click.stop>
                                                        <span class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-medium inline-block">Pilih File</span>
                                                        <input type="file" accept=".pdf" class="hidden" x-ref="fileInputEdit" @change="handleEditFileSelect($event)" />
                                                    </label>
                                                    <p class="text-gray-400 text-xs mt-4">Hanya PDF - Maksimal 10MB</p>
                                                </div>
                                                <div x-show="formData.fileName" class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-between">
                                                    <div class="flex items-center gap-2">
                                                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        <span class="text-sm text-gray-700" x-text="formData.fileName"></span>
                                                    </div>
                                                    <button @click="
                                                        formData.fileName = ''; 
                                                        $refs.fileInputEdit.value = ''; 
                                                        if(selectedActivity.originalFileUrl) {
                                                            selectedActivity.file_url = selectedActivity.originalFileUrl;
                                                            pdfTimestamp = Date.now();
                                                            editPdfKey++;
                                                            editPdfLoading = true;
                                                        }
                                                    " class="text-red-500 hover:text-red-700">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column - Form Details -->
                            <div class="w-1/2 flex flex-col">
                                <div class="flex-1 overflow-y-auto p-6">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Mahasiswa</label>
                                            <div class="bg-gray-50 border rounded-lg px-4 py-3 text-sm text-gray-700">
                                                {{ $user->student_id }} - {{ $user->name }}
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori Utama <span class="text-red-500">*</span></label>
                                            <select x-model="formData.mainCategory" @change="updateAvailableSubcategories()" class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="">Pilih Kategori Utama</option>
                                                <template x-for="(catGroup, idx) in categoryGroups" :key="catGroup.id">
                                                    <option :value="idx" x-text="(idx + 1) + '. ' + catGroup.name"></option>
                                                </template>
                                            </select>
                                        </div>

                                        <div x-show="formData.mainCategory !== ''">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Subkategori <span class="text-red-500">*</span></label>
                                            <select x-model="formData.subcategory" class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="">Pilih Subkategori</option>
                                                <template x-for="(sub, idx) in availableSubcategories" :key="idx">
                                                    <option :value="sub.name" x-text="sub.name + ' (' + sub.points + ' poin)'"></option>
                                                </template>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Judul Kegiatan <span class="text-red-500">*</span></label>
                                            <input type="text" x-model="formData.activityTitle" class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan judul kegiatan" />
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi <span class="text-red-500">*</span></label>
                                            <textarea x-model="formData.description" rows="4" class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan deskripsi"></textarea>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kegiatan <span class="text-red-500">*</span></label>
                                            <input type="date" x-model="formData.activityDate" class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Fixed Action Buttons -->
                                <div class="border-t bg-white px-6 py-4">
                                    <div class="flex gap-3 justify-end">
                                        <button @click="closeModal" :disabled="isSubmitting" class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 disabled:bg-gray-300 disabled:cursor-not-allowed rounded-lg text-sm font-medium transition-colors">Batal</button>
                                        <button @click="updateActivity" :disabled="isSubmitting" class="px-6 py-2.5 bg-blue-500 hover:bg-blue-600 disabled:bg-blue-400 disabled:cursor-not-allowed text-white rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                                            <span x-show="!isSubmitting">Perbarui</span>
                                            <span x-show="isSubmitting" class="flex items-center gap-2">
                                                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                                </svg>
                                                Memproses...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Mobile Detail Modal -->
        <div x-show="showMobileDetailModal" class="fixed inset-0 bg-black bg-opacity-50 z-[9998] flex items-end justify-center" style="display: none;" @click.self="showMobileDetailModal = false; mobileDetailActivity = null">
            <div class="bg-white w-full max-h-[85vh] rounded-t-2xl overflow-hidden flex flex-col animate-slide-up" @click.stop>
                <!-- Handle bar -->
                <div class="flex justify-center pt-3 pb-1">
                    <div class="w-10 h-1 bg-gray-300 rounded-full"></div>
                </div>
                <!-- Header -->
                <div class="px-4 pb-3 border-b flex justify-between items-center">
                    <h3 class="text-base font-semibold text-gray-800">Detail Aktivitas</h3>
                    <button @click="showMobileDetailModal = false; mobileDetailActivity = null" class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <!-- Body -->
                <template x-if="mobileDetailActivity">
                    <div class="flex-1 overflow-y-auto px-4 py-4 space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Judul Kegiatan</label>
                            <p class="text-sm font-medium text-gray-800" x-text="mobileDetailActivity.judul"></p>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Kategori Utama</label>
                                <p class="text-sm text-gray-700" x-text="mobileDetailActivity.mainCategory"></p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Subkategori</label>
                                <p class="text-sm text-gray-700" x-text="mobileDetailActivity.subcategory"></p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Deskripsi</label>
                            <p class="text-sm text-gray-700 whitespace-pre-wrap" x-text="mobileDetailActivity.keterangan"></p>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Poin</label>
                                <p class="text-sm font-semibold text-gray-800" x-text="mobileDetailActivity.point"></p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Waktu Input</label>
                                <p class="text-xs text-gray-700" x-text="mobileDetailActivity.waktu"></p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Status</label>
                                <span :class="{
                                    'bg-green-100 text-green-700': mobileDetailActivity.status === 'Approved',
                                    'bg-yellow-100 text-yellow-700': mobileDetailActivity.status === 'Waiting',
                                    'bg-red-100 text-red-700': mobileDetailActivity.status === 'Rejected'
                                }" class="px-2.5 py-1 rounded-full text-xs font-semibold inline-block" x-text="translateStatus(mobileDetailActivity.status)"></span>
                            </div>
                        </div>

                        <!-- Rejection info if rejected -->
                        <template x-if="mobileDetailActivity.status === 'Rejected' && (mobileDetailActivity.rejectionReason || mobileDetailActivity.rejection_reason)">
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                <p class="text-xs font-semibold text-red-800 uppercase tracking-wide mb-1">Alasan Penolakan</p>
                                <p class="text-sm text-red-700" x-text="mobileDetailActivity.rejectionReason || mobileDetailActivity.rejection_reason"></p>
                            </div>
                        </template>

                        <template x-if="mobileDetailActivity.pointAdjustmentReason">
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3">
                                <p class="text-xs font-semibold text-amber-800 uppercase tracking-wide mb-1">Alasan Pengurangan Poin</p>
                                <p class="text-sm text-amber-700" x-text="mobileDetailActivity.pointAdjustmentReason"></p>
                            </div>
                        </template>

                        <!-- Action buttons -->
                        <div class="pt-2 flex gap-2">
                            <template x-if="mobileDetailActivity.status === 'Waiting'">
                                <div class="flex gap-2 w-full">
                                    <button @click="let a = mobileDetailActivity; showMobileDetailModal = false; mobileDetailActivity = null; openDeleteModal(a);" 
                                        class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2.5 px-4 rounded-lg text-sm font-medium flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Hapus
                                    </button>
                                </div>
                            </template>
                            <template x-if="mobileDetailActivity.status !== 'Waiting'">
                                <button @click="let a = mobileDetailActivity; showMobileDetailModal = false; mobileDetailActivity = null; openViewModal(a);"
                                    class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2.5 px-4 rounded-lg text-sm font-medium flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Lihat Detail Lengkap
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- View Activity Modal - Full Screen -->
        <div x-show="showViewModal" class="fixed inset-0 bg-black bg-opacity-50 z-[9999]" style="display: none;">
            <div class="h-full w-full bg-white flex flex-col">
                <div class="bg-white border-b px-4 sm:px-6 py-3 sm:py-4 flex justify-between items-center flex-shrink-0">
                    <h3 class="text-lg sm:text-xl font-semibold">Lihat Pengajuan S-Core</h3>
                    <button @click="closeModal" class="text-gray-500 hover:text-gray-700 text-2xl leading-none w-8 h-8 flex items-center justify-center">×</button>
                </div>

                <div class="flex-1 overflow-hidden flex flex-col lg:flex-row">
                    <template x-if="selectedActivity">
                        <div class="flex flex-col lg:flex-row w-full h-full">
                            <!-- Left Column - PDF Viewer -->
                            <div class="w-full lg:w-1/2 bg-gray-100 lg:border-r overflow-hidden p-4 sm:p-6 order-2 lg:order-1 max-h-[50vh] lg:max-h-none flex-shrink-0 lg:flex-shrink-1">
                                <div class="bg-white rounded-lg shadow-sm h-full flex flex-col">
                                    <div class="bg-gray-800 text-white px-3 sm:px-4 py-2 sm:py-3 rounded-t-lg flex items-center justify-between">
                                        <span class="text-xs sm:text-sm font-medium">Certificate/Evidence</span>
                                        <span class="text-xs text-gray-300 truncate ml-2" x-text="selectedActivity ? (selectedActivity.certificate || 'document.pdf') : ''"></span>
                                    </div>

                                    <div class="flex-1 bg-gray-50 relative h-full overflow-hidden">
                                        <template x-if="!selectedActivity">
                                            <div class="flex items-center justify-center h-full text-gray-400 text-xs sm:text-sm">
                                                pilih submission...
                                            </div>
                                        </template>

                                        <template x-if="selectedActivity">
                                            <div class="h-full w-full">
                                                <template x-if="selectedActivity.file_url">
                                                    <div class="h-full flex flex-col bg-gray-200">
                                                        <iframe 
                                                            :src="selectedActivity.file_url" 
                                                            class="w-full flex-1" 
                                                            style="border: none;" 
                                                            type="application/pdf">
                                                        </iframe>
                                                    
                                                    </div>
                                                </template>

                                                <template x-if="!selectedActivity.file_url">
                                                    <div class="flex items-center justify-center h-full text-red-500 flex-col gap-2">
                                                        <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                                        <p class="text-xs sm:text-sm text-center px-4">File PDF tidak ditemukan di database.</p>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <!-- Right Column - Details -->
                            <div class="w-full lg:w-1/2 flex flex-col order-1 lg:order-2 min-h-0">
                                <div class="flex-1 overflow-y-auto p-4 sm:p-6">
                                    <div class="space-y-3 sm:space-y-4">
                                        <div>
                                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Mahasiswa</label>
                                            <div class="bg-gray-50 border rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm text-gray-700">{{ Auth::user()->name }}</div>
                                        </div>

                                        <div>
                                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Kategori Utama</label>
                                            <div class="bg-gray-50 border rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm" x-text="selectedActivity.mainCategory"></div>
                                        </div>

                                        <div>
                                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Subkategori</label>
                                            <div class="bg-gray-50 border rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm" x-text="selectedActivity.subcategory"></div>
                                        </div>

                                        <div>
                                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Judul Kegiatan</label>
                                            <div class="bg-gray-50 border rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-medium" x-text="selectedActivity.judul"></div>
                                        </div>

                                        <div>
                                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Deskripsi</label>
                                            <div class="bg-gray-50 border rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm whitespace-pre-wrap min-h-[80px] sm:min-h-[100px]" x-text="selectedActivity.keterangan"></div>
                                        </div>

                                        <div>
                                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Waktu Pengajuan</label>
                                            <div class="bg-gray-50 border rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm" x-text="selectedActivity.waktu"></div>
                                        </div>

                                        <div>
                                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Status</label>
                                            <div>
                                                <span :class="{
                                                    'bg-green-100 text-green-700': selectedActivity.status === 'Approved',
                                                    'bg-yellow-100 text-yellow-700': selectedActivity.status === 'Waiting',
                                                    'bg-red-100 text-red-700': selectedActivity.status === 'Rejected'
                                                }" class="px-2 sm:px-3 py-1 rounded-full text-xs font-semibold" x-text="translateStatus(selectedActivity.status)"></span>
                                            </div>
                                        </div>

                                        <!-- Rejection Details Section (Only for Rejected) -->
                                        <template x-if="selectedActivity.status === 'Rejected'">
                                            <div class="bg-red-50 border border-red-200 rounded-lg p-3 sm:p-4 mb-3 sm:mb-6 animate-pulse-once">
                                                <div class="flex gap-2 sm:gap-3">
                                                    <div class="flex-shrink-0">
                                                        <svg class="h-4 w-4 sm:h-5 sm:w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1">
                                                        <h3 class="text-xs sm:text-sm font-bold text-red-800 uppercase tracking-wide mb-1">
                                                            Pengajuan Ditolak
                                                        </h3>
                                                        
                                                        <div class="text-xs sm:text-sm text-red-700 bg-white bg-opacity-50 p-2 sm:p-3 rounded border border-red-100 mb-2 sm:mb-3">
                                                            <span class="font-semibold">Alasan:</span>
                                                            <span x-text="selectedActivity.rejectionReason || 'Tidak ada alasan spesifik yang diberikan.'"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <template x-if="selectedActivity.pointAdjustmentReason">
                                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 sm:p-4 mb-3 sm:mb-6">
                                                <div class="flex gap-2 sm:gap-3">
                                                    <div class="flex-shrink-0">
                                                        <svg class="h-4 w-4 sm:h-5 sm:w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1">
                                                        <h3 class="text-xs sm:text-sm font-bold text-amber-800 uppercase tracking-wide mb-1">Poin Dikurangi Admin</h3>
                                                        <div class="text-xs sm:text-sm text-amber-700 bg-white bg-opacity-60 p-2 sm:p-3 rounded border border-amber-100">
                                                            <span class="font-semibold">Alasan:</span>
                                                            <span x-text="selectedActivity.pointAdjustmentReason"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <div>
                                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Poin</label>
                                            <div class="bg-gray-50 border rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-bold" x-text="selectedActivity.point || '-'"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Action Button -->
                            <div class="order-3 lg:hidden border-t bg-white px-4 sm:px-6 py-3 sm:py-4 flex-shrink-0">
                                <div class="flex justify-end">
                                    <button @click="closeModal" class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-2.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-xs sm:text-sm font-medium transition-colors">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="hidden lg:block border-t bg-white px-4 sm:px-6 py-3 sm:py-4 flex-shrink-0">
                    <div class="flex justify-end">
                        <button @click="closeModal" class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-2.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-xs sm:text-sm font-medium transition-colors">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div x-show="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-[9999]" style="display: none;">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 shadow-2xl">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-red-600">Batalkan Pengajuan</h3>
                    <button @click="showDeleteModal = false" class="text-gray-500 hover:text-gray-700 text-2xl leading-none">×</button>
                </div>
                
                <template x-if="selectedActivity">
                    <div>
                        <p class="text-gray-600 mb-4">Apakah Anda yakin ingin membatalkan pengajuan ini?</p>
                        <div class="bg-gray-50 border rounded-lg p-3 mb-6">
                            <p class="text-sm font-medium text-gray-800" x-text="selectedActivity.judul"></p>
                            <p class="text-xs text-gray-500 mt-1"><span x-text="selectedActivity.mainCategory"></span> - <span x-text="selectedActivity.subcategory"></span></p>
                        </div>
                        <div class="flex gap-3 justify-end">
                            <button @click="showDeleteModal = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm font-medium">Tetap Simpan</button>
                            <button @click="confirmDelete" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium">Batalkan Pengajuan</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Generic Alert/Confirmation Modal -->
        <div x-show="showAlertModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-[9999]" style="display: none;">
            <div class="bg-white rounded-lg max-w-md w-full mx-4 shadow-2xl">
                <div class="p-6">
                    <div class="flex items-start gap-3 mb-4">
                        <!-- Icon based on alert type -->
                        <div class="flex-shrink-0">
                            <svg x-show="alertType === 'success'" class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <svg x-show="alertType === 'error'" class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <svg x-show="alertType === 'warning'" class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <svg x-show="alertType === 'info'" class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-lg mb-2" :class="{
                                'text-green-700': alertType === 'success',
                                'text-red-700': alertType === 'error',
                                'text-yellow-700': alertType === 'warning',
                                'text-blue-700': alertType === 'info'
                            }" x-text="alertTitle"></h3>
                            <p class="text-gray-600 whitespace-pre-line" x-text="alertMessage"></p>
                        </div>
                    </div>
                    <div class="flex gap-3 justify-end">
                        <button x-show="alertHasCancel" @click="closeAlertModal(false)" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm font-medium">Batal</button>
                        <button @click="closeAlertModal(true)" class="px-4 py-2 rounded text-sm font-medium text-white" :class="{
                            'bg-green-500 hover:bg-green-600': alertType === 'success',
                            'bg-red-500 hover:bg-red-600': alertType === 'error',
                            'bg-yellow-500 hover:bg-yellow-600': alertType === 'warning',
                            'bg-blue-500 hover:bg-blue-600': alertType === 'info'
                        }" x-text="alertHasCancel ? 'Konfirmasi' : 'OK'"></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div :class="isSidebarOpen ? 'w-64' : 'w-20'" class="sidebar-container bg-white shadow-lg transition-all duration-300 flex flex-col h-screen">
            <div class="p-4 border-b flex flex-col items-center flex-shrink-0">
                <img src="/images/logo.png" alt="Logo" class="w-12 h-12 object-contain">
                <div x-show="isSidebarOpen" class="sidebar-text mt-2 text-center">
                    <h2 class="text-sm font-bold text-gray-800">S-Core ITBSS</h2>
                    <p class="text-xs text-gray-500">Sistem Poin Mahasiswa Sabda Setia</p>
                </div>
            </div>

            <nav class="mt-4">
                <button @click="activeMenu = 'Dashboard'" class="w-full flex items-center py-3 text-left hover:bg-gray-100 transition-colors" :class="[
                    isSidebarOpen ? 'gap-3 px-4' : 'justify-center',
                    activeMenu === 'Dashboard' ? 'bg-blue-50 border-l-4 border-blue-500' : ''
                ]">
                    <svg class="w-6 h-6" :class="activeMenu === 'Dashboard' ? 'text-blue-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                    </svg>
                    <span x-show="isSidebarOpen" class="sidebar-text text-sm" :class="activeMenu === 'Dashboard' ? 'text-blue-700 font-medium' : 'text-gray-700'">Dashboard</span>
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

            <div class="border-t mt-auto flex-shrink-0">
                <button @click="showLogoutModal = true" class="w-full flex items-center py-3 text-left hover:bg-gray-100 transition-colors" :class="isSidebarOpen ? 'gap-3 px-4' : 'justify-center'">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span x-show="isSidebarOpen" class="sidebar-text text-sm text-red-500">Keluar</span>
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <div class="bg-white shadow-sm p-4 flex justify-between items-center sticky top-0 z-[100]">
                <button @click="isSidebarOpen = !isSidebarOpen" class="hamburger-btn p-2 hover:bg-gray-100 rounded">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium">{{ Auth::user()->name }}</span>
                </div>
            </div>

            <div class="flex-1 overflow-auto p-6">
                <!-- Dashboard Page -->
                <div x-show="activeMenu === 'Dashboard'">
                    <div class="mb-4 sm:mb-6">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 mb-4 sm:mb-6">
                            <div class="flex items-center gap-3 sm:gap-4">
                                <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800">S-Core</h1>
                            </div>

                            <button @click="showAddModal = true"
                                class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 sm:px-6 sm:py-3 rounded-lg text-xs sm:text-sm font-semibold flex items-center justify-center gap-2 shadow-md transition-all">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                <span class="hidden sm:inline">Tambah S-Core Baru</span>
                                <span class="sm:hidden">Tambah S-Core</span>
                            </button>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
                    <div class="bg-green-500 rounded-lg shadow p-3 sm:p-4">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center sm:justify-between gap-2">
                            <div class="flex-1">
                                <p class="text-xs sm:text-sm text-white">Poin Disetujui</p>
                                <p class="text-xl sm:text-2xl font-bold text-white" x-text="stats.approvedPoints"></p>
                            </div>
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-3 sm:p-4">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center sm:justify-between gap-2">
                            <div class="flex-1">
                                <p class="text-xs sm:text-sm text-gray-600">Menunggu Tinjauan</p>
                                <p class="text-xl sm:text-2xl font-bold text-yellow-600" x-text="stats.waiting"></p>
                            </div>
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-100 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-3 sm:p-4">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center sm:justify-between gap-2">
                            <div class="flex-1">
                                <p class="text-xs sm:text-sm text-gray-600">Disetujui</p>
                                <p class="text-xl sm:text-2xl font-bold text-green-600" x-text="stats.approved"></p>
                            </div>
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-3 sm:p-4">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center sm:justify-between gap-2">
                            <div class="flex-1">
                                <p class="text-xs sm:text-sm text-gray-600">Ditolak</p>
                                <p class="text-xl sm:text-2xl font-bold text-red-600" x-text="stats.rejected"></p>
                            </div>
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-red-100 rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- S-Core Eligibility & Report Section -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg shadow p-4 sm:p-6 mb-4 sm:mb-6 border-2 border-blue-200">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center sm:justify-between gap-3 sm:gap-0 mb-4">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h2 class="text-lg sm:text-xl font-bold text-gray-800">Laporan S-Core</h2>
                        </div>
                        <span id="reportEligibilityBadge" class="px-3 py-1.5 sm:px-4 sm:py-2 rounded-full text-xs sm:text-sm font-bold text-gray-600 bg-gray-200">Memeriksa...</span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4 mb-4">
                        <div class="bg-white rounded-lg p-3 sm:p-4">
                            <p class="text-xs sm:text-sm text-gray-600 mb-1">Minimum Poin Dibutuhkan</p>
                            <div class="flex items-baseline gap-2">
                                <span class="text-2xl sm:text-3xl font-bold" id="reportPoints">-</span>
                                <span class="text-xs sm:text-sm text-gray-500" id="minPointsLabel">/ 20 poin</span>
                            </div>
                            <div id="pointsStatusBar" class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div id="pointsProgressBar" class="h-full bg-blue-500 transition-all w-0"></div>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-lg p-3 sm:p-4">
                            <p class="text-xs sm:text-sm text-gray-600 mb-1">Kategori Selesai</p>
                            <div class="flex items-baseline gap-2">
                                <span class="text-2xl sm:text-3xl font-bold" id="reportCategories">-</span>
                                <span class="text-xs sm:text-sm text-gray-500" id="minCategoriesLabel">/ 6 kategori (min 5)</span>
                            </div>
                            <div id="categoriesStatusBar" class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div id="categoriesProgressBar" class="h-full bg-green-500 transition-all w-0"></div>
                            </div>
                        </div>
                    </div>

                    <button id="downloadReportBtn" 
                            @click="downloadSCoreReport()" 
                            disabled
                            class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white px-4 py-2.5 sm:px-6 sm:py-3 rounded-lg text-xs sm:text-sm font-bold flex items-center justify-center gap-2 transition-colors">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Unduh Laporan S-Core
                    </button>
                    <p id="reportMessage" class="text-xs text-center text-gray-500 mt-2">Memuat status kelayakan...</p>
                </div>

                <!-- Mandatory Categories Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden mb-4 sm:mb-6" x-data="{ expandedCategories: [false, false, false, false, false, false] }">
                    <div class="p-3 sm:p-6">
                        <h2 class="text-base sm:text-xl font-bold mb-3 sm:mb-4 text-center">Kategori Wajib S-Core</h2>
                    </div>

                    <!-- Desktop Table (>=1000px) -->
                    <div class="hidden min-[1000px]:block overflow-x-auto">
                        <table class="w-full border-collapse">
                        <thead>
                            <tr class="border-b-2 bg-gray-50">
                                <th class="text-left py-2 sm:py-3 px-2 sm:px-4 font-semibold text-xs sm:text-sm">Kategori</th>
                                <th class="text-center py-2 sm:py-3 px-2 sm:px-4 font-semibold text-xs sm:text-sm w-20 sm:w-24">Points</th>
                                <th class="text-center py-2 sm:py-3 px-2 sm:px-4 font-semibold text-xs sm:text-sm w-24 sm:w-32">Approved</th>
                                <th class="text-center py-2 sm:py-3 px-2 sm:px-4 font-semibold text-xs sm:text-sm w-24 sm:w-32">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(category, index) in mandatoryCategoryGroups" :key="index">
                                <tr>
                                    <td colspan="4" class="p-0">
                                        <table class="w-full">
                                            <tbody>
                                                <!-- Main Category Row -->
                                                <tr class="border-b bg-blue-50 hover:bg-blue-100 cursor-pointer" 
                                                    @click="expandedCategories[index] = !expandedCategories[index]">
                                                    <td class="py-2 sm:py-3 px-2 sm:px-4 font-bold text-gray-800">
                                                        <div class="flex items-center gap-1 sm:gap-2">
                                                            <svg class="w-3 h-3 sm:w-4 sm:h-4 transition-transform shrink-0" :class="expandedCategories[index] ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                            </svg>
                                                            <span class="text-xs sm:text-sm" x-text="(index + 1) + '. ' + category.name"></span>
                                                        </div>
                                                    </td>
                                                    <td class="text-center py-2 sm:py-3 px-2 sm:px-4 font-semibold text-xs sm:text-sm w-20 sm:w-24"></td>
                                                    <td class="text-center py-2 sm:py-3 px-2 sm:px-4 font-semibold text-blue-600 text-xs sm:text-sm w-24 sm:w-32" x-text="getCategoryTotal(category.subcategories, 'approvedCount')"></td>
                                                    <td class="text-center py-2 sm:py-3 px-2 sm:px-4 font-bold text-blue-600 text-xs sm:text-sm w-24 sm:w-32" x-text="getCategoryTotal(category.subcategories, 'totalPoints')"></td>
                                                </tr>
                                                
                                                <!-- Subcategories -->
                                                <template x-for="sub in category.subcategories" :key="sub.name">
                                                    <tr x-show="expandedCategories[index]" class="border-b hover:bg-gray-50" style="display: none;">
                                                        <td class="py-2 px-2 sm:px-4 pl-6 sm:pl-12 text-xs sm:text-sm text-gray-700 cursor-help relative" 
                                                            x-data="{ showTooltip: false }"
                                                            @mouseenter="showTooltip = true"
                                                            @mouseleave="showTooltip = false">
                                                            <span x-text="sub.name"></span>
                                                            <div x-show="showTooltip" 
                                                                class="absolute left-6 sm:left-12 top-full mt-1 bg-gray-800 text-white text-xs rounded py-2 px-3 z-50 w-48 sm:w-64 shadow-lg"
                                                                style="display: none;">
                                                                <p x-text="sub.description"></p>
                                                            </div>
                                                        </td>
                                                        <td class="text-center py-2 px-2 sm:px-4 text-xs sm:text-sm w-20 sm:w-24" x-text="sub.points"></td>
                                                        <td class="text-center py-2 px-2 sm:px-4 text-xs sm:text-sm w-24 sm:w-32" x-text="sub.approvedCount"></td>
                                                        <td class="text-center py-2 px-2 sm:px-4 text-xs sm:text-sm font-semibold text-gray-700 w-24 sm:w-32" x-text="sub.approvedCount * sub.points"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    </div>

                    <!-- Mobile Simple List (<1000px) -->
                    <div class="min-[1000px]:hidden">
                        <div class="border-b bg-gray-50 px-4 py-2.5 flex items-center">
                            <span class="flex-1 font-semibold text-xs text-gray-600 uppercase tracking-wide">Category</span>
                            <span class="w-16 text-center font-semibold text-xs text-gray-600 uppercase tracking-wide">Total</span>
                        </div>
                        <template x-for="(category, index) in mandatoryCategoryGroups" :key="'mc-'+index">
                            <div class="border-b px-4 py-3 flex items-center justify-between">
                                <span class="text-sm text-gray-800 font-medium flex-1 pr-3" x-text="(index + 1) + '. ' + category.name"></span>
                                <span class="text-sm font-bold text-blue-600 w-16 text-center" x-text="getCategoryTotal(category.subcategories, 'totalPoints')"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-lg shadow p-3 sm:p-4 mb-4 overflow-hidden">
                    <div class="flex flex-col min-[1000px]:flex-row flex-wrap items-stretch min-[1000px]:items-center gap-2 min-[1000px]:gap-3">
                        <select x-model="statusFilter" class="w-full min-[1000px]:w-auto border rounded px-3 py-2 sm:px-4 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Status</option> <option value="Approved">Disetujui</option>
                            <option value="Waiting">Menunggu</option>
                            <option value="Rejected">Ditolak</option>
                        </select>

                        <select x-model="categoryFilter" class="w-full min-[1000px]:w-auto border rounded px-3 py-2 sm:px-4 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Kategori</option>
                            <template x-for="cat in categoryGroups" :key="cat.id">
                                <option :value="cat.name" x-text="cat.name"></option>
                            </template>
                        </select>

                        <input type="text" x-model="searchQuery" placeholder="Cari judul..." class="w-full min-[1000px]:w-auto border rounded px-3 py-2 sm:px-4 text-xs sm:text-sm min-[1000px]:flex-1 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                </div>

                <!-- Activities Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <!-- Desktop Table (>=1000px) -->
                    <div class="hidden min-[1000px]:block overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left py-2 sm:py-3 px-2 sm:px-4 font-semibold text-xs sm:text-sm">Kategori Utama</th>
                                    <th class="text-left py-2 sm:py-3 px-2 sm:px-4 font-semibold text-xs sm:text-sm">Subkategori</th>
                                    <th class="text-left py-2 sm:py-3 px-2 sm:px-4 font-semibold text-xs sm:text-sm">Judul Kegiatan</th>
                                    <th class="text-left py-2 sm:py-3 px-2 sm:px-4 font-semibold text-xs sm:text-sm">Deskripsi</th>
                                    <th class="text-center py-2 sm:py-3 px-2 sm:px-4 font-semibold text-xs sm:text-sm">Poin</th>
                                    <th class="text-left py-2 sm:py-3 px-2 sm:px-4 font-semibold text-xs sm:text-sm">Waktu Input</th>
                                    <th class="text-center py-2 sm:py-3 px-2 sm:px-4 font-semibold text-xs sm:text-sm">Status</th>
                                    <th class="text-center py-2 sm:py-3 px-2 sm:px-4 font-semibold text-xs sm:text-sm">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="activity in filteredActivities" :key="activity.id">
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 sm:py-3 px-2 sm:px-4 text-xs sm:text-sm" x-text="activity.mainCategory"></td>
                                    <td class="py-2 sm:py-3 px-2 sm:px-4 text-xs sm:text-sm" x-text="activity.subcategory"></td>
                                    <td class="py-2 sm:py-3 px-2 sm:px-4 text-xs sm:text-sm" x-text="activity.judul"></td>
                                    <td class="py-2 sm:py-3 px-2 sm:px-4 text-xs sm:text-sm" x-text="activity.keterangan"></td>
                                    <td class="text-center py-2 sm:py-3 px-2 sm:px-4 text-xs sm:text-sm">
                                        <div class="flex flex-col items-center gap-1">
                                            <span x-text="activity.point"></span>
                                            <span
                                                x-show="activity.pointAdjustmentReason"
                                                class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-700"
                                                :title="activity.pointAdjustmentReason"
                                                style="display: none;"
                                            >
                                                Ada alasan
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-2 sm:py-3 px-2 sm:px-4 text-xs" x-text="activity.waktu"></td>
                                    <td class="text-center py-2 sm:py-3 px-2 sm:px-4">
                                        <span :class="{
                                            'bg-green-100 text-green-700': activity.status === 'Approved',
                                            'bg-yellow-100 text-yellow-700': activity.status === 'Waiting',
                                            'bg-red-100 text-red-700': activity.status === 'Rejected'
                                        }" class="px-3 py-1 rounded-full text-xs font-semibold" x-text="translateStatus(activity.status)"></span>
                                    </td>
                                    <td class="text-center py-2 sm:py-3 px-2 sm:px-4">
                                        <template x-if="activity.status === 'Waiting'">
                                            <div class="flex justify-center gap-1 sm:gap-2">
                                                <button @click="openDeleteModal(activity)" class="text-red-500 hover:text-red-700 p-1" title="Hapus">
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                        <template x-if="activity.status === 'Approved'">
                                            <button @click="openViewModal(activity)" class="text-blue-500 hover:text-blue-700 p-1" title="Lihat">
                                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </template>
                                        <template x-if="activity.status === 'Rejected'">
                                            <button @click="openViewModal(activity)" class="text-blue-500 hover:text-blue-700 p-1" title="Lihat">
                                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredActivities.length === 0">
                                <tr>
                                    <td colspan="8" class="text-center py-8 text-gray-500 text-xs sm:text-sm">Tidak ada aktivitas yang sesuai dengan filter Anda</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    </div>

                    <!-- Mobile Card List (<1000px) -->
                    <div class="min-[1000px]:hidden">
                        <div class="bg-gray-50 px-4 py-2.5 border-b flex items-center">
                            <span class="flex-1 font-semibold text-xs text-gray-600 uppercase tracking-wide">Judul Kegiatan</span>
                            <span class="w-24 text-center font-semibold text-xs text-gray-600 uppercase tracking-wide">Status</span>
                        </div>
                        <template x-for="activity in filteredActivities" :key="'m-'+activity.id">
                            <div @click="openMobileDetailModal(activity)" class="flex items-center px-4 py-3 border-b hover:bg-blue-50 cursor-pointer active:bg-blue-100 transition-colors">
                                <div class="flex-1 min-w-0 pr-3">
                                    <p class="text-sm font-medium text-gray-800 truncate" x-text="activity.judul"></p>
                                    <p class="text-xs text-gray-500 mt-0.5 truncate" x-text="activity.mainCategory"></p>
                                </div>
                                <div class="w-24 flex-shrink-0 text-center">
                                    <span :class="{
                                        'bg-green-100 text-green-700': activity.status === 'Approved',
                                        'bg-yellow-100 text-yellow-700': activity.status === 'Waiting',
                                        'bg-red-100 text-red-700': activity.status === 'Rejected'
                                    }" class="px-2.5 py-1 rounded-full text-xs font-semibold inline-block" x-text="translateStatus(activity.status)"></span>
                                </div>
                            </div>
                        </template>
                        <template x-if="filteredActivities.length === 0">
                            <div class="text-center py-8 text-gray-500 text-sm">Tidak ada aktivitas yang sesuai dengan filter Anda</div>
                        </template>
                    </div>
                </div>
                </div>
                <!-- End Dashboard Page -->

                <!-- Settings Page -->
                <div x-show="activeMenu === 'Settings'">
                    <div class="mb-4 sm:mb-6">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">Pengaturan</h1>
                        <p class="text-sm sm:text-base text-gray-600">Kelola pengaturan akun Anda</p>
                    </div>

                    <!-- Settings Sections -->
                    <div class="space-y-4 sm:space-y-6">
                        <!-- Profile Information -->
                        <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                            <h3 class="text-base sm:text-lg font-semibold mb-4 sm:mb-6 flex items-center gap-2">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Informasi Profil
                            </h3>

                            <div class="space-y-3 sm:space-y-4">
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">NIM</label>
                                    <div class="bg-gray-50 border rounded-lg px-3 py-2 sm:px-4 sm:py-3 text-xs sm:text-sm text-gray-700" x-text="currentUser.student_id"></div>
                                </div>
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Nama Lengkap</label>
                                    <div class="bg-gray-50 border rounded-lg px-3 py-2 sm:px-4 sm:py-3 text-xs sm:text-sm text-gray-700" x-text="currentUser.name"></div>
                                </div>
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Email</label>
                                    <div class="bg-gray-50 border rounded-lg px-3 py-2 sm:px-4 sm:py-3 text-xs sm:text-sm text-gray-700" x-text="currentUser.email"></div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Jurusan</label>
                                        <div class="bg-gray-50 border rounded-lg px-3 py-2 sm:px-4 sm:py-3 text-xs sm:text-sm text-gray-700" x-text="currentUser.major"></div>
                                    </div>
                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Semester</label>
                                        <div class="bg-gray-50 border rounded-lg px-3 py-2 sm:px-4 sm:py-3 text-xs sm:text-sm text-gray-700" x-text="currentUser.semester ? 'Semester ' + currentUser.semester : '-'" ></div>
                                    </div>
                                    <div>
                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Tahun Angkatan</label>
                                        <div class="bg-gray-50 border rounded-lg px-3 py-2 sm:px-4 sm:py-3 text-xs sm:text-sm text-gray-700" x-text="currentUser.year"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Change Password -->
                        <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                            <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                                Ubah Kata Sandi
                            </h3>
                            <p class="text-xs sm:text-sm text-gray-600 mb-4 sm:mb-6">Perbarui kata sandi untuk menjaga keamanan akun Anda</p>

                            <form @submit.prevent="updatePassword" class="space-y-3 sm:space-y-4">
                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Kata Sandi Saat Ini <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input :type="showCurrentPassword ? 'text' : 'password'" x-model="passwordData.currentPassword" class="w-full border rounded-lg px-3 py-2 sm:px-4 sm:py-2.5 pr-10 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan kata sandi saat ini">
                                        <button type="button" @click="showCurrentPassword = !showCurrentPassword" class="absolute right-2 sm:right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                            <svg x-show="!showCurrentPassword" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg x-show="showCurrentPassword" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Kata Sandi Baru <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input :type="showNewPassword ? 'text' : 'password'" x-model="passwordData.newPassword" class="w-full border rounded-lg px-3 py-2 sm:px-4 sm:py-2.5 pr-10 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan kata sandi baru">
                                        <button type="button" @click="showNewPassword = !showNewPassword" class="absolute right-2 sm:right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                            <svg x-show="!showNewPassword" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg x-show="showNewPassword" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter, termasuk huruf besar, huruf kecil, dan angka</p>
                                </div>

                                <div>
                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Konfirmasi Kata Sandi Baru <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input :type="showConfirmPassword ? 'text' : 'password'" x-model="passwordData.confirmPassword" class="w-full border rounded-lg px-3 py-2 sm:px-4 sm:py-2.5 pr-10 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Konfirmasi kata sandi baru">
                                        <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute right-2 sm:right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                            <svg x-show="!showConfirmPassword" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg x-show="showConfirmPassword" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row sm:justify-end gap-2 sm:gap-3 pt-2">
                                    <button type="button" @click="passwordData = {currentPassword: '', newPassword: '', confirmPassword: ''}" class="w-full sm:w-auto px-4 py-2 sm:px-6 sm:py-2.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-xs sm:text-sm font-medium transition-colors">
                                        Atur Ulang
                                    </button>
                                    <button type="submit" :disabled="isSubmitting" class="w-full sm:w-auto px-4 py-2 sm:px-6 sm:py-2.5 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-400 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors flex items-center justify-center gap-2">
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span x-text="isSubmitting ? 'Memperbarui...' : 'Perbarui Kata Sandi'"></span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- System Information -->
                        <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                            <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Informasi Sistem
                            </h3>
                            <div class="space-y-2 sm:space-y-3">
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center py-2 border-b border-gray-100">
                                    <span class="text-xs sm:text-sm text-gray-600 mb-1 sm:mb-0">Versi:</span>
                                    <span class="text-xs sm:text-sm font-medium text-gray-800">1.0.0</span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center py-2">
                                    <span class="text-xs sm:text-sm text-gray-600 mb-1 sm:mb-0">Terakhir Diperbarui:</span>
                                    <span class="text-xs sm:text-sm font-medium text-gray-800">27 Maret 2026</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Help Page -->
                <div x-show="activeMenu === 'Help'">
                    <div class="mb-4 sm:mb-6">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">Bantuan & Dokumentasi</h1>
                    <p class="text-sm sm:text-base text-gray-600">Dapatkan bantuan untuk menggunakan sistem S-Core</p>
                    </div>
                    
                    <!-- FAQ Section -->
                    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 mb-4 sm:mb-6">
                        <h2 class="text-lg sm:text-xl font-semibold text-gray-800 mb-3 sm:mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Pertanyaan yang Sering Diajukan
                        </h2>
                        
                        <div class="space-y-3 sm:space-y-4">
                            <div class="border-b pb-3 sm:pb-4">
                                <h3 class="font-semibold text-sm sm:text-base text-gray-800 mb-2 flex items-start gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold flex-shrink-0">1</span>
                                    <span>Bagaimana cara mengajukan aktivitas baru?</span>
                                </h3>
                                <p class="text-xs sm:text-sm text-gray-600 ml-7 mb-2">Klik tombol "Tambah S-Core Baru" di Dashboard, isi semua kolom wajib (kategori, judul kegiatan, deskripsi, tanggal kegiatan), unggah sertifikat/bukti, lalu klik Kirim. Pengajuan Anda akan masuk ke proses peninjauan.</p>
                                <div class="mt-2 ml-7 bg-green-50 border border-green-200 rounded p-2">
                                    <p class="text-xs text-green-800"><strong>📁 Penyimpanan File:</strong> Semua file yang diunggah otomatis disimpan di Google Drive untuk penyimpanan yang aman dan permanen. Anda dapat melihat pratinjau dan mengunduh sertifikat kapan saja.</p>
                                </div>
                            </div>
                            
                            <div class="border-b pb-3 sm:pb-4">
                                <h3 class="font-semibold text-sm sm:text-base text-gray-800 mb-2 flex items-start gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold flex-shrink-0">2</span>
                                    <span>Kapan pengajuan saya akan ditinjau?</span>
                                </h3>
                                <p class="text-xs sm:text-sm text-gray-600 ml-7 mb-2">Admin biasanya memproses pengajuan dalam 3-5 hari kerja. Anda dapat memeriksa status pengajuan pada tabel aktivitas di Dashboard. Status akan tampil sebagai "Menunggu", "Disetujui", atau "Ditolak".</p>
                                <div class="mt-2 ml-7 bg-blue-50 border border-blue-200 rounded p-2">
                                    <p class="text-xs text-blue-800"><strong>🕐 Zona Waktu:</strong> Semua timestamp di sistem menggunakan GMT+7 (waktu Jakarta). Waktu pengajuan Anda dicatat berdasarkan zona waktu Indonesia.</p>
                                </div>
                            </div>
                            
                            <div class="border-b pb-3 sm:pb-4">
                                <h3 class="font-semibold text-sm sm:text-base text-gray-800 mb-2 flex items-start gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold flex-shrink-0">3</span>
                                    <span>Bagaimana cara melihat poin S-Core saya?</span>
                                </h3>
                                <p class="text-xs sm:text-sm text-gray-600 ml-7">Poin S-Core yang disetujui ditampilkan di bagian atas Dashboard. Anda juga dapat melihat rincian pada kartu statistik yang menampilkan total aktivitas, menunggu tinjauan, disetujui, dan ditolak.</p>
                            </div>
                            
                            <div class="border-b pb-3 sm:pb-4">
                                <h3 class="font-semibold text-sm sm:text-base text-gray-800 mb-2 flex items-start gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold flex-shrink-0">4</span>
                                    <span>Apa itu Kategori Wajib?</span>
                                </h3>
                                <p class="text-xs sm:text-sm text-gray-600 ml-7">Kategori Wajib adalah jenis aktivitas tertentu yang dibutuhkan untuk kelulusan. Setiap kategori memiliki minimum yang disarankan dan Anda dapat memantau capaian serta total poin pada tabel Kategori Wajib di Dashboard.</p>
                            </div>
                            
                            <div class="border-b pb-3 sm:pb-4">
                                <h3 class="font-semibold text-sm sm:text-base text-gray-800 mb-2 flex items-start gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold flex-shrink-0">5</span>
                                    <span>Apa yang harus saya lakukan jika pengajuan ditolak?</span>
                                </h3>
                                <p class="text-xs sm:text-sm text-gray-600 ml-7 mb-2">Jika pengajuan Anda ditolak, Anda memiliki beberapa pilihan:</p>
                                <ul class="text-xs sm:text-sm text-gray-600 ml-7 space-y-1 list-disc list-inside">
                                    <li><strong>Lihat Alasan:</strong> Klik tombol "Lihat" untuk melihat detail alasan penolakan dari admin</li>
                                    <li><strong>Perbaiki & Kirim Ulang:</strong> Perbaiki hal yang disebutkan pada alasan penolakan lalu kirim ulang pengajuan Anda. Setelah diperbarui, status akan kembali menjadi "Menunggu" untuk ditinjau ulang</li>
                                    <li><strong>Ajukan Baru:</strong> Anda juga dapat mengajukan aktivitas baru dengan informasi dan dokumentasi yang benar</li>
                                </ul>
                                <div class="mt-2 ml-7 bg-blue-50 border border-blue-200 rounded p-2">
                                    <p class="text-xs text-blue-800"><strong>💡 Tips:</strong> Selalu baca alasan penolakan dengan teliti dan pastikan semua masalah diperbaiki sebelum mengirim ulang agar tidak ditolak lagi.</p>
                                </div>
                            </div>
                            
                            <div class="border-b pb-3 sm:pb-4">
                                <h3 class="font-semibold text-sm sm:text-base text-gray-800 mb-2 flex items-start gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold flex-shrink-0">6</span>
                                    <span>Apakah saya bisa mengubah atau menghapus aktivitas yang sudah diajukan?</span>
                                </h3>
                                <p class="text-xs sm:text-sm text-gray-600 ml-7 mb-2">Anda tidak dapat mengubah tetapi dapat menghapus aktivitas dengan status "Menunggu".</p>
                                <ul class="text-xs sm:text-sm text-gray-600 ml-7 space-y-1 list-disc list-inside mt-2">
                                    <li><strong>Status Menunggu:</strong> Dapat dihapus kapan saja sebelum admin meninjau</li>
                                    <li><strong>Status Ditolak:</strong> Lihat alasannya dan kirim ulang </li>
                                    <li><strong>Status Disetujui:</strong> Tidak dapat diubah atau dihapus (final)</li>
                                </ul>
                                <div class="mt-2 ml-7 bg-green-50 border border-green-200 rounded p-2">
                                    <p class="text-xs text-green-800"><strong>💡 Tips:</strong> Saat mengirim pengajuan yang ditolak, pastikan semua poin pada alasan penolakan sudah diperbaiki agar tidak ditolak lagi.</p>
                                </div>
                            </div>
                            
                            <div class="border-b pb-3 sm:pb-4">
                                <h3 class="font-semibold text-sm sm:text-base text-gray-800 mb-2 flex items-start gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold flex-shrink-0">7</span>
                                    <span>Batasan tanggal apa yang berlaku untuk pengajuan aktivitas?</span>
                                </h3>
                                <p class="text-xs sm:text-sm text-gray-600 ml-7">Tanggal aktivitas harus mengikuti aturan berikut:</p>
                                <ul class="text-xs sm:text-sm text-gray-600 ml-7 space-y-1 list-disc list-inside mt-2">
                                    <li>Tanggal aktivitas tidak boleh berada di masa depan</li>
                                    <li>Tanggal aktivitas tidak boleh lebih dari <strong>1 bulan ke belakang</strong> dari hari ini</li>
                                    <li>Aturan ini memastikan pengajuan tepat waktu dan mencerminkan aktivitas terbaru</li>
                                </ul>
                                <div class="mt-2 ml-7 bg-yellow-50 border border-yellow-200 rounded p-2">
                                    <p class="text-xs text-yellow-800"><strong>⚠️ Penting:</strong> Jika Anda memiliki aktivitas lebih lama dari 1 bulan yang belum diajukan, silakan hubungi admin untuk pertimbangan khusus atau persetujuan.</p>
                                </div>
                            </div>
                            
                            <div class="pb-3 sm:pb-4">
                                <h3 class="font-semibold text-sm sm:text-base text-gray-800 mb-2 flex items-start gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold flex-shrink-0">8</span>
                                    <span>Bagaimana cara mengganti kata sandi?</span>
                                </h3>
                                <p class="text-xs sm:text-sm text-gray-600 ml-7">Masuk ke Pengaturan > Ubah Kata Sandi. Masukkan kata sandi saat ini, lalu kata sandi baru dua kali untuk konfirmasi. Kata sandi baru minimal 8 karakter dan harus memuat huruf besar, huruf kecil, serta angka agar aman.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Support -->
                    <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4">Hubungi Dukungan</h3>
                        <p class="text-xs sm:text-sm text-gray-600 mb-3 sm:mb-4">Butuh bantuan tambahan? Hubungi tim dukungan kami.</p>
                        <div class="space-y-2 text-xs sm:text-sm">
                            {{-- <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span class="break-all">support@itbss.ac.id</span>
                            </div> --}}
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span>
                                    <a href="https://wa.me/628561117855" target="_blank" class="hover:text-green-500">
                                        628561117855
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Help Page -->
            </div>
        </div>
    </div>
    <script>
    function dashboardData() {
        return {
            activeMenu: 'Dashboard',
            isSidebarOpen: false,
            isSubmitting: false, 
            
            // --- DATA DARI CONTROLLER ---
            currentUser: @json($user), // <--- TAMBAHKAN INI (Data Profil Asli)
            activities: @json($activities), 
            categoryGroups: @json($categoryGroups),
            submissionCategoryGroups: [],
            stats: @json($stats),

            // --- UI VARS ---
            showLogoutModal: false, showAddModal: false, showEditModal: false,
            showViewModal: false, showDeleteModal: false, showConfirmSubmitModal: false,
            showMobileDetailModal: false, mobileDetailActivity: null,
            editShowUploadBox: false,
            editPdfLoading: true,
            pdfTimestamp: Date.now(),
            editPdfKey: 0,
            
            // --- FILTER VARS ---
            statusFilter: '', 
            categoryFilter: '',
            searchQuery: '',
            
            // --- FORM VARS ---
            dateValidationError: '',
            formData: { mainCategory: '', subcategory: '', activityTitle: '', description: '', activityDate: '', fileName: '' },
            selectedUploadFile: null,
            availableSubcategories: [],
            dragActive: false,
            
            // --- ACTION VARS ---
            selectedActivity: null, 
            appealFormOpen: false, appealSubmissionId: null, appealMessage: '',
            
            // --- PASSWORD & ALERT VARS ---
            passwordData: { currentPassword: '', newPassword: '', confirmPassword: '' },
            showCurrentPassword: false, showNewPassword: false, showConfirmPassword: false,
            passwordError: '', passwordSuccess: '',
            showAlertModal: false, alertType: 'info', alertTitle: '', alertMessage: '', alertHasCancel: false, alertCallback: null,

            // --- S-CORE REPORT VARS ---
            reportEligibility: { totalPoints: 0, minPointsMet: false, completedCategories: 0, totalCategories: 6, minCategoriesMet: false, isEligible: false },
            reportLoading: false,

            // --- COMPUTED ---
            get uniqueCategories() {
                const categorySet = new Set();
                this.activities.forEach(a => { if (a.mainCategory) categorySet.add(a.mainCategory); });
                return Array.from(categorySet).sort();
            },

            get maxDate() { return new Date().toISOString().split('T')[0]; },

            translateStatus(status) {
                const map = {
                    Approved: 'Disetujui',
                    Waiting: 'Menunggu',
                    Rejected: 'Ditolak'
                };
                return map[status] || status;
            },

            // --- FILTER LOGIC ---
            get filteredActivities() {
                return this.activities.filter(activity => {
                    const searchLower = (this.searchQuery || '').toLowerCase();
                    const matchesSearch = this.searchQuery === '' || 
                        (activity.judul && activity.judul.toLowerCase().includes(searchLower)) ||
                        (activity.keterangan && activity.keterangan.toLowerCase().includes(searchLower));
                    
                    const matchesStatus = this.statusFilter === '' || activity.status === this.statusFilter;
                    const matchesCategory = this.categoryFilter === '' || activity.mainCategory === this.categoryFilter;
                    
                    return matchesSearch && matchesStatus && matchesCategory;
                });
            },

            get mandatoryCategoryGroups() {
                const migrationByCategory = new Map();
                const excludedMigrationCategories = ['orkess', 'retreat'];

                this.activities.forEach(activity => {
                    if (activity.status !== 'Approved') {
                        return;
                    }

                    const title = String(activity.judul || '').trim().toLowerCase();
                    const subcategory = String(activity.subcategory || '').trim().toLowerCase();
                    const isMigration = title.startsWith('migrasi data csv') || subcategory.startsWith('migrasi');

                    if (!isMigration) {
                        return;
                    }

                    const categoryName = String(activity.mainCategory || '').trim();
                    if (!categoryName) {
                        return;
                    }

                    const current = Number(migrationByCategory.get(categoryName) || 0);
                    const points = Number(activity.point || 0);
                    migrationByCategory.set(categoryName, current + (Number.isFinite(points) ? points : 0));
                });

                return this.categoryGroups.map(category => {
                    const normalizedCategoryName = String(category.name || '').trim();
                    const normalizedKey = normalizedCategoryName.toLowerCase().replace(/\s+/g, '');
                    const isExcludedCategory = excludedMigrationCategories.some(keyword => normalizedKey.includes(keyword));
                    const migrationPoints = Number(migrationByCategory.get(normalizedCategoryName) || 0);
                    const subcategories = (category.subcategories || []).map(sub => ({ ...sub }));

                    if (!isExcludedCategory && migrationPoints > 0) {
                        const migrationIndex = subcategories.findIndex(sub =>
                            String(sub.name || '').trim().toLowerCase() === 'migrasi'
                        );

                        if (migrationIndex >= 0) {
                            subcategories[migrationIndex].points = Number(migrationPoints.toFixed(2));
                            subcategories[migrationIndex].approvedCount = 1;
                            if (!subcategories[migrationIndex].description) {
                                subcategories[migrationIndex].description = 'Subkategori otomatis untuk data migrasi lama';
                            }
                        } else {
                            subcategories.unshift({
                                id: `migration-${category.id}`,
                                name: 'Migrasi',
                                points: Number(migrationPoints.toFixed(2)),
                                approvedCount: 1,
                                description: 'Subkategori otomatis untuk data migrasi lama'
                            });
                        }
                    }

                    return {
                        ...category,
                        subcategories
                    };
                });
            },

            // --- HELPER FUNCTIONS ---
            updateAvailableSubcategories() {
                if (this.formData.mainCategory !== '') {
                    const idx = parseInt(this.formData.mainCategory);
                    if (this.categoryGroups[idx]) this.availableSubcategories = this.categoryGroups[idx].subcategories;
                    this.formData.subcategory = '';
                } else {
                    this.availableSubcategories = []; this.formData.subcategory = '';
                }
            },

            updateAvailableSubcategoriesForSubmission() {
                if (this.formData.mainCategory !== '') {
                    const idx = parseInt(this.formData.mainCategory);
                    if (this.submissionCategoryGroups[idx]) {
                        this.availableSubcategories = this.submissionCategoryGroups[idx].subcategories;
                    }
                    this.formData.subcategory = '';
                } else {
                    this.availableSubcategories = [];
                    this.formData.subcategory = '';
                }
            },

            isMandatoryCategory(category) {
                if (Object.prototype.hasOwnProperty.call(category, 'is_mandatory')) {
                    return !!category.is_mandatory;
                }

                const name = (category?.name || '').toLowerCase();
                return name.includes('orkess') || name.includes('retreat');
            },

            refreshSubmissionCategories() {
                this.submissionCategoryGroups = this.categoryGroups.filter(cat => {
                    if (this.isMandatoryCategory(cat)) {
                        return false;
                    }

                    return !(cat.is_quota_full === true || cat.is_quota_full === 1 || cat.is_quota_full === '1');
                });

                if (this.formData.mainCategory !== '') {
                    const idx = parseInt(this.formData.mainCategory);
                    if (!this.submissionCategoryGroups[idx]) {
                        this.formData.mainCategory = '';
                        this.formData.subcategory = '';
                        this.availableSubcategories = [];
                    }
                }
            },

            // Load categories dari API (Real-time sync)
            async loadCategories() {
                try {
                    const response = await fetch('/api/categories/student');
                    if (!response.ok) throw new Error('Failed to fetch categories');
                    
                    this.categoryGroups = await response.json();
                    this.refreshSubmissionCategories();
                } catch (error) {
                    console.error('Error loading categories:', error);
                    // Fallback: categoryGroups tetap menggunakan data awal dari server
                }
                
                // Load S-Core report eligibility status
                this.checkReportEligibility();
            },

            validateActivityDate(date) {
                if (!date) return false;
                const parts = date.split('-');
                const d = new Date(parts[0], parts[1]-1, parts[2]);
                const today = new Date(); today.setHours(0,0,0,0);
                const limit = new Date(today); limit.setMonth(limit.getMonth()-1);
                if (d > today) { this.dateValidationError = 'Tanggal di masa depan tidak diperbolehkan'; return false; }
                if (d < limit) { this.dateValidationError = 'Tanggal lebih dari 1 bulan yang lalu'; return false; }
                this.dateValidationError = ''; return true;
            },

            handleFileSelect(e) { 
                const file = e.target.files && e.target.files[0] ? e.target.files[0] : null;

                if (!file) {
                    this.clearSelectedFile();
                    return;
                }

                this.selectedUploadFile = file;
                this.formData.fileName = file.name;
            },

            handleFileDrop(e) {
                this.dragActive = false;

                const file = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0]
                    ? e.dataTransfer.files[0]
                    : null;

                if (!file) {
                    return;
                }

                if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
                    this.showAlert('warning', 'File Tidak Valid', 'Harap unggah file PDF saja.');
                    return;
                }

                this.selectedUploadFile = file;
                this.formData.fileName = file.name;
            },

            clearSelectedFile() {
                this.selectedUploadFile = null;
                this.formData.fileName = '';

                if (this.$refs.fileInput) {
                    this.$refs.fileInput.value = '';
                }

                if (this.$refs.fileInputDesktop) {
                    this.$refs.fileInputDesktop.value = '';
                }
            },

            handleEditFileSelect(e) {
                if(e.target.files[0]) {
                    const file = e.target.files[0];
                    console.log('File selected:', file.name, 'Size:', (file.size / 1024 / 1024).toFixed(2) + ' MB');
                    
                    // Set file name
                    this.formData.fileName = file.name;
                    
                    // Simpan original URL jika belum ada
                    if (!this.selectedActivity.originalFileUrl) {
                        this.selectedActivity.originalFileUrl = this.selectedActivity.file_url;
                    }
                    
                    // Buat preview URL untuk file baru
                    const newFileUrl = URL.createObjectURL(file);
                    console.log('Preview URL created:', newFileUrl);
                    
                    // Update file_url langsung (reactive)
                    this.selectedActivity.file_url = newFileUrl;
                    
                    // Update timestamp untuk force reload iframe
                    this.pdfTimestamp = Date.now();
                    
                    // INCREMENT editPdfKey untuk FORCE re-render x-if
                    this.editPdfKey++;
                    
                    // Hide upload box dan show loading
                    this.editShowUploadBox = false;
                    this.editPdfLoading = true;
                    
                    console.log('PDF changed! editPdfKey:', this.editPdfKey, 'New URL:', newFileUrl);
                    
                    // Force Alpine to update
                    this.$nextTick(() => {
                        console.log('After nextTick - file_url:', this.selectedActivity.file_url);
                    });
                }
            },

            // --- CRUD ACTIONS ---

            // 1. SAVE NEW (Store)
            // Show confirmation modal before submitting
            showSubmitConfirmation() {
                // Validate all fields
                if (!this.formData.mainCategory && this.formData.mainCategory !== 0 || !this.formData.subcategory || !this.formData.activityTitle || !this.formData.description || !this.formData.activityDate || !this.formData.fileName) {
                    this.showAlert('warning', 'Informasi Kurang', 'Harap isi semua kolom wajib');
                    return;
                }
                if (!this.validateActivityDate(this.formData.activityDate)) {
                    this.showAlert('warning', 'Tanggal Tidak Valid', this.dateValidationError);
                    return;
                }
                
                // Show confirmation modal
                this.showConfirmSubmitModal = true;
            },

            saveActivity() {
                if (this.isSubmitting) return;

                if (!this.selectedUploadFile) {
                    this.showAlert('warning', 'File Belum Dipilih', 'Harap pilih file PDF sebelum mengirim.');
                    return;
                }

                // Close confirmation modal
                this.showConfirmSubmitModal = false;

                this.isSubmitting = true;

                let data = new FormData();
                data.append('title', this.formData.activityTitle);
                data.append('description', this.formData.description);
                data.append('activity_date', this.formData.activityDate);
                data.append('mainCategory', this.submissionCategoryGroups[this.formData.mainCategory].name); 
                data.append('subcategory', this.formData.subcategory);
                data.append('certificate_file', this.selectedUploadFile);
                data.append('_token', '{{ csrf_token() }}');

                // Show file size in console
                const fileSize = (this.selectedUploadFile.size / 1024 / 1024).toFixed(2);
                console.log(`Uploading file: ${fileSize} MB`);

                // Create timeout controller (2 minutes)
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 120000);

                fetch('{{ route("submissions.store") }}', { 
                    method: 'POST', 
                    headers: {'Accept': 'application/json'}, 
                    body: data,
                    signal: controller.signal
                })
                .then(async res => { 
                    clearTimeout(timeoutId);
                    const json = await res.json(); 
                    if (!res.ok) throw new Error(json.message); 
                    return json; 
                })
                .then((json) => {
                    const storageNote = json.storage === 'local' ? ' (Local storage)' : '';
                    this.showAlert('success', 'Tersimpan', 'Pengajuan berhasil disimpan!' + storageNote + ' Memuat ulang...');
                    setTimeout(() => window.location.reload(), 1500);
                })
                .catch(err => {
                    clearTimeout(timeoutId);
                    if (err.name === 'AbortError') {
                        this.showAlert('error', 'Waktu Habis', 'Proses unggah terlalu lama. Coba file lebih kecil atau periksa koneksi.');
                    } else {
                        this.showAlert('error', 'Gagal', err.message || 'Terjadi kesalahan saat mengirim');
                    }
                    this.isSubmitting = false;
                });
            },

            // 2. OPEN EDIT MODAL - DISABLED
            openEditModal(activity) {
                // Edit functionality is disabled for all submissions
                this.showAlert('warning', 'Edit Tidak Diizinkan', 'Fitur edit dinonaktifkan. Silakan hapus lalu kirim ulang jika perlu perubahan.');
                return;
            },

            // 3. UPDATE EXISTING (Update)
            updateActivity() {
                if (this.isSubmitting) return;

                if (this.formData.mainCategory === '' || typeof this.formData.mainCategory === 'undefined' || !this.formData.subcategory || !this.formData.activityTitle || !this.formData.description || !this.formData.activityDate) {
                    this.showAlert('warning', 'Informasi Kurang', 'Harap isi semua kolom wajib'); 
                    this.isSubmitting = false;
                    return;
                }

                this.isSubmitting = true;

                let data = new FormData();
                data.append('_method', 'PUT');
                data.append('title', this.formData.activityTitle.trim());
                data.append('description', this.formData.description.trim());
                data.append('activity_date', this.formData.activityDate);
                
                const catIndex = parseInt(this.formData.mainCategory);
                if (isNaN(catIndex) || !this.categoryGroups[catIndex]) {
                    this.showAlert('error', 'Kategori Tidak Valid', 'Kategori yang dipilih tidak valid');
                    this.isSubmitting = false;
                    return;
                }
                
                data.append('mainCategory', this.categoryGroups[catIndex].name); 
                data.append('subcategory', this.formData.subcategory.trim());
                
                // Check if file was changed in edit modal
                if (this.$refs.fileInputEdit && this.$refs.fileInputEdit.files.length > 0) {
                    data.append('certificate_file', this.$refs.fileInputEdit.files[0]);
                    console.log('Updating with new file:', this.$refs.fileInputEdit.files[0].name);
                } else {
                    console.log('No new file selected - updating other fields only');
                }

                console.log('Submit update form with:', {
                    title: this.formData.activityTitle,
                    description: this.formData.description,
                    activity_date: this.formData.activityDate,
                    mainCategory: this.categoryGroups[catIndex].name,
                    subcategory: this.formData.subcategory,
                    hasFile: this.$refs.fileInputEdit && this.$refs.fileInputEdit.files.length > 0
                });

                fetch(`/submissions/${this.selectedActivity.id}`, { 
                    method: 'POST', 
                    headers: { 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }, 
                    body: data 
                })
                .then(async res => { 
                    const json = await res.json(); 
                    if (!res.ok) {
                        console.error('Update failed:', json.message);
                        throw new Error(json.message);
                    }
                    console.log('Update successful:', json);
                    return json;
                })
                .then(() => {
                    this.showAlert('success', 'Diperbarui', 'Aktivitas berhasil diperbarui!');
                    setTimeout(() => window.location.reload(), 1500);
                })
                .catch(err => {
                    this.showAlert('error', 'Pembaruan Gagal', err.message);
                    this.isSubmitting = false; 
                });
            },

            // 4. DELETE ACTIVITY
            openDeleteModal(a) { this.selectedActivity = a; this.showDeleteModal = true; },
            
            confirmDelete() {
                if (!this.selectedActivity) return;
                if (this.isSubmitting) return;

                this.isSubmitting = true; 

                fetch(`/submissions/${this.selectedActivity.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(async res => { const json = await res.json(); if (!res.ok) throw new Error(json.message); return json; })
                .then(() => {
                    this.showAlert('success', 'Dihapus', 'Pengajuan dihapus.');
                    setTimeout(() => window.location.reload(), 1500);
                })
                .catch(err => {
                    this.showAlert('error', 'Gagal Menghapus', err.message);
                    this.isSubmitting = false; 
                });
            },

            // --- PASSWORD ACTION (NEW) ---
            updatePassword() {
                // 1. Cegah Spam
                if (this.isSubmitting) return;

                // 2. Validasi Frontend
                if (!this.passwordData.currentPassword || !this.passwordData.newPassword || !this.passwordData.confirmPassword) {
                    this.showAlert('warning', 'Informasi Kurang', 'Harap isi semua kolom kata sandi.');
                    return;
                }

                if (this.passwordData.newPassword !== this.passwordData.confirmPassword) {
                    this.showAlert('warning', 'Tidak Cocok', 'Kata sandi baru dan konfirmasi tidak cocok.');
                    return;
                }

                if (this.passwordData.newPassword.length < 8) {
                    this.showAlert('warning', 'Kata Sandi Lemah', 'Kata sandi minimal 8 karakter.');
                    return;
                }

                // 3. Kunci Tombol
                this.isSubmitting = true;

                // 4. Kirim ke Backend
                fetch('{{ route("profile.update-password") }}', {
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
                })
                .then(async res => {
                    const json = await res.json();
                    if (!res.ok) throw new Error(json.message || 'Gagal memperbarui kata sandi');
                    return json;
                })
                .then(() => {
                    this.showAlert('success', 'Berhasil', 'Kata sandi berhasil diperbarui!');
                    
                    // Reset Form
                    this.passwordData = { currentPassword: '', newPassword: '', confirmPassword: '' };
                    this.passwordError = '';
                    
                    // Buka kunci (tidak perlu reload halaman untuk ganti password)
                    this.isSubmitting = false;
                })
                .catch(err => {
                    this.showAlert('error', 'Kesalahan', err.message);
                    this.isSubmitting = false; // Buka kunci jika error
                });
            },

            resetPasswordForm() {
                // 1. Kosongkan Field
                this.passwordData = {
                    currentPassword: '',
                    newPassword: '',
                    confirmPassword: ''
                };
                
                // 2. Hilangkan Error/Success Message (jika ada)
                this.passwordError = '';
                this.passwordSuccess = '';
                
                // 3. (Opsional) Reset visibilitas password jadi tersembunyi lagi
                this.showCurrentPassword = false;
                this.showNewPassword = false;
                this.showConfirmPassword = false;
            },
            
            // --- UI HELPERS ---
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
            openViewModal(a) { 
                console.log('Opening view modal with activity:', a); 
                console.log('File URL:', a.file_url); 
                this.selectedActivity = a; 
                this.showViewModal = true; 
            },

            openMobileDetailModal(a) {
                this.mobileDetailActivity = a;
                this.showMobileDetailModal = true;
            },
            
            closeModal() { 
                this.showAddModal = false; this.showEditModal = false; this.showViewModal = false; 
                this.showDeleteModal = false; this.showMobileDetailModal = false;
                this.selectedActivity = null; this.mobileDetailActivity = null; 
                this.formData = { mainCategory: '', subcategory: '', activityTitle: '', description: '', activityDate: '', fileName: '' };
                this.availableSubcategories = [];
                // Reset edit modal state
                this.editShowUploadBox = false;
                this.editPdfLoading = true;
                this.pdfTimestamp = Date.now();
                this.editPdfKey = 0;
                // Reset loading state on modal close just in case
                this.isSubmitting = false;
            },
            
            showAlert(type, title, msg, cancel=false, cb=null) { 
                this.alertType=type; this.alertTitle=title; this.alertMessage=msg; 
                this.alertHasCancel=cancel; this.alertCallback=cb; this.showAlertModal=true; 
            },
            
            closeAlertModal(conf) { 
                this.showAlertModal=false; if(conf && this.alertCallback) this.alertCallback(); 
            },

            // --- S-CORE REPORT FUNCTIONS ---
            async checkReportEligibility() {
                if (this.reportLoading) return;
                this.reportLoading = true;
                
                try {
                    const response = await fetch(`/student/${this.currentUser.student_id}/report/check`);
                    if (!response.ok) throw new Error('Gagal memeriksa kelayakan');
                    
                    this.reportEligibility = await response.json();
                    this.updateReportUI();
                } catch (error) {
                    console.error('Error checking report eligibility:', error);
                    document.getElementById('reportMessage').textContent = '❌ Gagal memuat status kelayakan';
                } finally {
                    this.reportLoading = false;
                }
            },

            updateReportUI() {
                const { totalPoints, minPointsMet, completedCategories, totalCategories, minCategoriesMet, isEligible, minPointsRequired, minCategoriesRequired } = this.reportEligibility;
                
                // Update badge
                const badge = document.getElementById('reportEligibilityBadge');
                if (isEligible) {
                    badge.textContent = '✅ Memenuhi Syarat';
                    badge.className = 'px-4 py-2 rounded-full text-sm font-bold text-green-700 bg-green-200';
                } else {
                    badge.textContent = '❌ Belum Memenuhi';
                    badge.className = 'px-4 py-2 rounded-full text-sm font-bold text-red-700 bg-red-200';
                }

                // Update points display with dynamic minimum
                document.getElementById('reportPoints').textContent = totalPoints;
                document.getElementById('minPointsLabel').textContent = '/ ' + minPointsRequired + ' poin';
                const pointsProgress = (totalPoints / minPointsRequired) * 100;
                document.getElementById('pointsProgressBar').style.width = Math.min(pointsProgress, 100) + '%';
                document.getElementById('pointsProgressBar').style.backgroundColor = minPointsMet ? '#3b82f6' : '#ef4444';

                // Update categories display with dynamic minimum
                document.getElementById('reportCategories').textContent = completedCategories;
                document.getElementById('minCategoriesLabel').textContent = '/ ' + totalCategories + ' kategori (min ' + minCategoriesRequired + ')';
                const categoriesProgress = (completedCategories / minCategoriesRequired) * 100;
                document.getElementById('categoriesProgressBar').style.width = Math.min(categoriesProgress, 100) + '%';
                document.getElementById('categoriesProgressBar').style.backgroundColor = minCategoriesMet ? '#22c55e' : '#ef4444';

                // Update button state
                const btn = document.getElementById('downloadReportBtn');
                const msg = document.getElementById('reportMessage');
                
                if (isEligible) {
                    btn.disabled = false;
                    btn.classList.remove('disabled:bg-gray-400', 'disabled:cursor-not-allowed');
                    msg.textContent = '✓ Anda memenuhi syarat untuk mengunduh laporan';
                    msg.className = 'text-xs text-center text-green-600 mt-2 font-medium';
                } else {
                    btn.disabled = true;
                    btn.classList.add('disabled:bg-gray-400', 'disabled:cursor-not-allowed');
                    let reason = [];
                    if (!minPointsMet) reason.push('Anda membutuhkan setidaknya ' + minPointsRequired + ' poin');
                    if (!minCategoriesMet) reason.push('Anda perlu menyelesaikan setidaknya ' + minCategoriesRequired + ' kategori');
                    msg.textContent = '⚠ ' + reason.join(' and ');
                    msg.className = 'text-xs text-center text-red-600 mt-2';
                }
            },

            downloadSCoreReport() {
                if (!this.reportEligibility.isEligible) {
                    const { minPointsRequired, minCategoriesRequired } = this.reportEligibility;
                    this.showAlert('warning', 'Belum Memenuhi', `Anda harus memiliki setidaknya ${minPointsRequired} poin dan menyelesaikan ${minCategoriesRequired} dari 6 kategori`);
                    return;
                }

                // Trigger download via window.location
                const reportUrl = `/student/${this.currentUser.student_id}/report`;
                window.location.href = reportUrl;
                
                this.showAlert('success', 'Berhasil', 'Laporan S-Core Anda sedang diunduh');
            },
            
            getCategoryTotal(sub, f) { 
                if(f==='approvedCount') return sub.reduce((s,i)=>s+(i.approvedCount||0),0);
                return sub.reduce((s,i)=>s+((i.approvedCount||0)*i.points),0);
            }
        }
    }
</script>
</body>
</html>
