<?php

namespace App\Livewire\Auth;

use App\Auth\Actions\SendMagicLink;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class OtpLogin extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    public bool $sent = false;

    public function submit(SendMagicLink $send): void
    {
        $this->validate();

        $send->handle($this->email, request()->ip());
        $this->sent = true;
    }

    public function render(): View
    {
        return view('livewire.auth.otp-login')
            ->layout('components.layouts.storefront', [
                'title' => 'Sign in',
            ]);
    }
}
