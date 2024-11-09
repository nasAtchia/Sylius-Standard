<?php

declare(strict_types=1);

namespace App\Tests\Integration\Factory;

use App\Entity\Product\Product;
use App\Factory\CartItemFactory;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @coversDefaultClass \App\Factory\CartItemFactory
 */
final class CartItemFactoryTest extends KernelTestCase
{
    public function testCreateProduct(): void
    {
        $container = static::getContainer();

        /** @var ProductRepositoryInterface<ProductInterface> $productRepository */
        $productRepository = $container->get('sylius.repository.product');
        /** @var Product $product */
        $product = $productRepository->findOneBy([]);

        /** @var CartItemFactory<OrderItemInterface> $cartItemFactory */
        $cartItemFactory = $container->get(CartItemFactory::class);

        $cartItem = $cartItemFactory->createForProduct($product);

        $this->assertSame(10, $cartItem->getQuantity());
    }
}
