<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        /** @var User|null $user */
        $user = User::where('email', $data['login'])
            ->orWhere('phone', $data['login'])
            ->first();

        if (! $user || $user->status !== 'activo' || ! Hash::check($data['password'], $user->password)) {
            return back()->withErrors(['login' => 'Credenciais inválidas ou conta suspensa.'])->withInput();
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')->with('status', 'Confirme o email para continuar.');
        }

        return redirect()->to($this->routeForPerfil($user->tipo_perfil));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Sessão terminada.');
    }

    private function routeForPerfil(string $perfil): string
    {
        return match ($perfil) {
            'cliente' => route('cliente.servicos.index'),
            'prestador' => route('prestador.dashboard'),
            'admin' => route('admin.backups.index'),
            default => route('home'),
        };
    }
}
