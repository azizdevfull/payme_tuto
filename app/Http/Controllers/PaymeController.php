<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            $t = Order::where('id', $a['order_id'])->where('price', $req->params['amount'])->first();
            $response = [

                'result' => [
                    'allow' => true,
                ]


            ];
            return json_encode($response);
        } else if ($req->method == "CreateTransaction") {
            if (empty($req->params['account'])) {
                $response = [
                    'id' => $req->id,
                    'error' => [
                        'code' => -32504,
                        'message' => "Bajarish usuli uchun imtiyozlar etarli emas."
                    ]
                ];
                return json_encode($response);
            } else {
                $account = $req->params['account'];
                $order = Order::where('id', $account['order_id'])->first();
                $order_id = $req->params['account']['order_id'];
                $transaction = Transaction::where('order_id', $order_id)->where('state', 1)->get();

                if (empty($order)) {
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
                } else if ($order->price != $req->params['amount']) {
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
                } elseif (count($transaction) == 0) {

                    $transaction = new Transaction();
                    $transaction->paycom_transaction_id = $req->params['id'];
                    $transaction->paycom_time = $req->params['time'];
                    $transaction->paycom_time_datetime = now();
                    $transaction->amount = $req->params['amount'];
                    $transaction->state = 1;
                    $transaction->order_id = $account['order_id'];
                    $transaction->save();

                    return response()->json([
                        "result" => [
                            'create_time' => $req->params['time'],
                            'transaction' => strval($transaction->id),
                            'state' => $transaction->state
                        ]
                    ]);
                } elseif ((count($transaction) == 1) and ($transaction->first()->paycom_time == $req->params['time']) and ($transaction->first()->paycom_transaction_id == $req->params['id'])) {
                    $response = [
                        'result' => [
                            "create_time" => $req->params['time'],
                            "transaction" => "{$transaction[0]->id}",
                            "state" => intval($transaction[0]->state)
                        ]
                    ];

                    return json_encode($response);
                } else {
                    $response = [
                        'id' => $req->id,
                        'error' => [
                            'code' => -31099,
                            'message' => [
                                "uz" => "Buyurtma tolovi hozirda amalga oshrilmoqda",
                                "ru" => "Оплата заказа в данный момент обрабатывается",
                                "en" => "Order payment is currently being processed"
                            ]
                        ]
                    ];
                    return json_encode($response);
                }
            }
        } else if ($req->method == "CheckTransaction") {
            // $ldate = date('Y-m-d H:i:s');
            $transaction = Transaction::where('paycom_transaction_id', $req->params['id'])->first();
            // Log::info($transaction);
            if (empty($transaction)) {
                $response = [
                    'id' => $req->id,
                    'error' => [
                        'code' => -31003,
                        'message' => "Транзакция не найдена."
                    ]
                ];
                return json_encode($response);
            } else if ($transaction->state == 1) {
                Log::info('Test');
                $response = [
                    "result" => [
                        'create_time' => intval($transaction->paycom_time),
                        'perform_time' => intval($transaction->perform_time_unix),
                        'cancel_time' => 0,
                        'transaction' => strval($transaction->id),
                        "state" => $transaction->state,
                        "reason" => $transaction->reason
                    ]
                ];
                return json_encode($response);
            }
        }
    }
}
