<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferHistory extends Model
{
    use HasFactory;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function transferable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function histories(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Account::class, "transferable_id");
    }

    /**
     * @param int $admin_id
     * @param int $consultant_id
     * @param Model $model
     */
    public static function setTransferHistory(int $sender_id, float $amount, Model $model){
        $History = new self();
        $History->sender_id = $sender_id;
        $History->amount = $amount;

        $model->transfers()->save($History);
    }
}
