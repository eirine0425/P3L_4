<?php 

namespace App\UseCases\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\UserRepositoryInterface;

class AuthUseCase
{
    public function __construct(
        protected UserRepositoryInterface $userRepo
    ) {}

    public function register(Request $request): array
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:6',
            'dob'           => 'required|date',
            'phone_number'  => 'nullable|string|max:20',
            'role_id'       => 'required|integer|exists:roles,role_id', // sesuaikan nama tabel dan kolom PK di roles
        ]);

        // Hash password sebelum simpan
        $validated['password'] = bcrypt($validated['password']);

        $user = $this->userRepo->create($validated);

        // Generate token dengan Laravel Passport atau Sanctum (pastikan sudah setup)
        $token = $user->createToken('auth_token')->accessToken;

        return [
            'token' => $token,
            'user'  => $user,
        ];
    }

    public function login(Request $request): array
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            abort(401, 'Unauthorized');
        }

        $user = $this->userRepo->getUserbyEmail($request->email);
        $token = $user->createToken('auth_token')->accessToken;

        return [
            'token' => $token,
            'user'  => $user,
        ];
    }


    public function me(Request $request)
    {
        return $this->userRepo->find($request->user()->id);
    }

    public function logout(Request $request): array
    {
        $request->user()->token()->revoke();

        return ['message' => 'Logged out'];
    }
}
