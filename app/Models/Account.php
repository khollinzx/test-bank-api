<?php

namespace App\Models;

use App\Services\EmailService;
use App\Services\Helper;
use App\Services\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $relationships = [
        "transfers",
        "user",
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function transfers(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(TransferHistory::class, 'transferable');
    }

    public function getClient(): User
    {
        return $this->user;
    }

    /**check if exist
     * @param int $userId
     * @param string $column
     * @param $value
     * @return mixed
     */
    public static function getRecordByColumnAndValue(int $userId, string $column, $value)
    {
        return self::with((new self())->relationships)
            ->where($column, $value)
            ->where("user_id", $userId)
            ->first();
    }

    /**check if exist
     * @param User $userId
     * @param string $column
     * @param $value
     * @return mixed
     */
    public static function getRecordByAccountNumber(string $column, $value)
    {
        return self::with((new self())->relationships)
            ->where($column, $value)
            ->first();
    }

    /**
     * @param Account $Account
     * @param float $amount
     * @return mixed
     */
    public static function creditReceiver(Account $Account, float $amount)
    {
            $credit = ($Account->amount + $amount);
            Helpers::saveModelRecord($Account, ["amount" => $credit]);

    }

/**
 * @param Account $Account
 * @param float $amount
 * @return mixed
 */
    public static function debitSender(Account $Account, float $amount)
    {
        $debit = ($Account->amount - $amount);
        Helpers::saveModelRecord($Account, ["amount" => $debit]);
        TransferHistory::setTransferHistory($Account->user_id, $amount, $Account);
    }

    /**
     * @param int $customerId
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function fetchAllAccounts(int $customerId)
    {
        return self::with($this->relationships)
            ->where('user_id', $customerId)
            ->orderByDesc('id')
            ->get();
    }

    /**
     * @param int $user_id
     * @param array $fields
     * @param float $amount
     * @return Account
     */
    public static function CreateAccountForCustomer(int $user_id, float $amount = 2000): Account
    {
        $Account = new self();
        $Account->user_id = $user_id;
        $Account->account_no = Helpers::generateAccountNo();
        $Account->amount = $amount;
        $Account->save();

        return $Account;
    }

    /**
     * @param int $userId
     * @return mixed
     */
    public function getAccountByUserId(int $userId)
    {
        return self::with($this->relationships)->where('user_id', $userId)->get();
    }

    /**check if a user with the username exist
     * @param int $accountId
     * @return mixed
     */
    public function getAccountById(int $accountId)
    {
        return self::with($this->relationships)->where('id', $accountId)->first();
    }

    /** send a client is request ticket no
     * @param Account $data
     * @param bool $withPassword
     * @param string $password
     */
    public static function sendCustomerCredentials(Account $data, bool $withPassword, string $password ="")
    {
        /**
         * Send Client request ticket no.
         */
        $config = [
            'sender_email' => "collinsbenson0039@gmail.com",
            'sender_name' => "Collins",
            'recipient_email' => $data->getClient()->getEmail(),
            'recipient_name' => ucwords($data->getClient()->getName()),
            'subject' => 'Fake Bank Account Creation!',
        ];

        $name = explode(" ", $data->getClient()->getName());
        $d = [
            'account' => $data->account_no,
            'name' => $name[0],
            'email' => $data->getClient()->getEmail(),
            'password' => $password
        ];

        $template = $withPassword ? "send_customer_details":"send_sub_account_details";

        /**
         * Dispatch the Email too Drive
         */
        (new EmailService())->getProvider()->sendMail($config, "emails.$template", $d);

    }
}
