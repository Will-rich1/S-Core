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
                            <div class="w-1/2 bg-gray-100 border-r overflow-auto p-6">
                                <div class="bg-white rounded-lg shadow-sm h-full flex flex-col">
                                    <div class="bg-gray-800 text-white px-4 py-3 rounded-t-lg flex items-center justify-between">
                                        <span class="text-sm font-medium">Certificate/Evidence</span>
                                        <span class="text-xs text-gray-300" x-text="selectedSubmission.certificate"></span>
                                    </div>
                                    <div class="flex-1 flex items-center justify-center p-4 bg-gray-50">
                                        <!-- PDF Viewer Placeholder -->
                                        <div class="w-full h-full border-2 border-dashed border-gray-300 rounded flex flex-col items-center justify-center">
                                            <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                            <p class="text-gray-500 text-sm mb-2">PDF Certificate Preview</p>
                                            <p class="text-gray-400 text-xs" x-text="selectedSubmission.certificate"></p>
                                            <p class="text-gray-400 text-xs mt-4">In production, this will display the actual PDF file</p>
                                        </div>
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

                                        <!-- Current Category -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Category</label>
                                            <div class="bg-gray-50 border rounded-lg px-4 py-3 text-sm" x-text="selectedSubmission.kategori"></div>
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
                                                Assign Category <span class="text-red-500">*</span>
                                            </label>
                                            <select x-model="assignedCategory" @change="checkCategoryChange" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Select Category</option>
                                                <option value="Internship/Practical Work">Internship/Practical Work</option>
                                                <option value="Independent Learning Campus Program">Independent Learning Campus Program</option>
                                                <option value="New Student Campus Life Introduction (PKKMB)">New Student Campus Life Introduction (PKKMB)</option>
                                                <option value="Workshop/Training/Seminar Activities">Workshop/Training/Seminar Activities</option>
                                                <option value="Achievement in Science, Literature and Other Academic Activities (olympiad, pitmapres, etc)">Achievement in Science, Literature and Other Academic Activities</option>
                                                <option value="Participant in Interest and Talent Activities (sports, arts, and spirituality)">Participant in Interest and Talent Activities</option>
                                                <option value="IPR/Patent">IPR/Patent</option>
                                            </select>
                                            <p class="text-xs text-gray-500 mt-2">
                                                <span class="font-medium">Suggested Points:</span> 
                                                <span class="text-blue-600 font-semibold" x-text="selectedSubmission.suggestedPoint"></span> points
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

        <!-- Category Management Modal -->
        <div x-show="showCategoryModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-[60]" style="display: none;">
            <div class="bg-white rounded-lg max-w-4xl w-full mx-4 shadow-2xl max-h-[90vh] flex flex-col">
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
                    <!-- Add New Category Section -->
                    <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-4 mb-6">
                        <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add New Category
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <input type="text" x-model="newCategory.name" placeholder="Category Name" class="col-span-2 border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            <input type="number" x-model="newCategory.points" placeholder="Points" class="border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <button @click="addCategory" class="mt-3 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                            Add Category
                        </button>
                    </div>

                    <!-- Categories List -->
                    <div class="space-y-3">
                        <h4 class="font-semibold text-gray-800 mb-3">Existing Categories</h4>
                        <template x-for="(category, index) in categories" :key="index">
                            <div class="bg-white border-2 border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                                <div class="flex items-center gap-3">
                                    <!-- Edit Mode -->
                                    <template x-if="category.isEditing">
                                        <div class="flex-1 flex items-center gap-3">
                                            <input 
                                                type="text" 
                                                x-model="category.name" 
                                                class="flex-1 border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            />
                                            <input 
                                                type="number" 
                                                x-model="category.points" 
                                                class="w-24 border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            />
                                            <button @click="requestSaveCategory(index)" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                                Save
                                            </button>
                                            <button @click="cancelEdit(index)" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">
                                                Cancel
                                            </button>
                                        </div>
                                    </template>
                                    
                                    <!-- View Mode -->
                                    <template x-if="!category.isEditing">
                                        <div class="flex-1 flex items-center justify-between">
                                            <div class="flex-1">
                                                <h5 class="font-medium text-gray-800" x-text="category.name"></h5>
                                                <p class="text-xs text-gray-500 mt-1">Default Points: <span class="font-semibold text-blue-600" x-text="category.points"></span></p>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <button @click="editCategory(index)" class="text-blue-500 hover:text-blue-700 p-2" title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <button @click="requestDeleteCategory(index)" class="text-red-500 hover:text-red-700 p-2" title="Delete">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="border-t p-6">
                    <div class="flex gap-3 justify-end">
                        <button @click="closeCategoryModal" class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm font-medium">Close</button>
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
                <button @click="activeMenu = 'Statistics'" class="w-full flex items-center py-3 text-left hover:bg-gray-100 transition-colors" :class="[
                    isSidebarOpen ? 'gap-3 px-4' : 'justify-center',
                    activeMenu === 'Statistics' ? 'bg-blue-50 border-l-4 border-blue-500' : ''
                ]">
                    <svg class="w-6 h-6" :class="activeMenu === 'Statistics' ? 'text-blue-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span x-show="isSidebarOpen" class="text-sm" :class="activeMenu === 'Statistics' ? 'text-blue-700 font-medium' : 'text-gray-700'">Statistics</span>
                </button>
                <button @click="activeMenu = 'Students'" class="w-full flex items-center py-3 text-left hover:bg-gray-100 transition-colors" :class="[
                    isSidebarOpen ? 'gap-3 px-4' : 'justify-center',
                    activeMenu === 'Students' ? 'bg-blue-50 border-l-4 border-blue-500' : ''
                ]">
                    <svg class="w-6 h-6" :class="activeMenu === 'Students' ? 'text-blue-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span x-show="isSidebarOpen" class="text-sm" :class="activeMenu === 'Students' ? 'text-blue-700 font-medium' : 'text-gray-700'">Students</span>
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
                    <span class="text-sm font-medium">ADMIN USER</span>
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
                            <option value="pass">Passed (≥1000 points)</option>
                            <option value="fail">Not Passed (<1000 points)</option>
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
                                            :class="student.approvedPoints >= 1000 ? 'text-green-600' : 'text-red-600'"
                                            @mouseenter="(e) => { 
                                                showTooltip = true; 
                                                const rect = e.target.getBoundingClientRect();
                                                tooltipX = rect.left + (rect.width / 2);
                                                tooltipY = rect.top + window.scrollY - 8;
                                            }"
                                            @mouseleave="showTooltip = false"
                                            x-text="student.approvedPoints"
                                        ></span>
                                        
                                        <!-- Hover Tooltip with Detailed Breakdown - Fixed Position (Appears Above) -->
                                        <div 
                                            x-show="showTooltip" 
                                            class="fixed z-[100] bg-white border-2 border-blue-500 rounded-lg shadow-2xl p-4 w-96 pointer-events-none"
                                            style="display: none;"
                                            :style="`left: ${tooltipX}px; top: ${tooltipY}px; transform: translate(-50%, -100%);`"
                                        >
                                            <div class="mb-3 pb-3 border-b border-gray-200">
                                                <div class="flex items-center justify-between mb-2">
                                                    <h4 class="font-bold text-gray-800" x-text="student.name"></h4>
                                                    <span :class="student.approvedPoints >= 1000 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="px-2 py-1 rounded-full text-xs font-semibold">
                                                        <span x-text="student.approvedPoints >= 1000 ? 'PASSED' : 'NOT PASSED'"></span>
                                                    </span>
                                                </div>
                                                <p class="text-xs text-gray-600" x-text="student.id"></p>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <div class="flex justify-between items-center mb-2">
                                                    <span class="text-sm font-semibold text-gray-700">Total Points:</span>
                                                    <span class="text-lg font-bold" :class="student.approvedPoints >= 1000 ? 'text-green-600' : 'text-red-600'" x-text="student.approvedPoints"></span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="h-2 rounded-full transition-all" :class="student.approvedPoints >= 1000 ? 'bg-green-500' : 'bg-red-500'" :style="`width: ${Math.min((student.approvedPoints / 1000) * 100, 100)}%`"></div>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1" x-text="`${Math.max(1000 - student.approvedPoints, 0)} points needed to pass`"></p>
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
                                        <span :class="student.approvedPoints >= 1000 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="px-3 py-1 rounded-full text-xs font-semibold">
                                            <span x-text="student.approvedPoints >= 1000 ? 'PASSED' : 'NOT PASSED'"></span>
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

            <!-- Settings Page -->
            <div x-show="activeMenu === 'Settings'">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Settings</h1>
                    <p class="text-gray-600">Configure S-Core system settings</p>
                </div>

                <!-- Settings Sections -->
                <div class="space-y-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            Category Management
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

                    <!-- <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4">Point Rules</h3>
                        <p class="text-sm text-gray-600 mb-4">Configure point allocation rules and thresholds</p>
                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">Configure Rules</button>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4">Notification Settings</h3>
                        <p class="text-sm text-gray-600 mb-4">Manage email notifications and alerts</p>
                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">Edit Notifications</button>
                    </div> -->

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
                                <p class="text-sm text-gray-600 ml-7">Click on the "Review" button in the Review Submissions page to view submission details. You can assign the correct category, view the certificate/evidence, and either approve or reject the submission. When approving, make sure to select the appropriate category and verify the suggested points.</p>
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
                            
                            <div class="pb-4">
                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">7</span>
                                    How do I export statistics and reports?
                                </h4>
                                <p class="text-sm text-gray-600 ml-7">Go to the Statistics page to view overall system metrics, or visit the Students page and use the "Export Report" button to download comprehensive reports. The export will include student information, total points, pass/fail status, and pending submissions. You can filter the data before exporting using the available filters.</p>
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
                showLogoutModal: false,
                showDetailModal: false,
                showRejectModal: false,
                showCategoryModal: false,
                showPinModal: false,
                showEditConfirmModal: false,
                showDeleteConfirmModal: false,
                pinInput: '',
                pinError: false,
                isPinVerified: false,
                editingCategory: null,
                deletingCategory: null,
                pendingSaveIndex: null,
                selectedSubmission: null,
                rejectReason: '',
                rejectReasonType: '',
                categoryChangeReason: '',
                categoryChanged: false,
                originalCategory: '',
                assignedPoints: '',
                assignedCategory: '',
                statusFilter: 'Waiting',
                categoryFilter: '',
                searchQuery: '',
                studentFilter: '',
                studentSearchQuery: '',
                yearFilter: '',
                majorFilter: '',
                statusPassFilter: '',
                newCategory: {
                    name: '',
                    points: ''
                },
                categories: [
                    { name: "Internship/Practical Work", points: 22, isEditing: false, originalName: "Internship/Practical Work", originalPoints: 22 },
                    { name: "Independent Learning Campus Program", points: 15, isEditing: false, originalName: "Independent Learning Campus Program", originalPoints: 15 },
                    { name: "New Student Campus Life Introduction (PKKMB)", points: 10, isEditing: false, originalName: "New Student Campus Life Introduction (PKKMB)", originalPoints: 10 },
                    { name: "Workshop/Training/Seminar Activities", points: 8, isEditing: false, originalName: "Workshop/Training/Seminar Activities", originalPoints: 8 },
                    { name: "Achievement in Science, Literature and Other Academic Activities (olympiad, pitmapres, etc)", points: 18, isEditing: false, originalName: "Achievement in Science, Literature and Other Academic Activities (olympiad, pitmapres, etc)", originalPoints: 18 },
                    { name: "Participant in Interest and Talent Activities (sports, arts, and spirituality)", points: 10, isEditing: false, originalName: "Participant in Interest and Talent Activities (sports, arts, and spirituality)", originalPoints: 10 },
                    { name: "IPR/Patent", points: 20, isEditing: false, originalName: "IPR/Patent", originalPoints: 20 }
                ],
                studentFilter: '',
                submissions: [
                    { id: 1, studentId: "2210426", studentName: "CALVIN WILLIAM", major: "STI", kategori: "Internship/Practical Work", judul: "Internship Certificate", keterangan: "Internship at HR Department for 3 months, handling recruitment and employee relations", point: null, suggestedPoint: 22, waktu: "12 Aug 2025 20:31:51", status: "Waiting", certificate: "internship_cert.pdf", activityDate: "2025-06-15", submittedDate: "2025-08-12", year: "2022" },
                    { id: 2, studentId: "2210427", studentName: "JANE DOE", major: "BD", kategori: "Independent Learning Campus Program", judul: "Getting Started with Python Programming", keterangan: "Completed the class 'Getting Started with Python Programming' on Dicoding platform with excellent grade", point: null, suggestedPoint: 15, waktu: "08 Aug 2025 21:23:33", status: "Waiting", certificate: "python_cert.pdf", activityDate: "2025-07-20", submittedDate: "2025-08-08", year: "2022" },
                    { id: 3, studentId: "2210428", studentName: "JOHN SMITH", major: "KWU", kategori: "Workshop/Training/Seminar Activities", judul: "Web Development Workshop", keterangan: "Attended 3-day intensive web development workshop covering HTML, CSS, and JavaScript", point: null, suggestedPoint: 8, waktu: "10 Aug 2025 15:45:12", status: "Waiting", certificate: "workshop_cert.pdf", activityDate: "2025-08-01", submittedDate: "2025-08-10", year: "2022" },
                    { id: 4, studentId: "2210426", studentName: "CALVIN WILLIAM", major: "STI", kategori: "Achievement in Science, Literature and Other Academic Activities (olympiad, pitmapres, etc)", judul: "Pilmapres Region III Finalist", keterangan: "Became Finalist of Pilmapres Region III representing ITBSS", point: 18, waktu: "07 Aug 2025 21:19:52", status: "Approved", certificate: "pilmapres_cert.pdf", activityDate: "2025-07-15", submittedDate: "2025-08-07", year: "2022" },
                    { id: 5, studentId: "2210429", studentName: "ALICE JOHNSON", major: "BD", kategori: "IPR/Patent", judul: "Mobile App UI/UX Design Patent", keterangan: "Registered intellectual property rights for innovative mobile application design", point: 20, waktu: "11 Aug 2025 10:30:00", status: "Approved", certificate: "ipr_cert.pdf", activityDate: "2025-07-25", submittedDate: "2025-08-11", year: "2022" },
                    { id: 6, studentId: "2210426", studentName: "CALVIN WILLIAM", major: "STI", kategori: "Participant in Interest and Talent Activities (sports, arts, and spirituality)", judul: "National Web Development Competition Participant", keterangan: "Became participant in Nation", point: 10, waktu: "07 Aug 2025 21:18:27", status: "Approved", certificate: "competition_cert.pdf", activityDate: "2025-07-10", submittedDate: "2025-08-07", year: "2022" },
                    { id: 7, studentId: "2210427", studentName: "JANE DOE", major: "BD", kategori: "Workshop/Training/Seminar Activities", judul: "AI Workshop 2025", keterangan: "Attended AI and Machine Learning workshop", point: 8, waktu: "05 Aug 2025 14:20:00", status: "Approved", certificate: "ai_workshop.pdf", activityDate: "2025-07-28", submittedDate: "2025-08-05", year: "2022" },
                    { id: 8, studentId: "2230001", studentName: "MICHAEL BROWN", major: "STI", kategori: "Internship/Practical Work", judul: "Software Engineering Internship", keterangan: "6-month internship at Tech Company", point: 22, waktu: "01 Aug 2025 09:15:00", status: "Approved", certificate: "intern_cert.pdf", activityDate: "2025-06-01", submittedDate: "2025-08-01", year: "2023" }
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
                        approved: this.submissions.filter(s => s.status === 'Approved').length,
                        rejected: this.submissions.filter(s => s.status === 'Rejected').length
                    };
                },
                get uniqueStudentsList() {
                    const studentMap = new Map();
                    this.submissions.forEach(sub => {
                        const key = sub.studentId;
                        if (!studentMap.has(key)) {
                            studentMap.set(key, {
                                id: sub.studentId,
                                name: sub.studentName,
                                major: sub.major,
                                year: sub.year || sub.studentId.substring(0, 4),
                                totalSubmissions: 0,
                                approvedPoints: 0,
                                approvedCount: 0,
                                pending: 0,
                                categoryBreakdown: {}
                            });
                        }
                        const student = studentMap.get(key);
                        student.totalSubmissions++;
                        if (sub.status === 'Approved') {
                            student.approvedPoints += sub.point || 0;
                            student.approvedCount++;
                            // Category breakdown
                            if (!student.categoryBreakdown[sub.kategori]) {
                                student.categoryBreakdown[sub.kategori] = 0;
                            }
                            student.categoryBreakdown[sub.kategori] += sub.point || 0;
                        }
                        if (sub.status === 'Waiting') {
                            student.pending++;
                        }
                    });
                    return Array.from(studentMap.values());
                },
                get filteredStudentsList() {
                    return this.uniqueStudentsList.filter(student => {
                        const matchesSearch = this.studentSearchQuery === '' ||
                            student.name.toLowerCase().includes(this.studentSearchQuery.toLowerCase()) ||
                            student.id.includes(this.studentSearchQuery);
                        const matchesMajor = this.majorFilter === '' || student.major === this.majorFilter;
                        const matchesYear = this.yearFilter === '' || student.year === this.yearFilter;
                        const matchesStatus = this.statusPassFilter === '' ||
                            (this.statusPassFilter === 'pass' && student.approvedPoints >= 1000) ||
                            (this.statusPassFilter === 'fail' && student.approvedPoints < 1000);
                        return matchesSearch && matchesMajor && matchesYear && matchesStatus;
                    });
                },
                get studentStats() {
                    const students = this.filteredStudentsList;
                    const passed = students.filter(s => s.approvedPoints >= 1000).length;
                    const failed = students.filter(s => s.approvedPoints < 1000).length;
                    const totalPoints = students.reduce((sum, s) => sum + s.approvedPoints, 0);
                    const average = students.length > 0 ? Math.round(totalPoints / students.length) : 0;
                    return {
                        passed,
                        failed,
                        average
                    };
                },
                confirmLogout() {
                    window.location.href = '/login';
                },
                checkCategoryChange() {
                    if (this.selectedSubmission) {
                        this.categoryChanged = this.assignedCategory !== this.selectedSubmission.kategori && this.assignedCategory !== '';
                        if (!this.categoryChanged) {
                            this.categoryChangeReason = '';
                        }
                    }
                },
                viewDetail(submission) {
                    this.selectedSubmission = submission;
                    this.originalCategory = submission.kategori;
                    this.assignedPoints = submission.suggestedPoint?.toString() || '';
                    this.assignedCategory = submission.kategori || '';
                    this.categoryChanged = false;
                    this.categoryChangeReason = '';
                    this.showDetailModal = true;
                },
                handleApprove() {
                    if (!this.assignedCategory) {
                        alert('Please select a category');
                        return;
                    }
                    
                    let confirmMessage = `Approve submission for ${this.selectedSubmission.studentName}?`;
                    if (this.categoryChanged && this.categoryChangeReason) {
                        confirmMessage += `\n\nCategory changed to: ${this.assignedCategory}\nReason: ${this.categoryChangeReason}`;
                    }
                    
                    if (confirm(confirmMessage)) {
                        const index = this.submissions.findIndex(s => s.id === this.selectedSubmission.id);
                        if (index !== -1) {
                            this.submissions[index].status = 'Approved';
                            this.submissions[index].kategori = this.assignedCategory;
                            this.submissions[index].point = this.selectedSubmission.suggestedPoint;
                            if (this.categoryChanged && this.categoryChangeReason) {
                                this.submissions[index].categoryChangeReason = this.categoryChangeReason;
                            }
                        }
                        alert('Submission approved successfully!');
                        this.closeModal();
                    }
                },
                handleRejectConfirm() {
                    if (!this.rejectReason.trim()) {
                        alert('Please provide a reason for rejection');
                        return;
                    }
                    
                    if (confirm(`Reject submission for ${this.selectedSubmission.studentName}?\n\nReason: ${this.rejectReason}`)) {
                        const index = this.submissions.findIndex(s => s.id === this.selectedSubmission.id);
                        if (index !== -1) {
                            this.submissions[index].status = 'Cancel';
                            this.submissions[index].rejectReason = this.rejectReason;
                        }
                        alert('Submission rejected successfully!');
                        this.showRejectModal = false;
                        this.showDetailModal = false;
                        this.selectedSubmission = null;
                        this.assignedPoints = '';
                        this.assignedCategory = '';
                        this.rejectReason = '';
                        this.rejectReasonType = '';
                        this.categoryChangeReason = '';
                        this.categoryChanged = false;
                    }
                },
                viewStudentDetail(student) {
                    alert(`Viewing detailed report for ${student.name}\n\nTotal Points: ${student.approvedPoints}\nStatus: ${student.approvedPoints >= 1000 ? 'PASSED' : 'NOT PASSED'}\n\nThis will open a detailed view in production.`);
                },
                exportReport() {
                    const reportData = this.filteredStudentsList.map(s => ({
                        id: s.id,
                        name: s.name,
                        major: s.major,
                        year: s.year,
                        totalPoints: s.approvedPoints,
                        status: s.approvedPoints >= 1000 ? 'PASSED' : 'NOT PASSED',
                        pending: s.pending
                    }));
                    console.log('Exporting report:', reportData);
                    alert('Report export feature will download Excel/PDF in production.\n\nTotal students: ' + reportData.length);
                },
                requestCategoryManagement() {
                    this.showPinModal = true;
                    this.pinInput = '';
                    this.pinError = false;
                },
                verifyPin() {
                    if (this.pinInput === '123456') {
                        this.isPinVerified = true;
                        this.pinError = false;
                        this.closePinModal();
                        this.openCategoryModal();
                    } else {
                        this.pinError = true;
                        this.pinInput = '';
                    }
                },
                closePinModal() {
                    this.showPinModal = false;
                    this.pinInput = '';
                    this.pinError = false;
                },
                openCategoryModal() {
                    this.showCategoryModal = true;
                },
                closeCategoryModal() {
                    this.showCategoryModal = false;
                    this.isPinVerified = false;
                    this.newCategory = { name: '', points: '' };
                    // Cancel any pending edits
                    this.categories.forEach(cat => {
                        if (cat.isEditing) {
                            cat.isEditing = false;
                            cat.name = cat.originalName;
                            cat.points = cat.originalPoints;
                        }
                    });
                },
                addCategory() {
                    if (!this.newCategory.name || !this.newCategory.points) {
                        alert('Please fill in both category name and points');
                        return;
                    }
                    
                    if (this.categories.some(cat => cat.name.toLowerCase() === this.newCategory.name.toLowerCase())) {
                        alert('Category with this name already exists');
                        return;
                    }
                    
                    this.categories.push({
                        name: this.newCategory.name,
                        points: parseInt(this.newCategory.points),
                        isEditing: false,
                        originalName: this.newCategory.name,
                        originalPoints: parseInt(this.newCategory.points)
                    });
                    
                    alert('Category added successfully!');
                    this.newCategory = { name: '', points: '' };
                },
                editCategory(index) {
                    // Save original values before editing
                    this.categories[index].originalName = this.categories[index].name;
                    this.categories[index].originalPoints = this.categories[index].points;
                    this.categories[index].isEditing = true;
                },
                requestSaveCategory(index) {
                    const category = this.categories[index];
                    
                    // Validate
                    if (!category.name.trim()) {
                        alert('Category name cannot be empty');
                        return;
                    }
                    
                    if (!category.points || category.points <= 0) {
                        alert('Points must be greater than 0');
                        return;
                    }
                    
                    // Check for duplicates (excluding current category)
                    const duplicate = this.categories.some((cat, idx) => 
                        idx !== index && cat.name.toLowerCase() === category.name.trim().toLowerCase()
                    );
                    
                    if (duplicate) {
                        alert('A category with this name already exists');
                        return;
                    }
                    
                    // Show confirmation modal
                    this.pendingSaveIndex = index;
                    this.editingCategory = { 
                        name: category.name, 
                        points: category.points 
                    };
                    this.showEditConfirmModal = true;
                },
                confirmSaveCategory() {
                    if (this.pendingSaveIndex !== null) {
                        const category = this.categories[this.pendingSaveIndex];
                        
                        // Save changes
                        category.originalName = category.name;
                        category.originalPoints = category.points;
                        category.isEditing = false;
                        
                        this.showEditConfirmModal = false;
                        this.editingCategory = null;
                        this.pendingSaveIndex = null;
                        
                        alert('Category updated successfully!');
                    }
                },
                cancelEdit(index) {
                    this.categories[index].name = this.categories[index].originalName;
                    this.categories[index].points = this.categories[index].originalPoints;
                    this.categories[index].isEditing = false;
                },
                requestDeleteCategory(index) {
                    const category = this.categories[index];
                    
                    // Check if category is in use
                    const usageCount = this.submissions.filter(sub => sub.kategori === category.name).length;
                    
                    this.deletingCategory = {
                        index: index,
                        name: category.name,
                        usageCount: usageCount
                    };
                    
                    this.showDeleteConfirmModal = true;
                },
                confirmDeleteCategoryFinal() {
                    if (this.deletingCategory) {
                        this.categories.splice(this.deletingCategory.index, 1);
                        alert('Category deleted successfully!');
                        
                        this.showDeleteConfirmModal = false;
                        this.deletingCategory = null;
                    }
                },
                closeModal() {
                    this.showDetailModal = false;
                    this.showRejectModal = false;
                    this.selectedSubmission = null;
                    this.assignedPoints = '';
                    this.assignedCategory = '';
                    this.rejectReason = '';
                    this.rejectReasonType = '';
                    this.categoryChangeReason = '';
                    this.categoryChanged = false;
                }
            }
        }
    </script>
</body>
</html>
