<?php

namespace App\Tests\Service;

use App\Dto\ProductDTO;
use App\Entity\Product;
use App\Enum\Mode;
use App\Logger\CsvLogger;
use App\Repository\ProductRepository;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PHPUnit\Framework\TestCase;

class ProductServiceTest extends TestCase
{
    private CsvLogger $logger;
    private ProductService $productService;
    private EntityManagerInterface $entityManager;
    private ProductRepository $productRepository;


    protected function setUp(): void
    {
        $this->logger = $this->createMock(CsvLogger::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->productRepository = $this->createMock(ProductRepository::class);

        $this->productService = new ProductService(
            $this->entityManager,
            $this->logger,
            $this->productRepository
        );
    }

    public function testProcessProductSuccess()
    {
        $mode = Mode::from('normal');
        $productDTO = new ProductDTO();
        $productDTO->setStrProductCode('P001');
        $productDTO->setStrProductName('Test Product');
        $productDTO->setStrProductDescription('A test product');
        $productDTO->setIntStockLevel(10);
        $productDTO->setDecPrice(99.99);
        $productDTO->setIsDiscontinued(false);

        $this->productRepository->method('findBy')->willReturn([]);

        $this->entityManager->expects($this->once())
            ->method('persist');
        $this->entityManager->expects($this->once())
            ->method('flush');

        $result = $this->productService->processProduct($productDTO, $mode);
        $this->assertTrue($result);
    }

    public function testProcessProductWithDuplicateCode()
    {
        $mode = Mode::from('normal');
        $productDTO = new ProductDTO();
        $productDTO->setStrProductCode('P001');
        $productDTO->setStrProductName('Test Product');
        $productDTO->setStrProductDescription('A test product');
        $productDTO->setIntStockLevel(10);
        $productDTO->setDecPrice(99.99);
        $productDTO->setIsDiscontinued(false);

        // Simulating that the product code already exists
        $this->productRepository->method('findBy')->willReturn([new Product()]);

        $this->logger->expects($this->once())
            ->method('error')
            ->with($this->stringContains('Product code already exists'));

        $result = $this->productService->processProduct($productDTO, $mode);
        $this->assertFalse($result);
    }

    public function testProcessProductInNormalModeWithDiscontinued()
    {
        $mode = Mode::from('normal');
        $productDTO = new ProductDTO();
        $productDTO->setStrProductCode('P002');
        $productDTO->setStrProductName('Another Product');
        $productDTO->setStrProductDescription('Another test product');
        $productDTO->setIntStockLevel(5);
        $productDTO->setDecPrice(49.99);
        $productDTO->setIsDiscontinued(true);

        $this->productRepository->method('findBy')->willReturn([]);

        $this->entityManager->expects($this->once())
            ->method('persist');
        $this->entityManager->expects($this->once())
            ->method('flush');

        $result = $this->productService->processProduct($productDTO, $mode);
        $this->assertTrue($result);
    }

    public function testProcessProductErrorHandling()
    {
        $mode = Mode::from('normal');
        $productDTO = new ProductDTO();
        $productDTO->setStrProductCode('P003');
        $productDTO->setStrProductName('Test Product');
        $productDTO->setStrProductDescription('A test product');
        $productDTO->setIntStockLevel(10);
        $productDTO->setDecPrice(99.99);
        $productDTO->setIsDiscontinued(false);

        $this->productRepository->method('findBy')->willReturn([]);

        // Simulating an exception when persisting
        $this->entityManager->method('persist')->willThrowException(new Exception('Database error'));

        $this->logger->expects($this->once())
            ->method('error')
            ->with($this->stringContains('Error, when inserting value into database: Database error'));

        $result = $this->productService->processProduct($productDTO, $mode);
        $this->assertFalse($result);
    }
}

