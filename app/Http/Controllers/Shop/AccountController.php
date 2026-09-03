<?php

namespace App\Http\Controllers\Shop;

use App\Auth\CustomerOrders;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __invoke(Request $request, CustomerOrders $orders): View
    {
        $user = $request->user();
        abort_unless($user instanceof User, 403);

        $orders->linkCustomer($user);

        return view('shop.account', [
            'user' => $user,
            'orders' => $orders->for($user),
        ]);
    }
}
