<?php

declare(strict_types=1);

namespace App\tests\Entity;

use App\Entity\Product;
use Exception;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    /**
     * @var Product
     */
    private $product;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->product = new Product(
            'TestName',
            'TestDescription',
            'TestCode0000',
            5,
            0,
            false
        )
        ;
    }

    public function testCreateProduct(): void
    {
        $this->assertSame('TestName', $this->product->getName());
        $this->assertSame('TestDescription', $this->product->getDescription());
        $this->assertSame('TestCode0000', $this->product->getCode());
        $this->assertSame(5, $this->product->getStock());
        $this->assertSame(0, $this->product->getCost());

        if (true === $this->product->getDiscontinuedAt()) {
            $this->assertIsObject($this->product->getDiscontinuedAt()());
        } else {
            $this->assertEmpty($this->product->getDiscontinuedAt());
        }
    }

    public function testGettersAndSetters(): void
    {
        $this->product->setName('newTestNameWeExpecting');
        $this->assertSame('newTestNameWeExpecting', $this->product->getName());

        $this->product->setDescription('It is new one');
        $this->assertSame('It is new one', $this->product->getDescription());

        $this->product->setCode('RussianVodka');
        $this->assertSame('RussianVodka', $this->product->getCode());

        $this->product->setStock(100);
        $this->assertSame(100, $this->product->getStock());

        $this->product->setCost(150);
        $this->assertSame(150, $this->product->getCost());

        $this->product->setDiscontinuedAt(new \DateTime());
        $this->assertInstanceOf(\DateTime::class, $this->product->getDiscontinuedAt());

        $this->product->setAddedAt(new \DateTime());
        $this->assertInstanceOf(\DateTime::class, $this->product->getAddedAt());

        $this->product->setTimestamp(new \DateTime());
        $this->assertInstanceOf(\DateTime::class, $this->product->getTimestamp());
    }
}
