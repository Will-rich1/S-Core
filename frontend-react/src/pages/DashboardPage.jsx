import { useState } from "react";
import { useNavigate } from "react-router-dom";
import logoImg from "../images/logo.png";

export default function DashboardPage() {
  const [activeMenu, setActiveMenu] = useState("S-Core");
  const [isSidebarOpen, setIsSidebarOpen] = useState(false);
  const [showLogoutModal, setShowLogoutModal] = useState(false);
  const [showAddModal, setShowAddModal] = useState(false);
  const [showEditModal, setShowEditModal] = useState(false);
  const [selectedActivity, setSelectedActivity] = useState(null);
  const navigate = useNavigate();

  // Filter states
  const [statusFilter, setStatusFilter] = useState("");
  const [categoryFilter, setCategoryFilter] = useState("");
  const [searchQuery, setSearchQuery] = useState("");

  // Form state for Add New modal
  const [formData, setFormData] = useState({
    category: "",
    activityTitle: "",
    description: "",
    activityDate: "",
    certificate: null,
  });

  const activities = [
    {
      kategori: "Internship/Practical Work",
      judul: "Internship Certificate",
      keterangan: "Internship at HR Department...",
      point: 22,
      waktu: "12 Aug 2025 20:31:51:823",
      status: "Approve",
    },
    {
      kategori: "Internship/Practical Work",
      judul: "Internship Certificate",
      keterangan: "Internship Certificate at HR...",
      point: "-",
      waktu: "12 Aug 2025 20:31:10:010",
      status: "Cancel",
    },
    {
      kategori: "Independent Learning Campus Program",
      judul: "Getting Started with Python Programming",
      keterangan: "Completed the class 'Getting...",
      point: "-",
      waktu: "08 Aug 2025 21:23:33:303",
      status: "Waiting",
    },
    {
      kategori: "Achievement in Science, Literature and Other Academic Activities (olympiad, pitmapres, etc)",
      judul: "Pilmapres Region III Finalist",
      keterangan: "Became Finalist of Pilmapres...",
      point: 18,
      waktu: "07 Aug 2025 21:19:52:307",
      status: "Approve",
    },
    {
      kategori: "Participant in Interest and Talent Activities (sports, arts, and spirituality)",
      judul: "National Web Development Competition Participant",
      keterangan: "Became participant in Nation...",
      point: 10,
      waktu: "07 Aug 2025 21:18:27:867",
      status: "Approve",
    },
    {
      kategori: "IPR/Patent",
      judul: "IPR Terrafrace Application (UI UX Design)",
      keterangan: "Proof of IPR Terrafrace appli...",
      point: 20,
      waktu: "10 Jul 2025 22:28:37:203",
      status: "Approve",
    },
  ];

  const categories = [
    { kategori: "OrKeSS (Orientasi Kemahasiswaan Sabda Setia)", suggestion: 1, capaian: 2, point: 22 },
    { kategori: "Retreat", suggestion: 1, capaian: 16, point: 292 },
    { kategori: "Penguasaan Bahasa Inggris Aktif (ITP TOEFL 450 atau setara)", suggestion: 1, capaian: 2, point: 40 },
    { kategori: "Penguasaan Bahasa Mandarin Aktif (HSK setara 4)", suggestion: 5, capaian: 29, point: 230 },
    { kategori: "Penguasaan Bahasa Asing lain", suggestion: 5, capaian: 29, point: 230 },
    { kategori: "Peningkatan kemampuan ilmiah dan penalaran", suggestion: 5, capaian: 29, point: 230 },
    { kategori: "Pemakalah/Pemateri/Presenter/Trainer", suggestion: 5, capaian: 29, point: 230 },
  ];

  // Get unique categories for filter dropdown
  const uniqueCategories = [...new Set(activities.map((activity) => activity.kategori))];

  // Filter activities based on search, status, and category
  const filteredActivities = activities.filter((activity) => {
    const matchesSearch =
      searchQuery === "" || activity.judul.toLowerCase().includes(searchQuery.toLowerCase()) || activity.keterangan.toLowerCase().includes(searchQuery.toLowerCase()) || activity.kategori.toLowerCase().includes(searchQuery.toLowerCase());

    const matchesStatus = statusFilter === "" || activity.status === statusFilter;

    const matchesCategory = categoryFilter === "" || activity.kategori === categoryFilter;

    return matchesSearch && matchesStatus && matchesCategory;
  });

  const menuItems = [
    // { icon: "dashboard", label: "Dashboard" },
    // { icon: "document", label: "KPRS" },
    // { icon: "calendar", label: "Schedule" },
    // { icon: "clipboard", label: "Attendance" },
    // { icon: "book", label: "Semester Grades" },
    // { icon: "file", label: "Transcript" },
    { icon: "graduation", label: "Student Affairs", hasSubmenu: true },
    // { icon: "users", label: "Mentoring" },
    { icon: "help", label: "Help" },
  ];

  const getMenuIcon = (iconName) => {
    const iconProps = { className: "w-6 h-6", fill: "none", stroke: "currentColor", viewBox: "0 0 24 24" };
    const pathProps = { strokeLinecap: "round", strokeLinejoin: "round", strokeWidth: 1.5 };

    switch (iconName) {
      case "graduation":
        return (
          <svg {...iconProps}>
            <path {...pathProps} d="M12 14l9-5-9-5-9 5 9 5z" />
            <path {...pathProps} d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
            <path
              {...pathProps}
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth={1.5}
              d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"
            />
          </svg>
        );
      case "help":
        return (
          <svg {...iconProps}>
            <path {...pathProps} d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        );
      default:
        return null;
    }
  };

  const handleLogout = () => {
    setShowLogoutModal(true);
  };

  const confirmLogout = () => {
    navigate("/login");
  };

  const cancelLogout = () => {
    setShowLogoutModal(false);
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  const handleFileChange = (e) => {
    const file = e.target.files[0];
    setFormData((prev) => ({
      ...prev,
      certificate: file,
    }));
  };

  const handleAddNew = () => {
    setShowAddModal(true);
  };

  const handleEdit = (activity, index) => {
    setSelectedActivity({ ...activity, index });
    setFormData({
      category: activity.kategori,
      activityTitle: activity.judul,
      description: activity.keterangan,
      activityDate: "", // You can add date field to activities array if needed
      certificate: null,
    });
    setShowEditModal(true);
  };

  const handleCloseModal = () => {
    setShowAddModal(false);
    setShowEditModal(false);
    setSelectedActivity(null);
    // Reset form
    setFormData({
      category: "",
      activityTitle: "",
      description: "",
      activityDate: "",
      certificate: null,
    });
  };

  const handleSaveActivity = () => {
    // TODO: Implement save logic here
    console.log("Saving activity:", formData);
    handleCloseModal();
  };

  const handleUpdateActivity = () => {
    // TODO: Implement update logic here
    console.log("Updating activity:", formData, "for activity index:", selectedActivity.index);
    handleCloseModal();
  };

  return (
    <div className="flex h-screen bg-gray-100">
      {/* Logout Confirmation Modal */}
      {showLogoutModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg p-6 max-w-sm w-full mx-4">
            <h3 className="text-lg font-semibold mb-4">Confirm Logout</h3>
            <p className="text-gray-600 mb-6">Are you sure you want to logout?</p>
            <div className="flex gap-3 justify-end">
              <button onClick={cancelLogout} className="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm font-medium">
                No
              </button>
              <button onClick={confirmLogout} className="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded text-sm font-medium">
                Yes
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Add New Activity Modal */}
      {showAddModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div className="flex justify-between items-center mb-6">
              <h3 className="text-xl font-semibold">Submit New S-Core</h3>
              <button onClick={handleCloseModal} className="text-gray-500 hover:text-gray-700 text-2xl leading-none">
                ×
              </button>
            </div>

            <div className="space-y-4">
              {/* Student Info */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Student</label>
                <div className="bg-gray-50 border rounded px-4 py-2 text-sm text-gray-700">2210426 - CALVIN WILLIAM</div>
              </div>

              {/* S-Core Category */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select name="category" value={formData.category} onChange={handleInputChange} className="w-full border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option value="">Select Category</option>
                  <option value="OrKeSS (Orientasi Kemahasiswaan Sabda Setia)">OrKeSS (Orientasi Kemahasiswaan Sabda Setia)</option>
                  <option value="Retreat">Retreat</option>
                  <option value="Penguasaan Bahasa Inggris Aktif (ITP TOEFL 450 atau setara)">Penguasaan Bahasa Inggris Aktif (ITP TOEFL 450 atau setara)</option>
                  <option value="Penguasaan Bahasa Mandarin Aktif (HSK setara 4)">Penguasaan Bahasa Mandarin Aktif (HSK setara 4)</option>
                  <option value="Penguasaan Bahasa Asing lain">Penguasaan Bahasa Asing lain</option>
                  <option value="Peningkatan kemampuan ilmiah dan penalaran">Peningkatan kemampuan ilmiah dan penalaran</option>
                  <option value="Pemakalah/Pemateri/Presenter/Trainer">Pemakalah/Pemateri/Presenter/Trainer</option>
                </select>
              </div>

              {/* Activity Title */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Activity Title</label>
                <input
                  type="text"
                  name="activityTitle"
                  value={formData.activityTitle}
                  onChange={handleInputChange}
                  className="w-full border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Enter activity title"
                />
              </div>

              {/* Description */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea
                  name="description"
                  value={formData.description}
                  onChange={handleInputChange}
                  rows="4"
                  className="w-full border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Enter description"
                />
              </div>

              {/* Activity Date */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Activity Date</label>
                <input type="date" name="activityDate" value={formData.activityDate} onChange={handleInputChange} className="w-full border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
              </div>

              {/* Certificate Upload */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Upload Certificate/Evidence <span className="text-red-500">*</span> [PDF] - MAX 10MB
                </label>
                <input type="file" accept=".pdf" onChange={handleFileChange} className="w-full border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                {formData.certificate && <p className="text-xs text-gray-600 mt-1">Selected: {formData.certificate.name}</p>}
              </div>
            </div>

            {/* Action Buttons */}
            <div className="flex gap-3 justify-end mt-6">
              <button onClick={handleCloseModal} className="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm font-medium">
                Close
              </button>
              <button onClick={handleSaveActivity} className="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded text-sm font-medium">
                Submit for Review
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Edit Activity Modal */}
      {showEditModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div className="flex justify-between items-center mb-6">
              <h3 className="text-xl font-semibold">Edit S-Core</h3>
              <button onClick={handleCloseModal} className="text-gray-500 hover:text-gray-700 text-2xl leading-none">
                ×
              </button>
            </div>

            <div className="space-y-4">
              {/* Student Info */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Student</label>
                <div className="bg-gray-50 border rounded px-4 py-2 text-sm text-gray-700">2210426 - CALVIN WILLIAM</div>
              </div>

              {/* S-Core Category */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select name="category" value={formData.category} onChange={handleInputChange} className="w-full border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option value="">Select Category</option>
                  <option value="OrKeSS (Orientasi Kemahasiswaan Sabda Setia)">OrKeSS (Orientasi Kemahasiswaan Sabda Setia)</option>
                  <option value="Retreat">Retreat</option>
                  <option value="Penguasaan Bahasa Inggris Aktif (ITP TOEFL 450 atau setara)">Penguasaan Bahasa Inggris Aktif (ITP TOEFL 450 atau setara)</option>
                  <option value="Penguasaan Bahasa Mandarin Aktif (HSK setara 4)">Penguasaan Bahasa Mandarin Aktif (HSK setara 4)</option>
                  <option value="Penguasaan Bahasa Asing lain">Penguasaan Bahasa Asing lain</option>
                  <option value="Peningkatan kemampuan ilmiah dan penalaran">Peningkatan kemampuan ilmiah dan penalaran</option>
                  <option value="Pemakalah/Pemateri/Presenter/Trainer">Pemakalah/Pemateri/Presenter/Trainer</option>
                </select>
              </div>

              {/* Activity Title */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Activity Title</label>
                <input
                  type="text"
                  name="activityTitle"
                  value={formData.activityTitle}
                  onChange={handleInputChange}
                  className="w-full border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Enter activity title"
                />
              </div>

              {/* Description */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea
                  name="description"
                  value={formData.description}
                  onChange={handleInputChange}
                  rows="4"
                  className="w-full border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Enter description"
                />
              </div>

              {/* Activity Date */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Activity Date</label>
                <input type="date" name="activityDate" value={formData.activityDate} onChange={handleInputChange} className="w-full border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
              </div>

              {/* Certificate Upload */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Upload Certificate/Evidence <span className="text-red-500">*</span> [PDF] - MAX 10MB
                </label>
                <input type="file" accept=".pdf" onChange={handleFileChange} className="w-full border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                {formData.certificate && <p className="text-xs text-gray-600 mt-1">Selected: {formData.certificate.name}</p>}
              </div>
            </div>

            {/* Action Buttons */}
            <div className="flex gap-3 justify-end mt-6">
              <button onClick={handleCloseModal} className="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm font-medium">
                Close
              </button>
              <button onClick={handleUpdateActivity} className="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded text-sm font-medium">
                Update
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Sidebar */}
      <div className={`${isSidebarOpen ? "w-64" : "w-20"} bg-white shadow-lg transition-all duration-300 flex flex-col`}>
        {/* Logo Header */}
        <div className="p-4 border-b flex flex-col items-center">
          <img src={logoImg} alt="Logo" className="w-12 h-12 object-contain" />
          {isSidebarOpen && (
            <div className="mt-2 text-center">
              <h2 className="text-sm font-bold text-gray-800">S-Core ITBSS</h2>
              <p className="text-xs text-gray-500">Sabda Setia Student Point System</p>
            </div>
          )}
        </div>

        {/* Menu Items */}
        <nav className="mt-4 flex-1">
          {menuItems.map((item, index) => (
            <div key={index}>
              <button
                onClick={() => setActiveMenu(item.label)}
                className={`w-full flex items-center ${isSidebarOpen ? "gap-3 px-4" : "justify-center px-0"} py-3 text-left hover:bg-gray-100 transition-colors ${activeMenu === item.label ? "bg-blue-50 border-l-4 border-primary" : ""}`}
              >
                {getMenuIcon(item.icon)}
                {isSidebarOpen && <span className="text-sm text-gray-700">{item.label}</span>}
              </button>
              {item.hasSubmenu && activeMenu === "Student Affairs" && isSidebarOpen && (
                <div className="bg-gray-50">
                  <button className="w-full text-left px-12 py-2 text-sm text-gray-600 hover:bg-gray-100 bg-orange-100 border-l-4 border-orange-500">S-Core</button>
                  {/* <button className="w-full text-left px-12 py-2 text-sm text-gray-600 hover:bg-gray-100">Graduation Checklist</button> */}
                </div>
              )}
            </div>
          ))}
        </nav>

        {/* Logout Button - Bottom */}
        <div className="border-t mt-auto">
          <button onClick={handleLogout} className={`w-full flex items-center ${isSidebarOpen ? "gap-3 px-4" : "justify-center px-0"} py-3 text-left hover:bg-gray-100 transition-colors`}>
            <svg className="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            {isSidebarOpen && <span className="text-sm text-red-500">Logout</span>}
          </button>
        </div>
      </div>

      {/* Main Content */}
      <div className="flex-1 overflow-auto">
        {/* Top Bar */}
        <div className="bg-white shadow-sm p-4 flex justify-between items-center">
          <button onClick={() => setIsSidebarOpen(!isSidebarOpen)} className="p-2 hover:bg-gray-100 rounded">
            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
          <div className="flex items-center gap-3">
            <div className="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
              <svg className="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <span className="text-sm font-medium">CALVIN WILLIAM</span>
          </div>
        </div>

        {/* Content */}
        <div className="p-6">
          {/* Header */}
          <div className="mb-6">
            <div className="flex items-center gap-4 mb-4">
              <h1 className="text-3xl font-bold text-gray-800">S-Core</h1>
              <span className="bg-green-500 text-white px-3 py-1 rounded text-sm font-semibold">APPROVED POINTS: 994</span>
              {/* <button className="bg-blue-500 text-white px-4 py-1 rounded text-sm hover:bg-blue-600">📄 Preview SKPI</button> */}
            </div>
          </div>

          {/* Kategori S-Core Wajib */}
          <div className="bg-white rounded-lg shadow p-6 mb-6">
            <h2 className="text-xl font-bold mb-4 text-center">Mandatory S-Core Categories</h2>
            <table className="w-full">
              <thead>
                <tr className="border-b">
                  <th className="text-left py-3 px-4">Category</th>
                  <th className="text-center py-3 px-4">Point Suggested</th>
                  <th className="text-center py-3 px-4">Achievement</th>
                  <th className="text-center py-3 px-4">Points</th>
                </tr>
              </thead>
              <tbody>
                {categories.map((cat, index) => (
                  <tr key={index} className="border-b hover:bg-gray-50">
                    <td className="py-3 px-4">{cat.kategori}</td>
                    <td className="text-center py-3 px-4">{cat.suggestion}</td>
                    <td className="text-center py-3 px-4">{cat.capaian}</td>
                    <td className="text-center py-3 px-4">{cat.point}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {/* Filters and Actions */}
          <div className="bg-white rounded-lg shadow p-4 mb-4">
            <div className="flex justify-between items-center gap-4">
              <div className="flex gap-2">
                <select value={statusFilter} onChange={(e) => setStatusFilter(e.target.value)} className="border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option value="">All Status</option>
                  <option value="Approve">Approve</option>
                  <option value="Waiting">Waiting</option>
                  <option value="Cancel">Cancel</option>
                </select>
                <select value={categoryFilter} onChange={(e) => setCategoryFilter(e.target.value)} className="border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option value="">All Categories</option>
                  {uniqueCategories.map((category, index) => (
                    <option key={index} value={category}>
                      {category}
                    </option>
                  ))}
                </select>
              </div>
              <div className="flex gap-2">
                <input
                  type="text"
                  placeholder="Search title, description, or category..."
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  className="border rounded px-4 py-2 text-sm w-80 focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <button onClick={handleAddNew} className="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm whitespace-nowrap">
                  + Add New
                </button>
              </div>
            </div>
          </div>

          {/* Activities Table */}
          <div className="bg-white rounded-lg shadow overflow-hidden">
            <table className="w-full">
              <thead className="bg-gray-50">
                <tr>
                  <th className="text-left py-3 px-4 font-semibold text-sm">Category</th>
                  <th className="text-left py-3 px-4 font-semibold text-sm">Activity Title</th>
                  <th className="text-left py-3 px-4 font-semibold text-sm">Description</th>
                  <th className="text-center py-3 px-4 font-semibold text-sm">Points</th>
                  <th className="text-center py-3 px-4 font-semibold text-sm">Certificate</th>
                  <th className="text-left py-3 px-4 font-semibold text-sm">Input Time</th>
                  <th className="text-center py-3 px-4 font-semibold text-sm">Status</th>
                  <th className="text-center py-3 px-4 font-semibold text-sm"></th>
                </tr>
              </thead>
              <tbody>
                {filteredActivities.length > 0 ? (
                  filteredActivities.map((activity, index) => (
                    <tr key={index} className="border-b hover:bg-gray-50">
                      <td className="py-3 px-4 text-sm">{activity.kategori}</td>
                      <td className="py-3 px-4 text-sm">{activity.judul}</td>
                      <td className="py-3 px-4 text-sm">{activity.keterangan}</td>
                      <td className="text-center py-3 px-4 text-sm">{activity.point}</td>
                      <td className="text-center py-3 px-4">
                        <button className="text-blue-500 hover:text-blue-700 p-1">
                          <svg className="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                          </svg>
                        </button>
                      </td>
                      <td className="py-3 px-4 text-xs text-gray-600">{activity.waktu}</td>
                      <td className="text-center py-3 px-4">
                        <span
                          className={`px-3 py-1 rounded-full text-xs font-semibold ${
                            activity.status === "Approve" ? "bg-green-100 text-green-700" : activity.status === "Waiting" ? "bg-yellow-100 text-yellow-700" : "bg-red-100 text-red-700"
                          }`}
                        >
                          {activity.status}
                        </span>
                      </td>
                      <td className="text-center py-3 px-4">
                        {activity.status === "Waiting" ? (
                          <>
                            <button onClick={() => handleEdit(activity, index)} className="text-green-500 hover:text-green-700 mr-2 p-1">
                              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                              </svg>
                            </button>
                            <button className="text-red-500 hover:text-red-700 p-1">
                              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                              </svg>
                            </button>
                          </>
                        ) : (
                          <button className="text-blue-500 hover:text-blue-700 p-1">
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                          </button>
                        )}
                      </td>
                    </tr>
                  ))
                ) : (
                  <tr>
                    <td colSpan="8" className="text-center py-8 text-gray-500">
                      No activities found matching your filters
                    </td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  );
}
