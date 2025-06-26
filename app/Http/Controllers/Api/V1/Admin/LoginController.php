<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

final class LoginController extends BaseController
{

    public function __construct()
    {
        $this->middleware('auth:api')->only(['getUser', 'logout']);
    }

    // Set validasi
    public function index(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // get "email" and "password" from request
        $credentials = $request->only('email', 'password');

        // check jika "email" dan "password" valid
        if (!$token = auth()->guard('api')->attempt($credentials)) {

            // response login "failed"
            return response()->json([
                'status' => 'error',
                'message' => 'Login failed, invalid credentials',
            ], 401);
        }

        // response login "success"
        return response()->json([
            'status' => true,
            'message' => 'Login success',
            'data' => [
                'user' => auth()->guard('api')->user(),
                'token' => $token,
            ]
        ], 200);

    }

    /**
     * getUser
     *
     * @return void
     */
    public function getUser(): \Illuminate\Http\JsonResponse
    {
        //response data "user" yang sedang login
        return response()->json([
            'status' => true,
            'user'    => auth()->guard('api')->user()
        ], 200);

    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken(Request $request)
    {
        $token = JWTAuth::getToken();

        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => 'Token not provided or expired',
            ], 401);
        }

        //refresh "token"
        $refreshToken = JWTAuth::refresh(JWTAuth::getToken());

        //set user dengan "token" baru
        $user = JWTAuth::setToken($refreshToken)->toUser();

        //set header "Authorization" dengan type Bearer + "token" baru
        $request->headers->set('Authorization', 'Bearer ' . $refreshToken);

        //response data "user" dengan "token" baru
        return response()->json([
            'status' => true,
            'user'    => $user,
            'token'   => $refreshToken,
        ], 200);
    }

    /**
     * logout
     *
     * @return void
     */
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'status' => true,
                'message' => 'Logout successful',
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to logout, token might be invalid or expired',
            ], 401);
        }
    }
}
