<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserAccountTest extends TestCase
{
    use DatabaseTransactions;

    public function test_catalist_user_can_login_with_user_role()
    {
        $user = User::where('email', 'catalist@mitrasbp')->first();
        $this->assertNotNull($user);
        $this->assertEquals('user', strtolower($user->role));
        $this->assertTrue($user->isAdmin());

        $attempt = Auth::attempt([
            'email' => 'catalist@mitrasbp',
            'password' => 'balitengah',
        ]);

        $this->assertTrue($attempt);
    }

    public function test_visitor1_user_can_login_with_visitor_role()
    {
        $user = User::where('email', 'visitor1@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('visitor', strtolower($user->role));
        $this->assertFalse($user->isAdmin());

        $attempt = Auth::attempt([
            'email' => 'visitor1@gmail.com',
            'password' => 'visitor1',
        ]);

        $this->assertTrue($attempt);
    }
}
