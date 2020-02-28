<?php

declare(strict_types=1);

namespace App\tests\Service\Factory;

use App\Service\Factory\ReaderFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xml;
use PHPUnit\Framework\TestCase;

class ReaderFactoryTest extends TestCase
{
    /**
     * @var ReaderFactory
     */
    private $readerFactory;

    public function setUp(): void
    {
        $this->readerFactory = new ReaderFactory();
    }


    public function testGetFileReader(): void
    {
        $this->assertInstanceOf(Csv::class, $this->readerFactory->getFileReader('csv'));
        $this->assertInstanceOf(Xlsx::class, $this->readerFactory->getFileReader('xlsx'));
        $this->assertInstanceOf(Xml::class, $this->readerFactory->getFileReader('xml'));
        $this->assertSame(null, $this->readerFactory->getFileReader('data'));
    }
}
