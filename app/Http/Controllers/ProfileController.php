<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('profile.edit');
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->only(['name', 'email', 'phone']);

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo_path'] = $request->file('profile_photo')->store('profile_photos', 'public');
        }

        if ($request->hasFile('curriculum')) {
            $data['curriculum_path'] = $request->file('curriculum')->store('curriculums', 'public');
        }

        $user->update($data);

        if ($request->filled('email') && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }

        return back()->with('status', 'Perfil actualizado com sucesso.');
    }
}
