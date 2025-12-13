<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StoreClienteRegistrationRequest;
use App\Http\Requests\Auth\StorePrestadorRegistrationRequest;
use App\Models\Cliente;
use App\Models\Prestador;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showClienteForm(): View
    {
        return view('auth.register-cliente', [
            'cities' => config('cities.mozambique_cities'),
        ]);
    }

    public function showPrestadorForm(): View
    {
        return view('auth.register-prestador', [
            'cities' => config('cities.mozambique_cities'),
            'documentTypes' => [
                'bilhete_identidade' => 'Bilhete de Identidade',
                'passaporte' => 'Passaporte',
                'dire' => 'DIRE',
                'carta_conducao' => 'Carta de Condução',
            ],
        ]);
    }

    public function storeCliente(StoreClienteRegistrationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = DB::transaction(function () use ($data) {
            /** @var User $user */
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'tipo_perfil' => 'cliente',
                'status' => 'activo',
            ]);

            Cliente::create([
                'user_id' => $user->id,
                'morada_principal' => $data['morada_principal'] ?? null,
                'cidade' => $data['cidade'],
                'bairro_principal' => $data['bairro_principal'] ?? null,
                'referencia_localizacao_texto' => $data['referencia_localizacao_texto'] ?? null,
            ]);

            return $user;
        });

        Auth::login($user);
        $user->sendEmailVerificationNotification();

        return redirect()->route('verification.notice')->with('status', 'Conta criada! Confirme o email para continuar.');
    }

    public function storePrestador(StorePrestadorRegistrationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = DB::transaction(function () use ($data) {
            /** @var User $user */
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'tipo_perfil' => 'prestador',
                'status' => 'activo',
            ]);

            Prestador::create([
                'user_id' => $user->id,
                'bio' => $data['bio'] ?? null,
                'tipo_documento' => $data['tipo_documento'],
                'numero_documento' => $data['numero_documento'],
                'tipo_carteira' => $data['tipo_carteira'],
                'numero_carteira' => $data['numero_carteira'],
                'estado_verificacao' => 'pendente',
                'esta_disponivel' => true,
                'aceita_servicos_urgentes' => $data['aceita_servicos_urgentes'] ?? false,
                'cidade' => $data['cidade'],
                'bairro_principal' => $data['bairro_principal'] ?? null,
            ]);

            return $user;
        });

        Auth::login($user);
        $user->sendEmailVerificationNotification();

        return redirect()->route('verification.notice')->with('status', 'Perfil de prestador criado! Confirme o email para activar e concluir o onboarding.');
    }
}
