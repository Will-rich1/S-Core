import { useState } from "react";
import { useNavigate } from "react-router-dom";
import logoImg from "../images/logo.png";

export default function AdminReviewPage() {
  const [activeMenu, setActiveMenu] = useState("Review Submissions");
  const [isSidebarOpen, setIsSidebarOpen] = useState(false);
  const [showLogoutModal, setShowLogoutModal] = useState(false);
  const [showDetailModal, setShowDetailModal] = useState(false);
  const [showRejectModal, setShowRejectModal] = useState(false);
  const [selectedSubmission, setSelectedSubmission] = useState(null);
  const [rejectReason, setRejectReason] = useState("");
  const [assignedPoints, setAssignedPoints] = useState("");
  const navigate = useNavigate();

  // Filter states
  const [statusFilter, setStatusFilter] = useState("Waiting");
  const [categoryFilter, setCategoryFilter] = useState("");
  const [searchQuery, setSearchQuery] = useState("");
  const [studentFilter, setStudentFilter] = useState("");

  // Mock data - submissions from students
  const [submissions, setSubmissions] = useState([
    {
      id: 1,
      studentId: "2210426",
      studentName: "CALVIN WILLIAM",
      kategori: "Internship/Practical Work",
      judul: "Internship Certificate",
      keterangan: "Internship at HR Department for 3 months, handling recruitment and employee relations",
      point: null,
      suggestedPoint: 22,
      waktu: "12 Aug 2025 20:31:51",
      status: "Waiting",
      certificate: "internship_cert.pdf",
      activityDate: "2025-06-15",
      submittedDate: "2025-08-12",
    },
    {
      id: 2,
      studentId: "2210427",
      studentName: "JANE DOE",
      kategori: "Independent Learning Campus Program",
      judul: "Getting Started with Python Programming",
      keterangan: "Completed the class 'Getting Started with Python Programming' on Dicoding platform with excellent grade",
      point: null,
      suggestedPoint: 15,
      waktu: "08 Aug 2025 21:23:33",
      status: "Waiting",
      certificate: "python_cert.pdf",
      activityDate: "2025-07-20",
      submittedDate: "2025-08-08",
    },
    {
      id: 3,
      studentId: "2210428",
      studentName: "JOHN SMITH",
      kategori: "Workshop/Training/Seminar Activities",
      judul: "Web Development Workshop",
      keterangan: "Attended 3-day intensive web development workshop covering HTML, CSS, and JavaScript",
      point: null,
      suggestedPoint: 8,
      waktu: "10 Aug 2025 15:45:12",
      status: "Waiting",
      certificate: "workshop_cert.pdf",
      activityDate: "2025-08-01",
      submittedDate: "2025-08-10",
    },
    {
      id: 4,
      studentId: "2210426",
      studentName: "CALVIN WILLIAM",
      kategori: "Achievement in Science, Literature and Other Academic Activities (olympiad, pitmapres, etc)",
      judul: "Pilmapres Region III Finalist",
      keterangan: "Became Finalist of Pilmapres Region III representing ITBSS",
      point: 18,
      waktu: "07 Aug 2025 21:19:52",
      status: "Approve",
      certificate: "pilmapres_cert.pdf",
      activityDate: "2025-07-15",
      submittedDate: "2025-08-07",
      reviewedBy: "Admin User",
      reviewedDate: "2025-08-08",
    },
    {
      id: 5,
      studentId: "2210429",
      studentName: "ALICE JOHNSON",
      kategori: "IPR/Patent",
      judul: "Mobile App UI/UX Design Patent",
      keterangan: "Registered intellectual property rights for innovative mobile application design",
      point: null,
      waktu: "11 Aug 2025 10:30:00",
      status: "Cancel",
      certificate: "ipr_cert.pdf",
      activityDate: "2025-07-25",
      submittedDate: "2025-08-11",
      rejectReason: "Certificate does not match the activity description. Please resubmit with correct documentation.",
      reviewedBy: "Admin User",
      reviewedDate: "2025-08-11",
    },
  ]);

  // Get unique categories and students for filters
  const uniqueCategories = [...new Set(submissions.map((sub) => sub.kategori))];
  const uniqueStudents = [...new Set(submissions.map((sub) => `${sub.studentId} - ${sub.studentName}`))];

  // Filter submissions
  const filteredSubmissions = submissions.filter((submission) => {
    const matchesSearch =
      searchQuery === "" ||
      submission.judul.toLowerCase().includes(searchQuery.toLowerCase()) ||
      submission.keterangan.toLowerCase().includes(searchQuery.toLowerCase()) ||
      submission.kategori.toLowerCase().includes(searchQuery.toLowerCase()) ||
      submission.studentName.toLowerCase().includes(searchQuery.toLowerCase()) ||
      submission.studentId.includes(searchQuery);

    const matchesStatus = statusFilter === "" || submission.status === statusFilter;
    const matchesCategory = categoryFilter === "" || submission.kategori === categoryFilter;
    const matchesStudent = studentFilter === "" || `${submission.studentId} - ${submission.studentName}` === studentFilter;

    return matchesSearch && matchesStatus && matchesCategory && matchesStudent;
  });

  // Statistics
  const stats = {
    total: submissions.length,
    waiting: submissions.filter((s) => s.status === "Waiting").length,
    approved: submissions.filter((s) => s.status === "Approve").length,
    rejected: submissions.filter((s) => s.status === "Cancel").length,
  };

  const menuItems = [
    { icon: "clipboard", label: "Review Submissions" },
    { icon: "chart", label: "Statistics" },
    { icon: "users", label: "Students" },
    { icon: "settings", label: "Settings" },
    { icon: "help", label: "Help" },
  ];

  const getMenuIcon = (iconName) => {
    const iconProps = { className: "w-6 h-6", fill: "none", stroke: "currentColor", viewBox: "0 0 24 24" };
    const pathProps = { strokeLinecap: "round", strokeLinejoin: "round", strokeWidth: 1.5 };

    switch (iconName) {
      case "clipboard":
        return (
          <svg {...iconProps}>
            <path {...pathProps} d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
          </svg>
        );
      case "chart":
        return (
          <svg {...iconProps}>
            <path {...pathProps} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
        );
      case "users":
        return (
          <svg {...iconProps}>
            <path {...pathProps} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
        );
      case "settings":
        return (
          <svg {...iconProps}>
            <path
              {...pathProps}
              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
            />
            <path {...pathProps} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
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

  const handleViewDetail = (submission) => {
    setSelectedSubmission(submission);
    setAssignedPoints(submission.suggestedPoint?.toString() || "");
    setShowDetailModal(true);
  };

  const handleApprove = () => {
    if (!assignedPoints || assignedPoints <= 0) {
      alert("Please enter valid points");
      return;
    }

    setSubmissions((prev) =>
      prev.map((sub) =>
        sub.id === selectedSubmission.id
          ? {
              ...sub,
              status: "Approve",
              point: parseInt(assignedPoints),
              reviewedBy: "Admin User",
              reviewedDate: new Date().toISOString().split("T")[0],
            }
          : sub
      )
    );

    setShowDetailModal(false);
    setSelectedSubmission(null);
    setAssignedPoints("");
  };

  const handleRejectClick = () => {
    setShowDetailModal(false);
    setShowRejectModal(true);
  };

  const handleRejectConfirm = () => {
    if (!rejectReason.trim()) {
      alert("Please provide a reason for rejection");
      return;
    }

    setSubmissions((prev) =>
      prev.map((sub) =>
        sub.id === selectedSubmission.id
          ? {
              ...sub,
              status: "Cancel",
              rejectReason: rejectReason,
              reviewedBy: "Admin User",
              reviewedDate: new Date().toISOString().split("T")[0],
            }
          : sub
      )
    );

    setShowRejectModal(false);
    setSelectedSubmission(null);
    setRejectReason("");
  };

  const handleCloseModal = () => {
    setShowDetailModal(false);
    setShowRejectModal(false);
    setSelectedSubmission(null);
    setAssignedPoints("");
    setRejectReason("");
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

      {/* Detail Modal */}
      {showDetailModal && selectedSubmission && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg p-6 max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div className="flex justify-between items-center mb-6">
              <h3 className="text-xl font-semibold">Review Submission</h3>
              <button onClick={handleCloseModal} className="text-gray-500 hover:text-gray-700 text-2xl leading-none">
                ×
              </button>
            </div>

            <div className="space-y-4">
              {/* Student Info */}
              <div className="bg-blue-50 border border-blue-200 rounded p-4">
                <h4 className="font-semibold text-blue-900 mb-2">Student Information</h4>
                <div className="grid grid-cols-2 gap-3 text-sm">
                  <div>
                    <span className="text-gray-600">Student ID:</span>
                    <span className="ml-2 font-medium">{selectedSubmission.studentId}</span>
                  </div>
                  <div>
                    <span className="text-gray-600">Name:</span>
                    <span className="ml-2 font-medium">{selectedSubmission.studentName}</span>
                  </div>
                  <div>
                    <span className="text-gray-600">Submitted:</span>
                    <span className="ml-2 font-medium">{selectedSubmission.submittedDate}</span>
                  </div>
                  <div>
                    <span className="text-gray-600">Activity Date:</span>
                    <span className="ml-2 font-medium">{selectedSubmission.activityDate}</span>
                  </div>
                </div>
              </div>

              {/* Category */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <div className="bg-gray-50 border rounded px-4 py-2 text-sm">{selectedSubmission.kategori}</div>
              </div>

              {/* Activity Title */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Activity Title</label>
                <div className="bg-gray-50 border rounded px-4 py-2 text-sm">{selectedSubmission.judul}</div>
              </div>

              {/* Description */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <div className="bg-gray-50 border rounded px-4 py-3 text-sm whitespace-pre-wrap">{selectedSubmission.keterangan}</div>
              </div>

              {/* Certificate */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Certificate/Evidence</label>
                <div className="flex items-center gap-3">
                  <button className="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm flex items-center gap-2">
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    View Certificate
                  </button>
                  <span className="text-sm text-gray-600">{selectedSubmission.certificate}</span>
                </div>
              </div>

              {/* Points Assignment */}
              <div className="bg-yellow-50 border border-yellow-200 rounded p-4">
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Assign Points <span className="text-red-500">*</span>
                </label>
                <div className="flex items-center gap-3">
                  <input
                    type="number"
                    value={assignedPoints}
                    onChange={(e) => setAssignedPoints(e.target.value)}
                    className="border rounded px-4 py-2 text-sm w-32 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Points"
                    min="0"
                  />
                  <span className="text-sm text-gray-600">Suggested: {selectedSubmission.suggestedPoint} points</span>
                </div>
              </div>
            </div>

            {/* Action Buttons */}
            <div className="flex gap-3 justify-end mt-6">
              <button onClick={handleCloseModal} className="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm font-medium">
                Cancel
              </button>
              <button onClick={handleRejectClick} className="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded text-sm font-medium">
                Reject
              </button>
              <button onClick={handleApprove} className="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded text-sm font-medium">
                Approve
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Reject Modal */}
      {showRejectModal && selectedSubmission && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg p-6 max-w-xl w-full mx-4">
            <div className="flex justify-between items-center mb-6">
              <h3 className="text-xl font-semibold text-red-600">Reject Submission</h3>
              <button onClick={handleCloseModal} className="text-gray-500 hover:text-gray-700 text-2xl leading-none">
                ×
              </button>
            </div>

            <div className="mb-4">
              <p className="text-sm text-gray-600 mb-4">
                You are about to reject submission from <strong>{selectedSubmission.studentName}</strong> ({selectedSubmission.studentId})
              </p>
              <p className="text-sm font-medium mb-2">Activity: {selectedSubmission.judul}</p>
            </div>

            <div className="mb-6">
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Reason for Rejection <span className="text-red-500">*</span>
              </label>
              <textarea
                value={rejectReason}
                onChange={(e) => setRejectReason(e.target.value)}
                rows="4"
                className="w-full border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
                placeholder="Please provide a clear reason for rejection so the student can understand and resubmit correctly..."
              />
            </div>

            <div className="flex gap-3 justify-end">
              <button onClick={handleCloseModal} className="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm font-medium">
                Cancel
              </button>
              <button onClick={handleRejectConfirm} className="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded text-sm font-medium">
                Confirm Rejection
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
              <h2 className="text-sm font-bold text-gray-800">S-Core Admin</h2>
              <p className="text-xs text-gray-500">Review & Management</p>
            </div>
          )}
        </div>

        {/* Menu Items */}
        <nav className="mt-4 flex-1">
          {menuItems.map((item, index) => (
            <button
              key={index}
              onClick={() => setActiveMenu(item.label)}
              className={`w-full flex items-center ${isSidebarOpen ? "gap-3 px-4" : "justify-center px-0"} py-3 text-left hover:bg-gray-100 transition-colors ${activeMenu === item.label ? "bg-blue-50 border-l-4 border-blue-500" : ""}`}
            >
              {getMenuIcon(item.icon)}
              {isSidebarOpen && <span className="text-sm text-gray-700">{item.label}</span>}
            </button>
          ))}
        </nav>

        {/* Logout Button */}
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
            <span className="text-sm font-medium text-blue-600">👤 Admin User</span>
          </div>
        </div>

        {/* Content */}
        <div className="p-6">
          {/* Header */}
          <div className="mb-6">
            <h1 className="text-3xl font-bold text-gray-800 mb-2">S-Core Submission Review</h1>
            <p className="text-gray-600">Review and approve student activity submissions</p>
          </div>

          {/* Statistics Cards */}
          <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div className="bg-white rounded-lg shadow p-4">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm text-gray-600">Total Submissions</p>
                  <p className="text-2xl font-bold text-gray-800">{stats.total}</p>
                </div>
                <div className="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                  <svg className="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-lg shadow p-4">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm text-gray-600">Waiting Review</p>
                  <p className="text-2xl font-bold text-yellow-600">{stats.waiting}</p>
                </div>
                <div className="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                  <svg className="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-lg shadow p-4">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm text-gray-600">Approved</p>
                  <p className="text-2xl font-bold text-green-600">{stats.approved}</p>
                </div>
                <div className="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                  <svg className="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-lg shadow p-4">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm text-gray-600">Rejected</p>
                  <p className="text-2xl font-bold text-red-600">{stats.rejected}</p>
                </div>
                <div className="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                  <svg className="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
              </div>
            </div>
          </div>

          {/* Filters */}
          <div className="bg-white rounded-lg shadow p-4 mb-4">
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
              <select value={statusFilter} onChange={(e) => setStatusFilter(e.target.value)} className="border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="Waiting">Waiting</option>
                <option value="Approve">Approved</option>
                <option value="Cancel">Rejected</option>
              </select>

              <select value={categoryFilter} onChange={(e) => setCategoryFilter(e.target.value)} className="border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Categories</option>
                {uniqueCategories.map((category, index) => (
                  <option key={index} value={category}>
                    {category.substring(0, 30)}...
                  </option>
                ))}
              </select>

              <select value={studentFilter} onChange={(e) => setStudentFilter(e.target.value)} className="border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Students</option>
                {uniqueStudents.map((student, index) => (
                  <option key={index} value={student}>
                    {student}
                  </option>
                ))}
              </select>

              <input type="text" placeholder="Search submissions..." value={searchQuery} onChange={(e) => setSearchQuery(e.target.value)} className="border rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
          </div>

          {/* Submissions Table */}
          <div className="bg-white rounded-lg shadow overflow-hidden">
            <table className="w-full">
              <thead className="bg-gray-50">
                <tr>
                  <th className="text-left py-3 px-4 font-semibold text-sm">Student</th>
                  <th className="text-left py-3 px-4 font-semibold text-sm">Category</th>
                  <th className="text-left py-3 px-4 font-semibold text-sm">Activity Title</th>
                  <th className="text-center py-3 px-4 font-semibold text-sm">Points</th>
                  <th className="text-left py-3 px-4 font-semibold text-sm">Submitted</th>
                  <th className="text-center py-3 px-4 font-semibold text-sm">Status</th>
                  <th className="text-center py-3 px-4 font-semibold text-sm">Action</th>
                </tr>
              </thead>
              <tbody>
                {filteredSubmissions.length > 0 ? (
                  filteredSubmissions.map((submission) => (
                    <tr key={submission.id} className="border-b hover:bg-gray-50">
                      <td className="py-3 px-4">
                        <div className="text-sm">
                          <div className="font-medium">{submission.studentName}</div>
                          <div className="text-gray-500 text-xs">{submission.studentId}</div>
                        </div>
                      </td>
                      <td className="py-3 px-4 text-sm max-w-xs">
                        <div className="truncate" title={submission.kategori}>
                          {submission.kategori}
                        </div>
                      </td>
                      <td className="py-3 px-4 text-sm">{submission.judul}</td>
                      <td className="text-center py-3 px-4 text-sm font-medium">{submission.point || "-"}</td>
                      <td className="py-3 px-4 text-xs text-gray-600">{submission.waktu}</td>
                      <td className="text-center py-3 px-4">
                        <span
                          className={`px-3 py-1 rounded-full text-xs font-semibold ${
                            submission.status === "Approve" ? "bg-green-100 text-green-700" : submission.status === "Waiting" ? "bg-yellow-100 text-yellow-700" : "bg-red-100 text-red-700"
                          }`}
                        >
                          {submission.status}
                        </span>
                      </td>
                      <td className="text-center py-3 px-4">
                        <button onClick={() => handleViewDetail(submission)} className="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs font-medium">
                          Review
                        </button>
                      </td>
                    </tr>
                  ))
                ) : (
                  <tr>
                    <td colSpan="7" className="text-center py-8 text-gray-500">
                      No submissions found matching your filters
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
