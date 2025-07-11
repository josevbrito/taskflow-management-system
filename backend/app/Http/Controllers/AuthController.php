<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;
use PragmaRX\Google2FAQRCode\Google2FA as Google2FAQRCode;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Usuário registrado com sucesso! Por favor, faça login.'
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        if ($user->is_2fa_enabled && !$request->has('one_time_password')) {
            return response()->json([
                'message' => '2FA required',
                'needs_2fa' => true,
                'user' => $user->only(['id', 'email', 'name'])
            ], 200);
        }

        if ($user->is_2fa_enabled && $request->has('one_time_password')) {
            $google2fa = app(Google2FA::class);
            if (!$google2fa->verifyKey($user->google2fa_secret, $request->one_time_password)) {
                throw ValidationException::withMessages([
                    'one_time_password' => ['Código 2FA inválido.'],
                ]);
            }
            $user->google2fa_verified_at = now();
            $user->save();
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully!',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully!'
        ], 200);
    }

    /**
     * Habilita 2FA para o usuário autenticado e retorna o QR Code.
     */
    public function enable2FA(Request $request)
    {
        $user = $request->user();
        $google2fa = app(Google2FA::class);

        if (empty($user->google2fa_secret)) {
            $user->google2fa_secret = $google2fa->generateSecretKey();
            $user->is_2fa_enabled = 1;
            $user->save();
        }

        $google2faQrcode = new Google2FAQRCode();

        $otpAuthUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $user->google2fa_secret
        );

        $qrCodeImageData = $google2faQrcode->getQrCodeInline(
            config('app.name'),
            $user->email,
            $user->google2fa_secret
        );

        return response()->json([
            'message' => '2FA secret generated. Scan QR code to activate.',
            'qr_code_url' => $qrCodeImageData,
            'secret' => $user->google2fa_secret,
            'otp_auth_url' => $otpAuthUrl
        ]);
    }

    /**
     * Desabilita 2FA para o usuário autenticado.
     */
    public function disable2FA(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = $request->user();

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Senha incorreta.'], 401);
        }

        $user->is_2fa_enabled = 0;
        $user->google2fa_secret = null;
        $user->google2fa_verified_at = null;
        $user->save();

        return response()->json([
            'message' => '2FA desabilitado com sucesso.'
        ]);
    }

    /**
     * Verifica o código 2FA fornecido.
     */
    public function verify2FA(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'one_time_password' => 'required|string|digits:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->is_2fa_enabled) {
            return response()->json(['message' => '2FA não habilitado ou usuário não encontrado.'], 400);
        }

        $google2fa = app(Google2FA::class);
        if (!$google2fa->verifyKey($user->google2fa_secret, $request->one_time_password)) {
            return response()->json(['message' => 'Código 2FA inválido.'], 401);
        }

        $user->google2fa_verified_at = now();
        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => '2FA verificado com sucesso!',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Retorna o usuário autenticado.
     */
    public function getAuthenticatedUser(Request $request)
    {
        return response()->json(['user' => $request->user()]);
    }
}
