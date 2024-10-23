<?php

namespace App\Command;

use App\Dto\ProductDTO;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\Serializer\Denormalizer;
use League\Csv\UnavailableStream;
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
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    protected function configure(): void
    {
        $this
            ->setDescription("Import products from CSV file")
            ->addArgument('filePath', InputArgument::REQUIRED, 'Path to CSV file')
            ->addArgument('mode', InputArgument::REQUIRED, 'Mode. Supported modes: test, normal')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $pathToFile = $input->getArgument('filePath');
        $mode = $input->getArgument('mode');

        if (!in_array($mode, ['normal', 'test'])) {
            $io->note(sprintf("Supported modes: normal, test. You provided: %s", $mode));
            $io->error(sprintf('Invalid mode: %s', $mode));

            return Command::FAILURE;
        }

        try {
            $csvFile = Reader::createFromPath($pathToFile, 'r');
            $csvFile->setHeaderOffset(0);
            $header = $csvFile->getHeader();

            $formatter = function (array $row) use ($header) {
                foreach ($row as $key => &$value) {
                    if (str_contains($value, '$')) {
                        $value = str_replace("$", '', $value);
                    }
                }

                return $row;
            };

            $csvFile->addFormatter($formatter);

            $records = $csvFile
                ->getRecordsAsObject(ProductDTO::class)
            ;

            foreach ($records as $key => $record) {
                /** @var ProductDTO $record */
                try {
                    if (($record->getDecPrice() !== null) && $record->getDecPrice() < 5.00) {
                        $this->logger->info("$key row have price less than $5, row skipped");
                        continue;
                    }

                    if (($record->getIntStockLevel() !== null) && $record->getIntStockLevel() < 10) {
                        $this->logger->info("$key row have less than 10 items, row skipped");
                        continue;
                    }

                    if (($record->getDecPrice() !== null) && $record->getDecPrice() > 1000.00) {
                        $this->logger->info("$key row have price more than $1000.00, row skipped");
                        continue;
                    }

                    $product = new Product();
                } catch (Throwable $e) {
                    dump($record);
                    $io->error($e->getMessage());
                    continue;
                }
            }
        } catch (Throwable $exception) {
            $this->logger->error("Problem with {$records->key()} row: {$exception->getMessage()}");

            $io->error($exception->getMessage());
        }



//        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
