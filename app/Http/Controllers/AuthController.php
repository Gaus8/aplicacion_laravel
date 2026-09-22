<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\ContactMessage;

class AuthController extends Controller
{
    public function showRegistro()
    {
        return view('auth.registro');
    }

    public function register(Request $request)
    {
        $credentials = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // 1. ASIGNAR A LA VARIABLE $user
        $user = User::create([
            'name' => $credentials['name'],
            'email' => $credentials['email'],
            'password' => Hash::make($credentials['password']),
        ]);

        // Petición desde Postman / API
        if ($request->wantsJson()) {
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Usuario registrado con éxito',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 201);
        }

        return redirect()->route('login')->with('success', 'Registro exitoso. Inicia sesión.');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            if ($request->wantsJson()) {
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'message' => 'Inicio de sesión exitoso',
                    'user' => $user,
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ], 200);
            }

            $request->session()->regenerate();
            return redirect()->intended('/admin/dashboard');
        }

        // Si la autenticación falla en API
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Las credenciales no coinciden con nuestros registros.'
            ], 401);
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        if ($request->wantsJson()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Sesión cerrada correctamente']);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    // Muestra la vista del formulario de envíos (emails.blade.php)
    public function emails()
    {
        return view('admin.emails');
    }

    // Procesa el envío del correo desde el formulario
    public function sendEmail(Request $request)
    {
        $validated = $request->validate([
            'recipient' => 'required|email',
            'subject'   => 'required|string|max:255',
            'message'   => 'required|string',
        ]);

        $data = [
            'name'    => auth()->user()->name,
            'email'   => auth()->user()->email,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
        ];

        // Envía el correo usando el Mailable generado
        Mail::to($validated['recipient'])->send(new ContactMessage($data));

        return redirect()->route('dashboard')->with('success', 'Correo electrónico enviado correctamente.');
    }
}