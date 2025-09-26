<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        // Trae roles existentes desde la BD (llenados por el seeder)
        $roles = Role::query()->orderBy('name')->pluck('name')->toArray();

        return view('auth.register', compact('roles'));
    }

    public function register(Request $request)
    {
        $roles = Role::pluck('name')->toArray();

        $data = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','string','email','max:255','unique:users'],
            'password' => ['required','string','min:8','confirmed'],
            'role'     => ['required', Rule::in($roles)],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Asigna el rol seleccionado
        $user->assignRole($data['role']);

        return redirect()->route('login')->with('success', 'Usuario registrado correctamente. Inicia sesión.');
    }
}
