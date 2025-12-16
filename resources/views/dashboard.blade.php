<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - S-Core ITBSS</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>
    <div class="flex h-screen bg-gray-100" x-data="dashboardData()">
        <!-- Logout Confirmation Modal -->
        <div x-show="showLogoutModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
            <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4">
                <h3 class="text-lg font-semibold mb-4">Confirm Logout</h3>
                <p class="text-gray-600 mb-6">Are you sure you want to logout?</p>
                <div class="flex gap-3 justify-end">
                    <button @click="showLogoutModal = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm font-medium">No</button>
                    <button @click="confirmLogout" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded text-sm font-medium">Yes</button>
                </div>
            </div>
        </div>

        <!-- Add New Activity Modal - Full Screen -->
        <div x-show="showAddModal" class="fixed inset-0 bg-black bg-opacity-50 z-50" style="display: none;">
            <div class="h-full w-full bg-white flex flex-col">
                <div class="bg-white border-b px-6 py-4 flex justify-between items-center">
                    <h3 class="text-xl font-semibold">Submit New S-Core</h3>
                    <button @click="closeModal" class="text-gray-500 hover:text-gray-700 text-2xl leading-none w-8 h-8 flex items-center justify-center">×</button>
                </div>

                <div class="flex-1 overflow-hidden flex">
                    <div class="flex w-full h-full">
                        <!-- Left Column - File Upload -->
                        <div class="w-1/2 bg-gray-100 border-r overflow-auto p-6">
                            <div class="bg-white rounded-lg shadow-sm h-full flex flex-col p-6">
                                <h4 class="font-semibold text-gray-800 mb-4">Upload Certificate/Evidence</h4>
                                <div class="flex-1 border-2 border-dashed border-gray-300 rounded-lg flex flex-col items-center justify-center p-8 hover:border-blue-400 transition-colors cursor-pointer">
                                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="text-gray-600 text-sm mb-2">Drag and drop your PDF file here</p>
                                    <p class="text-gray-400 text-xs mb-4">or</p>
                                    <label class="cursor-pointer">
                                        <span class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-medium inline-block">Browse Files</span>
                                        <input type="file" accept=".pdf" class="hidden" x-ref="fileInput" @change="handleFileSelect" />
                                    </label>
                                    <p class="text-gray-400 text-xs mt-4">PDF only - Maximum 10MB</p>
                                </div>
                                <div x-show="formData.fileName" class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span class="text-sm text-gray-700" x-text="formData.fileName"></span>
                                    </div>
                                    <button @click="formData.fileName = ''" class="text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Form Details -->
                        <div class="w-1/2 flex flex-col">
                            <div class="flex-1 overflow-y-auto p-6">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Student</label>
                                        <div class="bg-gray-50 border rounded-lg px-4 py-3 text-sm text-gray-700">{{ Auth::user()->name }}</div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Main Category <span class="text-red-500">*</span></label>
                                        <select x-model="formData.mainCategory" @change="updateAvailableSubcategories()" class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="">Select Main Category</option>
                                            <template x-for="(catGroup, idx) in categoryGroups" :key="idx">
                                                <option :value="idx" x-text="(idx + 1) + '. ' + catGroup.name"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <div x-show="formData.mainCategory !== ''">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Subcategory <span class="text-red-500">*</span></label>
                                        <select x-model="formData.subcategory" class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="">Select Subcategory</option>
                                            <template x-for="(sub, idx) in availableSubcategories" :key="idx">
                                                <option :value="sub.name" x-text="sub.name + ' (' + sub.points + ' pts)'"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Activity Title <span class="text-red-500">*</span></label>
                                        <input type="text" x-model="formData.activityTitle" class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter activity title" />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Description <span class="text-red-500">*</span></label>
                                        <textarea x-model="formData.description" rows="4" class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter description"></textarea>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Activity Date <span class="text-red-500">*</span></label>
                                        <input type="date" x-model="formData.activityDate" :max="maxDate" class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                        <p class="text-xs text-gray-500 mt-1">Maximum 1 month from today</p>
                                        <p x-show="dateValidationError" class="text-xs text-red-500 mt-1" x-text="dateValidationError"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Fixed Action Buttons -->
                            <div class="border-t bg-white px-6 py-4">
                                <div class="flex gap-3 justify-end">
                                    <button @click="closeModal" class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm font-medium transition-colors">Cancel</button>
                                    <button @click="saveActivity" class="px-6 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium transition-colors">Submit for Review</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Activity Modal - Full Screen -->
        <div x-show="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 z-50" style="display: none;">
            <div class="h-full w-full bg-white flex flex-col">
                <div class="bg-white border-b px-6 py-4 flex justify-between items-center">
                    <h3 class="text-xl font-semibold">Edit S-Core Submission</h3>
                    <button @click="closeModal" class="text-gray-500 hover:text-gray-700 text-2xl leading-none w-8 h-8 flex items-center justify-center">×</button>
                </div>

                <div class="flex-1 overflow-hidden flex">
                    <template x-if="selectedActivity">
                        <div class="flex w-full h-full">
                            <!-- Left Column - File Upload -->
                            <div class="w-1/2 bg-gray-100 border-r overflow-auto p-6">
                                <div class="bg-white rounded-lg shadow-sm h-full flex flex-col p-6">
                                    <h4 class="font-semibold text-gray-800 mb-4">Update Certificate/Evidence</h4>
                                    <div class="flex-1 border-2 border-dashed border-gray-300 rounded-lg flex flex-col items-center justify-center p-8 hover:border-blue-400 transition-colors cursor-pointer">
                                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <p class="text-gray-600 text-sm mb-2">Drag and drop your PDF file here</p>
                                        <p class="text-gray-400 text-xs mb-4">or</p>
                                        <label class="cursor-pointer">
                                            <span class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-medium inline-block">Browse Files</span>
                                            <input type="file" accept=".pdf" class="hidden" @change="handleFileSelect" />
                                        </label>
                                        <p class="text-gray-400 text-xs mt-4">PDF only - Maximum 10MB</p>
                                    </div>
                                    <div x-show="formData.fileName" class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span class="text-sm text-gray-700" x-text="formData.fileName"></span>
                                        </div>
                                        <button @click="formData.fileName = ''" class="text-red-500 hover:text-red-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column - Form Details -->
                            <div class="w-1/2 flex flex-col">
                                <div class="flex-1 overflow-y-auto p-6">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Student</label>
                                            <div class="bg-gray-50 border rounded-lg px-4 py-3 text-sm text-gray-700">
                                                {{ $user->student_id }} - {{ $user->name }}
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Main Category <span class="text-red-500">*</span></label>
                                            <select x-model="formData.mainCategory" @change="updateAvailableSubcategories()" class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="">Select Main Category</option>
                                                <template x-for="(catGroup, idx) in categoryGroups" :key="idx">
                                                    <option :value="idx" x-text="(idx + 1) + '. ' + catGroup.name"></option>
                                                </template>
                                            </select>
                                        </div>

                                        <div x-show="formData.mainCategory !== ''">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Subcategory <span class="text-red-500">*</span></label>
                                            <select x-model="formData.subcategory" class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="">Select Subcategory</option>
                                                <template x-for="(sub, idx) in availableSubcategories" :key="idx">
                                                    <option :value="sub.name" x-text="sub.name + ' (' + sub.points + ' pts)'"></option>
                                                </template>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Activity Title <span class="text-red-500">*</span></label>
                                            <input type="text" x-model="formData.activityTitle" class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter activity title" />
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Description <span class="text-red-500">*</span></label>
                                            <textarea x-model="formData.description" rows="4" class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter description"></textarea>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Activity Date <span class="text-red-500">*</span></label>
                                            <input type="date" x-model="formData.activityDate" class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Fixed Action Buttons -->
                                <div class="border-t bg-white px-6 py-4">
                                    <div class="flex gap-3 justify-end">
                                        <button @click="closeModal" class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm font-medium transition-colors">Cancel</button>
                                        <button @click="updateActivity" class="px-6 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium transition-colors">Update</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- View Activity Modal - Full Screen -->
        <div x-show="showViewModal" class="fixed inset-0 bg-black bg-opacity-50 z-50" style="display: none;">
            <div class="h-full w-full bg-white flex flex-col">
                <div class="bg-white border-b px-6 py-4 flex justify-between items-center">
                    <h3 class="text-xl font-semibold">View S-Core Submission</h3>
                    <button @click="closeModal" class="text-gray-500 hover:text-gray-700 text-2xl leading-none w-8 h-8 flex items-center justify-center">×</button>
                </div>

                <div class="flex-1 overflow-hidden flex">
                    <template x-if="selectedActivity">
                        <div class="flex w-full h-full">
                            <!-- Left Column - PDF Viewer -->
                            <div class="w-1/2 bg-gray-100 border-r overflow-hidden p-6">
                                <div class="bg-white rounded-lg shadow-sm h-full flex flex-col">
                                    <div class="bg-gray-800 text-white px-4 py-3 rounded-t-lg flex items-center justify-between">
                                        <span class="text-sm font-medium">Certificate/Evidence</span>
                                        <span class="text-xs text-gray-300" x-text="selectedSubmission ? (selectedSubmission.certificate || 'document.pdf') : ''"></span>
                                    </div>

                                    <div class="flex-1 bg-gray-50 relative h-full overflow-hidden">
                                        <template x-if="!selectedSubmission">
                                            <div class="flex items-center justify-center h-full text-gray-400">
                                                pilih submission...
                                            </div>
                                        </template>

                                        <template x-if="selectedSubmission">
                                            <div class="h-full w-full">
                                                <template x-if="selectedSubmission.file_url">
                                                    <div class="h-full flex flex-col bg-gray-200">
                                                        <iframe 
                                                            :src="selectedSubmission.file_url" 
                                                            class="w-full flex-1" 
                                                            style="border: none;" 
                                                            type="application/pdf">
                                                        </iframe>
                                                        
                                                        <div class="p-2 bg-white text-center border-t">
                                                            <a :href="selectedSubmission.file_url" target="_blank" class="text-blue-600 hover:underline text-sm font-semibold">
                                                                Download / Buka di Tab Baru
                                                            </a>
                                                        </div>
                                                    </div>
                                                </template>

                                                <template x-if="!selectedSubmission.file_url">
                                                    <div class="flex items-center justify-center h-full text-red-500 flex-col gap-2">
                                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                                        <p>File PDF tidak ditemukan di database.</p>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <!-- Right Column - Details -->
                            <div class="w-1/2 flex flex-col">
                                <div class="flex-1 overflow-y-auto p-6">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Student</label>
                                            <div class="bg-gray-50 border rounded-lg px-4 py-3 text-sm text-gray-700">{{ Auth::user()->name }}</div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Main Category</label>
                                            <div class="bg-gray-50 border rounded-lg px-4 py-3 text-sm" x-text="selectedActivity.mainCategory"></div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Subcategory</label>
                                            <div class="bg-gray-50 border rounded-lg px-4 py-3 text-sm" x-text="selectedActivity.subcategory"></div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Activity Title</label>
                                            <div class="bg-gray-50 border rounded-lg px-4 py-3 text-sm font-medium" x-text="selectedActivity.judul"></div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                            <div class="bg-gray-50 border rounded-lg px-4 py-3 text-sm whitespace-pre-wrap min-h-[100px]" x-text="selectedActivity.keterangan"></div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Submission Time</label>
                                            <div class="bg-gray-50 border rounded-lg px-4 py-3 text-sm" x-text="selectedActivity.waktu"></div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                            <div>
                                                <span :class="{
                                                    'bg-green-100 text-green-700': selectedActivity.status === 'Approved',
                                                    'bg-yellow-100 text-yellow-700': selectedActivity.status === 'Waiting',
                                                    'bg-red-100 text-red-700': selectedActivity.status === 'Rejected'
                                                }" class="px-3 py-1 rounded-full text-xs font-semibold" x-text="selectedActivity.status"></span>
                                            </div>
                                        </div>

                                        <!-- Rejection Details Section (Only for Rejected) -->
                                        <template x-if="selectedActivity.status === 'Rejected'">
                                            <div class="bg-red-50 border-2 border-red-200 rounded-lg p-4 space-y-4">
                                                <div>
                                                    <label class="block text-sm font-semibold text-red-700 mb-2">❌ Rejection Reason</label>
                                                    <div class="bg-white border border-red-200 rounded-lg px-4 py-3 text-sm text-gray-700 whitespace-pre-wrap" x-text="selectedActivity.rejectionReason || 'No reason provided'"></div>
                                                </div>

                                                <div class="flex gap-3">
                                                    <button @click="closeModal(); openEditModal(selectedActivity)" class="flex-1 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium transition-colors">
                                                        Edit & Resubmit
                                                    </button>
                                                    <button @click="openAppealForm(selectedActivity.id)" class="flex-1 px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-sm font-medium transition-colors">
                                                        File Appeal
                                                    </button>
                                                </div>

                                                <!-- Inline Appeal Form -->
                                                <div x-show="appealFormOpen && appealSubmissionId === selectedActivity.id" class="border-t-2 border-red-300 pt-4 mt-4">
                                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Appeal Message</label>
                                                    <textarea x-model="appealMessage" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Explain why you believe this submission should be reconsidered..."></textarea>
                                                    <div class="flex gap-2 mt-3">
                                                        <button @click="submitAppeal(selectedActivity.id)" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-sm font-medium">Submit Appeal</button>
                                                        <button @click="closeAppealForm()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-lg text-sm font-medium">Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Points</label>
                                            <div class="bg-gray-50 border rounded-lg px-4 py-3 text-sm font-bold" x-text="selectedActivity.point || '-'"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Fixed Action Button -->
                                <div class="border-t bg-white px-6 py-4">
                                    <div class="flex gap-3 justify-end">
                                        <button @click="closeModal" class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm font-medium transition-colors">Close</button>
                                        <button x-show="selectedActivity && selectedActivity.status === 'Cancel'" @click="openComplaintModal(selectedActivity)" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-sm font-medium transition-colors">File Complaint</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div x-show="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-60" style="display: none;">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 shadow-2xl">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-red-600">Cancel Submission</h3>
                    <button @click="showDeleteModal = false" class="text-gray-500 hover:text-gray-700 text-2xl leading-none">×</button>
                </div>
                
                <template x-if="selectedActivity">
                    <div>
                        <p class="text-gray-600 mb-4">Are you sure you want to cancel this submission?</p>
                        <div class="bg-gray-50 border rounded-lg p-3 mb-6">
                            <p class="text-sm font-medium text-gray-800" x-text="selectedActivity.judul"></p>
                            <p class="text-xs text-gray-500 mt-1"><span x-text="selectedActivity.mainCategory"></span> - <span x-text="selectedActivity.subcategory"></span></p>
                        </div>
                        <div class="flex gap-3 justify-end">
                            <button @click="showDeleteModal = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm font-medium">Keep</button>
                            <button @click="confirmDelete" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium">Cancel Submission</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Complaint Modal -->
        <div x-show="showComplaintModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-60" style="display: none;">
            <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 shadow-2xl">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-orange-600">File Complaint / Appeal</h3>
                    <button @click="showComplaintModal = false" class="text-gray-500 hover:text-gray-700 text-2xl leading-none">×</button>
                </div>
                
                <template x-if="selectedActivity">
                    <div>
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-orange-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-orange-800">Submission Details</p>
                                    <p class="text-sm text-orange-700 mt-1"><strong>Title:</strong> <span x-text="selectedActivity.judul"></span></p>
                                    <p class="text-sm text-orange-700"><strong>Main Category:</strong> <span x-text="selectedActivity.mainCategory"></span></p>
                                    <p class="text-sm text-orange-700"><strong>Subcategory:</strong> <span x-text="selectedActivity.subcategory"></span></p>
                                    <p class="text-sm text-orange-700"><strong>Status:</strong> Rejected</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Complaint Type <span class="text-red-500">*</span></label>
                                <select x-model="complaintData.type" class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="">Select Complaint Type</option>
                                    <option value="wrong_category">Wrong Category Assessment</option>
                                    <option value="wrong_points">Incorrect Points Calculation</option>
                                    <option value="missing_evidence">Evidence Not Reviewed Properly</option>
                                    <option value="other">Other Reason</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Explanation <span class="text-red-500">*</span></label>
                                <textarea x-model="complaintData.explanation" rows="6" class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Explain why you are filing this complaint and what you believe should be the correct assessment..."></textarea>
                                <p class="text-xs text-gray-500 mt-1">Please provide detailed explanation to support your complaint</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Additional Evidence (Optional)</label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-orange-400 transition-colors cursor-pointer">
                                    <div class="flex items-center justify-center gap-3">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <div>
                                            <label class="cursor-pointer">
                                                <span class="text-sm text-blue-500 hover:text-blue-600 font-medium">Upload additional evidence</span>
                                                <input type="file" accept=".pdf,.jpg,.jpeg,.png" class="hidden" @change="handleComplaintFileSelect" />
                                            </label>
                                            <p class="text-xs text-gray-500">PDF, JPG, PNG - Max 10MB</p>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="complaintData.fileName" class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span class="text-sm text-gray-700" x-text="complaintData.fileName"></span>
                                    </div>
                                    <button @click="complaintData.fileName = ''" class="text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3 justify-end mt-6">
                            <button @click="showComplaintModal = false" class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm font-medium">Cancel</button>
                            <button @click="submitComplaint" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-sm font-medium">Submit Complaint</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Generic Alert/Confirmation Modal -->
        <div x-show="showAlertModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-[70]" style="display: none;">
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
                        <button x-show="alertHasCancel" @click="closeAlertModal(false)" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm font-medium">Cancel</button>
                        <button @click="closeAlertModal(true)" class="px-4 py-2 rounded text-sm font-medium text-white" :class="{
                            'bg-green-500 hover:bg-green-600': alertType === 'success',
                            'bg-red-500 hover:bg-red-600': alertType === 'error',
                            'bg-yellow-500 hover:bg-yellow-600': alertType === 'warning',
                            'bg-blue-500 hover:bg-blue-600': alertType === 'info'
                        }" x-text="alertHasCancel ? 'Confirm' : 'OK'"></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div :class="isSidebarOpen ? 'w-64' : 'w-20'" class="bg-white shadow-lg transition-all duration-300 flex flex-col">
            <div class="p-4 border-b flex flex-col items-center">
                <img src="/images/logo.png" alt="Logo" class="w-12 h-12 object-contain">
                <div x-show="isSidebarOpen" class="mt-2 text-center">
                    <h2 class="text-sm font-bold text-gray-800">S-Core ITBSS</h2>
                    <p class="text-xs text-gray-500">Sabda Setia Student Point System</p>
                </div>
            </div>

            <nav class="mt-4 flex-1">
                <button @click="activeMenu = 'Dashboard'" class="w-full flex items-center py-3 text-left hover:bg-gray-100 transition-colors" :class="[
                    isSidebarOpen ? 'gap-3 px-4' : 'justify-center',
                    activeMenu === 'Dashboard' ? 'bg-blue-50 border-l-4 border-blue-500' : ''
                ]">
                    <svg class="w-6 h-6" :class="activeMenu === 'Dashboard' ? 'text-blue-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                    </svg>
                    <span x-show="isSidebarOpen" class="text-sm" :class="activeMenu === 'Dashboard' ? 'text-blue-700 font-medium' : 'text-gray-700'">Dashboard</span>
                </button>
                <button @click="activeMenu = 'Settings'" class="w-full flex items-center py-3 text-left hover:bg-gray-100 transition-colors" :class="[
                    isSidebarOpen ? 'gap-3 px-4' : 'justify-center',
                    activeMenu === 'Settings' ? 'bg-blue-50 border-l-4 border-blue-500' : ''
                ]">
                    <svg class="w-6 h-6" :class="activeMenu === 'Settings' ? 'text-blue-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span x-show="isSidebarOpen" class="text-sm" :class="activeMenu === 'Settings' ? 'text-blue-700 font-medium' : 'text-gray-700'">Settings</span>
                </button>
                <button @click="activeMenu = 'Help'" class="w-full flex items-center py-3 text-left hover:bg-gray-100 transition-colors" :class="[
                    isSidebarOpen ? 'gap-3 px-4' : 'justify-center',
                    activeMenu === 'Help' ? 'bg-blue-50 border-l-4 border-blue-500' : ''
                ]">
                    <svg class="w-6 h-6" :class="activeMenu === 'Help' ? 'text-blue-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-show="isSidebarOpen" class="text-sm" :class="activeMenu === 'Help' ? 'text-blue-700 font-medium' : 'text-gray-700'">Help</span>
                </button>
            </nav>

            <div class="border-t mt-auto">
                <button @click="showLogoutModal = true" class="w-full flex items-center py-3 text-left hover:bg-gray-100 transition-colors" :class="isSidebarOpen ? 'gap-3 px-4' : 'justify-center'">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span x-show="isSidebarOpen" class="text-sm text-red-500">Logout</span>
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <div class="bg-white shadow-sm p-4 flex justify-between items-center sticky top-0 z-10">
                <button @click="isSidebarOpen = !isSidebarOpen" class="p-2 hover:bg-gray-100 rounded">
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
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-4">
                                <h1 class="text-3xl font-bold text-gray-800">S-Core</h1>
                                <!-- <span class="bg-green-500 text-white px-6 py-3 rounded-lg text-sm font-semibold flex items-center shadow-md">
                                    APPROVED POINTS: 994
                                </span> -->
                            </div>

                            <button @click="showAddModal = true"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg text-sm font-semibold flex items-center gap-2 shadow-md transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Add New S-Core
                            </button>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-green-500 rounded-lg shadow p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-white">Approved Points</p>
                                <p class="text-2xl font-bold text-white" x-text="stats.approvedPoints"></p>
                            </div>
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Waiting Review</p>
                                <p class="text-2xl font-bold text-yellow-600" x-text="stats.waiting"></p>
                            </div>
                            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Approved</p>
                                <p class="text-2xl font-bold text-green-600" x-text="stats.approved"></p>
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
                                <p class="text-sm text-gray-600">Rejected</p>
                                <p class="text-2xl font-bold text-red-600" x-text="stats.rejected"></p>
                            </div>
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mandatory Categories Table -->
                <div class="bg-white rounded-lg shadow p-6 mb-6" x-data="{ expandedCategories: [false, false, false, false, false, false] }">
                    <h2 class="text-xl font-bold mb-4 text-center">Mandatory S-Core Categories</h2>
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="border-b-2 bg-gray-50">
                                <th class="text-left py-3 px-4 font-semibold">Category</th>
                                <th class="text-center py-3 px-4 font-semibold w-24">Points</th>
                                <th class="text-center py-3 px-4 font-semibold w-32">Approved Count</th>
                                <th class="text-center py-3 px-4 font-semibold w-32">Total Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(category, index) in categoryGroups" :key="index">
                                <tr>
                                    <td colspan="4" class="p-0">
                                        <table class="w-full">
                                            <tbody>
                                                <!-- Main Category Row -->
                                                <tr class="border-b bg-blue-50 hover:bg-blue-100 cursor-pointer" 
                                                    @click="expandedCategories[index] = !expandedCategories[index]">
                                                    <td class="py-3 px-4 font-bold text-gray-800">
                                                        <div class="flex items-center gap-2">
                                                            <svg class="w-4 h-4 transition-transform" :class="expandedCategories[index] ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                            </svg>
                                                            <span x-text="(index + 1) + '. ' + category.name"></span>
                                                        </div>
                                                    </td>
                                                    <td class="text-center py-3 px-4 font-semibold w-24"></td>
                                                    <td class="text-center py-3 px-4 font-semibold text-blue-600 w-32" x-text="getCategoryTotal(category.subcategories, 'approvedCount')"></td>
                                                    <td class="text-center py-3 px-4 font-bold text-blue-600 w-32" x-text="getCategoryTotal(category.subcategories, 'totalPoints')"></td>
                                                </tr>
                                                
                                                <!-- Subcategories -->
                                                <template x-for="sub in category.subcategories" :key="sub.name">
                                                    <tr x-show="expandedCategories[index]" class="border-b hover:bg-gray-50" style="display: none;">
                                                        <td class="py-2 px-4 pl-12 text-sm text-gray-700 cursor-help relative" 
                                                            x-data="{ showTooltip: false }"
                                                            @mouseenter="showTooltip = true"
                                                            @mouseleave="showTooltip = false">
                                                            <span x-text="sub.name"></span>
                                                            <div x-show="showTooltip" 
                                                                class="absolute left-12 top-full mt-1 bg-gray-800 text-white text-xs rounded py-2 px-3 z-50 w-64 shadow-lg"
                                                                style="display: none;">
                                                                <p x-text="sub.description"></p>
                                                            </div>
                                                        </td>
                                                        <td class="text-center py-2 px-4 text-sm w-24" x-text="sub.points"></td>
                                                        <td class="text-center py-2 px-4 text-sm w-32" x-text="sub.approvedCount"></td>
                                                        <td class="text-center py-2 px-4 text-sm font-semibold text-gray-700 w-32" x-text="sub.approvedCount * sub.points"></td>
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

                <!-- Filters -->
                <div class="bg-white rounded-lg shadow p-4 mb-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <select x-model="statusFilter" class="border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Status</option> <option value="Approved">Approved</option>
                            <option value="Waiting">Waiting</option>
                            <option value="Rejected">Rejected</option>
                        </select>

                        <select x-model="categoryFilter" class="border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Categories</option> <template x-for="cat in uniqueCategories" :key="cat">
                                <option :value="cat" x-text="cat"></option>
                            </template>
                        </select>

                        <input type="text" x-model="searchQuery" placeholder="Search title..." class="border rounded px-4 py-2 text-sm flex-1 min-w-[200px] focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                </div>

                <!-- Activities Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left py-3 px-4 font-semibold text-sm">Main Category</th>
                                <th class="text-left py-3 px-4 font-semibold text-sm">Subcategory</th>
                                <th class="text-left py-3 px-4 font-semibold text-sm">Activity Title</th>
                                <th class="text-left py-3 px-4 font-semibold text-sm">Description</th>
                                <th class="text-center py-3 px-4 font-semibold text-sm">Points</th>
                                <th class="text-left py-3 px-4 font-semibold text-sm">Input Time</th>
                                <th class="text-center py-3 px-4 font-semibold text-sm">Status</th>
                                <th class="text-center py-3 px-4 font-semibold text-sm">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="activity in filteredActivities" :key="activity.id">
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-4 text-sm" x-text="activity.mainCategory"></td>
                                    <td class="py-3 px-4 text-sm" x-text="activity.subcategory"></td>
                                    <td class="py-3 px-4 text-sm" x-text="activity.judul"></td>
                                    <td class="py-3 px-4 text-sm" x-text="activity.keterangan"></td>
                                    <td class="text-center py-3 px-4 text-sm" x-text="activity.point"></td>
                                    <td class="py-3 px-4 text-xs text-gray-600" x-text="activity.waktu"></td>
                                    <td class="text-center py-3 px-4">
                                        <span :class="{
                                            'bg-green-100 text-green-700': activity.status === 'Approved',
                                            'bg-yellow-100 text-yellow-700': activity.status === 'Waiting',
                                            'bg-red-100 text-red-700': activity.status === 'Rejected'
                                        }" class="px-3 py-1 rounded-full text-xs font-semibold" x-text="activity.status"></span>
                                    </td>
                                    <td class="text-center py-3 px-4">
                                        <template x-if="activity.status === 'Waiting'">
                                            <div class="flex justify-center gap-2">
                                                <button @click="openEditModal(activity)" class="text-green-500 hover:text-green-700 p-1" title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <button @click="openDeleteModal(activity)" class="text-red-500 hover:text-red-700 p-1" title="Delete">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                        <template x-if="activity.status === 'Approved'">
                                            <button @click="openViewModal(activity)" class="text-blue-500 hover:text-blue-700 p-1" title="View">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </template>
                                        <template x-if="activity.status === 'Rejected'">
                                            <div class="flex justify-center gap-2">
                                                <button @click="openViewModal(activity)" class="text-blue-500 hover:text-blue-700 p-1" title="View">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                                <button @click="openComplaintModal(activity)" class="text-orange-500 hover:text-orange-700 p-1" title="File Complaint">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredActivities.length === 0">
                                <tr>
                                    <td colspan="7" class="text-center py-8 text-gray-500">No activities found matching your filters</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                </div>
                <!-- End Dashboard Page -->

                <!-- Settings Page -->
                <div x-show="activeMenu === 'Settings'">
                    <div class="mb-6">
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">Settings</h1>
                        <p class="text-gray-600">Manage your account settings</p>
                    </div>

                    <!-- Settings Sections -->
                    <div class="space-y-6">
                        <!-- Profile Information -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold mb-4">Profile Information</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Student ID</label>
                                    <input type="text" value="2210426" disabled class="w-full border rounded-lg px-4 py-2.5 text-sm bg-gray-50 text-gray-600" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                    <input type="text" value="{{ Auth::user()->name }}" disabled class="w-full border rounded-lg px-4 py-2.5 text-sm bg-gray-50 text-gray-600" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input type="email" value="calvin.william@student.itbss.ac.id" disabled class="w-full border rounded-lg px-4 py-2.5 text-sm bg-gray-50 text-gray-600" />
                                </div>
                            </div>
                        </div>

                        <!-- Change Password -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                                Change Password
                            </h3>
                            <p class="text-sm text-gray-600 mb-4">Update your password to keep your account secure</p>
                            
                            <form @submit.prevent="changePassword" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Password <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input :type="showCurrentPassword ? 'text' : 'password'" x-model="passwordData.currentPassword" class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10" placeholder="Enter current password" />
                                        <button type="button" @click="showCurrentPassword = !showCurrentPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
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
                                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input :type="showNewPassword ? 'text' : 'password'" x-model="passwordData.newPassword" class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10" placeholder="Enter new password" />
                                        <button type="button" @click="showNewPassword = !showNewPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                            <svg x-show="!showNewPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg x-show="showNewPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Minimum 8 characters, include uppercase, lowercase, and numbers</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input :type="showConfirmPassword ? 'text' : 'password'" x-model="passwordData.confirmPassword" class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10" placeholder="Confirm new password" />
                                        <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
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

                                <div x-show="passwordError" class="bg-red-50 border border-red-200 rounded-lg p-3 flex items-start gap-2">
                                    <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-sm text-red-700" x-text="passwordError"></p>
                                </div>

                                <div x-show="passwordSuccess" class="bg-green-50 border border-green-200 rounded-lg p-3 flex items-start gap-2">
                                    <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-sm text-green-700" x-text="passwordSuccess"></p>
                                </div>

                                <div class="flex gap-3 pt-2">
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">Update Password</button>
                                    <button type="button" @click="resetPasswordForm" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">Reset</button>
                                </div>
                            </form>
                        </div>

                        <!-- Notification Preferences -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold mb-4">Notification Preferences</h3>
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" checked class="w-4 h-4 text-blue-500 rounded focus:ring-2 focus:ring-blue-500" />
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Email notifications for submission status</p>
                                        <p class="text-xs text-gray-500">Receive email when your submission is reviewed</p>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" checked class="w-4 h-4 text-blue-500 rounded focus:ring-2 focus:ring-blue-500" />
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">S-Core milestone alerts</p>
                                        <p class="text-xs text-gray-500">Get notified when you reach point milestones</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- System Information -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold mb-4">System Information</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Version:</span>
                                    <span class="font-medium">1.0.0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Last Updated:</span>
                                    <span class="font-medium">November 19, 2025</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Help Page -->
                <div x-show="activeMenu === 'Help'">
                    <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Help & Documentation</h1>
                    <p class="text-gray-600">Get help with using the S-Core system</p>
                    </div>
                    
                    <!-- FAQ Section -->
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Frequently Asked Questions
                        </h2>
                        
                        <div class="space-y-4">
                            <div class="border-b pb-4">
                                <h3 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">1</span>
                                    How do I submit a new activity?
                                </h3>
                                <p class="text-sm text-gray-600 ml-7">Click the "Add New Activity" button in the Dashboard, fill in all required fields (category, activity title, description, activity date), upload your certificate, and click Submit. Your submission will be sent for review.</p>
                            </div>
                            
                            <div class="border-b pb-4">
                                <h3 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">2</span>
                                    When will my submission be reviewed?
                                </h3>
                                <p class="text-sm text-gray-600 ml-7">Admin reviewers typically process submissions within 3-5 business days. You can check the status of your submission in the Activities Table on the Dashboard. Status will show as "Waiting", "Approved", or "Rejected".</p>
                            </div>
                            
                            <div class="border-b pb-4">
                                <h3 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">3</span>
                                    How do I check my S-Core points?
                                </h3>
                                <p class="text-sm text-gray-600 ml-7">Your approved S-Core points are displayed at the top of the Dashboard. You can also see detailed breakdowns in the Statistics Cards showing Total Activities, Waiting for Review, Approved, and Rejected submissions.</p>
                            </div>
                            
                            <div class="border-b pb-4">
                                <h3 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">4</span>
                                    What are Mandatory Categories?
                                </h3>
                                <p class="text-sm text-gray-600 ml-7">Mandatory Categories are specific activity types required for graduation. Each category has a suggested minimum and you can track your achievement (Capaian) and total points earned in the Mandatory Categories Table on the Dashboard.</p>
                            </div>
                            
                            <div class="border-b pb-4">
                                <h3 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">5</span>
                                    What should I do if my submission is rejected?
                                </h3>
                                <p class="text-sm text-gray-600 ml-7 mb-2">If your submission is rejected, you have several options:</p>
                                <ul class="text-sm text-gray-600 ml-7 space-y-1 list-disc list-inside">
                                    <li><strong>View Reason:</strong> Click the "View" button to see the detailed rejection reason from the admin</li>
                                    <li><strong>File a Complaint:</strong> If you believe the rejection was incorrect, click the "Complaint" button to submit an appeal with supporting documents</li>
                                    <li><strong>Resubmit:</strong> After reviewing the rejection reason, you can correct the issues and submit a new activity with the proper documentation</li>
                                </ul>
                                <div class="mt-2 ml-7 bg-blue-50 border border-blue-200 rounded p-2">
                                    <p class="text-xs text-blue-800"><strong>💡 Tip:</strong> Always read the rejection reason carefully before filing a complaint or resubmitting to ensure your next submission meets all requirements.</p>
                                </div>
                            </div>
                            
                            <div class="border-b pb-4">
                                <h3 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">6</span>
                                    How do I file a complaint for a rejected submission?
                                </h3>
                                <p class="text-sm text-gray-600 ml-7 mb-2">To file a complaint:</p>
                                <ol class="text-sm text-gray-600 ml-7 space-y-1 list-decimal list-inside">
                                    <li>Find the rejected submission in your Activities Table</li>
                                    <li>Click the orange "Complaint" button (📝 icon)</li>
                                    <li>Select the complaint type (Wrong Category, Incorrect Rejection, etc.)</li>
                                    <li>Provide a detailed explanation of why you believe the rejection was incorrect</li>
                                    <li>Optionally attach supporting documents (additional evidence, clarifications, etc.)</li>
                                    <li>Click "Submit Complaint" to send your appeal to the admin</li>
                                </ol>
                                <p class="text-sm text-gray-600 ml-7 mt-2">Your complaint will be reviewed by the admin team, and you'll receive a response via email or through the system notification.</p>
                            </div>
                            
                            <div class="border-b pb-4">
                                <h3 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">7</span>
                                    Can I edit or delete a submitted activity?
                                </h3>
                                <p class="text-sm text-gray-600 ml-7">You can only edit or delete activities that are still in "Waiting" status (pending review). Once a submission has been approved or rejected, it cannot be edited or deleted. However, you can:</p>
                                <ul class="text-sm text-gray-600 ml-7 space-y-1 list-disc list-inside mt-2">
                                    <li>View the details of approved submissions</li>
                                    <li>File a complaint for rejected submissions</li>
                                    <li>Submit a new corrected version for rejected activities</li>
                                </ul>
                            </div>
                            
                            <div class="border-b pb-4">
                                <h3 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">8</span>
                                    What date restrictions apply to activity submissions?
                                </h3>
                                <p class="text-sm text-gray-600 ml-7">Activity dates must follow these rules:</p>
                                <ul class="text-sm text-gray-600 ml-7 space-y-1 list-disc list-inside mt-2">
                                    <li>Activity date cannot be in the future</li>
                                    <li>Activity date cannot be more than <strong>1 month in the past</strong> from today</li>
                                    <li>This ensures submissions are timely and reflect recent activities</li>
                                </ul>
                                <div class="mt-2 ml-7 bg-yellow-50 border border-yellow-200 rounded p-2">
                                    <p class="text-xs text-yellow-800"><strong>⚠️ Important:</strong> If you have activities older than 1 month that you haven't submitted, please contact the admin for special consideration or approval.</p>
                                </div>
                            </div>
                            
                            <div class="pb-4">
                                <h3 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">9</span>
                                    How do I change my password?
                                </h3>
                                <p class="text-sm text-gray-600 ml-7">Go to Settings > Change Password section. Enter your current password, then your new password twice to confirm. Your new password must be at least 8 characters long and include uppercase letters, lowercase letters, and numbers for security.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Support -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4">Contact Support</h3>
                        <p class="text-sm text-gray-600 mb-4">Need additional help? Contact our support team.</p>
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
                <!-- End Help Page -->
            </div>
        </div>
    </div>
    <script>
    function dashboardData() {
        return {
            activeMenu: 'Dashboard',
            isSidebarOpen: false,
            
            // --- DATA DARI CONTROLLER ---
            activities: @json($activities), 
            categoryGroups: @json($categoryGroups),
            stats: @json($stats),

            // --- UI VARS ---
            showLogoutModal: false, showAddModal: false, showEditModal: false,
            showViewModal: false, showDeleteModal: false, showComplaintModal: false,
            
            // --- FILTER VARS (Default Kosong = All) ---
            statusFilter: '', 
            categoryFilter: '',
            searchQuery: '',
            
            // --- FORM VARS ---
            dateValidationError: '',
            formData: { mainCategory: '', subcategory: '', activityTitle: '', description: '', activityDate: '', fileName: '' },
            availableSubcategories: [],
            
            // --- ACTION VARS ---
            selectedActivity: null, 
            appealFormOpen: false, appealSubmissionId: null, appealMessage: '',
            complaintData: { type: '', explanation: '', fileName: '' },
            
            // --- PASSWORD & ALERT VARS ---
            passwordData: { currentPassword: '', newPassword: '', confirmPassword: '' },
            showCurrentPassword: false, showNewPassword: false, showConfirmPassword: false,
            passwordError: '', passwordSuccess: '',
            showAlertModal: false, alertType: 'info', alertTitle: '', alertMessage: '', alertHasCancel: false, alertCallback: null,

            // --- COMPUTED ---
            get uniqueCategories() {
                const categorySet = new Set();
                this.activities.forEach(a => { if (a.mainCategory) categorySet.add(a.mainCategory); });
                return Array.from(categorySet).sort();
            },

            get maxDate() { return new Date().toISOString().split('T')[0]; },

            // --- FILTER LOGIC (ANTI CRASH & SUPPORT ALL) ---
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

            // --- HELPER FUNCTIONS ---
            updateAvailableSubcategories() {
                if (this.formData.mainCategory !== '') {
                    // mainCategory di formData menyimpan INDEX array
                    const idx = parseInt(this.formData.mainCategory);
                    if (this.categoryGroups[idx]) this.availableSubcategories = this.categoryGroups[idx].subcategories;
                    this.formData.subcategory = '';
                } else {
                    this.availableSubcategories = []; this.formData.subcategory = '';
                }
            },

            validateActivityDate(date) {
                if (!date) return false;
                const parts = date.split('-');
                const d = new Date(parts[0], parts[1]-1, parts[2]);
                const today = new Date(); today.setHours(0,0,0,0);
                const limit = new Date(today); limit.setMonth(limit.getMonth()-1);
                if (d > today) { this.dateValidationError = 'Future date not allowed'; return false; }
                if (d < limit) { this.dateValidationError = 'Date > 1 month old'; return false; }
                this.dateValidationError = ''; return true;
            },

            handleFileSelect(e) { if(e.target.files[0]) this.formData.fileName = e.target.files[0].name; },
            handleComplaintFileSelect(e) { if(e.target.files[0]) this.complaintData.fileName = e.target.files[0].name; },

            // --- CRUD ACTIONS ---

            // 1. SAVE NEW (Store)
            saveActivity() {
                if (!this.formData.mainCategory || !this.formData.subcategory || !this.formData.activityTitle || !this.formData.description || !this.formData.activityDate || !this.formData.fileName) {
                    this.showAlert('warning', 'Missing Info', 'Fill all fields'); return;
                }
                if (!this.validateActivityDate(this.formData.activityDate)) {
                    this.showAlert('warning', 'Invalid Date', this.dateValidationError); return;
                }

                let data = new FormData();
                data.append('title', this.formData.activityTitle);
                data.append('description', this.formData.description);
                data.append('activity_date', this.formData.activityDate);
                // Ambil nama kategori berdasarkan index
                data.append('mainCategory', this.categoryGroups[this.formData.mainCategory].name); 
                data.append('subcategory', this.formData.subcategory);
                data.append('certificate_file', this.$refs.fileInput.files[0]);
                data.append('_token', '{{ csrf_token() }}');

                fetch('{{ route("submissions.store") }}', { method: 'POST', headers: {'Accept': 'application/json'}, body: data })
                .then(async res => { const json = await res.json(); if (!res.ok) throw new Error(json.message); return json; })
                .then(() => {
                    this.showAlert('success', 'Saved', 'Reloading...');
                    setTimeout(() => window.location.reload(), 1500);
                    this.closeModal();
                })
                .catch(err => this.showAlert('error', 'Failed', err.message));
            },

            // 2. OPEN EDIT MODAL (Pre-fill Data)
            openEditModal(activity) {
                this.selectedActivity = activity;
                this.showEditModal = true;

                // Cari index kategori berdasarkan nama yang ada di activity
                let catIndex = this.categoryGroups.findIndex(c => c.name === activity.mainCategory);
                if (catIndex === -1) catIndex = '';

                // Masukkan data lama ke form
                this.formData = {
                    mainCategory: catIndex,
                    subcategory: '', // Nanti diisi setelah updateAvailableSubcategories
                    activityTitle: activity.judul,
                    description: activity.keterangan,
                    // Parse tanggal ke format YYYY-MM-DD
                    activityDate: activity.waktu ? new Date(activity.waktu).toISOString().split('T')[0] : '',
                    fileName: '' 
                };

                // Generate dropdown subkategori
                this.updateAvailableSubcategories();

                // Set subkategori terpilih (tunggu sebentar biar dropdown render)
                setTimeout(() => {
                    this.formData.subcategory = activity.subcategory;
                    // Kalau tanggal di database formatnya beda, sesuaikan logic di atas
                    // activity.waktu di view controller biasanya format d M Y H:i, 
                    // sebaiknya controller kirim raw date juga (activityDate)
                }, 50);
            },

            // 3. UPDATE EXISTING (Update)
            updateActivity() {
                if (this.formData.mainCategory === '' || !this.formData.subcategory || !this.formData.activityTitle || !this.formData.description || !this.formData.activityDate) {
                    this.showAlert('warning', 'Missing Info', 'Fill all required fields'); return;
                }

                let data = new FormData();
                data.append('_method', 'PUT'); // Method Spoofing untuk Laravel
                data.append('title', this.formData.activityTitle);
                data.append('description', this.formData.description);
                data.append('activity_date', this.formData.activityDate);
                
                const catIndex = this.formData.mainCategory;
                data.append('mainCategory', this.categoryGroups[catIndex].name); 
                data.append('subcategory', this.formData.subcategory);
                
                // File opsional saat update
                if (this.$refs.fileInput && this.$refs.fileInput.files.length > 0) {
                    data.append('certificate_file', this.$refs.fileInput.files[0]);
                }
                
                data.append('_token', '{{ csrf_token() }}');

                fetch(`/submissions/${this.selectedActivity.id}`, { 
                    method: 'POST', 
                    headers: { 'Accept': 'application/json' }, 
                    body: data 
                })
                .then(async res => { const json = await res.json(); if (!res.ok) throw new Error(json.message); return json; })
                .then(() => {
                    this.showAlert('success', 'Updated', 'Activity updated successfully!');
                    setTimeout(() => window.location.reload(), 1500);
                    this.closeModal();
                })
                .catch(err => this.showAlert('error', 'Update Failed', err.message));
            },

            // 4. DELETE ACTIVITY
            openDeleteModal(a) { this.selectedActivity = a; this.showDeleteModal = true; },
            
            confirmDelete() {
                if (!this.selectedActivity) return;
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
                    this.showAlert('success', 'Deleted', 'Submission deleted.');
                    setTimeout(() => window.location.reload(), 1500);
                    this.showDeleteModal = false;
                })
                .catch(err => this.showAlert('error', 'Delete Failed', err.message));
            },

            // 5. COMPLAINT / APPEAL
            openComplaintModal(a) { this.selectedActivity = a; this.showComplaintModal = true; },
            
            submitComplaint() {
                if (!this.complaintData.type || !this.complaintData.explanation) {
                    this.showAlert('warning', 'Missing Info', 'Please fill type and explanation'); return;
                }

                fetch('{{ route("submissions.complaint") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        submission_id: this.selectedActivity.id,
                        type: this.complaintData.type,
                        explanation: this.complaintData.explanation
                    })
                })
                .then(async res => { const json = await res.json(); if (!res.ok) throw new Error(json.message); return json; })
                .then(() => {
                    this.showAlert('success', 'Submitted', 'Complaint sent to admin.');
                    this.showComplaintModal = false;
                })
                .catch(err => this.showAlert('error', 'Error', err.message));
            },

            // --- UI HELPERS ---
            confirmLogout() { fetch('{{ route("logout") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' } }).then(() => window.location.href = '/login'); },
            openViewModal(a) { this.selectedActivity = a; this.showViewModal = true; },
            
            closeModal() { 
                this.showAddModal = false; this.showEditModal = false; this.showViewModal = false; 
                this.showDeleteModal = false; this.showComplaintModal = false; this.selectedActivity = null; 
                this.formData = { mainCategory: '', subcategory: '', activityTitle: '', description: '', activityDate: '', fileName: '' };
                this.availableSubcategories = [];
            },
            
            showAlert(type, title, msg, cancel=false, cb=null) { 
                this.alertType=type; this.alertTitle=title; this.alertMessage=msg; 
                this.alertHasCancel=cancel; this.alertCallback=cb; this.showAlertModal=true; 
            },
            
            closeAlertModal(conf) { 
                this.showAlertModal=false; if(conf && this.alertCallback) this.alertCallback(); 
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
