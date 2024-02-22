<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public $minAmount = 1;
    public $maxAmount = 9_999_999_99;
    public function index(Request $req)
    {
        if ($req->method == "CheckPerformTransaction") {
            if (empty($req->params['account']) && empty($req->params['amount'])) {
                $response = [
                    'id' => $req->id,
                    'error' => [
                        'code' => -32504,
                        'message' => "Недостаточно привилегий для выполнения метода"
                    ]
                ];
                return json_encode($response);
            }
            $amount = $req->params['amount'];

            if ($amount < $this->minAmount || $amount > $this->maxAmount) {
                $response = [
                    'id' => $req->id,
                    'code' => -31001,
                    'error' => [
                        "uz" => "Notug'ri summa.",
                        "ru" => "Неверная сумма.",
                        "en" => "Wrong amount.",
                    ]
                ];
                return $response;
            }
            $account = $req->params['account'];
            if (!array_key_exists('user_id', $account)) {
                $response = [
                    'id' => $req->id,
                    'error' => [
                        'code' => -31050,
                        "uz" => "Foydalanuvchi topilmadi",
                        "ru" => "Пользователь не найден",
                        "en" => "User not found",
                    ]
                ];
                return $response;
            }
            $user = User::where('id', $account['user_id'])->first();
            if (!$user) {
                $response = [
                    'id' => $req->id,
                    'error' => [
                        'code' => -31050,
                        "uz" => "Foydalanuvchi topilmadi",
                        "ru" => "Пользователь не найден",
                        "en" => "User not found",
                    ]
                ];
                return $response;
            }
            $response = [
                'result' => [
                    'allow' => true,
                ]
            ];
            return json_encode($response);
        }
    }
}
