<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileApiController extends Controller
{
    /**
     * Return the authenticated user's full profile data including skills and CV meta.
     */
    public function show(Request $request)
    {
        $user    = $request->user();
        $profile = $user->profile;
        $skills  = $user->skills()->orderBy('name')->get();

        return response()->json([
            'user'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->role,
                'avatar_url' => $profile?->avatar_url
                                    ? Storage::url($profile->avatar_url)
                                    : null,
            ],
            'profile' => [
                'bio'      => $profile?->bio,
                'cv_score' => $profile?->cv_score,
                'cv_path'  => $profile?->cv_path ? true : false, // existence flag only
            ],
            'skills'  => $skills,
        ]);
    }

    /**
     * Update basic profile fields: name, bio.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:80',
            'bio'  => 'sometimes|nullable|string|max:500',
        ]);

        $user = $request->user();

        if (isset($data['name'])) {
            $user->update(['name' => $data['name']]);
        }

        if (array_key_exists('bio', $data)) {
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                ['bio'     => $data['bio']]
            );
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ]);
    }

    /**
     * Change the authenticated user's password.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password'      => 'required|string',
            'password'              => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect.',
                'errors'  => ['current_password' => ['Current password is incorrect.']],
            ], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json(['message' => 'Password changed successfully.']);
    }

    /**
     * Upload a profile avatar image.
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,webp|max:2048',
        ]);

        $user = $request->user();

        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);

        // Delete old avatar from storage to avoid orphaned files
        if ($profile->avatar_url) {
            Storage::delete($profile->avatar_url);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $profile->update(['avatar_url' => $path]);

        return response()->json([
            'message'    => 'Avatar updated successfully.',
            'avatar_url' => Storage::url($path),
        ]);
    }

    /**
     * Add a single skill manually entered by the user.
     */
    public function addSkill(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:60',
            'proficiency' => 'nullable|in:beginner,intermediate,advanced,expert',
        ]);

        $user = $request->user();

        // Prevent exact duplicate skill names (case-insensitive)
        $exists = $user->skills()
                       ->whereRaw('LOWER(name) = ?', [strtolower($request->name)])
                       ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'This skill already exists in your profile.',
                'errors'  => ['name' => ['Skill already exists.']],
            ], 422);
        }

        $skill = $user->skills()->create([
            'name'        => trim($request->name),
            'proficiency' => $request->proficiency ?? 'intermediate',
        ]);

        return response()->json([
            'message' => 'Skill added.',
            'skill'   => $skill,
        ], 201);
    }

    /**
     * Remove a skill from the authenticated user's profile.
     */
    public function removeSkill(Request $request, int $skillId)
    {
        $user  = $request->user();
        $skill = $user->skills()->findOrFail($skillId);
        $skill->delete();

        return response()->json(['message' => 'Skill removed.']);
    }
}
