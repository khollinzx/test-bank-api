<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ExampleTest extends TestCase
{

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_welcome()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get('api/v1/welcome');
        $response->assertStatus(200);
    }

    public function test_login_admin()
    {
        $fields = [
            "email" => "fake@bank.com",
            "password" => "password"
            ];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('api/v1/onboard/auth/AdminLogin', $fields);
        $response->assertStatus(200);

        $token = $response["data"]["access_token"];

        //creating a new account for customer
        $customerDetail = [
            "name" => "Collins Benson",
            "email" => "collinsbenson0039@gmail.com",
            "phone" => "12345678901",
            "bvn" => "1212121212"
        ];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => "Bearer $token",
        ])->post('api/v1/admins/create?guard=admin', $customerDetail);
        $response->assertStatus(200);

        //creating sub-account for an existing customer
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => "Bearer $token",
        ])->post('api/v1/admins/subCreate?guard=admin', ["bvn" => "1212121212"]);
        $response->assertStatus(200);

        //get all customers
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => "Bearer $token",
        ])->get('api/v1/admins/pull/all?guard=admin');
        $response->assertStatus(200);
    }

    public function test_login_customer()
    {
        $fields = [
            "email" => "pizcmr@gmail.com",
            "password" => "password"
        ];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('api/v1/onboard/auth/customerLogin', $fields);
        $response->assertStatus(200);
        $token = $response["data"]["access_token"];
        $authUserId = $response["data"]["profile"]["id"];

        //get all accounts
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => "Bearer $token",
        ])->get('api/v1/customers/pull/all?guard=customer');
        $response->assertStatus(200);

        //transfer money to another customer's account
        $senderAccount = Account::getRecordByAccountNumber('user_id', $authUserId);
        $receiver = User::getUserByBVN("1212121212");
        $receiverAccount = Account::getRecordByAccountNumber('user_id', $receiver->id);
        $details = [
            "transferred_account_no" => $senderAccount->account_no,
            "receiver_account_no" => $receiverAccount->account_no,
            "amount" => 300
        ];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => "Bearer $token",
        ])->post('api/v1/customers/transfer?guard=customer', $details);
        $response->assertStatus(200);

        //Retrieve account histories
        $senderAccount = Account::getRecordByAccountNumber('user_id', $authUserId);
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => "Bearer $token",
        ])->get("api/v1/customers/$senderAccount->id/histories?guard=customer", $details);
        $response->assertStatus(200);
    }
}
