<?php

namespace App\Service;

use App\Dto\ProductDTO;
use App\Entity\Product;
use App\Enum\Mode;
use App\Logger\CsvLogger;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Exception;
use Throwable;

class ProductService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CsvLogger $csvLogger,
        private readonly ProductRepository $productRepository,
    )
    {
    }

    public function processProduct(ProductDTO $denormalizedProduct, Mode $mode): bool
    {
        try {
            $duplicatedCodes = $this->productRepository->findBy(
                ['strProductCode' => $denormalizedProduct->getStrProductCode()]
            );

            if ($duplicatedCodes) {
                throw new Exception('Product code already exists.');
            }

            $product = new Product();
            $product->setStrProductCode($denormalizedProduct->getStrProductCode());
            $product->setStrProductName($denormalizedProduct->getStrProductName());
            $product->setStrProductDesc($denormalizedProduct->getStrProductDescription());
            $product->setIntStockLevel($denormalizedProduct->getIntStockLevel());
            $product->setDecPrice($denormalizedProduct->getDecPrice());
            $product->setDtmAdded(new \DateTime());
            $product->setDtmTimestamp(new \DateTime());

            if ($denormalizedProduct->getIsDiscontinued()) {
                $product->setDtmDiscontinued(new \DateTime());
            }

            if ($mode->value == 'normal') {
                $this->entityManager->persist($product);
                $this->entityManager->flush();
            }

            return true;
        } catch (Throwable $exception) {
            $this->csvLogger->error("Error, when inserting value into database: " . $exception->getMessage());

            return false;
        }
    }
}