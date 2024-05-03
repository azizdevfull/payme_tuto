<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PaymeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $authorization = $request->header('Authorization');
        if (!$authorization || !preg_match('/^\s*Basic\s+(\S+)\s*$/i', $authorization, $matches)) {
            return response()->json([
                'jsonrpc' => '2.0',
                'error' => [
                    'code' => -32504,
                    'message' => [
                        "uz" => "Avtorizatsiyadan otishda xatolik",
                        "ru" => "Ошибка аутентификации",
                        "en" => "Auth error"
                    ]
                ]
            ]);
        }

        $decodedCredentials = base64_decode($matches[1]);

        list($username, $password) = explode(':', $decodedCredentials);

        $expectedUsername = "Paycom";
        $expectedPassword = "VrumKE@#8NRTC&pts0q%TCqNBu7?@IbivQuO";

        if ($username !== $expectedUsername || $password !== $expectedPassword) {
            return response()->json([
                'jsonrpc' => '2.0',
                'error' => [
                    'code' => -32504,
                    'message' => [
                        "uz" => "Avtorizatsiyadan otishda xatolik",
                        "ru" => "Ошибка аутентификации",
                        "en" => "Auth error"
                    ]
                ]
            ]);
        }

        return $next($request);
    }
}
