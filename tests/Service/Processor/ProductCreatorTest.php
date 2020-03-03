<?php

declare(strict_types=1);

namespace App\Tests\Service\Processor;

use App\Service\ImportTool\FileDataValidator;
use App\Service\Processor\ProductCreator;
use App\Service\Reporter\FileImportReporter;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class ProductCreatorTest extends TestCase
{
    /**
     * @var ProductCreator
     */
    private $creator;

    /**
     * @var FileImportReporter
     */
    private $reporter;

    public function setUp(): void
    {
        parent::setUp();

        $mockValidator = $this->getMockBuilder(FileDataValidator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockValidator->expects($this->any())
            ->method('validate')
            ->willReturnCallback(
                function ($isValid) {
                    if ('yes' === $isValid['isValid']) {
                        return true;
                    }
                    return false;
                }
            );

        $mockEm = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();

        $this->reporter = new FileImportReporter();

        $this->creator = new ProductCreator($mockValidator, $mockEm, $this->reporter);
    }

    /**
     * @dataProvider provideDataToCreateProducts
     * @param $dataToCreate
     * @param $expectedResult
     * @throws \Exception
     */
    public function testCreateProducts($dataToCreate, $expectedResult): void
    {
        $this->creator->createProducts($dataToCreate['product']);
        self::assertSame($this->reporter->getInvalidProducts(), $expectedResult['invalidProducts']);
        self::assertSame($this->reporter->getNumberCreatedProducts(), $expectedResult['numberCreatedProducts']);
    }

    /**
     * @return array
     */
    public function provideDataToCreateProducts(): array
    {
        return[
            [
                [
                    'product' => [
                        [
                            FileDataValidator::PRODUCT_NAME_COLUMN => 'name0',
                            FileDataValidator::PRODUCT_DESCRIPTION_COLUMN => 'description 0',
                            FileDataValidator::PRODUCT_CODE_COLUMN => 'P00055',
                            FileDataValidator::PRODUCT_DISCONTINUED_COLUMN => 'yes',
                            FileDataValidator::PRODUCT_STOCK_COLUMN => 5,
                            FileDataValidator::PRODUCT_COST_COLUMN => 100,
                            'isValid' => 'yes'
                        ],
                        [
                            FileDataValidator::PRODUCT_NAME_COLUMN => 'name2',
                            FileDataValidator::PRODUCT_DESCRIPTION_COLUMN => 'description 3',
                            FileDataValidator::PRODUCT_CODE_COLUMN => 'P00057',
                            FileDataValidator::PRODUCT_DISCONTINUED_COLUMN => 'yes',
                            FileDataValidator::PRODUCT_STOCK_COLUMN => 2,
                            FileDataValidator::PRODUCT_COST_COLUMN => 115,
                            'isValid' => 'yes'
                        ],
                        [
                            FileDataValidator::PRODUCT_NAME_COLUMN => 9,
                            FileDataValidator::PRODUCT_DESCRIPTION_COLUMN => 197,
                            FileDataValidator::PRODUCT_CODE_COLUMN => null,
                            FileDataValidator::PRODUCT_DISCONTINUED_COLUMN => 'error',
                            FileDataValidator::PRODUCT_STOCK_COLUMN => 1,
                            FileDataValidator::PRODUCT_COST_COLUMN => 0,
                            'isValid' => 'no'
                        ],
                    ]
                ],
                [
                    'invalidProducts' => ["9 197  error 1 0 no"],
                    'numberCreatedProducts' => 2
                ]
            ],
            [
                [
                    'product' => [
                        [
                            FileDataValidator::PRODUCT_NAME_COLUMN => 'name0',
                            FileDataValidator::PRODUCT_DESCRIPTION_COLUMN => 'description 0',
                            FileDataValidator::PRODUCT_CODE_COLUMN => 'P00055',
                            FileDataValidator::PRODUCT_DISCONTINUED_COLUMN => 'yes',
                            FileDataValidator::PRODUCT_STOCK_COLUMN => 5,
                            FileDataValidator::PRODUCT_COST_COLUMN => 100,
                            'isValid' => 'yes'
                        ],
                        [
                            FileDataValidator::PRODUCT_NAME_COLUMN => 'name9',
                            FileDataValidator::PRODUCT_DESCRIPTION_COLUMN => 'description 111',
                            FileDataValidator::PRODUCT_CODE_COLUMN => 'P0005',
                            FileDataValidator::PRODUCT_DISCONTINUED_COLUMN => '',
                            FileDataValidator::PRODUCT_STOCK_COLUMN => 99,
                            FileDataValidator::PRODUCT_COST_COLUMN => 10,
                            'isValid' => 'yes'
                        ],
                        [
                            FileDataValidator::PRODUCT_NAME_COLUMN => 'name1000',
                            FileDataValidator::PRODUCT_DESCRIPTION_COLUMN => 'description 1000',
                            FileDataValidator::PRODUCT_CODE_COLUMN => 'P0001000',
                            FileDataValidator::PRODUCT_DISCONTINUED_COLUMN => 'yes',
                            FileDataValidator::PRODUCT_STOCK_COLUMN => 3,
                            FileDataValidator::PRODUCT_COST_COLUMN => 651,
                            'isValid' => 'yes'
                        ],
                        [
                            FileDataValidator::PRODUCT_NAME_COLUMN => 'name2',
                            FileDataValidator::PRODUCT_DESCRIPTION_COLUMN => 'description 3',
                            FileDataValidator::PRODUCT_CODE_COLUMN => 'P00057',
                            FileDataValidator::PRODUCT_DISCONTINUED_COLUMN => 'yes',
                            FileDataValidator::PRODUCT_STOCK_COLUMN => 2,
                            FileDataValidator::PRODUCT_COST_COLUMN => 115,
                            'isValid' => 'yes'
                        ],
                        [
                            FileDataValidator::PRODUCT_NAME_COLUMN => 9,
                            FileDataValidator::PRODUCT_DESCRIPTION_COLUMN => 197,
                            FileDataValidator::PRODUCT_CODE_COLUMN => null,
                            FileDataValidator::PRODUCT_DISCONTINUED_COLUMN => 'error',
                            FileDataValidator::PRODUCT_STOCK_COLUMN => 1,
                            FileDataValidator::PRODUCT_COST_COLUMN => 0,
                            'isValid' => 'no'
                        ],
                        [
                            FileDataValidator::PRODUCT_NAME_COLUMN => 'dam',
                            FileDataValidator::PRODUCT_DESCRIPTION_COLUMN => 13,
                            FileDataValidator::PRODUCT_CODE_COLUMN => null,
                            FileDataValidator::PRODUCT_DISCONTINUED_COLUMN => 'Fatal error',
                            FileDataValidator::PRODUCT_STOCK_COLUMN => 1,
                            FileDataValidator::PRODUCT_COST_COLUMN => 100000,
                            'isValid' => 'no'
                        ],
                        [
                            FileDataValidator::PRODUCT_NAME_COLUMN => 'name',
                            FileDataValidator::PRODUCT_DESCRIPTION_COLUMN => 'old smoke',
                            FileDataValidator::PRODUCT_CODE_COLUMN => 'codddd',
                            FileDataValidator::PRODUCT_DISCONTINUED_COLUMN => 'error',
                            FileDataValidator::PRODUCT_STOCK_COLUMN => 1,
                            FileDataValidator::PRODUCT_COST_COLUMN => 3,
                            'isValid' => 'no'
                        ],
                    ]
                ],
                [
                    'invalidProducts' =>[
                        "9 197  error 1 0 no",
                        "dam 13  Fatal error 1 100000 no",
                        "name old smoke codddd error 1 3 no",
                    ],
                    'numberCreatedProducts' => 4,
                ]
            ],
        ];
    }
}
