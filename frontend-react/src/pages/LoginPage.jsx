import { useState } from "react";
import axios from "axios";

export default function LoginPage() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");

    try {
      const response = await axios.post("http://localhost:8000/api/login", { email, password });
      alert(response.data.message);
    } catch (err) {
      setError("Email atau password salah");
    }
  };

  return (
    <div className="flex h-screen">
      {/* Kiri - Gambar Kampus */}
      <div className="w-1/2 bg-cover bg-center" style={{ backgroundImage: "url(/campus.jpg)" }}></div>

      {/* Kanan - Form Login */}
      <div className="w-1/2 flex flex-col justify-center items-center bg-gray-50">
        <div className="w-80">
          <div className="text-center mb-8">
            <img src="/logo.png" alt="Logo" className="mx-auto w-32" />
            <h1 className="text-2xl font-bold mt-3 text-gray-800">SIAKAD PRADITA</h1>
            <p className="text-gray-500 text-sm">Sistem Informasi Akademik Pradita University</p>
          </div>

          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <label className="text-gray-600 text-sm">Email</label>
              <input type="email" value={email} onChange={(e) => setEmail(e.target.value)} className="w-full border rounded-md p-2 mt-1 focus:outline-primary focus:ring-1 focus:ring-primary" />
            </div>

            <div>
              <label className="text-gray-600 text-sm">Password</label>
              <input type="password" value={password} onChange={(e) => setPassword(e.target.value)} className="w-full border rounded-md p-2 mt-1 focus:outline-primary focus:ring-1 focus:ring-primary" />
            </div>

            {error && <p className="text-red-500 text-sm">{error}</p>}

            <div className="flex justify-between items-center text-sm">
              <a href="#" className="text-primary hover:underline">
                Forgot your password?
              </a>
              <label className="flex items-center">
                <input type="checkbox" className="mr-1" />
                Remember me
              </label>
            </div>

            <button type="submit" className="w-full bg-primary text-white py-2 rounded-md font-semibold hover:bg-blue-600">
              Login
            </button>
          </form>
        </div>
      </div>
    </div>
  );
}
