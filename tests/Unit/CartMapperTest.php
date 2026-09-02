<?php

namespace Tests\Unit;

use App\Shopify\Mappers\CartMapper;
use PHPUnit\Framework\TestCase;

class CartMapperTest extends TestCase
{
    public function test_maps_applicable_discount_codes_only(): void
    {
        $cart = (new CartMapper)->map([
            'totalQuantity' => 2,
            'discountCodes' => [
                ['code' => 'SAVE10', 'applicable' => true],
                ['code' => 'DEAD', 'applicable' => false],
            ],
            'cost' => [
                'subtotalAmount' => ['amount' => '20.00', 'currencyCode' => 'GBP'],
                'totalAmount' => ['amount' => '18.00', 'currencyCode' => 'GBP'],
            ],
            'lines' => ['nodes' => []],
        ]);

        $this->assertSame(['SAVE10'], $cart->discountCodes);
        $this->assertSame('GBP 18.00', $cart->total->formatted());
    }
}
