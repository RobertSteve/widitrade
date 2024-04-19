<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\ProductDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductDetail>
 *
 * @method ProductDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductDetail[]    findAll()
 * @method ProductDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductDetail::class);
    }

    public function createDetailByApi(Product $product, string $description): ProductDetail
    {
        $detail = new ProductDetail();
        $detail->setProduct($product);
        $detail->setDescription($description);
        $this->getEntityManager()->persist($detail);
        return $detail;
    }
}
