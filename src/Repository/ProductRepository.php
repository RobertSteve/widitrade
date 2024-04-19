<?php

namespace App\Repository;

use App\Entity\Business;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function ratingOrdered(): array
    {
        return $this->createQueryBuilder('product')
            ->orderBy('product.rating', 'desc')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function createByApi(array $item, Business $business): Product
    {
        $code = $item['ASIN'];
        $product = new Product();
        $product->setBusiness($business);
        $product->setCode($code);
        list($rating, $label) = $this->getRatingAndLabel($code);
        $product->setRating($rating);
        $product->setLabel($label);
        $ratingDescription = $this->getRatingDescription($rating);
        $product->setRatingDescription($ratingDescription);
        $product->setTitle($item['ItemInfo']['Title']['DisplayValue']);
        $product->setDiscount($item['Offers']['Listings'][0]['Price']['Savings']['Percentage']);
        $product->setFreeShipping($item['Offers']['Listings'][0]['DeliveryInfo']['IsFreeShippingEligible']);
        $product->setLink($item['DetailPageURL']);
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();
        return $product;
    }

    private function getRatingAndLabel(string $code): array
    {
        return match ($code) {
            'B0CL6ZMTCG' => [9.9, 'MEJOR OPCION 2024'],
            'B07YV31M5P' => [9.8, null],
            'B0CD1L5DJ1' => [9.7, 'MEJOR VALOR 2024'],
            'B08HM31MQP' => [9.7, null],
            'B079Z4HS7S' => [9.5, null],
            'B07GYPCN7L' => [9.3, null],
            'B09YRV4S14' => [9.2, null],
            'B0BC1TXTV8' => [9.1, null],
            'B0CCRDYSNS' => [9.1, null],
            'B0BV272MC5' => [9.0, null],
        };
    }

    private function getRatingDescription(float $rating): string
    {
        return match (true) {
            $rating >= 9.8 => 'Excepcional',
            $rating >= 9.3 => 'Excelente',
            $rating >= 9.0 => 'Genial',
            default => 'Regular',
        };
    }
}
