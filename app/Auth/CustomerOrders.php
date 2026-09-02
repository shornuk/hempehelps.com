<?php

namespace App\Auth;

use App\Models\User;
use App\Shopify\AdminClient;
use App\Shopify\Exceptions\MissingCredentialsException;
use Illuminate\Support\Facades\Cache;

class CustomerOrders
{
    public function __construct(private readonly AdminClient $admin) {}

    /**
     * @return list<array<string, mixed>>
     */
    public function for(User $user): array
    {
        if ($user->email_verified_at === null) {
            return [];
        }

        return Cache::remember(
            'orders:'.$user->id,
            config('shopify.cache_ttl.orders'),
            fn (): array => $this->fetch($user),
        );
    }

    public function linkCustomer(User $user): void
    {
        if ($user->shopify_customer_id || $user->email_verified_at === null) {
            return;
        }

        try {
            $data = $this->admin->query($this->admin->document('customers-by-email'), [
                'query' => 'email:'.$user->email,
            ]);
        } catch (MissingCredentialsException) {
            return;
        }

        $id = $data['customers']['nodes'][0]['id'] ?? null;

        if (is_string($id)) {
            $user->forceFill(['shopify_customer_id' => $id])->save();
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function fetch(User $user): array
    {
        try {
            $data = $this->admin->query($this->admin->document('customers-by-email'), [
                'query' => $user->shopify_customer_id
                    ? 'id:'.basename($user->shopify_customer_id)
                    : 'email:'.$user->email,
            ]);
        } catch (MissingCredentialsException) {
            return [];
        }

        $orders = $data['customers']['nodes'][0]['orders']['nodes'] ?? [];

        return is_array($orders) ? $orders : [];
    }
}
