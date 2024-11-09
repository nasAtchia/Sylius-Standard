<?php

declare(strict_types=1);

namespace App\Factory;

use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;

/**
 * Override the `CartItemFactory` class to set the default order item quantity to 10.
 *
 * @see \Sylius\Component\Core\Factory\CartItemFactory
 *
 * @template T of OrderItemInterface
 *
 * @implements CartItemFactoryInterface<T>
 */
final readonly class CartItemFactory implements CartItemFactoryInterface
{
    private const DEFAULT_QUANTITY = 10;

    /** @param CartItemFactoryInterface<T> $decoratedFactory */
    public function __construct(
        private CartItemFactoryInterface $decoratedFactory,
        private OrderItemQuantityModifierInterface $orderItemQuantityModifier,
    ) {
    }

    public function createNew(): OrderItemInterface
    {
        return $this->decoratedFactory->createNew();
    }

    public function createForProduct(ProductInterface $product): OrderItemInterface
    {
        $cartItem = $this->decoratedFactory->createForProduct($product);

        $this->orderItemQuantityModifier->modify($cartItem, self::DEFAULT_QUANTITY);

        return $cartItem;
    }

    public function createForCart(OrderInterface $order): OrderItemInterface
    {
        return $this->decoratedFactory->createForCart($order);
    }
}
