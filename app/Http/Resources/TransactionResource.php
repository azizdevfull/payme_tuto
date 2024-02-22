<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->transaction,
            'time' => $this->payme_time,
            'amount' => $this->amount,
            'account' => [
                'user_id' => $this->owner_id,
            ],
            'create_time' => intval($this->payme_time),
            'perform_time' => intval($this->perform_time),
            'cancel_time' => intval($this->cancel_time) ?? 0,
            'transaction' => $this->id,
            'state' => $this->state,
            'reason' => $this->reason
        ];;
    }
}
