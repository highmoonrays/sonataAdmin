<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\ProductDataMessage;
use App\Service\Processor\ImportProcessor;
use App\Service\Processor\ProductCreator;
use App\Service\Reporter\FileImportReporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ProductDataMessageHandler implements MessageHandlerInterface
{
    /**
     * @var string
     */
    public const TOPIC_FOR_UPDATE_REPORT_DATA = 'http://localhost:8000/uploadFile';

    /**
     * @var ImportProcessor
     */
    private $importProcessor;

    /**
     * @var FileImportReporter
     */
    private $importReporter;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ProductCreator
     */
    private $productCreator;

    /**
     * @var PublisherInterface
     */
    private $publisher;

    /**
     * CreateProductFromUploadedFileController constructor.
     * @param ImportProcessor $importProcessor
     * @param FileImportReporter $importReporter
     * @param EntityManagerInterface $em
     * @param ProductCreator $productCreator
     * @param PublisherInterface $publisher
     */
    public function __construct(
        ImportProcessor $importProcessor,
        FileImportReporter $importReporter,
        EntityManagerInterface $em,
        ProductCreator $productCreator,
        PublisherInterface $publisher
    ) {
        $this->importReporter = $importReporter;
        $this->importProcessor = $importProcessor;
        $this->em = $em;
        $this->productCreator = $productCreator;
        $this->publisher = $publisher;
    }

    /**
     * @param ProductDataMessage $productDataMessage
     * @throws \Exception
     */
    public function __invoke(ProductDataMessage $productDataMessage): void
    {
        $rowWithKeys = $productDataMessage->getRowWithKeys();
        $this->productCreator->createProducts($rowWithKeys);

        if (false === $isTestMode = $productDataMessage->isTest()) {
            $this->em->flush();
        }
        $invalidProducts = $this->importReporter->getInvalidProducts();
        $messages = $this->importReporter->getMessages();

        if ($invalidProducts && $messages) {
            $productData = $this->importReporter->getInvalidProducts();
            $message = $this->importReporter->getMessages();
            $update = new Update(self::TOPIC_FOR_UPDATE_REPORT_DATA, implode(', ', $productData).' || Error: '.implode(' ', $message));
        } else {
            $update = new Update(self::TOPIC_FOR_UPDATE_REPORT_DATA, 'Product created!');
        }
        $this->importReporter->clearReport();
        $publisher = $this->publisher;
        $publisher($update);
    }
}
