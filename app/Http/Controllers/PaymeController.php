<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymeController extends Controller
{
    public function index(Request $req)
    {
        if ($req->method == "CheckPerformTransaction") {
            if (empty($req->params['account'])) {
                $response = [
                    'id' => $req->id,
                    'error' => [
                        'code' => -32504,
                        'message' => "Недостаточно привилегий для выполнения метода"
                    ]
                ];
                return json_encode($response);
            } else {
                $a = $req->params['account'];
                $t = Order::where('id', $a['order_id'])->first();
                if (empty($t)) {
                    $response = [
                        'id' => $req->id,
                        'error' => [
                            'code' => -31050,
                            'message' => [
                                "uz" => "Buyurtma topilmadi",
                                "ru" => "Заказ не найден",
                                "en" => "Order not found"
                            ]
                        ]
                    ];
                    return json_encode($response);
                } else if ($t->price != $req->params['amount']) {
                    $response = [
                        'id' => $req->id,
                        'error' => [
                            'code' => -31001,
                            'message' => [
                                "uz" => "Notogri summa",
                                "ru" => "Неверная сумма",
                                "en" => "Incorrect amount"
                            ]
                        ]
                    ];
                    return json_encode($response);
                }
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
