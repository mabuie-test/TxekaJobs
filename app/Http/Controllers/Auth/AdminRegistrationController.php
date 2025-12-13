<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRegistrationRequest;
use App\Services\Auth\AdminRegistrationService;
use Illuminate\Http\JsonResponse;

class AdminRegistrationController extends Controller
{
    public function store(
        AdminRegistrationRequest $request,
        AdminRegistrationService $service
    ): JsonResponse {
        $token = $request->bearerToken() ?? $request->header('X-Admin-Registration-Token');

        if ($token !== config('app.admin_registration_token')) {
            return response()->json([
                'message' => 'Acesso não autorizado para criar administradores.',
            ], 403);
        }

        $admin = $service->registerAdmin($request->validated());

        return response()->json([
            'message' => 'Administrador criado com sucesso.',
            'data' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'phone' => $admin->phone,
                'tipo_perfil' => $admin->tipo_perfil,
                'status' => $admin->status,
            ],
        ], 201);
    }
}
