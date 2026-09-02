<?php

namespace App\Http\Controllers\Auth;

use App\Auth\CustomerOrders;
use App\Auth\Models\MagicLinkToken;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class VerifyMagicLinkController extends Controller
{
    public function __invoke(Request $request, CustomerOrders $orders): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
        ]);

        $record = MagicLinkToken::query()
            ->where('token', $request->string('token'))
            ->where('email', strtolower((string) $request->string('email')))
            ->first();

        if (! $record || ! $record->isValid()) {
            return redirect()->route('login')->with('error', 'That login link is invalid or has expired.');
        }

        $record->consume();

        $user = User::query()->firstOrCreate(
            ['email' => $record->email],
            [
                'name' => Str::before($record->email, '@'),
                'password' => Str::password(32),
                'email_verified_at' => now(),
            ],
        );

        if ($user->email_verified_at === null) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        Auth::login($user, true);
        $orders->linkCustomer($user);

        return redirect()->intended($user->isStaff() ? '/admin' : '/account');
    }
}
