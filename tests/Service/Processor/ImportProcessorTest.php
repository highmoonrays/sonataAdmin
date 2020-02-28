<?php

declare(strict_types=1);

namespace App\tests\Service\Processor;

use App\Service\Factory\ReaderFactory;
use App\Service\Tool\MatrixToAssociativeArrayTransformer;
use App\Service\Processor\ImportProcessor;
use App\Service\Processor\ProductCreator;
use App\Service\Tool\FileExtensionFinder;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBus;

class ImportProcessorTest extends TestCase
{
    /**
     * @var ImportProcessor
     */
    private $importProcessor;

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
     * @var MessageBus
     */
    private $bus;

    public function setUp(): void
    {
        parent::setUp();
        $mockProductCreator = $this->getMockBuilder(ProductCreator::class)
                                    ->disableOriginalConstructor()
                                    ->getMock();

        $this->readerFactory = new ReaderFactory();

        $this->extensionFinder = new FileExtensionFinder();

        $this->transformer = new MatrixToAssociativeArrayTransformer();

        $this->bus = new MessageBus();

        $this->importProcessor = new ImportProcessor(
            $mockProductCreator,
            $this->readerFactory,
            $this->extensionFinder,
            $this->transformer,
            $this->bus
        );
    }

    /**
     * @throws Exception
     */
    public function testProcess(): void
    {
        $processor = $this->importProcessor;

        $this->assertSame(true, $processor->process('public/data/stock.xlsx'));
        $this->assertSame(true, $processor->process('public/data/stock.csv'));
        $this->assertSame(true, $processor->process('public/data/stock.xml'));
    }

    /**
     * @dataProvider provideInvalidData
     *
     * @param $invalidPathOrFile
     * @param $expectedMessage
     * @throws Exception
     */
    public function testExceptionCase($invalidPathOrFile, $expectedMessage): void
    {
        $this->expectExceptionMessage($expectedMessage[0]);
        $this->assertSame(false, $this->importProcessor->process($invalidPathOrFile[0]));
    }

    /**
     * @return array
     */
    public function provideInvalidData(): array
    {
        return[
            [['stock2.csv'], ['File "stock2.csv" does not exist.']],
            [['public/data/stock2.csv'], ['Invalid data in given file!']]
        ];
    }
}
