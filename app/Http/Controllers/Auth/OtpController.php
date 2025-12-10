<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OtpController extends Controller
{
    public function __construct(private OtpService $otpService)
    {
    }

    public function show(): View
    {
        return view('auth.otp');
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $userId = $request->session()->get('pending_user_id');
        $fingerprint = $request->session()->get('pending_device_fingerprint');

        if (! $userId || ! $fingerprint) {
            return redirect()->route('login')->withErrors(['code' => 'Sessão expirada. Faça login novamente.']);
        }

        $user = Auth::getProvider()->retrieveById($userId);

        if (! $user) {
            return redirect()->route('login')->withErrors(['code' => 'Utilizador não encontrado.']);
        }

        $session = $this->otpService->verify($user, $request->input('code'), $fingerprint);

        if (! $session) {
            return back()->withErrors(['code' => 'Código inválido ou expirado.']);
        }

        Auth::login($user);
        $request->session()->forget(['pending_user_id', 'pending_device_fingerprint']);
        $request->session()->regenerate();

        $user->forceFill([
            'phone_verified_at' => $user->phone_verified_at ?: now(),
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();

        return redirect()->route('home')->with('status', 'Sessão autenticada com sucesso.');
    }
}
