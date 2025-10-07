import { useState } from "react";
import logoImg from "../images/logo.png";

export default function DashboardPage() {
  const [activeMenu, setActiveMenu] = useState("S-Core");
  const [isSidebarOpen, setIsSidebarOpen] = useState(false);

  const activities = [
    {
      kategori: "Magang/Kerja Praktik",
      judul: "Sertifikat Magang",
      keterangan: "Magang di Bagian Kepegawaian...",
      point: 22,
      waktu: "12 Aug 2025 20:31:51:823",
      status: "Approve",
    },
    {
      kategori: "Magang/Kerja Praktik",
      judul: "Sertifikat Magang",
      keterangan: "Sertifikat Magang di Bagian K...",
      point: "-",
      waktu: "12 Aug 2025 20:31:10:010",
      status: "Cancel",
    },
    {
      kategori: "Merdeka Belajar Kampus Merdeka",
      judul: "Memulai Pemrograman dengan Python",
      keterangan: "Menyelesaikan kelas 'Memula...",
      point: "-",
      waktu: "08 Aug 2025 21:23:33:303",
      status: "Waiting",
    },
    {
      kategori: "Prestasi dalam Bidang Sains, Literatur dan Kegiatan Akademik Lain (olimpiade, pitmapres, etc)",
      judul: "Finalis Pilmapres wilayah III",
      keterangan: "Menjadi Finalis Pilmapres wil...",
      point: 18,
      waktu: "07 Aug 2025 21:19:52:307",
      status: "Approve",
    },
    {
      kategori: "Peserta Kegiatan Minat dan Bakat (olahraga, seni, dan kerohanian)",
      judul: "Peserta lomba Nasional Web Development",
      keterangan: "Menjadi peserta lomba Nasion...",
      point: 10,
      waktu: "07 Aug 2025 21:18:27:867",
      status: "Approve",
    },
    {
      kategori: "HKI/Paten",
      judul: "HKI Aplikasi Terrafrace (UI UX Design)",
      keterangan: "Bukti HKI aplikasi Terrafrace",
      point: 20,
      waktu: "10 Jul 2025 22:28:37:203",
      status: "Approve",
    },
  ];

  const categories = [
    { kategori: "Magang/Kerja Praktik", minimal: 1, capaian: 2, point: 22 },
    { kategori: "Merdeka Belajar Kampus Merdeka", minimal: 1, capaian: 16, point: 292 },
    { kategori: "Pengenalan Kehidupan Kampus Mahasiswa Baru (PKKMB)", minimal: 1, capaian: 2, point: 40 },
    { kategori: "Kegiatan Lokakarya/Pelatihan/Workshop/Seminar", minimal: 5, capaian: 29, point: 230 },
  ];

  const menuItems = [
    { icon: "📊", label: "Dashboard" },
    { icon: "📋", label: "KPRS" },
    { icon: "📅", label: "Jadwal" },
    { icon: "📝", label: "Daftar Hadir" },
    { icon: "📚", label: "Nilai Semester" },
    { icon: "📄", label: "Transkrip Nilai" },
    { icon: "🎓", label: "Kemahasiswaan", hasSubmenu: true },
    { icon: "👥", label: "Bimbingan" },
    { icon: "❓", label: "Help" },
    { icon: "🚪", label: "Logout" },
  ];

  return (
    <div className="flex h-screen bg-gray-100">
      {/* Sidebar */}
      <div className={`${isSidebarOpen ? "w-64" : "w-16"} bg-white shadow-lg transition-all duration-300`}>
        {/* Logo Header */}
        <div className="p-4 border-b flex flex-col items-center">
          <img src={logoImg} alt="Logo" className="w-12 h-12" />
          {isSidebarOpen && (
            <div className="mt-2 text-center">
              <h2 className="text-sm font-bold text-gray-800">S-Core ITBSS</h2>
              <p className="text-xs text-gray-500">Sabda Setia Student Point System</p>
            </div>
          )}
        </div>

        {/* Menu Items */}
        <nav className="mt-4">
          {menuItems.map((item, index) => (
            <div key={index}>
              <button onClick={() => setActiveMenu(item.label)} className={`w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-gray-100 transition-colors ${activeMenu === item.label ? "bg-blue-50 border-l-4 border-primary" : ""}`}>
                <span className="text-xl">{item.icon}</span>
                {isSidebarOpen && <span className="text-sm text-gray-700">{item.label}</span>}
              </button>
              {item.hasSubmenu && activeMenu === "Kemahasiswaan" && isSidebarOpen && (
                <div className="bg-gray-50">
                  <button className="w-full text-left px-12 py-2 text-sm text-gray-600 hover:bg-gray-100 bg-orange-100 border-l-4 border-orange-500">S-Core</button>
                  <button className="w-full text-left px-12 py-2 text-sm text-gray-600 hover:bg-gray-100">Checklist Yudisium</button>
                </div>
              )}
            </div>
          ))}
        </nav>
      </div>

      {/* Main Content */}
      <div className="flex-1 overflow-auto">
        {/* Top Bar */}
        <div className="bg-white shadow-sm p-4 flex justify-between items-center">
          <button onClick={() => setIsSidebarOpen(!isSidebarOpen)} className="p-2 hover:bg-gray-100 rounded">
            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
          <div className="flex items-center gap-3">
            <div className="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
              <span className="text-sm">👤</span>
            </div>
            <span className="text-sm font-medium">Calvin William</span>
          </div>
        </div>

        {/* Content */}
        <div className="p-6">
          {/* Header */}
          <div className="mb-6">
            <div className="flex items-center gap-4 mb-4">
              <h1 className="text-3xl font-bold text-gray-800">S-Core</h1>
              <span className="bg-green-500 text-white px-3 py-1 rounded text-sm font-semibold">POINT APPROVED: 994</span>
              <button className="bg-blue-500 text-white px-4 py-1 rounded text-sm hover:bg-blue-600">📄 Preview SKPI</button>
            </div>
          </div>

          {/* Kategori S-Core Wajib */}
          <div className="bg-white rounded-lg shadow p-6 mb-6">
            <h2 className="text-xl font-bold mb-4 text-center">Kategori S-Core Wajib</h2>
            <table className="w-full">
              <thead>
                <tr className="border-b">
                  <th className="text-left py-3 px-4">Kategori</th>
                  <th className="text-center py-3 px-4">Minimal</th>
                  <th className="text-center py-3 px-4">Capaian</th>
                  <th className="text-center py-3 px-4">Point</th>
                </tr>
              </thead>
              <tbody>
                {categories.map((cat, index) => (
                  <tr key={index} className="border-b hover:bg-gray-50">
                    <td className="py-3 px-4">{cat.kategori}</td>
                    <td className="text-center py-3 px-4">{cat.minimal}</td>
                    <td className="text-center py-3 px-4">{cat.capaian}</td>
                    <td className="text-center py-3 px-4">{cat.point}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {/* Filters and Actions */}
          <div className="bg-white rounded-lg shadow p-4 mb-4 flex justify-between items-center">
            <select className="border rounded px-4 py-2 text-sm">
              <option>Status</option>
              <option>Approve</option>
              <option>Waiting</option>
              <option>Cancel</option>
            </select>
            <div className="flex gap-2">
              <input type="text" placeholder="Search" className="border rounded px-4 py-2 text-sm" />
              <button className="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded text-sm">🔍</button>
              <button className="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">+ Add New</button>
            </div>
          </div>

          {/* Activities Table */}
          <div className="bg-white rounded-lg shadow overflow-hidden">
            <table className="w-full">
              <thead className="bg-gray-50">
                <tr>
                  <th className="text-left py-3 px-4 font-semibold text-sm">Kategori</th>
                  <th className="text-left py-3 px-4 font-semibold text-sm">Judul Kegiatan</th>
                  <th className="text-left py-3 px-4 font-semibold text-sm">Keterangan</th>
                  <th className="text-center py-3 px-4 font-semibold text-sm">Point</th>
                  <th className="text-center py-3 px-4 font-semibold text-sm">Sertifikat</th>
                  <th className="text-left py-3 px-4 font-semibold text-sm">Waktu Input</th>
                  <th className="text-center py-3 px-4 font-semibold text-sm">Status</th>
                  <th className="text-center py-3 px-4 font-semibold text-sm"></th>
                </tr>
              </thead>
              <tbody>
                {activities.map((activity, index) => (
                  <tr key={index} className="border-b hover:bg-gray-50">
                    <td className="py-3 px-4 text-sm">{activity.kategori}</td>
                    <td className="py-3 px-4 text-sm">{activity.judul}</td>
                    <td className="py-3 px-4 text-sm">{activity.keterangan}</td>
                    <td className="text-center py-3 px-4 text-sm">{activity.point}</td>
                    <td className="text-center py-3 px-4">
                      <span className="text-blue-500 text-2xl">🖼️</span>
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
                      <button className="text-blue-500 hover:text-blue-700 mr-2">👁️</button>
                      {activity.status === "Waiting" && (
                        <>
                          <button className="text-green-500 hover:text-green-700 mr-2">✏️</button>
                          <button className="text-red-500 hover:text-red-700">🗑️</button>
                        </>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  );
}
