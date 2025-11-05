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

        <!-- Add New Activity Modal -->
        <div x-show="showAddModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
            <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold">Submit New S-Core</h3>
                    <button @click="closeModal" class="text-gray-500 hover:text-gray-700 text-2xl leading-none">×</button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Student</label>
                        <div class="bg-gray-50 border rounded px-4 py-2 text-sm text-gray-700">2210426 - CALVIN WILLIAM</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select x-model="formData.category" class="w-full border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Category</option>
                            <option value="OrKeSS (Orientasi Kemahasiswaan Sabda Setia)">OrKeSS (Orientasi Kemahasiswaan Sabda Setia)</option>
                            <option value="Retreat">Retreat</option>
                            <option value="Penguasaan Bahasa Inggris Aktif (ITP TOEFL 450 atau setara)">Penguasaan Bahasa Inggris Aktif (ITP TOEFL 450 atau setara)</option>
                            <option value="Penguasaan Bahasa Mandarin Aktif (HSK setara 4)">Penguasaan Bahasa Mandarin Aktif (HSK setara 4)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Activity Title</label>
                        <input type="text" x-model="formData.activityTitle" class="w-full border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter activity title" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea x-model="formData.description" rows="4" class="w-full border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter description"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Activity Date</label>
                        <input type="date" x-model="formData.activityDate" class="w-full border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Certificate/Evidence <span class="text-red-500">*</span> [PDF] - MAX 10MB</label>
                        <input type="file" accept=".pdf" class="w-full border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                </div>

                <div class="flex gap-3 justify-end mt-6">
                    <button @click="closeModal" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm font-medium">Close</button>
                    <button @click="saveActivity" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded text-sm font-medium">Submit for Review</button>
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
                <div>
                    <button @click="activeMenu = 'Student Affairs'" class="w-full flex items-center py-3 text-left hover:bg-gray-100 transition-colors" :class="[
                        isSidebarOpen ? 'gap-3 px-4' : 'justify-center',
                        activeMenu === 'Student Affairs' && isSidebarOpen ? 'bg-blue-50 border-l-4 border-primary' : ''
                    ]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        </svg>
                        <span x-show="isSidebarOpen" class="text-sm text-gray-700">Student Affairs</span>
                    </button>
                    <div x-show="activeMenu === 'Student Affairs' && isSidebarOpen" class="bg-gray-50">
                        <button class="w-full text-left px-12 py-2 text-sm text-gray-600 hover:bg-gray-100 bg-orange-100 border-l-4 border-orange-500">S-Core</button>
                    </div>
                </div>
                <button @click="activeMenu = 'Help'" class="w-full flex items-center py-3 text-left hover:bg-gray-100 transition-colors" :class="[
                    isSidebarOpen ? 'gap-3 px-4' : 'justify-center',
                    activeMenu === 'Help' && isSidebarOpen ? 'bg-blue-50 border-l-4 border-primary' : ''
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
                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium">CALVIN WILLIAM</span>
                </div>
            </div>

            <div class="flex-1 overflow-auto p-6">
                <div class="mb-6">
                    <div class="flex items-center gap-4 mb-4">
                        <h1 class="text-3xl font-bold text-gray-800">S-Core</h1>
                        <span class="bg-green-500 text-white px-3 py-1 rounded text-sm font-semibold">APPROVED POINTS: 994</span>
                    </div>
                </div>

                <!-- Mandatory Categories Table -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4 text-center">Mandatory S-Core Categories</h2>
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-3 px-4">Category</th>
                                <th class="text-center py-3 px-4">Point Suggested</th>
                                <th class="text-center py-3 px-4">Achievement</th>
                                <th class="text-center py-3 px-4">Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="cat in categories" :key="cat.kategori">
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-4" x-text="cat.kategori"></td>
                                    <td class="text-center py-3 px-4" x-text="cat.suggestion"></td>
                                    <td class="text-center py-3 px-4" x-text="cat.capaian"></td>
                                    <td class="text-center py-3 px-4" x-text="cat.point"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Filters and Actions -->
                <div class="bg-white rounded-lg shadow p-4 mb-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <select x-model="statusFilter" class="border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Status</option>
                            <option value="Approve">Approve</option>
                            <option value="Waiting">Waiting</option>
                            <option value="Cancel">Cancel</option>
                        </select>
                        <select x-model="categoryFilter" class="border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Categories</option>
                            <template x-for="cat in uniqueCategories" :key="cat">
                                <option :value="cat" x-text="cat"></option>
                            </template>
                        </select>
                        <input type="text" x-model="searchQuery" placeholder="Search title, description, or category..." class="border rounded px-4 py-2 text-sm flex-1 min-w-[200px] focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        <button @click="showAddModal = true" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm whitespace-nowrap">+ Add New</button>
                    </div>
                </div>

                <!-- Activities Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left py-3 px-4 font-semibold text-sm">Category</th>
                                <th class="text-left py-3 px-4 font-semibold text-sm">Activity Title</th>
                                <th class="text-left py-3 px-4 font-semibold text-sm">Description</th>
                                <th class="text-center py-3 px-4 font-semibold text-sm">Points</th>
                                <th class="text-center py-3 px-4 font-semibold text-sm">Certificate</th>
                                <th class="text-left py-3 px-4 font-semibold text-sm">Input Time</th>
                                <th class="text-center py-3 px-4 font-semibold text-sm">Status</th>
                                <th class="text-center py-3 px-4 font-semibold text-sm"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="activity in filteredActivities" :key="activity.judul + activity.waktu">
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-4 text-sm" x-text="activity.kategori"></td>
                                    <td class="py-3 px-4 text-sm" x-text="activity.judul"></td>
                                    <td class="py-3 px-4 text-sm" x-text="activity.keterangan"></td>
                                    <td class="text-center py-3 px-4 text-sm" x-text="activity.point"></td>
                                    <td class="text-center py-3 px-4">
                                        <button class="text-blue-500 hover:text-blue-700 p-1">
                                            <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </button>
                                    </td>
                                    <td class="py-3 px-4 text-xs text-gray-600" x-text="activity.waktu"></td>
                                    <td class="text-center py-3 px-4">
                                        <span :class="{
                                            'bg-green-100 text-green-700': activity.status === 'Approve',
                                            'bg-yellow-100 text-yellow-700': activity.status === 'Waiting',
                                            'bg-red-100 text-red-700': activity.status === 'Cancel'
                                        }" class="px-3 py-1 rounded-full text-xs font-semibold" x-text="activity.status"></span>
                                    </td>
                                    <td class="text-center py-3 px-4">
                                        <template x-if="activity.status === 'Waiting'">
                                            <div class="flex justify-center gap-2">
                                                <button class="text-green-500 hover:text-green-700 p-1">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <button class="text-red-500 hover:text-red-700 p-1">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                        <template x-if="activity.status !== 'Waiting'">
                                            <button class="text-blue-500 hover:text-blue-700 p-1">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    <td colspan="8" class="text-center py-8 text-gray-500">No activities found matching your filters</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function dashboardData() {
            return {
                activeMenu: 'S-Core',
                isSidebarOpen: false,
                showLogoutModal: false,
                showAddModal: false,
                statusFilter: '',
                categoryFilter: '',
                searchQuery: '',
                formData: {
                    category: '',
                    activityTitle: '',
                    description: '',
                    activityDate: '',
                },
                categories: [
                    { kategori: "OrKeSS (Orientasi Kemahasiswaan Sabda Setia)", suggestion: 1, capaian: 2, point: 22 },
                    { kategori: "Retreat", suggestion: 1, capaian: 16, point: 292 },
                    { kategori: "Penguasaan Bahasa Inggris Aktif (ITP TOEFL 450 atau setara)", suggestion: 1, capaian: 2, point: 40 },
                    { kategori: "Penguasaan Bahasa Mandarin Aktif (HSK setara 4)", suggestion: 5, capaian: 29, point: 230 },
                    { kategori: "Penguasaan Bahasa Asing lain", suggestion: 5, capaian: 29, point: 230 },
                    { kategori: "Peningkatan kemampuan ilmiah dan penalaran", suggestion: 5, capaian: 29, point: 230 },
                    { kategori: "Pemakalah/Pemateri/Presenter/Trainer", suggestion: 5, capaian: 29, point: 230 }
                ],
                activities: [
                    { kategori: "Internship/Practical Work", judul: "Internship Certificate", keterangan: "Internship at HR Department...", point: 22, waktu: "12 Aug 2025 20:31:51:823", status: "Approve" },
                    { kategori: "Internship/Practical Work", judul: "Internship Certificate", keterangan: "Internship Certificate at HR...", point: "-", waktu: "12 Aug 2025 20:31:10:010", status: "Cancel" },
                    { kategori: "Independent Learning Campus Program", judul: "Getting Started with Python Programming", keterangan: "Completed the class 'Getting...", point: "-", waktu: "08 Aug 2025 21:23:33:303", status: "Waiting" },
                    { kategori: "Achievement in Science, Literature and Other Academic Activities (olympiad, pitmapres, etc)", judul: "Pilmapres Region III Finalist", keterangan: "Became Finalist of Pilmapres...", point: 18, waktu: "07 Aug 2025 21:19:52:307", status: "Approve" },
                    { kategori: "Participant in Interest and Talent Activities (sports, arts, and spirituality)", judul: "National Web Development Competition Participant", keterangan: "Became participant in Nation...", point: 10, waktu: "07 Aug 2025 21:18:27:867", status: "Approve" },
                    { kategori: "IPR/Patent", judul: "IPR Terrafrace Application (UI UX Design)", keterangan: "Proof of IPR Terrafrace appli...", point: 20, waktu: "10 Jul 2025 22:28:37:203", status: "Approve" }
                ],
                get uniqueCategories() {
                    return [...new Set(this.activities.map(a => a.kategori))];
                },
                get filteredActivities() {
                    return this.activities.filter(activity => {
                        const matchesSearch = this.searchQuery === '' || 
                            activity.judul.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            activity.keterangan.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            activity.kategori.toLowerCase().includes(this.searchQuery.toLowerCase());
                        const matchesStatus = this.statusFilter === '' || activity.status === this.statusFilter;
                        const matchesCategory = this.categoryFilter === '' || activity.kategori === this.categoryFilter;
                        return matchesSearch && matchesStatus && matchesCategory;
                    });
                },
                confirmLogout() {
                    window.location.href = '/login';
                },
                closeModal() {
                    this.showAddModal = false;
                    this.formData = {
                        category: '',
                        activityTitle: '',
                        description: '',
                        activityDate: '',
                    };
                },
                saveActivity() {
                    console.log('Saving activity:', this.formData);
                    this.closeModal();
                }
            }
        }
    </script>
</body>
</html>
