<?php

declare(strict_types=1);

namespace App\Tests\Functional\Shop;

use App\Entity\Locale\Locale;
use App\Entity\Product\Product;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ProductControllerTest extends WebTestCase
{
    public function testProductPageDisplaysQuantityInputFieldWithCorrectDefaultValue(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();

        /** @var UrlGeneratorInterface $router */
        $router = $container->get('router');

        /** @var EntityRepository $localeRepository */
        $localeRepository = $container->get('sylius.repository.locale');
        /** @var Locale $locale */
        $locale = $localeRepository->findOneBy([]);

        /** @var ProductRepositoryInterface<ProductInterface> $productRepository */
        $productRepository = $container->get('sylius.repository.product');
        /** @var Product $product */
        $product = $productRepository->findOneBy([]);
        $productUri = $router->generate('sylius_shop_product_show', [
            '_locale' => $locale->getCode(),
            'slug' => $product->getSlug(),
        ]);

        $crawler = $client->request('GET', $productUri);

        $this->assertResponseIsSuccessful();

        // Look for the quantity input field
        $quantityInputField = $crawler->filter('input[name="sylius_shop_add_to_cart[cartItem][quantity]"]');
        $this->assertSame(1, $quantityInputField->count());
        $this->assertEquals(10, $quantityInputField->attr('value'));
        $this->assertEquals(10, $quantityInputField->attr('min'));
        $this->assertEquals(10, $quantityInputField->attr('step'));
    }
}
