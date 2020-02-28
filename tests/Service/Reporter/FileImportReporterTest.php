<?php

declare(strict_types=1);

namespace App\Tests\Service\Reporter;

use App\Service\Reporter\FileImportReporter;
use PHPUnit\Framework\TestCase;

class FileImportReporterTest extends TestCase
{
    /**
     * @var FileImportReporter
     */
    private $reporter;

    public function setUp(): void
    {
        parent::setUp();

        $this->reporter = new FileImportReporter();
    }

    public function testGettersAndSetters(): void
    {
        $this->reporter->addInvalidProducts(implode([implode(['product1']), implode(['product2']), implode(['product3'])]));
        $this->reporter->setNumberCreatedProducts(8);
        $this->reporter->addMessage('Product is already exists');

        $expectedInvalidProducts[] = implode([implode(['product1']), implode(['product2']), implode(['product3'])]);

        $this->assertSame($expectedInvalidProducts, $this->reporter->getInvalidProducts());

        $expectedNumberCreatedProducts = 8;

        $this->assertSame($expectedNumberCreatedProducts, $this->reporter->getNumberCreatedProducts());

        $expectedMessages = ['Product is already exists'];

        $this->assertSame($expectedMessages, $this->reporter->getMessages());
    }
}
