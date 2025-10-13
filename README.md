# S-Core ITBSS

## Sabda Setia Student Point System

Sistem informasi akademik untuk manajemen SKPI (Surat Keterangan Pendamping Ijazah) mahasiswa Institut Teknologi & Bisnis Sabda Setia.

---

## 📁 Struktur Project

```
S-Core/
├── backend-laravel/     # Laravel 10 (PHP 8.1+)
│   ├── app/
│   ├── routes/
│   ├── config/
│   └── ...
│
└── frontend-react/      # React + Vite + Tailwind CSS
    ├── src/
    ├── public/
    └── ...
```

---

## 🚀 Cara Install & Menjalankan

### **Prerequisites:**

- PHP 8.1 atau lebih tinggi
- Composer
- Node.js & npm
- Git
- MySQL (opsional untuk development)

---

### **1. Clone Repository**

```bash
git clone https://github.com/YOUR_USERNAME/S-Core.git
cd S-Core
```

---

### **2. Setup Backend (Laravel)**

```bash
cd backend-laravel

# Install dependencies
composer install

# Copy environment file
copy .env.example .env

# Generate application key
php artisan key:generate

# Jalankan server
php artisan serve
```

Backend akan berjalan di: `http://localhost:8000`

---

### **3. Setup Frontend (React)**

```bash
cd frontend-react

# Install dependencies
npm install

# Jalankan development server
npm run dev
```

Frontend akan berjalan di: `http://localhost:5173`

---

## 🔑 Testing Login

**Credential untuk testing:**

- Email: `admin@itbss.ac.id`
- Password: `password`

---

## 📦 Folder yang TIDAK PERLU di-push ke GitHub

### **Backend Laravel:**

- ❌ `/vendor/` - Dependencies PHP (~100-200 MB)
- ❌ `/node_modules/` - Dependencies Node.js
- ❌ `.env` - File konfigurasi sensitif
- ❌ `/bootstrap/cache/` - Cache files
- ❌ `/storage/logs/` - Log files
- ✅ `.env.example` - Template environment (boleh push)

### **Frontend React:**

- ❌ `/node_modules/` - Dependencies (~300-500 MB)
- ❌ `/dist/` - Build output
- ❌ `.env` - Environment variables

**Semua sudah dikonfigurasi di `.gitignore`** ✅

---

## 📝 API Endpoints

| Method | Endpoint      | Description          |
| ------ | ------------- | -------------------- |
| POST   | `/api/login`  | Login authentication |
| POST   | `/api/logout` | Logout               |
| GET    | `/api/test`   | Test API connection  |

---

## 🛠️ Tech Stack

### **Backend:**

- Laravel 10
- PHP 8.1+
- Laravel Sanctum (Authentication)
- CORS enabled

### **Frontend:**

- React 18
- Vite
- Tailwind CSS
- React Router
- Axios

---

## 👥 Developer

Institut Teknologi & Bisnis Sabda Setia
