<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class LoginController extends Controller
{
    // Muestra el formulario de login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Maneja el login del usuario
    public function login(Request $request)
    {
        // Validación de las credenciales
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Intentamos autenticar al usuario con los datos proporcionados
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            // Redirige al usuario a la página que intentaba acceder originalmente
            return redirect()->intended('/');
        }

        // Si la autenticación falla, redirige de nuevo con un mensaje de error
        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas son incorrectas.',
        ]);
    }

    // Cierra la sesión del usuario
    public function logout()
    {
        Auth::logout(); // Cierra la sesión
        return redirect('/'); // Redirige al login
    }
}
