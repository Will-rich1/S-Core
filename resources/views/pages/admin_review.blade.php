<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Review - S-Core ITBSS</title>
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
    <div class="flex h-screen bg-gray-100" x-data="adminReviewData()">
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

        <!-- Detail Modal -->
        <div x-show="showDetailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
            <div class="bg-white rounded-lg p-6 max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold">Review Submission</h3>
                    <button @click="closeModal" class="text-gray-500 hover:text-gray-700 text-2xl leading-none">×</button>
                </div>

                <template x-if="selectedSubmission">
                    <div class="space-y-4">
                        <div class="bg-blue-50 border border-blue-200 rounded p-4">
                            <h4 class="font-semibold text-blue-900 mb-2">Student Information</h4>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <span class="text-gray-600">Student ID:</span>
                                    <span class="ml-2 font-medium" x-text="selectedSubmission.studentId"></span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Name:</span>
                                    <span class="ml-2 font-medium" x-text="selectedSubmission.studentName"></span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Submitted:</span>
                                    <span class="ml-2 font-medium" x-text="selectedSubmission.submittedDate"></span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Activity Date:</span>
                                    <span class="ml-2 font-medium" x-text="selectedSubmission.activityDate"></span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <div class="bg-gray-50 border rounded px-4 py-2 text-sm" x-text="selectedSubmission.kategori"></div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Activity Title</label>
                            <div class="bg-gray-50 border rounded px-4 py-2 text-sm" x-text="selectedSubmission.judul"></div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <div class="bg-gray-50 border rounded px-4 py-3 text-sm whitespace-pre-wrap" x-text="selectedSubmission.keterangan"></div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Certificate/Evidence</label>
                            <div class="flex items-center gap-3">
                                <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View Certificate
                                </button>
                                <span class="text-sm text-gray-600" x-text="selectedSubmission.certificate"></span>
                            </div>
                        </div>

                        <div class="bg-yellow-50 border border-yellow-200 rounded p-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Assign Points <span class="text-red-500">*</span></label>
                            <div class="flex items-center gap-3">
                                <input type="number" x-model="assignedPoints" class="border rounded px-4 py-2 text-sm w-32 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Points" min="0" />
                                <span class="text-sm text-gray-600">Suggested: <span x-text="selectedSubmission.suggestedPoint"></span> points</span>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="flex gap-3 justify-end mt-6">
                    <button @click="closeModal" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm font-medium">Cancel</button>
                    <button @click="showRejectModal = true; showDetailModal = false" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded text-sm font-medium">Reject</button>
                    <button @click="handleApprove" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded text-sm font-medium">Approve</button>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div x-show="showRejectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
            <div class="bg-white rounded-lg p-6 max-w-xl w-full mx-4">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold text-red-600">Reject Submission</h3>
                    <button @click="closeModal" class="text-gray-500 hover:text-gray-700 text-2xl leading-none">×</button>
                </div>

                <template x-if="selectedSubmission">
                    <div>
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-4">
                                You are about to reject submission from <strong x-text="selectedSubmission.studentName"></strong> (<span x-text="selectedSubmission.studentId"></span>)
                            </p>
                            <p class="text-sm font-medium mb-2">Activity: <span x-text="selectedSubmission.judul"></span></p>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Rejection <span class="text-red-500">*</span></label>
                            <textarea x-model="rejectReason" rows="4" class="w-full border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Please provide a clear reason for rejection so the student can understand and resubmit correctly..."></textarea>
                        </div>

                        <div class="flex gap-3 justify-end">
                            <button @click="closeModal" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm font-medium">Cancel</button>
                            <button @click="handleRejectConfirm" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded text-sm font-medium">Confirm Rejection</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Sidebar -->
        <div :class="isSidebarOpen ? 'w-64' : 'w-20'" class="bg-white shadow-lg transition-all duration-300 flex flex-col">
            <div class="p-4 border-b flex flex-col items-center">
                <img src="/images/logo.png" alt="Logo" class="w-12 h-12 object-contain">
                <div x-show="isSidebarOpen" class="mt-2 text-center">
                    <h2 class="text-sm font-bold text-gray-800">S-Core Admin</h2>
                    <p class="text-xs text-gray-500">Review & Management</p>
                </div>
            </div>

            <nav class="mt-4 flex-1">
                <button @click="activeMenu = 'Review Submissions'" class="w-full flex items-center py-3 text-left hover:bg-gray-100 transition-colors" :class="[
                    isSidebarOpen ? 'gap-3 px-4' : 'justify-center',
                    activeMenu === 'Review Submissions' && isSidebarOpen ? 'bg-blue-50 border-l-4 border-blue-500' : ''
                ]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span x-show="isSidebarOpen" class="text-sm text-gray-700">Review Submissions</span>
                </button>
                <button @click="activeMenu = 'Statistics'" class="w-full flex items-center py-3 text-left hover:bg-gray-100 transition-colors" :class="[
                    isSidebarOpen ? 'gap-3 px-4' : 'justify-center',
                    activeMenu === 'Statistics' && isSidebarOpen ? 'bg-blue-50 border-l-4 border-blue-500' : ''
                ]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span x-show="isSidebarOpen" class="text-sm text-gray-700">Statistics</span>
                </button>
                <button @click="activeMenu = 'Students'" class="w-full flex items-center py-3 text-left hover:bg-gray-100 transition-colors" :class="[
                    isSidebarOpen ? 'gap-3 px-4' : 'justify-center',
                    activeMenu === 'Students' && isSidebarOpen ? 'bg-blue-50 border-l-4 border-blue-500' : ''
                ]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span x-show="isSidebarOpen" class="text-sm text-gray-700">Students</span>
                </button>
                <button @click="activeMenu = 'Settings'" class="w-full flex items-center py-3 text-left hover:bg-gray-100 transition-colors" :class="[
                    isSidebarOpen ? 'gap-3 px-4' : 'justify-center',
                    activeMenu === 'Settings' && isSidebarOpen ? 'bg-blue-50 border-l-4 border-blue-500' : ''
                ]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span x-show="isSidebarOpen" class="text-sm text-gray-700">Settings</span>
                </button>
                <button @click="activeMenu = 'Help'" class="w-full flex items-center py-3 text-left hover:bg-gray-100 transition-colors" :class="[
                    isSidebarOpen ? 'gap-3 px-4' : 'justify-center',
                    activeMenu === 'Help' && isSidebarOpen ? 'bg-blue-50 border-l-4 border-blue-500' : ''
                ]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-show="isSidebarOpen" class="text-sm text-gray-700">Help</span>
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
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium">Admin User</span>
                </div>
            </div>

            <div class="flex-1 overflow-auto p-6">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">S-Core Submission Review</h1>
                    <p class="text-gray-600">Review and approve student activity submissions</p>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Total Submissions</p>
                                <p class="text-2xl font-bold text-gray-800" x-text="stats.total"></p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
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

                <!-- Filters -->
                <div class="bg-white rounded-lg shadow p-4 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                        <select x-model="statusFilter" class="border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Status</option>
                            <option value="Waiting">Waiting</option>
                            <option value="Approve">Approved</option>
                            <option value="Cancel">Rejected</option>
                        </select>

                        <select x-model="categoryFilter" class="border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Categories</option>
                            <template x-for="cat in uniqueCategories" :key="cat">
                                <option :value="cat" x-text="cat.substring(0, 30) + '...'"></option>
                            </template>
                        </select>

                        <select x-model="studentFilter" class="border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Students</option>
                            <template x-for="student in uniqueStudents" :key="student">
                                <option :value="student" x-text="student"></option>
                            </template>
                        </select>

                        <input type="text" x-model="searchQuery" placeholder="Search submissions..." class="border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                </div>

                <!-- Submissions Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left py-3 px-4 font-semibold text-sm">Student</th>
                                <th class="text-left py-3 px-4 font-semibold text-sm">Category</th>
                                <th class="text-left py-3 px-4 font-semibold text-sm">Activity Title</th>
                                <th class="text-center py-3 px-4 font-semibold text-sm">Points</th>
                                <th class="text-left py-3 px-4 font-semibold text-sm">Submitted</th>
                                <th class="text-center py-3 px-4 font-semibold text-sm">Status</th>
                                <th class="text-center py-3 px-4 font-semibold text-sm">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="submission in filteredSubmissions" :key="submission.id">
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-4">
                                        <div class="text-sm">
                                            <div class="font-medium" x-text="submission.studentName"></div>
                                            <div class="text-gray-500 text-xs" x-text="submission.studentId"></div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-sm max-w-xs">
                                        <div class="truncate" :title="submission.kategori" x-text="submission.kategori"></div>
                                    </td>
                                    <td class="py-3 px-4 text-sm" x-text="submission.judul"></td>
                                    <td class="text-center py-3 px-4 text-sm font-medium" x-text="submission.point || '-'"></td>
                                    <td class="py-3 px-4 text-xs text-gray-600" x-text="submission.waktu"></td>
                                    <td class="text-center py-3 px-4">
                                        <span :class="{
                                            'bg-green-100 text-green-700': submission.status === 'Approve',
                                            'bg-yellow-100 text-yellow-700': submission.status === 'Waiting',
                                            'bg-red-100 text-red-700': submission.status === 'Cancel'
                                        }" class="px-3 py-1 rounded-full text-xs font-semibold" x-text="submission.status"></span>
                                    </td>
                                    <td class="text-center py-3 px-4">
                                        <button @click="viewDetail(submission)" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs font-medium">Review</button>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredSubmissions.length === 0">
                                <tr>
                                    <td colspan="7" class="text-center py-8 text-gray-500">No submissions found matching your filters</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function adminReviewData() {
            return {
                activeMenu: 'Review Submissions',
                isSidebarOpen: false,
                showLogoutModal: false,
                showDetailModal: false,
                showRejectModal: false,
                selectedSubmission: null,
                rejectReason: '',
                assignedPoints: '',
                statusFilter: 'Waiting',
                categoryFilter: '',
                searchQuery: '',
                studentFilter: '',
                submissions: [
                    { id: 1, studentId: "2210426", studentName: "CALVIN WILLIAM", kategori: "Internship/Practical Work", judul: "Internship Certificate", keterangan: "Internship at HR Department for 3 months, handling recruitment and employee relations", point: null, suggestedPoint: 22, waktu: "12 Aug 2025 20:31:51", status: "Waiting", certificate: "internship_cert.pdf", activityDate: "2025-06-15", submittedDate: "2025-08-12" },
                    { id: 2, studentId: "2210427", studentName: "JANE DOE", kategori: "Independent Learning Campus Program", judul: "Getting Started with Python Programming", keterangan: "Completed the class 'Getting Started with Python Programming' on Dicoding platform with excellent grade", point: null, suggestedPoint: 15, waktu: "08 Aug 2025 21:23:33", status: "Waiting", certificate: "python_cert.pdf", activityDate: "2025-07-20", submittedDate: "2025-08-08" },
                    { id: 3, studentId: "2210428", studentName: "JOHN SMITH", kategori: "Workshop/Training/Seminar Activities", judul: "Web Development Workshop", keterangan: "Attended 3-day intensive web development workshop covering HTML, CSS, and JavaScript", point: null, suggestedPoint: 8, waktu: "10 Aug 2025 15:45:12", status: "Waiting", certificate: "workshop_cert.pdf", activityDate: "2025-08-01", submittedDate: "2025-08-10" },
                    { id: 4, studentId: "2210426", studentName: "CALVIN WILLIAM", kategori: "Achievement in Science, Literature and Other Academic Activities (olympiad, pitmapres, etc)", judul: "Pilmapres Region III Finalist", keterangan: "Became Finalist of Pilmapres Region III representing ITBSS", point: 18, waktu: "07 Aug 2025 21:19:52", status: "Approve", certificate: "pilmapres_cert.pdf", activityDate: "2025-07-15", submittedDate: "2025-08-07" },
                    { id: 5, studentId: "2210429", studentName: "ALICE JOHNSON", kategori: "IPR/Patent", judul: "Mobile App UI/UX Design Patent", keterangan: "Registered intellectual property rights for innovative mobile application design", point: null, waktu: "11 Aug 2025 10:30:00", status: "Cancel", certificate: "ipr_cert.pdf", activityDate: "2025-07-25", submittedDate: "2025-08-11", rejectReason: "Certificate does not match the activity description. Please resubmit with correct documentation." }
                ],
                get uniqueCategories() {
                    return [...new Set(this.submissions.map(s => s.kategori))];
                },
                get uniqueStudents() {
                    return [...new Set(this.submissions.map(s => `${s.studentId} - ${s.studentName}`))];
                },
                get filteredSubmissions() {
                    return this.submissions.filter(submission => {
                        const matchesSearch = this.searchQuery === '' ||
                            submission.judul.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            submission.keterangan.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            submission.kategori.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            submission.studentName.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            submission.studentId.includes(this.searchQuery);
                        const matchesStatus = this.statusFilter === '' || submission.status === this.statusFilter;
                        const matchesCategory = this.categoryFilter === '' || submission.kategori === this.categoryFilter;
                        const matchesStudent = this.studentFilter === '' || `${submission.studentId} - ${submission.studentName}` === this.studentFilter;
                        return matchesSearch && matchesStatus && matchesCategory && matchesStudent;
                    });
                },
                get stats() {
                    return {
                        total: this.submissions.length,
                        waiting: this.submissions.filter(s => s.status === 'Waiting').length,
                        approved: this.submissions.filter(s => s.status === 'Approve').length,
                        rejected: this.submissions.filter(s => s.status === 'Cancel').length
                    };
                },
                confirmLogout() {
                    window.location.href = '/login';
                },
                viewDetail(submission) {
                    this.selectedSubmission = submission;
                    this.assignedPoints = submission.suggestedPoint?.toString() || '';
                    this.showDetailModal = true;
                },
                handleApprove() {
                    if (!this.assignedPoints || this.assignedPoints <= 0) {
                        alert('Please enter valid points');
                        return;
                    }
                    const index = this.submissions.findIndex(s => s.id === this.selectedSubmission.id);
                    if (index !== -1) {
                        this.submissions[index].status = 'Approve';
                        this.submissions[index].point = parseInt(this.assignedPoints);
                    }
                    this.closeModal();
                },
                handleRejectConfirm() {
                    if (!this.rejectReason.trim()) {
                        alert('Please provide a reason for rejection');
                        return;
                    }
                    const index = this.submissions.findIndex(s => s.id === this.selectedSubmission.id);
                    if (index !== -1) {
                        this.submissions[index].status = 'Cancel';
                        this.submissions[index].rejectReason = this.rejectReason;
                    }
                    this.closeModal();
                },
                closeModal() {
                    this.showDetailModal = false;
                    this.showRejectModal = false;
                    this.selectedSubmission = null;
                    this.assignedPoints = '';
                    this.rejectReason = '';
                }
            }
        }
    </script>
</body>
</html>
