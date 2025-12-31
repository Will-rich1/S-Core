<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Admin Review - S-Core ITBSS (v2.0)</title>
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
</head>
<body>
    <div class="flex h-screen bg-gray-100" x-data="adminReviewData()" x-init="init()">
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

        <!-- Detail Modal - Full Screen -->
        <div x-show="showDetailModal" class="fixed inset-0 bg-black bg-opacity-50 z-50" style="display: none;">
            <div class="h-full w-full bg-white flex flex-col">
                <!-- Header -->
                <div class="bg-white border-b px-6 py-4 flex justify-between items-center">
                    <h3 class="text-xl font-semibold">Review Submission</h3>
                    <button @click="closeModal" class="text-gray-500 hover:text-gray-700 text-2xl leading-none w-8 h-8 flex items-center justify-center">×</button>
                </div>

                <!-- Content Area - 2 Columns -->
                <div class="flex-1 overflow-hidden flex">
                    <template x-if="selectedSubmission">
                        <div class="flex w-full h-full">
                            <!-- Left Column - PDF Viewer -->
                            <div class="w-1/2 bg-gray-100 border-r overflow-hidden p-6">
                                <div class="bg-white rounded-lg shadow-sm h-full flex flex-col">
                                    <div class="bg-gray-800 text-white px-4 py-3 rounded-t-lg flex items-center justify-between">
                                        <span class="text-sm font-medium">Certificate/Evidence</span>
                                        <span class="text-xs text-gray-300" x-text="selectedSubmission.certificate"></span>
                                    </div>
                                    <div class="flex-1 bg-gray-50 relative h-full overflow-hidden" x-data="{ loading: true }">
                                        <template x-if="selectedSubmission.file_url">
                                            <div class="relative w-full h-full">
                                                <!-- Loading spinner -->
                                                <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-gray-50 z-10">
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
                                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                                <p>File PDF tidak ditemukan di database.</p>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column - Form Details -->
                            <div class="w-1/2 flex flex-col">
                                <!-- Scrollable Content -->
                                <div class="flex-1 overflow-y-auto p-6">
                                    <div class="space-y-4">
                                        <!-- Student Information -->
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                            <h4 class="font-semibold text-blue-900 mb-3">Student Information</h4>
                                            <div class="grid grid-cols-2 gap-4 text-sm">
                                                <div>
                                                    <span class="text-gray-600 block mb-1">Student ID</span>
                                                    <span class="font-medium" x-text="selectedSubmission.studentId"></span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-600 block mb-1">Name</span>
                                                    <span class="font-medium" x-text="selectedSubmission.studentName"></span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-600 block mb-1">Submitted Date</span>
                                                    <span class="font-medium" x-text="selectedSubmission.submittedDate"></span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-600 block mb-1">Activity Date</span>
                                                    <span class="font-medium" x-text="selectedSubmission.activityDate"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Current Main Category -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Main Category</label>
                                            <div class="bg-gray-50 border rounded-lg px-4 py-3 text-sm" x-text="selectedSubmission.mainCategory"></div>
                                        </div>

                                        <!-- Current Subcategory -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Subcategory</label>
                                            <div class="bg-gray-50 border rounded-lg px-4 py-3 text-sm" x-text="selectedSubmission.subcategory"></div>
                                        </div>

                                        <!-- Activity Title -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Activity Title</label>
                                            <div class="bg-gray-50 border rounded-lg px-4 py-3 text-sm font-medium" x-text="selectedSubmission.judul"></div>
                                        </div>

                                        <!-- Description -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                            <div class="bg-gray-50 border rounded-lg px-4 py-3 text-sm whitespace-pre-wrap min-h-[100px]" x-text="selectedSubmission.keterangan"></div>
                                        </div>

                                        <!-- Assign Category -->
                                        <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Assign Main Category <span class="text-red-500">*</span>
                                            </label>
                                            <select x-model="assignedMainCategory" @change="updateAssignedSubcategories" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-3">
                                                <option value="">Select Main Category</option>
                                                <template x-for="(catGroup, idx) in categoryGroups" :key="idx">
                                                    <option :value="idx" x-text="catGroup.name"></option>
                                                </template>
                                            </select>

                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Assign Subcategory <span class="text-red-500">*</span>
                                            </label>
                                            <select x-model="assignedSubcategory" :disabled="assignedMainCategory === ''" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Select Subcategory</option>
                                                <template x-for="(subcat, subIdx) in assignedAvailableSubcategories" :key="subIdx">
                                                    <option :value="subIdx" x-text="subcat.name + ' (' + subcat.points + ' points)'"></option>
                                                </template>
                                            </select>
                                            <p class="text-xs text-gray-500 mt-2" x-show="assignedSubcategory !== ''">
                                                <span class="font-medium">Points for this category:</span> 
                                                <span class="text-blue-600 font-semibold" x-text="assignedAvailableSubcategories[assignedSubcategory]?.points || 0"></span> points
                                            </p>
                                            
                                            <!-- Category Change Reason (Optional) -->
                                            <div x-show="categoryChanged" class="mt-4 pt-4 border-t border-yellow-300">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Reason for Category Change (Optional)
                                                </label>
                                                <textarea x-model="categoryChangeReason" rows="3" class="w-full border border-yellow-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white" placeholder="Explain why you changed the category from the student's original selection (this will be included in the notification email)..."></textarea>
                                                <p class="text-xs text-gray-500 mt-1">This note will help the student understand the category reassignment</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Fixed Action Buttons -->
                                <div class="border-t bg-white px-6 py-4">
                                    <div class="flex gap-3 justify-end">
                                        <button @click="closeModal" class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm font-medium transition-colors">
                                            Cancel
                                        </button>
                                        <button @click="showRejectModal = true" class="px-6 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium transition-colors">
                                            Reject
                                        </button>
                                        <button @click="handleApprove" class="px-6 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm font-medium transition-colors">
                                            Approve
                                        </button>
                                    </div>
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
                    <h3 class="text-xl font-semibold text-red-600">Reject Submission</h3>
                    <button @click="showRejectModal = false" class="text-gray-500 hover:text-gray-700 text-2xl leading-none">×</button>
                </div>

                <template x-if="selectedSubmission">
                    <div>
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-4">
                                You are about to reject submission from <strong x-text="selectedSubmission.studentName"></strong> (<span x-text="selectedSubmission.studentId"></span>)
                            </p>
                            <p class="text-sm font-medium mb-2">Activity: <span x-text="selectedSubmission.judul"></span></p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Rejection <span class="text-red-500">*</span></label>
                            <select x-model="rejectReasonType" @change="if(rejectReasonType !== 'other') rejectReason = rejectReasonType" class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 mb-3">
                                <option value="">Select rejection reason</option>
                                <option value="Certificate does not match the activity description. Please resubmit with correct documentation.">Certificate does not match activity description</option>
                                <option value="Evidence/certificate is unclear or incomplete. Please upload a clearer version.">Evidence unclear or incomplete</option>
                                <option value="Activity date exceeds the allowed timeframe (maximum 1 month). Please submit activities within the valid period.">Activity date exceeds allowed timeframe</option>
                                <option value="Wrong category selected. Please resubmit with the correct category.">Wrong category selected</option>
                                <option value="Duplicate submission detected. This activity has already been submitted.">Duplicate submission</option>
                                <option value="Activity does not meet S-Core requirements. Please refer to the guidelines.">Does not meet S-Core requirements</option>
                                <option value="other">Other (specify below)</option>
                            </select>
                            
                            <div x-show="rejectReasonType === 'other' || rejectReasonType === ''" class="mt-3">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span x-show="rejectReasonType === 'other'">Specify Reason / Additional Notes</span>
                                    <span x-show="rejectReasonType === ''">Custom Reason</span>
                                </label>
                                <textarea x-model="rejectReason" rows="4" class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500" :placeholder="rejectReasonType === 'other' ? 'Please specify the reason for rejection or provide additional notes for the student...' : 'Please provide a clear reason for rejection so the student can understand and resubmit correctly...'"></textarea>
                            </div>
                        </div>

                        <div class="flex gap-3 justify-end">
                            <button @click="showRejectModal = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm font-medium">Cancel</button>
                            <button @click="handleRejectConfirm" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded text-sm font-medium">Confirm Rejection</button>
                        </div>
                    </div>
                </template>
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
                            Major: <span x-text="selectedStudent?.major"></span> | 
                            Year: <span x-text="selectedStudent?.year"></span>
                        </p>
                    </div>
                    <button @click="showStudentDetailModal = false" class="text-gray-500 hover:text-gray-700 text-2xl leading-none">×</button>
                </div>

                <div class="flex-1 overflow-y-auto p-6 bg-gray-50">
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="bg-white p-4 rounded border shadow-sm text-center">
                            <p class="text-xs text-gray-500">Total Points</p>
                            <p class="text-xl font-bold text-green-600" x-text="selectedStudent?.approvedPoints"></p>
                        </div>
                        <div class="bg-white p-4 rounded border shadow-sm text-center">
                            <p class="text-xs text-gray-500">Status</p>
                            <span :class="selectedStudent?.approvedPoints >= 20 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="px-2 py-1 rounded-full text-xs font-bold mt-1 inline-block">
                                <span x-text="selectedStudent?.approvedPoints >= 20 ? 'PASSED' : 'NOT PASSED'"></span>
                            </span>
                        </div>
                        <div class="bg-white p-4 rounded border shadow-sm text-center">
                            <p class="text-xs text-gray-500">Submissions</p>
                            <p class="text-xl font-bold text-blue-600" x-text="selectedStudent?.totalSubmissions"></p>
                        </div>
                    </div>

                    <h4 class="font-semibold text-gray-700 mb-3">Submission History</h4>
                    
                    <div class="bg-white rounded-lg shadow overflow-hidden border">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                                <tr>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3">Title</th>
                                    <th class="px-4 py-3">Category</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3 text-center">Points</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template x-for="sub in selectedStudent?.submissions_list" :key="sub.id">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap" x-text="sub.date"></td>
                                        <td class="px-4 py-3 font-medium text-gray-800" x-text="sub.title"></td>
                                        <td class="px-4 py-3 text-gray-600">
                                            <div x-text="sub.category" class="font-semibold text-xs"></div>
                                            <div x-text="sub.subcategory" class="text-xs"></div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span :class="{
                                                'bg-green-100 text-green-700': sub.status === 'Approved',
                                                'bg-yellow-100 text-yellow-700': sub.status === 'Waiting',
                                                'bg-red-100 text-red-700': sub.status === 'Rejected'
                                            }" class="px-2 py-1 rounded-full text-xs font-semibold" x-text="sub.status"></span>
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold" 
                                            :class="sub.status === 'Approved' ? 'text-green-600' : 'text-gray-400'" 
                                            x-text="sub.points || '-'"></td>
                                    </tr>
                                </template>
                                <template x-if="!selectedStudent?.submissions_list?.length">
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">No submissions found for this student.</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white border-t px-6 py-4 flex justify-end gap-3 items-center">
                    <button @click="resetStudentPassword()" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-sm font-medium">Reset Password</button>
                    <button @click="showStudentDetailModal = false" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm font-medium">Close</button>
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
                            <h3 class="text-xl font-semibold">Security Verification Required</h3>
                            <p class="text-red-100 text-sm mt-1">Category Management is a sensitive operation</p>
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
                                <p class="font-semibold">Warning!</p>
                                <p>Changes to categories can affect all student submissions. Please enter PIN to proceed.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Enter PIN Code</label>
                        <input 
                            type="password" 
                            x-model="pinInput" 
                            @keyup.enter="verifyPin"
                            placeholder="Enter 6-digit PIN" 
                            maxlength="6"
                            class="w-full border-2 rounded-lg px-4 py-3 text-center text-2xl tracking-widest font-mono focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                            :class="pinError ? 'border-red-500 bg-red-50' : 'border-gray-300'"
                        />
                        <p x-show="pinError" class="text-red-600 text-sm mt-2 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            Incorrect PIN. Please try again.
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <button @click="closePinModal" class="flex-1 px-4 py-2.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm font-medium">Cancel</button>
                        <button @click="verifyPin" class="flex-1 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium">Verify PIN</button>
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
                            <h3 class="text-lg font-semibold text-gray-800">Confirm Edit Category</h3>
                            <p class="text-sm text-gray-600">Are you sure you want to save these changes?</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4" x-show="editingCategory">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Category:</span>
                                <span class="font-semibold" x-text="editingCategory?.name"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Points:</span>
                                <span class="font-semibold" x-text="editingCategory?.points"></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button @click="showEditConfirmModal = false" class="flex-1 px-4 py-2.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm font-medium">Cancel</button>
                        <button @click="confirmSaveCategory" class="flex-1 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium">Save Changes</button>
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
                            <h3 class="text-lg font-semibold text-gray-800">Confirm Delete Category</h3>
                            <p class="text-sm text-gray-600">This action cannot be undone!</p>
                        </div>
                    </div>

                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4" x-show="deletingCategory">
                        <p class="text-sm text-red-800 mb-2">
                            You are about to delete: <strong x-text="deletingCategory?.name"></strong>
                        </p>
                        <p class="text-xs text-red-700" x-show="deletingCategory?.usageCount > 0">
                            ⚠️ This category is currently used in <strong x-text="deletingCategory?.usageCount"></strong> submission(s). Deleting it may affect existing data.
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <button @click="showDeleteConfirmModal = false; deletingCategory = null" class="flex-1 px-4 py-2.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm font-medium">Cancel</button>
                        <button @click="confirmDeleteCategoryFinal" class="flex-1 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium">Delete Category</button>
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
                        <button x-show="alertHasCancel" @click="closeAlertModal(false)" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm font-medium">Cancel</button>
                        <button @click="closeAlertModal(true)" class="px-4 py-2 rounded-lg text-sm font-medium text-white" :class="{
                            'bg-green-500 hover:bg-green-600': alertType === 'success',
                            'bg-blue-500 hover:bg-blue-600': alertType === 'info',
                            'bg-yellow-500 hover:bg-yellow-600': alertType === 'warning',
                            'bg-red-500 hover:bg-red-600': alertType === 'error'
                        }" x-text="alertHasCancel ? 'Confirm' : 'OK'"></button>
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
                            <h3 class="text-lg font-semibold text-gray-800">Approve Submission</h3>
                            <p class="text-sm text-gray-600">Confirm approval details</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 mb-4" x-show="selectedSubmission">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Student:</span>
                                <span class="font-semibold" x-text="selectedSubmission?.studentName"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Main Category:</span>
                                <span class="font-semibold" x-text="approveModalMainCategory"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subcategory:</span>
                                <span class="font-semibold" x-text="approveModalSubcategory"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Points:</span>
                                <span class="font-semibold text-green-600" x-text="approveModalPoints"></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button @click="showApproveModal = false" class="flex-1 px-4 py-2.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm font-medium">Cancel</button>
                        <button @click="confirmApprove" class="flex-1 px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm font-medium">Approve</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Management Modal -->
        <div x-show="showCategoryModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-[60]" style="display: none;">
            <div class="bg-white rounded-lg max-w-6xl w-full mx-4 shadow-2xl max-h-[90vh] flex flex-col">
                <div class="flex justify-between items-center p-6 border-b">
                    <div class="flex items-center gap-2">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-800">Category Management</h3>
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-semibold">Verified</span>
                    </div>
                    <button @click="closeCategoryModal" class="text-gray-500 hover:text-gray-700 text-2xl leading-none">×</button>
                </div>

                <div class="flex-1 overflow-y-auto p-6">
                    <!-- Add New Main Category Section -->
                    <div class="bg-green-50 border-2 border-green-200 rounded-lg p-4 mb-4">
                        <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add New Main Category
                        </h4>
                        <div class="flex gap-3">
                            <input type="text" x-model="newMainCategory" placeholder="Main Category Name" class="flex-1 border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500" />
                            <button @click="addMainCategory" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                Add Main Category
                            </button>
                        </div>
                    </div>

                    <!-- Add New Subcategory Section -->
                    <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-4 mb-6">
                        <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add New Subcategory
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <select x-model="newCategory.mainCategoryIndex" class="border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Main Category</option>
                                <template x-for="(cat, idx) in categories" :key="cat.id">
                                    <option :value="idx" x-text="(idx + 1) + '. ' + cat.name"></option>
                                </template>
                            </select>
                            <input type="text" x-model="newCategory.name" placeholder="Subcategory Name" class="border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            <input type="number" x-model="newCategory.points" placeholder="Points" class="border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            <input type="text" x-model="newCategory.description" placeholder="Description" class="border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <button @click="addSubcategory" class="mt-3 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                            Add Subcategory
                        </button>
                    </div>

                    <!-- Categories List -->
                    <div class="space-y-4">
                        <h4 class="font-semibold text-gray-800 mb-3">Existing Categories & Subcategories</h4>
                        
                        <template x-for="(cat, catIndex) in categories" :key="cat.id">
                            <div class="bg-gray-50 border-2 border-gray-300 rounded-lg p-4 mb-4">
                                
                                <div class="flex items-center justify-between mb-3">
                                        <template x-if="!cat.isEditing">
                                            <h5 class="font-bold text-lg text-gray-800">
                                                <span x-text="(catIndex + 1) + '. ' + cat.name"></span>
                                                <span x-show="cat.is_active == 0 || cat.is_active === false" class="ml-2 inline-block text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded">Inactive</span>
                                            </h5>
                                        </template>
                                    
                                    <template x-if="cat.isEditing">
                                        <input type="text" x-model="cat.name" class="flex-1 border-2 border-blue-500 rounded-lg px-3 py-1.5 text-lg font-bold focus:outline-none focus:ring-2 focus:ring-blue-500 mr-3" />
                                    </template>

                                    <div class="flex gap-2">
                                        <template x-if="!cat.isEditing">
                                            <button @click="cat.isEditing = true" class="text-blue-500 hover:text-blue-700 p-1" title="Edit Main Category">
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
                                            <button @click="deleteMainCategory(catIndex)" class="text-red-500 hover:text-red-700 p-1" title="Delete Main Category">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </template>
                                        <template x-if="cat.is_active == 0 || cat.is_active === false">
                                            <button @click="reactivateCategory(catIndex)" class="text-green-600 hover:text-green-800 p-1" title="Restore Category">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                
                                <div class="space-y-2 ml-4">
                                    <template x-for="(subcat, subIndex) in cat.subcategories" :key="subIndex">
                                        <div class="bg-white border border-gray-200 rounded-lg p-3 hover:border-blue-300 transition-colors">
                                            <div class="flex items-center gap-3">
                                                
                                                <template x-if="subcat.isEditing">
                                                    <div class="flex-1 flex items-center gap-2">
                                                        <input type="text" x-model="subcat.name" placeholder="Name" class="flex-1 border rounded px-2 py-1 text-sm">
                                                        <input type="number" x-model="subcat.points" placeholder="Pts" class="w-16 border rounded px-2 py-1 text-sm">
                                                        <input type="text" x-model="subcat.description" placeholder="Desc" class="flex-1 border rounded px-2 py-1 text-sm">
                                                        <button @click="saveSubcategory(catIndex, subIndex)" class="text-green-600 hover:text-green-800"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></button>
                                                        <button @click="cancelEditSubcategory(catIndex, subIndex)" class="text-gray-500 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                                                    </div>
                                                </template>
                                                
                                                <template x-if="!subcat.isEditing">
                                                    <div class="flex-1 flex items-center justify-between">
                                                        <div class="flex-1">
                                                            <h6 class="font-medium text-gray-800 text-sm" x-text="subcat.name"></h6>
                                                            <p class="text-xs text-gray-500 mt-1">
                                                                Points: <span class="font-semibold text-blue-600" x-text="subcat.points"></span> | 
                                                                <span x-text="subcat.description"></span>
                                                            </p>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <div class="flex items-center gap-2">
                                                                <button @click="editSubcategory(catIndex, subIndex)" class="text-blue-500 hover:text-blue-700 p-1" title="Edit Subcategory">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                                </button>
                                                                <!-- show delete only when active (1) or when property missing; hide for 0/'0'/false -->
                                                                <button x-show="subcat.is_active == 1 || typeof(subcat.is_active) === 'undefined'" @click="deleteSubcategory(catIndex, subIndex)" class="flex items-center gap-1 text-red-500 hover:text-red-700 px-3 py-1 rounded-md text-sm bg-red-50 hover:bg-red-100" title="Delete Subcategory" aria-label="Delete Subcategory">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                                    <span class="hidden sm:inline">Hapus</span>
                                                                </button>
                                                            </div>
                                                            <template x-if="subcat.is_active == 0 || subcat.is_active === false">
                                                                <button @click="reactivateSubcategory(catIndex, subIndex)" class="text-green-600 hover:text-green-800 p-1" title="Restore Subcategory">
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

                <div class="border-t p-6">
                    <div class="flex gap-3 justify-between items-center">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" class="mr-2" x-model="showInactiveCategories" @change="loadCategories()">
                            <span class="text-xs text-gray-600">Show inactive</span>
                        </label>
                        <div class="flex gap-3 justify-end">
                            <button @click="closeCategoryModal" class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm font-medium">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>        <!-- Sidebar -->
        <div :class="isSidebarOpen ? 'w-64' : 'w-20'" class="bg-white shadow-lg transition-all duration-300 flex flex-col">
            <div class="p-4 border-b flex flex-col items-center">
                <img src="/images/logo.png" alt="Logo" class="w-12 h-12 object-contain">
                <div x-show="isSidebarOpen" class="mt-2 text-center">
                    <h2 class="text-sm font-bold text-gray-800">S-Core ITBSS</h2>
                    <p class="text-xs text-gray-500">Admin Review & Management</p>
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
                    <span x-show="isSidebarOpen" class="text-sm" :class="activeMenu === 'Review Submissions' ? 'text-blue-700 font-medium' : 'text-gray-700'">Review Submissions</span>
                </button>
                <!-- <button @click="activeMenu = 'Statistics'" class="w-full flex items-center py-3 text-left hover:bg-gray-100 transition-colors" :class="[
                    isSidebarOpen ? 'gap-3 px-4' : 'justify-center',
                    activeMenu === 'Statistics' ? 'bg-blue-50 border-l-4 border-blue-500' : ''
                ]">
                    <svg class="w-6 h-6" :class="activeMenu === 'Statistics' ? 'text-blue-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span x-show="isSidebarOpen" class="text-sm" :class="activeMenu === 'Statistics' ? 'text-blue-700 font-medium' : 'text-gray-700'">Statistics</span>
                </button> -->
                <button @click="activeMenu = 'Students'" class="w-full flex items-center py-3 text-left hover:bg-gray-100 transition-colors" :class="[
                    isSidebarOpen ? 'gap-3 px-4' : 'justify-center',
                    activeMenu === 'Students' ? 'bg-blue-50 border-l-4 border-blue-500' : ''
                ]">
                    <svg class="w-6 h-6" :class="activeMenu === 'Students' ? 'text-blue-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span x-show="isSidebarOpen" class="text-sm" :class="activeMenu === 'Students' ? 'text-blue-700 font-medium' : 'text-gray-700'">Students</span>
                </button>
                <button @click="activeMenu = 'Bulk Score'" class="w-full flex items-center py-3 text-left hover:bg-gray-100 transition-colors" :class="[
                    isSidebarOpen ? 'gap-3 px-4' : 'justify-center',
                    activeMenu === 'Bulk Score' ? 'bg-blue-50 border-l-4 border-blue-500' : ''
                ]">
                    <svg class="w-6 h-6" :class="activeMenu === 'Bulk Score' ? 'text-blue-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span x-show="isSidebarOpen" class="text-sm" :class="activeMenu === 'Bulk Score' ? 'text-blue-700 font-medium' : 'text-gray-700'">Bulk Score</span>
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
                    <div class="mb-6">
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">S-Core Submission Review</h1>
                        <p class="text-gray-600">Review and approve student activity submissions</p>
                    </div>

                    <!-- Filters -->
                    <div class="bg-white rounded-lg shadow p-4 mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                            <select x-model="statusFilter" class="border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">All Status</option>
                                <option value="Waiting">Waiting</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected">Rejected</option>
                            </select>

                            <select x-model="categoryFilter" class="border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">All Categories</option>
                                <template x-for="cat in categories" :key="cat.id">
                                    <option :value="cat.name" x-text="cat.name.length > 30 ? cat.name.substring(0, 30) + '...' : cat.name"></option>
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
                                    <th class="text-left py-3 px-4 font-semibold text-sm">Main Category</th>
                                    <th class="text-left py-3 px-4 font-semibold text-sm">Subcategory</th>
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

                <!-- Statistics Page -->
                <div x-show="activeMenu === 'Statistics'">
                    <div class="mb-6">
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">Statistics</h1>
                        <p class="text-gray-600">Overview of S-Core submission statistics</p>
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

                <!-- Chart Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4">Submission Trends</h3>
                        <div class="flex items-center justify-center h-64 bg-gray-50 rounded border-2 border-dashed">
                            <p class="text-gray-500">Chart will be displayed here</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4">Category Distribution</h3>
                        <div class="flex items-center justify-center h-64 bg-gray-50 rounded border-2 border-dashed">
                            <p class="text-gray-500">Chart will be displayed here</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Recent Activity</h3>
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
                                }" class="px-2 py-1 rounded-full text-xs font-semibold" x-text="submission.status"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Students Page -->
            <div x-show="activeMenu === 'Students'">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Student Management & Reports</h1>
                    <p class="text-gray-600">Comprehensive student performance reports and S-Core tracking</p>
                </div>

                <!-- Filter Bar -->
                <div class="bg-white rounded-lg shadow p-4 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                        <input type="text" x-model="studentSearchQuery" placeholder="Search by name or ID..." class="border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        
                        <select x-model="majorFilter" class="border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Majors</option>
                            <option value="STI">STI</option>
                            <option value="BD">BD</option>
                            <option value="KWU">KWU</option>
                        </select>
                        
                        <select x-model="yearFilter" class="border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Years</option>
                            <option value="2021">Angkatan 2021</option>
                            <option value="2022">Angkatan 2022</option>
                            <option value="2023">Angkatan 2023</option>
                            <option value="2024">Angkatan 2024</option>
                            <option value="2025">Angkatan 2025</option>
                        </select>
                        
                        <select x-model="statusPassFilter" class="border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Status</option>
                            <option value="pass">Passed (>= 20 points)</option>
                            <option value="fail">Not Passed (< 20 points)</option>
                        </select>
                        
                        <button @click="exportReport" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export Report
                        </button>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Total Students</p>
                                <p class="text-2xl font-bold text-gray-800" x-text="filteredStudentsList.length"></p>
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
                                <p class="text-sm text-gray-600">Passed Requirements</p>
                                <p class="text-2xl font-bold text-green-600" x-text="studentStats.passed"></p>
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
                                <p class="text-sm text-gray-600">Not Passed</p>
                                <p class="text-2xl font-bold text-red-600" x-text="studentStats.failed"></p>
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
                                <p class="text-sm text-gray-600">Average Points</p>
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
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left py-3 px-4 font-semibold text-sm">Student ID</th>
                                <th class="text-left py-3 px-4 font-semibold text-sm">Name</th>
                                <th class="text-center py-3 px-4 font-semibold text-sm">Major</th>
                                <th class="text-center py-3 px-4 font-semibold text-sm">Year</th>
                                <th class="text-center py-3 px-4 font-semibold text-sm">Total Points</th>
                                <th class="text-center py-3 px-4 font-semibold text-sm">Status</th>
                                <th class="text-center py-3 px-4 font-semibold text-sm">Pending</th>
                                <th class="text-center py-3 px-4 font-semibold text-sm">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <template x-for="student in filteredStudentsList" :key="student.id">
                            <tr class="border-b hover:bg-blue-50 transition-colors group" x-data="{ showTooltip: false, tooltipX: 0, tooltipY: 0 }">
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
                                                <span :class="student.approvedPoints >= 20 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="px-2 py-1 rounded-full text-xs font-semibold">
                                                    <span x-text="student.approvedPoints >= 20 ? 'PASSED' : 'NOT PASSED'"></span>
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-600" x-text="student.id"></p>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="flex justify-between items-center mb-2">
                                                <span class="text-sm font-semibold text-gray-700">Total Points:</span>
                                                <span class="text-lg font-bold" :class="student.approvedPoints >= 20 ? 'text-green-600' : 'text-red-600'" x-text="student.approvedPoints"></span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="h-2 rounded-full transition-all" :class="student.approvedPoints >= 20 ? 'bg-green-500' : 'bg-red-500'" :style="`width: ${Math.min((student.approvedPoints / 20) * 100, 100)}%`"></div>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1" x-text="`${Math.max(20 - student.approvedPoints, 0)} points needed to pass`"></p>
                                        </div>
                                        
                                        <div class="space-y-2">
                                            <p class="text-xs font-semibold text-gray-700 mb-2">Points by Category:</p>
                                            <template x-for="(points, category) in student.categoryBreakdown" :key="category">
                                                <div class="flex justify-between text-xs">
                                                    <span class="text-gray-600 truncate pr-2" :title="category" x-text="category.substring(0, 35) + (category.length > 35 ? '...' : '')"></span>
                                                    <span class="font-semibold text-blue-600" x-text="points + ' pts'"></span>
                                                </div>
                                            </template>
                                        </div>
                                        
                                        <div class="mt-3 pt-3 border-t border-gray-200 grid grid-cols-3 gap-2 text-center">
                                            <div>
                                                <p class="text-xs text-gray-600">Total</p>
                                                <p class="font-semibold text-sm" x-text="student.totalSubmissions"></p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-600">Approved</p>
                                                <p class="font-semibold text-sm text-green-600" x-text="student.approvedCount"></p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-600">Pending</p>
                                                <p class="font-semibold text-sm text-yellow-600" x-text="student.pending"></p>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-center py-3 px-4">
                                    <span :class="student.approvedPoints >= 20 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="px-3 py-1 rounded-full text-xs font-semibold">
                                        <span x-text="student.approvedPoints >= 20 ? 'PASSED' : 'NOT PASSED'"></span>
                                    </span>
                                </td>

                                <td class="text-center py-3 px-4 text-sm text-yellow-600 font-medium" x-text="student.pending"></td>
                                
                                <td class="text-center py-3 px-4">
                                    <button @click="viewStudentDetail(student)" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs font-medium">View Details</button>
                                </td>
                            </tr>
                        </template>
                        
                        <template x-if="filteredStudentsList.length === 0">
                            <tr>
                                <td colspan="8" class="text-center py-8 text-gray-500">No students found matching your filters</td>
                            </tr>
                        </template>
                    </tbody>
                    </table>
                </div>
            </div>

            <!-- Bulk Score Page -->
            <div x-show="activeMenu === 'Bulk Score'">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Bulk S-Core Assignment</h1>
                    <p class="text-gray-600">Create S-Core submissions for multiple students at once based on filters</p>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <form @submit.prevent="applyBulkScore()" class="space-y-6">
                        <!-- Filter Type Selection -->
                        <div class="border-b pb-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">1. Select Target Students</h3>
                            <p class="text-sm text-gray-600 mb-3">You can select multiple filters to target specific students. Leave all filters empty to target all students.</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Major Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Major</label>
                                    <select x-model="bulkScore.selectedMajor" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">All Majors</option>
                                        <option value="STI">STI</option>
                                        <option value="BD">BD</option>
                                        <option value="KWU">KWU</option>
                                    </select>
                                </div>

                                <!-- Year Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Batch Year</label>
                                    <select x-model="bulkScore.selectedYear" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">All Years</option>
                                        <option value="2021">2021</option>
                                        <option value="2022">2022</option>
                                        <option value="2023">2023</option>
                                        <option value="2024">2024</option>
                                        <option value="2025">2025</option>
                                    </select>
                                </div>

                                <!-- Shift Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Shift</label>
                                    <select x-model="bulkScore.selectedShift" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">All Shifts</option>
                                        <option value="pagi">Pagi</option>
                                        <option value="sore">Sore</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Activity Details Section -->
                        <div class="border-b pb-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">2. Activity Details</h3>
                            
                            <!-- Main Category -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Main Category <span class="text-red-500">*</span></label>
                                <select x-model="bulkScore.mainCategory" @change="bulkScore.subcategory = ''" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    <option value="">Choose main category...</option>
                                    <template x-for="(catGroup, idx) in categoryGroups" :key="idx">
                                        <option :value="catGroup.id" x-text="(idx + 1) + '. ' + catGroup.name"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Subcategory -->
                            <div class="mb-4" x-show="bulkScore.mainCategory !== ''">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Subcategory <span class="text-red-500">*</span></label>
                                <select x-model="bulkScore.subcategory" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    <option value="">Choose subcategory...</option>
                                    <template x-for="(catGroup, idx) in categoryGroups" :key="idx">
                                        <template x-if="catGroup.id == bulkScore.mainCategory">
                                            <template x-for="sub in catGroup.subcategories" :key="sub.id">
                                                <option :value="sub.id" x-text="sub.name + ' (' + sub.points + ' pts)'"></option>
                                            </template>
                                        </template>
                                    </template>
                                </select>
                            </div>

                            <!-- Activity Title -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Activity Title <span class="text-red-500">*</span></label>
                                <input type="text" x-model="bulkScore.activityTitle" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter activity title" required>
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description <span class="text-red-500">*</span></label>
                                <textarea x-model="bulkScore.description" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter description" required></textarea>
                            </div>

                            <!-- Activity Date -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Activity Date <span class="text-red-500">*</span></label>
                                <input type="date" x-model="bulkScore.activityDate" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>

                            <!-- Certificate Upload -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Certificate/Proof <span class="text-gray-500">(Optional)</span></label>
                                <input type="file" x-ref="bulkCertificate" accept=".pdf,.jpg,.jpeg,.png" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Upload PDF, JPG, or PNG if available (max 10MB)</p>
                            </div>
                        </div>

                        <!-- Auto Points Display -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <p class="text-sm text-gray-700 mb-1"><strong>Points to be awarded:</strong></p>
                            <template x-for="(catGroup, idx) in categoryGroups" :key="idx">
                                <template x-if="catGroup.id == bulkScore.mainCategory">
                                    <template x-for="sub in catGroup.subcategories" :key="sub.id">
                                        <template x-if="sub.id == bulkScore.subcategory">
                                            <div>
                                                <p class="text-2xl font-bold text-green-600" x-text="sub.points"></p>
                                                <p class="text-xs text-gray-500 mt-1">Points will be automatically assigned and approved</p>
                                            </div>
                                        </template>
                                    </template>
                                </template>
                            </template>
                            <template x-if="!bulkScore.subcategory">
                                <div>
                                    <p class="text-2xl font-bold text-gray-400">0</p>
                                    <p class="text-xs text-gray-500 mt-1">Select a subcategory to see points</p>
                                </div>
                            </template>
                        </div>

                        <!-- Preview --> 
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h3 class="font-semibold text-blue-900 mb-2">Preview</h3>
                            <div class="text-sm text-blue-800 space-y-1">
                                <p><strong>Target Students:</strong></p>
                                <ul class="list-disc ml-5">
                                    <li x-show="bulkScore.selectedMajor">Major: <span class="font-bold" x-text="bulkScore.selectedMajor"></span></li>
                                    <li x-show="bulkScore.selectedYear">Year: <span class="font-bold" x-text="bulkScore.selectedYear"></span></li>
                                    <li x-show="bulkScore.selectedShift">Shift: <span class="font-bold" x-text="bulkScore.selectedShift === 'pagi' ? 'Pagi' : 'Sore'"></span></li>
                                    <li x-show="!bulkScore.selectedMajor && !bulkScore.selectedYear && !bulkScore.selectedShift" class="text-gray-600">All students (no filter applied)</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-3 pt-4">
                            <button 
                                type="submit"
                                :disabled="bulkScore.isSubmitting"
                                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white rounded-lg text-sm font-medium transition">
                                <span x-show="!bulkScore.isSubmitting">Create Bulk Submissions</span>
                                <span x-show="bulkScore.isSubmitting">Processing...</span>
                            </button>
                            <button 
                                type="button"
                                @click="resetBulkScoreForm()"
                                class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition">
                                Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Settings Page -->
            <div x-show="activeMenu === 'Settings'" x-data="{ settingsTab: 'students' }">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Settings</h1>
                    <p class="text-gray-600">Configure S-Core system settings and manage user accounts</p>
                </div>

                <!-- Tab Navigation -->
                <div class="mb-6 border-b border-gray-200">
                    <nav class="flex gap-4">
                        <button @click="settingsTab = 'students'" :class="settingsTab === 'students' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-2 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Student Accounts
                        </button>
                        <button @click="settingsTab = 'admins'" :class="settingsTab === 'admins' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-2 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            Admin Accounts
                        </button>
                        <button @click="settingsTab = 'categories'" :class="settingsTab === 'categories' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-2 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            Category Management
                        </button>
                        <button @click="settingsTab = 'system'" :class="settingsTab === 'system' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-2 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            System Info
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
                <div x-show="settingsTab === 'students'" class="space-y-6">
                    <!-- CSV Upload Form -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            Bulk Upload Student Accounts (CSV)
                        </h3>

                        <div class="mb-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <p class="text-sm text-blue-800"><strong>CSV Format Requirements:</strong></p>
                                <a href="/sample_students.csv" download class="text-xs bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Download Sample
                                </a>
                            </div>
                            <p class="text-sm text-blue-700 mb-2">The CSV file must have the following columns (in order):</p>
                            <code class="text-xs bg-blue-100 px-2 py-1 rounded block mb-2">name,email,password,student_id,major,batch_year</code>
                            <p class="text-xs text-blue-600">Example: <code class="bg-blue-100 px-1 rounded">John Doe,john.doe@itbss.ac.id,password123,2021001,STI,2021</code></p>
                            <div class="mt-2 pt-2 border-t border-blue-200">
                                <p class="text-xs text-blue-600"><strong>Notes:</strong></p>
                                <ul class="text-xs text-blue-600 list-disc ml-4 mt-1">
                                    <li>Do not include a header row</li>
                                    <li>All fields are required</li>
                                    <li>Major must be: STI, BD, or KWU</li>
                                    <li>Email must be unique</li>
                                </ul>
                            </div>
                        </div>

                        <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select CSV File <span class="text-red-500">*</span></label>
                                <input type="file" name="csv_file" accept=".csv" required class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    Upload & Import Students
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Manual Add Student Form -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            Add Single Student Account
                        </h3>

                        <form action="{{ route('students.store') }}" method="POST" class="space-y-4">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter full name">
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" required class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter email address">
                                </div>

                                <!-- Password -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                                    <input type="password" name="password" required class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter password (min 6 characters)">
                                </div>

                                <!-- Student ID -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Student ID <span class="text-red-500">*</span></label>
                                    <input type="text" name="student_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter student ID">
                                </div>

                                <!-- Major -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Major <span class="text-red-500">*</span></label>
                                    <select name="major" required class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Major</option>
                                        <option value="STI">STI</option>
                                        <option value="BD">BD</option>
                                        <option value="KWU">KWU</option>
                                    </select>
                                </div>

                                <!-- Batch Year -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Batch Year <span class="text-red-500">*</span></label>
                                    <input type="number" name="batch_year" required class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., 2022">
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end pt-4">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Add Student
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
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            Add New Admin Account
                        </h3>

                        <div class="mb-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <p class="text-sm text-yellow-800"><strong>⚠️ Important:</strong> Admin accounts have full access to the system. Please ensure you trust the person before creating an admin account.</p>
                        </div>

                        <form action="{{ route('admins.store') }}" method="POST" class="space-y-4">
                            @csrf

                            <div class="grid grid-cols-1 gap-4">
                                <!-- Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" x-model="adminName" required class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Enter admin full name">
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" x-model="adminEmail" required class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Enter email address">
                                </div>

                                <!-- Password -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                                    <input type="password" name="password" x-model="adminPassword" @input="checkPasswordStrength(); checkPasswordMatch()" required minlength="6" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Enter password (minimum 6 characters)">
                                    
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
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password <span class="text-red-500">*</span></label>
                                    <input type="password" name="password_confirmation" x-model="adminPasswordConfirm" @input="checkPasswordMatch()" required class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" :class="!passwordMatch ? 'border-red-500' : ''" placeholder="Re-enter password">
                                    
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
                                <button type="submit" :disabled="!passwordMatch || adminPassword.length < 6" :class="!passwordMatch || adminPassword.length < 6 ? 'bg-gray-400 cursor-not-allowed' : 'bg-purple-500 hover:bg-purple-600'" class="text-white px-6 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Create Admin Account
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Category Management Tab -->
                <div x-show="settingsTab === 'categories'" class="space-y-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            Manage Categories
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">Manage S-Core categories, rename, add/remove categories, and configure their default point values</p>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">Total Categories: <span class="font-bold" x-text="categories.length"></span></span>
                        </div>
                        <button @click="requestCategoryManagement" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Manage Categories
                        </button>
                    </div>
                </div>

                <!-- System Info Tab -->
                <div x-show="settingsTab === 'system'" class="space-y-6">
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
                                <span class="font-medium">December 21, 2025</span>
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
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            Bulk Upload Student Accounts (CSV)
                        </h3>

                        <div class="mb-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <p class="text-sm text-blue-800"><strong>CSV Format Requirements:</strong></p>
                                <a href="/sample_students.csv" download class="text-xs bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Download Sample
                                </a>
                            </div>
                            <p class="text-sm text-blue-700 mb-2">The CSV file must have the following columns (in order):</p>
                            <code class="text-xs bg-blue-100 px-2 py-1 rounded block mb-2">name,email,password,student_id,major,batch_year</code>
                            <p class="text-xs text-blue-600">Example: <code class="bg-blue-100 px-1 rounded">John Doe,john@example.com,password123,2021001,STI,2021</code></p>
                            <div class="mt-2 pt-2 border-t border-blue-200">
                                <p class="text-xs text-blue-600"><strong>Notes:</strong></p>
                                <ul class="text-xs text-blue-600 list-disc ml-4 mt-1">
                                    <li>Do not include a header row</li>
                                    <li>All fields are required</li>
                                    <li>Major must be: STI, BD, or KWU</li>
                                    <li>Email must be unique</li>
                                </ul>
                            </div>
                        </div>

                        <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select CSV File <span class="text-red-500">*</span></label>
                                <input type="file" name="csv_file" accept=".csv" required class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    Upload & Import Students
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Manual Add Student Form -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            Add Single Student Account
                        </h3>

                        <form action="{{ route('students.store') }}" method="POST" class="space-y-4">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter full name">
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" required class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter email address">
                                </div>

                                <!-- Password -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                                    <input type="password" name="password" required class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter password (min 6 characters)">
                                </div>

                                <!-- Student ID -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Student ID <span class="text-red-500">*</span></label>
                                    <input type="text" name="student_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter student ID">
                                </div>

                                <!-- Major -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Major <span class="text-red-500">*</span></label>
                                    <select name="major" required class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Major</option>
                                        <option value="STI">STI</option>
                                        <option value="BD">BD</option>
                                        <option value="KWU">KWU</option>
                                    </select>
                                </div>

                                <!-- Batch Year -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Batch Year <span class="text-red-500">*</span></label>
                                    <input type="number" name="batch_year" required class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., 2022">
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end pt-4">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Add Student
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
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            Add New Admin Account
                        </h3>

                        <div class="mb-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <p class="text-sm text-yellow-800"><strong>⚠️ Important:</strong> Admin accounts have full access to the system. Please ensure you trust the person before creating an admin account.</p>
                        </div>

                        <form action="{{ route('admins.store') }}" method="POST" class="space-y-4">
                            @csrf

                            <div class="grid grid-cols-1 gap-4">
                                <!-- Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" x-model="adminName" required class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Enter admin full name">
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" x-model="adminEmail" required class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Enter email address">
                                </div>

                                <!-- Password -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                                    <input type="password" name="password" x-model="adminPassword" @input="checkPasswordStrength(); checkPasswordMatch()" required minlength="6" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Enter password (minimum 6 characters)">
                                    
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
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password <span class="text-red-500">*</span></label>
                                    <input type="password" name="password_confirmation" x-model="adminPasswordConfirm" @input="checkPasswordMatch()" required class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" :class="!passwordMatch ? 'border-red-500' : ''" placeholder="Re-enter password">
                                    
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
                                <button type="submit" :disabled="!passwordMatch || adminPassword.length < 6" :class="!passwordMatch || adminPassword.length < 6 ? 'bg-gray-400 cursor-not-allowed' : 'bg-purple-500 hover:bg-purple-600'" class="text-white px-6 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Create Admin Account
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Help Page -->
            <div x-show="activeMenu === 'Help'">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Help & Documentation</h1>
                    <p class="text-gray-600">Get help with using the S-Core system</p>
                </div>

                <div class="space-y-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Frequently Asked Questions
                        </h3>
                        <div class="space-y-4">
                            <div class="border-b pb-4">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">1</span>
                                    How do I review a submission?
                                </h4>
                                <p class="text-sm text-gray-600 ml-7 mb-2">Click on the "Review" button in the Review Submissions page to view submission details. You can assign the correct category, preview the certificate/proof directly from Google Drive, and either approve or reject the submission. When approving, make sure to select the appropriate category and verify the suggested points.</p>
                                <div class="mt-2 ml-7 bg-green-50 border border-green-200 rounded p-2">
                                    <p class="text-xs text-green-800"><strong>📁 Google Drive Integration:</strong> All certificates are stored in Google Drive. You can preview PDFs directly in the browser without downloading.</p>
                                </div>
                            </div>

                            <div class="border-b pb-4">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">2</span>
                                    What are the point allocation rules?
                                </h4>
                                <p class="text-sm text-gray-600 ml-7">Points are automatically suggested based on the activity category. Each category has a default point value that can be configured in Settings > Category Management. The system suggests these points during the review process, but you can verify and adjust if needed.</p>
                            </div>

                            <div class="border-b pb-4">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">3</span>
                                    How do I manage categories and points?
                                </h4>
                                <p class="text-sm text-gray-600 ml-7 mb-2">Go to Settings > Category Management and click "Manage Categories". You can:</p>
                                <ul class="text-sm text-gray-600 ml-7 space-y-1 list-disc list-inside">
                                    <li><strong>Add new categories:</strong> Enter category name and default points, then click "Add Category"</li>
                                    <li><strong>Edit/Rename categories:</strong> Click the edit (pencil) icon, modify the name or points, then click "Save"</li>
                                    <li><strong>Update default points:</strong> Edit the category and change the points value</li>
                                    <li><strong>Delete categories:</strong> Click the delete (trash) icon. System will warn if the category is currently in use</li>
                                </ul>
                                <div class="mt-2 ml-7 bg-yellow-50 border border-yellow-200 rounded p-2">
                                    <p class="text-xs text-yellow-800"><strong>⚠️ Important:</strong> Be careful when deleting categories that are currently used in submissions. The system will show a warning with the number of affected submissions.</p>
                                </div>
                            </div>

                            <div class="border-b pb-4">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">4</span>
                                    How can I change a student's submission category?
                                </h4>
                                <p class="text-sm text-gray-600 ml-7">When reviewing a submission, use the "Assign Category" dropdown to select the correct category. If you change the category from the student's original selection, an optional field will appear where you can provide a reason for the change. This helps the student understand why their submission was recategorized.</p>
                            </div>

                            <div class="border-b pb-4">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">5</span>
                                    What rejection reasons are available?
                                </h4>
                                <p class="text-sm text-gray-600 ml-7 mb-2">When rejecting a submission, you can select from predefined reasons or write a custom message:</p>
                                <ul class="text-sm text-gray-600 ml-7 space-y-1 list-disc list-inside">
                                    <li>Certificate does not match activity description</li>
                                    <li>Evidence unclear or incomplete</li>
                                    <li>Activity date exceeds allowed timeframe</li>
                                    <li>Wrong category selected</li>
                                    <li>Duplicate submission</li>
                                    <li>Does not meet S-Core requirements</li>
                                    <li>Other (custom reason) - Select this to write your own detailed explanation</li>
                                </ul>
                            </div>

                            <div class="border-b pb-4">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">6</span>
                                    How do I view detailed student information?
                                </h4>
                                <p class="text-sm text-gray-600 ml-7">In the Students page, hover your mouse over a student's total points to see a detailed breakdown including:</p>
                                <ul class="text-sm text-gray-600 ml-7 space-y-1 list-disc list-inside">
                                    <li>Total points and progress toward 1000 points requirement</li>
                                    <li>Pass/Not Pass status</li>
                                    <li>Points breakdown by category</li>
                                    <li>Total submissions, approved count, and pending submissions</li>
                                </ul>
                                <p class="text-sm text-gray-600 ml-7 mt-2">You can also filter students by year (angkatan) and pass/fail status using the filters at the top.</p>
                            </div>

                            <div class="border-b pb-4">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">7</span>
                                    How do I export statistics and reports?
                                </h4>
                                <p class="text-sm text-gray-600 ml-7">Go to the Statistics page to view overall system metrics, or visit the Students page and use the "Export Report" button to download comprehensive reports. The export will include student information, total points, pass/fail status, and pending submissions. You can filter the data before exporting using the available filters.</p>
                            </div>

                            <div class="border-b pb-4">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">8</span>
                                    How do I add new users to the system?
                                </h4>
                                <p class="text-sm text-gray-600 ml-7">Go to User Management and fill out the Add New User form. Enter the required information (name, email, password, role) and optional student details. The user will be automatically added to the database when you click "Add User". Passwords are securely hashed before storage.</p>
                            </div>

                            <div class="pb-4">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">9</span>
                                    How do I use the Bulk Score feature?
                                </h4>
                                <p class="text-sm text-gray-600 ml-7 mb-2">The Bulk Score feature allows you to assign S-Core points to multiple students at once for group activities. To use it:</p>
                                <ol class="text-sm text-gray-600 ml-7 space-y-1 list-decimal list-inside">
                                    <li>Go to the <strong>Bulk Score</strong> menu in the sidebar</li>
                                    <li>Select target students using filters:
                                        <ul class="ml-5 mt-1 space-y-1 list-disc list-inside">
                                            <li><strong>Major:</strong> Choose the program (Informatika, Sistem Informasi, etc.)</li>
                                            <li><strong>Year (Angkatan):</strong> Select graduation year</li>
                                            <li><strong>Shift:</strong> Morning (Pagi) or Afternoon (Sore) classes</li>
                                        </ul>
                                    </li>
                                    <li>Select the <strong>Main Category</strong> and <strong>Subcategory</strong> for the activity</li>
                                    <li>Fill in:
                                        <ul class="ml-5 mt-1 space-y-1 list-disc list-inside">
                                            <li><strong>Activity Title:</strong> Name of the group activity</li>
                                            <li><strong>Description:</strong> Details about the activity</li>
                                            <li><strong>Activity Date:</strong> When the activity took place</li>
                                            <li><strong>Certificate/Proof (Optional):</strong> Upload shared certificate if available</li>
                                        </ul>
                                    </li>
                                    <li>Click <strong>Submit Bulk Score</strong></li>
                                </ol>
                                <div class="mt-2 ml-7 bg-blue-50 border border-blue-200 rounded p-2">
                                    <p class="text-xs text-blue-800 mb-2"><strong>🎯 Key Features:</strong></p>
                                    <ul class="text-xs text-blue-800 space-y-1 list-disc list-inside">
                                        <li>Submissions are <strong>auto-approved</strong> and immediately visible to students</li>
                                        <li>All students receive the same points based on the selected category</li>
                                        <li>Perfect for group events, seminars, workshops, or competitions</li>
                                        <li>If certificate is uploaded, all students share the same certificate file</li>
                                        <li>System will show preview of how many students will receive the score before submitting</li>
                                    </ul>
                                </div>
                                <div class="mt-2 ml-7 bg-yellow-50 border border-yellow-200 rounded p-2">
                                    <p class="text-xs text-yellow-800"><strong>⚠️ Important:</strong> Double-check your filters before submitting, as bulk scores are immediately approved and cannot be easily undone. Make sure the selected students actually participated in the activity.</p>
                                </div>
                            </div>
                        </div>
                    </div>

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
        studentStats: @json($studentStats), 
        
        // Bulk Score Data
        bulkScore: {
            selectedMajor: '',
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
        
        alertType: 'info',
        alertTitle: '',
        alertMessage: '',
        alertHasCancel: false,
        alertCallback: null,
        
        approveModalMainCategory: '',
        approveModalSubcategory: '',
        approveModalPoints: 0,
        
        pinInput: '',
        pinError: false,
        isPinVerified: false,
        
        editingCategory: null,
        deletingCategory: null,
        selectedSubmission: null,
        
        rejectReason: '',
        rejectReasonType: '',
        categoryChangeReason: '',
        categoryChanged: false,
        
        assignedMainCategory: '',
        assignedSubcategory: '',
        assignedAvailableSubcategories: [],
        
        newMainCategory: '',
        newCategory: { mainCategoryIndex: '', name: '', points: '', description: '' },
        showInactiveCategories: false,
        
        // --- VARIABLE FILTER ---
        statusFilter: 'Waiting', 
        categoryFilter: '',      
        studentFilter: '',       
        searchQuery: '',         
        
        // Filter Student Management
        studentSearchQuery: '',
        majorFilter: '',
        yearFilter: '',
        statusPassFilter: '',    
        
        showStudentDetailModal: false, 
        selectedStudent: null,
        
        // Tabs
        userTab: 'students',
        settingsTab: 'students',

        // ============================================================
        //  FUNGSI INIT (PENTING: JANGAN DIHAPUS)
        //  Fungsi ini berjalan otomatis saat halaman dimuat
        // ============================================================
        async init() {
            // LOAD CATEGORIES dari API (Real-time)
            await this.loadCategories();
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
                    isEditing: false,
                    subcategories: (cat.subcategories || []).map(sub => ({
                        ...sub,
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
                    isEditing: false,
                    subcategories: (cat.subcategories || []).map(sub => ({
                        ...sub,
                        isEditing: false
                    }))
                }));
                this.categoryGroups = this.categories;
            }
        },

        // Reactivate main category
        reactivateCategory(catIndex) {
            const cat = this.categories[catIndex];
            if (!confirm(`Restore category "${cat.name}"?`)) return;

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
                    // If server returned HTML (stack trace), log it and show concise message
                    if (data && data.__raw && /<\/?html|<!doctype/i.test(data.__raw)) {
                        console.error('Server returned HTML error on reactivateCategory:', data.__raw);
                        throw new Error('Server error (see console)');
                    }
                    throw new Error(msg);
                }
                return data;
            })
            .then(json => {
                this.showAlert('success', 'Success', json.message || 'Category restored');
                this.loadCategories();
            })
            .catch(err => this.showAlert('error', 'Failed', err.message));
        },

        // Reactivate subcategory
        reactivateSubcategory(catIndex, subIndex) {
            const sub = this.categories[catIndex].subcategories[subIndex];
            if (!confirm(`Restore subcategory "${sub.name}"?`)) return;

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
                this.showAlert('success', 'Success', json.message || 'Subcategory restored');
                this.loadCategories();
            })
            .catch(err => this.showAlert('error', 'Failed', err.message));
        },
        // ============================================================
        
        // --- COMPUTED PROPERTIES ---

        get uniqueCategories() {
            // Mengambil semua nama kategori untuk dropdown filter
            return this.categories.map(c => c.name).sort();
        },

        get uniqueStudents() {
            return [...new Set(this.submissions.map(s => `${s.studentId} - ${s.studentName}`))];
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
                const matchesStudent = this.studentFilter === '' || `${submission.studentId} - ${submission.studentName}` === this.studentFilter;
                
                return matchesSearch && matchesStatus && matchesCategory && matchesStudent;
            });
        },

        // Filter Logic Student Tab
        get filteredStudentsList() {
            return this.students.filter(student => {
                const searchLower = this.studentSearchQuery.toLowerCase();
                const matchesSearch = student.name.toLowerCase().includes(searchLower) || 
                                      student.id.toString().includes(searchLower);

                const matchesMajor = this.majorFilter === '' || student.major === this.majorFilter;
                const matchesYear = this.yearFilter === '' || student.year == this.yearFilter;

                let matchesStatus = true;
                if (this.statusPassFilter === 'pass') {
                    matchesStatus = student.approvedPoints >= 20;
                } else if (this.statusPassFilter === 'fail') {
                    matchesStatus = student.approvedPoints < 20;
                }

                return matchesSearch && matchesMajor && matchesYear && matchesStatus;
            });
        },

        // --- FUNGSI UTAMA (Review, Approve, Reject) ---

        viewDetail(submission) {
            this.selectedSubmission = submission;
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
            } else {
                this.assignedAvailableSubcategories = [];
                this.assignedSubcategory = '';
            }
        },

        handleApprove() {
            if (this.assignedMainCategory === '' || this.assignedSubcategory === '') {
                this.showAlert('warning', 'Incomplete', 'Please verify/select the correct Category and Subcategory.');
                return;
            }

            const mainCat = this.categories[this.assignedMainCategory];
            const subCat = mainCat.subcategories[this.assignedSubcategory];

            this.approveModalMainCategory = mainCat.name;
            this.approveModalSubcategory = subCat.name;
            this.approveModalPoints = subCat.points;
            
            this.showApproveModal = true;
        },

        confirmApprove() {
            const mainCat = this.categories[this.assignedMainCategory];
            const subCat = mainCat.subcategories[this.assignedSubcategory];
            const url = `/admin/submissions/${this.selectedSubmission.id}/approve`;
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    assigned_subcategory_id: subCat.id, 
                    points: subCat.points
                })
            })
            .then(async response => {
                if (response.ok) {
                    this.showApproveModal = false;
                    this.showAlert('success', 'Approved', 'Submission successfully approved! Page will reload.');
                    setTimeout(() => window.location.reload(), 1500);
                    this.closeModal();
                } else {
                    const data = await response.json();
                    throw new Error(data.message || 'Failed to approve');
                }
            })
            .catch(error => {
                console.error(error);
                this.showAlert('error', 'Error', error.message);
            });
        },

        viewStudentDetail(student) {
            this.selectedStudent = student;
            this.showStudentDetailModal = true;
        },

        // Admin: reset selected student's password
        async resetStudentPassword() {
            if (!this.selectedStudent || !this.selectedStudent.id) return;
            const id = this.selectedStudent.id;
            let pw = prompt('Enter new password for user (leave empty to generate a random one):');
            if (pw !== null && pw !== '') {
                if (pw.length < 6) { alert('Password must be at least 6 characters'); return; }
            }

            if (!confirm('Proceed to reset password for ' + this.selectedStudent.name + '?')) return;

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

                const message = data.message || 'Password reset successfully';
                if (data.generated && data.password) {
                    this.showAlert('success', 'Password Reset', message + '\nGenerated password: ' + data.password);
                } else {
                    this.showAlert('success', 'Password Reset', message);
                }
                this.showStudentDetailModal = false;
            } catch (err) {
                console.error(err);
                this.showAlert('error', 'Failed', err.message || 'Failed to reset password');
            }
        },

        handleRejectConfirm() {
            if (!this.rejectReason || this.rejectReason.trim() === '') {
                this.showAlert('warning', 'Missing Reason', 'Please provide a reason for rejection.');
                return;
            }

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
                if (!response.ok) throw new Error(data.message || 'Failed to reject submission');
                return data;
            })
            .then(data => {
                this.showRejectModal = false;
                this.showAlert('success', 'Rejected', 'Submission has been rejected successfully.');
                setTimeout(() => window.location.reload(), 1500);
                this.closeModal();
            })
            .catch(error => {
                console.error('Reject Error:', error);
                this.showAlert('error', 'Rejection Failed', error.message);
            });
        },

        exportReport() {
            const data = this.filteredStudentsList;
            if(data.length === 0) { 
                this.showAlert('warning', 'No Data', 'No students found to export with current filters.'); 
                return; 
            }
            
            let csvContent = "data:text/csv;charset=utf-8,";
            csvContent += "Student ID,Name,Major,Year,Total Points,Status,Pending Submissions\n"; 
            
            data.forEach(row => {
                let status = row.approvedPoints >= 20 ? "Passed" : "Not Passed";
                csvContent += `${row.id},"${row.name}",${row.major},${row.year},${row.approvedPoints},${status},${row.pending}\n`;
            });

            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "S-Core_Student_Report.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },

        confirmLogout() {
            fetch('{{ route("logout") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(() => { window.location.href = '/login'; })
            .catch(error => {
                console.error('Logout error:', error);
                window.location.href = '/login';
            });
        },

        closeModal() {
            this.showDetailModal = false;
            this.showRejectModal = false;
            this.showApproveModal = false;
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
        
        verifyPin() {
            if (this.pinInput === '123456') {
                this.isPinVerified = true;
                this.showPinModal = false;
                this.showCategoryModal = true;
            } else {
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
                body: JSON.stringify({ name: this.newMainCategory })
            })
            .then(async res => {
                const json = await res.json();
                if (!res.ok) throw new Error(json.message);
                return json;
            })
            .then(json => {
                // Reload categories dari API untuk ensure sinkronisasi
                this.newMainCategory = '';
                this.showAlert('success', 'Saved', 'Category added! Reloading...');
                this.loadCategories();
            })
            .catch(err => {
                this.showAlert('error', 'Error', err.message || 'Failed to add category');
            });
        },

        // 2. EDIT/SAVE MAIN CATEGORY
        saveMainCategory(index) {
            const cat = this.categories[index];
            fetch(`/admin/categories/${cat.id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ name: cat.name })
            })
            .then(res => res.ok ? res.json() : Promise.reject(res))
            .then(() => {
                cat.isEditing = false;
                this.showAlert('success', 'Updated', 'Category updated!');
                // Reload categories dari API untuk ensure sinkronisasi
                this.loadCategories();
            })
            .catch(() => this.showAlert('error', 'Error', 'Failed to update category'));
        },

        // 3. DELETE MAIN CATEGORY
        deleteMainCategory(index) {
            const cat = this.categories[index];
            if (!confirm(`Delete category "${cat.name}" and all its subcategories?`)) return;

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
                const msg = (json && json.message) ? json.message : 'Category deleted or deactivated. Reloading categories...';
                this.showAlert('success', 'Success', msg);
                // Reload categories dari API untuk sinkronisasi dengan server
                this.loadCategories();
            })
            .catch(err => this.showAlert('error', 'Failed', err.message));
        },

        // 4. ADD SUBCATEGORY
        addSubcategory() {
            if (this.newCategory.mainCategoryIndex === '' || !this.newCategory.name) {
                this.showAlert('warning', 'Missing Info', 'Select category and enter name'); return;
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
                    description: this.newCategory.description
                })
            })
            .then(res => res.ok ? res.json() : Promise.reject(res))
            .then(() => {
                // Reset form
                this.newCategory = { mainCategoryIndex: '', name: '', points: '', description: '' };
                this.showAlert('success', 'Saved', 'Subcategory added. Reloading...');
                // Reload categories dari API untuk sinkronisasi
                this.loadCategories();
            })
            .catch(() => this.showAlert('error', 'Error', 'Failed to add subcategory'));
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
                    description: sub.description
                })
            })
            .then(res => res.ok ? res.json() : Promise.reject(res))
            .then(() => {
                sub.isEditing = false;
                this.showAlert('success', 'Updated', 'Subcategory updated!');
                // Reload categories dari API untuk sinkronisasi
                this.loadCategories();
            })
            .catch(() => this.showAlert('error', 'Error', 'Failed to update subcategory'));
        },

        // 7. DELETE SUBCATEGORY
        deleteSubcategory(catIndex, subIndex) {
            const sub = this.categories[catIndex].subcategories[subIndex];
            if (!confirm(`Delete subcategory "${sub.name}"?`)) return;

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
                const msg = (json && json.message) ? json.message : 'Subcategory deleted or deactivated. Reloading...';
                this.showAlert('success', 'Success', msg);
                // Reload categories dari API untuk sinkronisasi
                this.loadCategories();
            })
            .catch(err => this.showAlert('error', 'Failed', err.message || 'Unknown error'));
        },

        // Bulk Score Management
        async applyBulkScore() {
            if (!this.bulkScore.mainCategory || !this.bulkScore.subcategory) {
                this.showAlert('error', 'Error', 'Please select category and subcategory');
                return;
            }
            
            if (!this.bulkScore.activityTitle || !this.bulkScore.description || !this.bulkScore.activityDate) {
                this.showAlert('error', 'Error', 'Please fill all required fields');
                return;
            }
            
            this.bulkScore.isSubmitting = true;
            
            try {
                const formData = new FormData();
                formData.append('selectedMajor', this.bulkScore.selectedMajor);
                formData.append('selectedYear', this.bulkScore.selectedYear);
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
                
                const result = await response.json();
                
                if (result.success) {
                    this.showAlert('success', 'Success', result.message);
                    this.resetBulkScoreForm();
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    this.showAlert('error', 'Error', result.message || 'Failed to create submissions');
                }
            } catch (error) {
                console.error('Bulk score error:', error);
                this.showAlert('error', 'Error', 'Failed to connect to server');
            } finally {
                this.bulkScore.isSubmitting = false;
            }
        },
        
        resetBulkScoreForm() {
            this.bulkScore.selectedMajor = '';
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
