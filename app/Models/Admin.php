<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    public static $NAME = 'Collins Benson';
    public static $EMAIL = 'fake@bank.com';
    public static $PHONE = '08188531726';
    public static $PASSWORD = 'password';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * This is the authentication guard to be used on this Model
     * This overrides the default guard which is the user guard
     * @var string
     */
    protected static $guard = 'admin';

    /**
     * This forces the auth guard to use the drivers table for authentication
     * @var string
     */
    protected $table = 'admins';

    protected $relationships = [
        "role"
    ];

    /**check if a user with the username exist
     * @param string $email
     * @return mixed
     */
    public static function getUserByEmail(string $email)
    {
        return self::where('email', $email)->first();
    }

    /**
     * @return BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class, "role_id");
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
     * This is initializes a default user
     */
    public static function initAdmin()
    {
        if(!self::getUserByEmail(self::$EMAIL))
        {
            $Admin = new self();
            $Admin->name = ucwords(self::$NAME);
            $Admin->email = strtolower(self::$EMAIL);
            $Admin->password = Hash::make(self::$PASSWORD);
            $Admin->phone = self::$PHONE;
            $Admin->role_id = Role::getRolesByName(Role::$SUPER_ADMIN)->id;
            $Admin->is_active = 1;
            $Admin->save();
        }
    }

    /**
     * @param array $fields
     * @param string $password
     * @return Admin
     */
    public static function CreateNewAdminAccount(array $fields, string $password): Admin
    {

        $Admin = new self();
        $Admin->name = ucwords($fields["name"]);
        $Admin->email = strtolower($fields["email"]);
        $Admin->password = Hash::make($password);
        $Admin->phone = $fields["phone"];
        $Admin->role_id = (int)$fields["role_id"];
        $Admin->is_active = 1;
        $Admin->save();

        return $Admin;
    }
}
