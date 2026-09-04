<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    protected function authUser(): User
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless($user, 403);

        return $user;
    }
}
