<?php

namespace App\Models;

use App\Services\Helpers;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use  HasApiTokens, HasFactory, Notifiable;

    public static $NAME = 'Piz Cmr';
    public static $EMAIL = 'pizcmr@gmail.com';
    public static $PHONE = "08188531726";
    public static $PASSWORD = 'password';
    public static $BVN = 23980497731;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected static $guard = 'api';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    /**
     * @return BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class, "role_id");
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    protected $relationships = [
        "accounts",
        "role"
    ];

    public function getId(): int
    {
        return $this->attributes['id'];
    }

    public function getName(): string
    {
        return $this->attributes['name'];
    }

    public function getEmail(): string
    {
        return $this->attributes['email'];
    }

    /**
     * @param string $email
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public static function getUserByEmail(string $email)
    {
        return self::with((new self())->relationships)->where('email', $email)->first();
    }

    /**check if a user with the username exist
     * @param int $BVN
     * @return mixed
     */
    public static function getUserByBVN(int $BVN)
    {
        return self::with((new self())->relationships)->where('bvn', $BVN)->first();
    }

    /**check if exist
     * @param string $column
     * @param $value
     * @return mixed
     */
    public static function checkIfNumberExist(string $column, $value)
    {
        return self::with((new self())->relationships)->where($column, $value)->first();
    }

    /**check if exist
     * @param string $column
     * @param $value
     * @return mixed
     */
    public static function getRecordByColumnAndValue(string $column, $value)
    {
        return self::with((new self())->relationships)->where($column, $value)->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function fetchAllCustomer()
    {
        return self::with($this->relationships)
            ->orderByDesc('id')
            ->get();
    }

    /**
     * This is initializes a default user
     * @throws \Exception
     */
    public static function initUser()
    {
        if(!self::getUserByEmail(self::$EMAIL))
        {
            $User = new self();
            $User->name = ucwords(self::$NAME);
            $User->email = strtolower(self::$EMAIL);
            $User->phone = self::$PHONE;
            $User->bvn = self::$BVN;
            $User->password = Hash::make(self::$PASSWORD);
            $User->role_id = Role::getRolesByName(Role::$CUSTOMER)->id;
            $User->is_active = 1;
            $User->save();

            $account = Account::CreateAccountForCustomer($User->id);

            Account::sendCustomerCredentials($account, self::$PASSWORD);
        }
    }

    /**
     * @param array $fields
     * @param string $password
     * @return User
     */
    public static function CreateNewAdminAccount(array $fields, string $password): User
    {
        $User = new self();
        $User->name = ucwords($fields["name"]);
        $User->email = strtolower($fields["email"]);
        $User->password = Hash::make($password);
        $User->phone = $fields["phone"];
        $User->bvn = $fields["bvn"];
        $User->role_id = Role::getRolesByName(Role::$CUSTOMER)->id;
        $User->is_active = 1;
        $User->save();

        return $User;
    }
}
