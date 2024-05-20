<?php

namespace App\Http\Controllers;


use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Support\Exceptions\OAuthException;
use App\Support\Traits\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    use Authenticatable;

    /**
     * Get a JWT via given credentials.
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
{
    // Attempt to authenticate the user
    if (!$token = Auth::attempt($request->only('email', 'password'))) {
        return response()->json([
            'error' => 'Kredensial ini tidak cocok dengan data kami.',
            'code' => 401 // Unauthorized
        ], 401);
    }

    // Return a success response with the token
    $response = [
        'success' => true,
        'data' => [
            'user' => auth()->user(),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]
    ];
    return response()->json($response, 200); // OK
}

public function register(RegisterRequest $request): JsonResponse
{
    try {
        // Validate user input
        $validatedData = $request->validated();

        // Check if email already exists
        if (User::where('email', $validatedData['email'])->exists()) {
            throw new HttpResponseException(response()->json([
                'error' => 'Email already registered'
            ], 400));
        }

        // Create a new user instance
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']), 
            'role' => 'user', // Assuming role is predefined
        ]);

        // Attempt to login the user to generate a JWT token
        if (!$token = Auth::attempt(['email' => $validatedData['email'], 'password' => $request->password])) {
            return response()->json([
                'error' => 'Kredensial ini tidak cocok dengan data kami.',
                'code' => 401 // Unauthorized
            ], 401);
        }

        // Return a success response with the token
        return response()->json([
            'success' => true,
            'message' => 'Akun telah berhasil registrasi, silahkan login !',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
            ]
        ], 201); // Created
    } catch (\Exception $e) {
        // Return an error response if an exception occurs
        return response()->json([
            'success' => false,
            'message' => 'Gagal mendaftarkan akun. Terjadi kesalahan internal.',
            'error' => $e->getMessage()
        ], 500); // Internal Server Error
    }
}

    /**
     * Refresh a token.
     *
     * @return \App\Modules\Auth\Collections\TokenResource
     */
    public function refresh(): JsonResponse
    {
        return $this->responseWithToken(access_token: auth()->refresh());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->logout();

        return new JsonResponse(['sucess' => true]);
    }
}
