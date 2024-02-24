<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public $minAmount = 1;
    public $maxAmount = 9_999_999_99;
    protected int $timeout = 6000 * 1000;
    public function index(Request $req)
    {
        if ($req->method == "CheckPerformTransaction") {
            if (empty($req->params['account']) && empty($req->params['amount'])) {
                $response = [

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

                    'error' => [
                        'code' => -31001,
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
        } else if ($req->method == "CreateTransaction") {
            if (empty($req->params['account']) && empty($req->params['amount']) && empty($req->params['time']) && empty($req->params['account']['user_id'])) {
                $response = [

                    'error' => [
                        'code' => -32504,
                        'message' => "Недостаточно привилегий для выполнения метода"
                    ]
                ];
                return json_encode($response);
            }
            $id = $req->params['id'];
            $time = $req->params['time'];
            $amount = $req->params['amount'];
            $account = $req->params['account'];
            if (!array_key_exists('user_id', $account)) {
                $response = [

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

                    'error' => [
                        'code' => -31050,
                        "uz" => "Foydalanuvchi topilmadi",
                        "ru" => "Пользователь не найден",
                        "en" => "User not found",
                    ]
                ];
                return $response;
            }

            if ($amount < $this->minAmount || $amount > $this->maxAmount) {
                $response = [

                    'error' => [
                        'code' => -31001,
                        "uz" => "Notug'ri summa.",
                        "ru" => "Неверная сумма.",
                        "en" => "Wrong amount.",
                    ]
                ];
                return $response;
            }
            $transaction = Transaction::where('transaction', $id)->first();
            Log::info($transaction);
            if ($transaction) {
                if ($transaction->state != 1) {
                    $response = [

                        'error' => [
                            'code' => -31001,
                            "uz" => "Bu operatsiyani bajarish mumkin emas",
                            "ru" => "Невозможно выполнить данную операцию.",
                            "en" => "Can't perform transaction",
                        ]
                    ];
                    return $response;
                }
                if ($transaction->state == 1) {
                    $response = [
                        "result" => [
                            'create_time' => intval($transaction->create_time),
                            'perform_time' => 0,
                            'cancel_time' => 0,
                            'transaction' => strval($transaction->id),
                            'state' => intval($transaction->state),
                            'reason' => null
                        ]
                    ];
                    return $response;
                }

                if (!$this->checkTimeout($transaction->create_time)) {
                    $transaction->update([
                        'state' => -1,
                        'reason' => 4
                    ]);

                    $response = [

                        'error' => [
                            'code' => -31008,
                            "uz" => "Vaqt tugashi o'tdi",
                            "ru" => "Тайм-аут прошел",
                            "en" => "Timeout passed"
                        ]
                    ];
                    return $response;
                }

                $response = [
                    "result" => [
                        'create_time' => $transaction->create_time,
                        'perform_time' => 0,
                        'cancel_time' => 0,
                        'transaction' => strval($transaction->id),
                        'state' => $transaction->state,
                        'reason' => null
                    ]
                ];
                return $response;
            }


            $transaction = Transaction::create([
                'transaction' => $id,
                'payme_time' => $time,
                'amount' => $amount,
                'state' => 1,
                'create_time' => $this->microtime(),
                'owner_id' => $account['user_id'],
            ]);

            $response = [
                "result" => [
                    'create_time' => $transaction->create_time,
                    'perform_time' => 0,
                    'cancel_time' => 0,
                    'transaction' => strval($transaction->id),
                    'state' => $transaction->state,
                    'reason' => null
                ]
            ];
            return $response;
        } else if ($req->method == "CheckTransaction") {
            if (empty($req->params['id'])) {
                $response = [
                    'error' => [
                        'code' => -32504,
                        'message' => "Недостаточно привилегий для выполнения метода"
                    ]
                ];
                return json_encode($response);
            }
            $id = $req->params['id'];
            $transaction = Transaction::where('transaction', $id)->first();
            if ($transaction) {
                if ($transaction->reason) {
                    $tr = intval($transaction->reason);
                } else {
                    $tr = null;
                }
                $response = [
                    "result" => [
                        "create_time" => intval($transaction->create_time) ?? 0,
                        "perform_time" => intval($transaction->perform_time) ?? 0,
                        "cancel_time" => intval($transaction->cancel_time) ?? 0,
                        "transaction" => strval($transaction->id),
                        "state" => intval($transaction->state),
                        "reason" => $tr
                    ]
                ];
                return json_encode($response);
            } else {
                $response = [
                    'error' => [
                        'message' => [
                            'code' => -31003,
                            "uz" => "Transaksiya topilmadi",
                            "ru" => "Трансакция не найдена",
                            "en" => "Transaction not found"
                        ]
                    ]
                ];
                return json_encode($response);
            }
        } else if ($req->method == "PerformTransaction") {
            if (empty($req->params['id'])) {
                $response = [
                    'error' => [
                        'code' => -32504,
                        'message' => "Недостаточно привилегий для выполнения метода"
                    ]
                ];
                return json_encode($response);
            }
            $id = $req->params['id'];
            $transaction = Transaction::where('transaction', $id)->first();
            if (!$transaction) {
                $response = [
                    'error' => [
                        'code' => -31003,
                        'message' => [
                            "uz" => "Transaksiya topilmadi",
                            "ru" => "Трансакция не найдена",
                            "en" => "Transaction not found"
                        ]
                    ]
                ];
                return json_encode($response);
            }
            // Log::info($transaction->state);
            if ($transaction->state != 1) {
                if ($transaction->state == 2) {
                    $response = [
                        'result' => [
                            "state" => intval($transaction->state),
                            "perform_time" => intval($transaction->perform_time),
                            "transaction" => strval($transaction->id),
                        ]
                    ];
                    return json_encode($response);
                } else {
                    $response = [
                        'error' => [
                            'code' => -31008,
                            "message" => [
                                "uz" => "Bu operatsiyani bajarish mumkin emas",
                                "ru" => "Невозможно выполнить данную операцию.",
                                "en" => "Can't perform transaction",
                            ]
                        ]
                    ];
                    return $response;
                }
            }

            if (!$this->checkTimeout($transaction->create_time)) {
                $transaction->update([
                    'state' => -1,
                    'reason' => 4
                ]);

                $response = [

                    'error' => [
                        'code' => -31008,
                        "message" => [
                            "uz" => "Vaqt tugashi o'tdi",
                            "ru" => "Тайм-аут прошел",
                            "en" => "Timeout passed"
                        ]
                    ]
                ];
                return $response;
            }

            $transaction->state = 2;
            $transaction->perform_time = $this->microtime();
            $transaction->save();

            $user = User::where('id', $transaction->owner_id)->first();
            $user->money += $transaction->amount;
            $user->save();

            $response = [
                'result' => [
                    "state" => $transaction->state,
                    "perform_time" => $transaction->perform_time,
                    "transaction" => strval($transaction->id),
                ]
            ];
            return json_encode($response);
        }
    }



    protected function microtime(): int
    {
        return (time() * 1000);
    }
    private function checkTimeout($created_time)
    {
        return   $this->microtime() <= ($created_time + $this->timeout);
    }
}
