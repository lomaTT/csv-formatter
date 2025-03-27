<?php

namespace App\Tests\Command;

use App\Command\ImportProductsCommand;
use App\Logger\CsvLogger;
use App\Service\ProductService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ImportProductsCommandTest extends TestCase
{
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $logger = $this->createMock(CsvLogger::class);
        $productService = $this->createMock(ProductService::class);

        $command = new ImportProductsCommand($productService, $logger);
        $application = new Application();
        $application->add($command);

        $this->commandTester = new CommandTester($command);
    }

    public function testExecuteWithValidData()
    {
        $this->commandTester->setInputs(['app:import-products']);

        $this->commandTester->execute([
            'filePath' => 'stock.csv',
            'mode' => 'normal',
            'showConsoleInfo' => false,
        ]);

        $this->assertStringContainsString('CSV file processed successfully.', $this->commandTester->getDisplay());
    }

    public function testExecuteWithInvalidData()
    {
        $this->commandTester->setInputs(['app:import-products']);

        $this->commandTester->execute([
            'filePath' => 'invalid_stock.csv',
            'mode' => 'normal',
            'showConsoleInfo' => false,
        ]);

        $this->assertStringContainsString('General error:', $this->commandTester->getDisplay());
    }
}
