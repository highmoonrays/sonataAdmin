<?php

declare(strict_types=1);

namespace App\Service\Processor;

use App\Exception\InvalidDataInFileException;
use App\Message\ProductDataMessage;
use App\Service\Factory\ReaderFactory;
use App\Service\Tool\MatrixToAssociativeArrayTransformer;
use App\Service\Tool\FileExtensionFinder;
use Exception;
use Symfony\Component\Messenger\MessageBusInterface;

class ImportProcessor
{
    /**
     * @var ProductCreator
     */
    private $productCreator;

    /**
     * @var ReaderFactory
     */
    private $readerFactory;

    /**
     * @var FileExtensionFinder
     */
    private $extensionFinder;

    /**
     * @var MatrixToAssociativeArrayTransformer
     */
    private $transformer;

    /**
     * @var MessageBusInterface
     */
    private $bus;

    /**
     * ImportProcessor constructor.
     * @param ProductCreator $productCreator
     * @param ReaderFactory $readerFactory
     * @param FileExtensionFinder $extensionFinder
     * @param MatrixToAssociativeArrayTransformer $transformer
     */
    public function __construct(
        ProductCreator $productCreator,
        ReaderFactory $readerFactory,
        FileExtensionFinder $extensionFinder,
        MatrixToAssociativeArrayTransformer $transformer,
        MessageBusInterface $bus
    ) {
        $this->productCreator = $productCreator;
        $this->readerFactory = $readerFactory;
        $this->extensionFinder = $extensionFinder;
        $this->transformer = $transformer;
        $this->bus = $bus;
    }

    /**
     * @param $pathToProcessFile
     *
     * @return bool
     * @throws Exception
     */
    public function process($pathToProcessFile): bool
    {
        $isProcessSuccess = false;
        $rows = $this->readFile($pathToProcessFile);

        if ($rows) {
            $rowsWithKeys = $this->transformArrayToAssociative($rows);

            if ($rowsWithKeys) {
                $this->productCreator->createProducts($rowsWithKeys);
                $isProcessSuccess = true;
            } else {
                throw new InvalidDataInFileException('Invalid data in given file!');
            }
        }

        return $isProcessSuccess;
    }

    /**
     * @param $pathToProcessFile
     * @return string|null
     * @throws Exception
     */
    public function getFileExtension(string $pathToProcessFile): ?string
    {
        return $fileExtension = $this->extensionFinder->findFileExtensionFromPath($pathToProcessFile);
    }

    /**
     * @param $pathToProcessFile
     * @return array|null
     * @throws Exception
     */
    public function readFile(string $pathToProcessFile): ?array
    {
        $fileExtension = $this->getFileExtension($pathToProcessFile);

        if ($fileExtension) {
            $reader = $this->readerFactory->getFileReader($fileExtension);

            if ($reader) {
                $spreadSheet = $reader->load($pathToProcessFile);
                return $spreadSheet->getActiveSheet()->toArray();
            }
        }

        return null;
    }

    /**
     * @param $rows
     * @return array
     */
    public function transformArrayToAssociative(array $rows): array
    {
        return $this->transformer->transformArrayToAssociative($rows);
    }

    /**
     * @param $pathToFile
     * @param $isTestMode
     * @throws Exception
     */
    public function scheduleProductCreation(string $pathToFile, bool $isTestMode): void
    {
        $rows = $this->readFile($pathToFile);
        $rowsWithKeys = $this->transformArrayToAssociative($rows);

        if (count($rowsWithKeys) > 1) {
            foreach ($rowsWithKeys as $rowWithKeys) {
                $message = new ProductDataMessage(array($rowWithKeys), $isTestMode);
                $this->bus->dispatch($message);
            }
        }
    }
}
