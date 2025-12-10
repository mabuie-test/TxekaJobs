<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(private OtpService $otpService)
    {
    }

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

        $fingerprint = $this->otpService->generateFingerprint(
            $request->input('device_fingerprint'),
            $request->userAgent(),
            $request->ip()
        );

        $this->otpService->issue($user, $fingerprint, $request->userAgent(), $request->ip());

        $request->session()->put('pending_user_id', $user->id);
        $request->session()->put('pending_device_fingerprint', $fingerprint);

        return redirect()->route('otp.show')->with('status', 'Código OTP enviado por SMS.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Sessão terminada.');
    }
}
