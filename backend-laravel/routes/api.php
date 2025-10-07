use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if (Auth::attempt($credentials)) {
        return response()->json(['message' => 'Login berhasil'], 200);
    }

    return response()->json(['message' => 'Email atau password salah'], 401);
});
