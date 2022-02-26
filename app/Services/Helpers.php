<?php


namespace App\Services;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Class Helper
 * @package App\Services
 */
class Helpers
{

    /**
     * @param Model $model
     * @param string $column
     * @param string $value
     * @return mixed
     */
    public static function findByUserAndColumn(
        Model $model,
        string $column,
        string $value
    ) {
        return $model::findByUserAndColumn($column, $value);
    }

    /**
     * @param Model $model
     * @param string $column
     * @param string $value
     * @return mixed
     */
    public static function checkIfUserExist(
        Model $model,
        string $email,
        int $id
    ) {
        return $model::checkIfUserExist($email, $id);
    }

    /**
     * This generates an otp
     * @return int
     * @throws \Exception
     */
    public static function generateOTP(): int
    {
        return random_int(1000, 9999) . time();
    }

    /**
     * This generates a ticket number
     * @return int
     * @throws \Exception
     */
    public static function generateAccountNo()
    {
        return rand(10000, 90000) . rand(11111, 99999);
    }

    /**
     * @param int $length
     * @return string
     */
    public static function generateRandomStrings(int $length): string
    {
        // String of all alphanumeric character
        $result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        // Shuffle the $str_result and returns substring
        // of specified length
        return substr(str_shuffle($result), 0, $length);
    }

    /**
     * This saves a model records
     * @param Model $model
     * @param array $records
     * @return Model
     */
    public static function saveModelRecord(Model $model, array $records = []): Model
    {
        if(count($records))
        {
            foreach ($records as $k => $v)
                $model->$k = $v;

            $model->save();
        }

        return $model;
    }
}
