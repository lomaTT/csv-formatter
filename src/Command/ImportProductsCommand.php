<?php

namespace App\Command;

use App\Dto\ProductDTO;
use App\Entity\Product;
use App\Enum\Mode;
use App\Logger\CsvLogger;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\Serializer\Denormalizer;
use League\Csv\UnavailableStream;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(
    name: 'app:import-products',
    description: 'Add a short description for your command',
)]
class ImportProductsCommand extends Command
{
    private CsvLogger $logger;
    private ProductService $productService;

    public function __construct(
        ProductService $productService,
        CsvLogger $logger
    )
    {
        parent::__construct();
        $this->logger = $logger;
        $this->productService = $productService;
    }

    protected function configure(): void
    {
        $this
            ->setDescription("Import products from CSV file")
            ->addArgument('filePath', InputArgument::REQUIRED, 'Path to CSV file')
            ->addArgument('mode', InputArgument::REQUIRED, 'Mode. Supported modes: test, normal')
            ->addArgument('showConsoleInfo', InputArgument::OPTIONAL, 'Show console messages.', 1)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $pathToFile = $input->getArgument('filePath');
        $mode = $input->getArgument('mode');
        $showConsoleInfo = (bool)$input->getArgument('showConsoleInfo');

        if (!in_array($mode, ['normal', 'test'])) {
            $io->note(sprintf("Supported modes: normal, test. You provided: %s", $mode));
            $io->error(sprintf('Invalid mode: %s', $mode));

            return Command::FAILURE;
        }

        $mode = Mode::from($mode);
        $currentTime = (new \DateTime())->format('Y-m-d H:i:s');
        $this->logger->info("Importing products from CSV file. Time: $currentTime, mode: $mode->value");
        $insertedRows = 0;
        $skippedRows = 0;
        $invalidRows = 0;

        try {
            $csvFile = Reader::createFromPath($pathToFile, 'r');
            $csvFile->setHeaderOffset(0);
            $header = $csvFile->getHeader();

            $denormalizer = new Denormalizer(ProductDTO::class, $header);

            // Get all records of csv file
            $records = $csvFile->getRecords();

            foreach ($records as $key => $record) {
                try {
                    /** @var ProductDTO $denormalizedRecord */

                    // Denormalize record to RecordDTO
                    // (this will help us find some errors with formatting)
                    $denormalizedRecord = $denormalizer->denormalize($record);

                    // Validate record (custom rules)
                    $denormalizedRecord->validate();

                    // Business logic
                    if (($denormalizedRecord->getDecPrice() !== null) && $denormalizedRecord->getDecPrice() < 5.00) {
                        $this->logger->info("Row number $key has price less than $5, row skipped");

                        if ($showConsoleInfo) {
                            $io->info("Row number $key has price less than $5, row skipped");
                        }
                        $skippedRows++;
                        continue;
                    }

                    if (($denormalizedRecord->getIntStockLevel() !== null) && $denormalizedRecord->getIntStockLevel() < 10) {
                        $this->logger->info("Row number $key has less than 10 items in stock, row skipped");

                        if ($showConsoleInfo) {
                            $io->info("Row number $key has less than 10 items in stock, row skipped");
                        }
                        $skippedRows++;
                        continue;
                    }

                    if (($denormalizedRecord->getDecPrice() !== null) && $denormalizedRecord->getDecPrice() > 1000.00) {
                        $this->logger->info("Row number $key has price greater than $1000.00, row skipped");

                        if ($showConsoleInfo) {
                            $io->info("Row number $key has price greater than $1000.00, row skipped");
                        }
                        $skippedRows++;
                        continue;
                    }

                    $result = $this->productService->processProduct($denormalizedRecord, $mode);

                    if ($result) {
                        $insertedRows++;
                    } else {
                        if ($showConsoleInfo) {
                            $io->error("Error processing row $key. Check log file to find possible reasons.");
                        }

                        $invalidRows++;
                    }
                } catch (Throwable $e) {
                    // Handle errors specific to the current record
                    $this->logger->error("Error processing row $key: {$e->getMessage()}");

                    if ($showConsoleInfo) {
                        $io->error("Error processing row $key: {$e->getMessage()}");
                    }
                    $invalidRows++;
                    continue;
                }
            }
        } catch (Throwable $exception) {
            // Handle errors that occur when reading the file or setting up the reader
            $this->logger->error("General error: {$exception->getMessage()}");
            $io->error("General error: {$exception->getMessage()}");

            return Command::FAILURE;
        }

        $io->success(
            "CSV file processed successfully.\n" .
            "Skipped rows: $skippedRows, invalid rows: $invalidRows, inserted rows: $insertedRows");

        return Command::SUCCESS;
    }
}
