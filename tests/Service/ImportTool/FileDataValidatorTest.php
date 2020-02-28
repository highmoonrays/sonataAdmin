<?php

declare(strict_types=1);

namespace App\Tests\Service\ImportTool;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\Reporter\FileImportReporter;
use App\Service\ImportTool\FileDataValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FileDataValidatorTest extends TestCase
{
    /**
     * @var FileDataValidator
     */
    private $validator;

    /**
     * @var FileImportReporter
     */
    private $reporter;

    /**
     * @var MockObject
     */
    private $mockProductRepository;

    /**
     * @var string
     */
    private const DATA_TO_VALIDATE_KEY = 'dataToValidate';

    /**
     * @var string
     */
    private const EXPECTED_RESULT_KEY = 'expectedResult';

    /**
     * @var string
     */
    private const EXPECTED_MESSAGE_KEY = 'expectedMessage';

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->mockProductRepository = $this->getMockBuilder(ProductRepository::class)
                                ->disableOriginalConstructor()
                                ->setMethods(['FindOneBy'])
                                ->getMock();

        $this->mockProductRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturnCallback(
                function ($code) {
                    if (['code' => 'P00050'] === $code) {
                        return new Product(
                        'Already',
                        'Existing product, so validation will fail',
                        'P00050',
                        10,
                        20,
                        true
                    );
                    }
                    return null;
                }
            );

        $this->reporter = new FileImportReporter();

        $this->validator = new FileDataValidator($this->reporter, $this->mockProductRepository);
    }

    /**
     * @param $dataToValidate
     * @param $result
     * @param $expectedMessage
     * @return void
     * @dataProvider provideInvalidDataToValidate
     */
    public function testValidateInvalidProducts($dataToValidate, $result, $expectedMessage): void
    {
        $isValid[] = $this->validator->validate($dataToValidate);

        $this->assertSame($isValid, $result);

        if ($result != true) {
            $realMessage = $this->reporter->getMessages();
            $this->assertSame($expectedMessage, $realMessage);
        }
    }

    /**
     * @return array
     */
    public function provideInvalidDataToValidate(): array
    {
        return[
                [
                    FileDataValidatorTest::DATA_TO_VALIDATE_KEY => [
                                FileDataValidator::PRODUCT_NAME_COLUMN => '',
                                FileDataValidator::PRODUCT_DESCRIPTION_COLUMN => 'description-1',
                                FileDataValidator::PRODUCT_CODE_COLUMN => 'P00011000',
                                FileDataValidator::PRODUCT_DISCONTINUED_COLUMN => '',
                                FileDataValidator::PRODUCT_STOCK_COLUMN => 9,
                                FileDataValidator::PRODUCT_COST_COLUMN => 100
                    ],
                    FileDataValidatorTest::EXPECTED_RESULT_KEY => [false],
                    FileDataValidatorTest::EXPECTED_MESSAGE_KEY => ['Invalid product name']
                ],
                [
                    FileDataValidatorTest::DATA_TO_VALIDATE_KEY => [
                        FileDataValidator::PRODUCT_NAME_COLUMN => 'Some name',
                        FileDataValidator::PRODUCT_DESCRIPTION_COLUMN => '',
                        FileDataValidator::PRODUCT_CODE_COLUMN => 'P000250',
                        FileDataValidator::PRODUCT_DISCONTINUED_COLUMN => 'yes',
                        FileDataValidator::PRODUCT_STOCK_COLUMN => 9,
                        FileDataValidator::PRODUCT_COST_COLUMN => 100
                    ],
                    FileDataValidatorTest::EXPECTED_RESULT_KEY => [false],
                    FileDataValidatorTest::EXPECTED_MESSAGE_KEY => ['Invalid product description']
                ],
                [
                    FileDataValidatorTest::DATA_TO_VALIDATE_KEY => [
                        FileDataValidator::PRODUCT_NAME_COLUMN => 'Some name',
                        FileDataValidator::PRODUCT_DESCRIPTION_COLUMN => 'description',
                        FileDataValidator::PRODUCT_CODE_COLUMN => '',
                        FileDataValidator::PRODUCT_DISCONTINUED_COLUMN => 'yes',
                        FileDataValidator::PRODUCT_STOCK_COLUMN => 9,
                        FileDataValidator::PRODUCT_COST_COLUMN => 100
                    ],
                    FileDataValidatorTest::EXPECTED_RESULT_KEY => [false],
                    FileDataValidatorTest::EXPECTED_MESSAGE_KEY => ['Invalid product code']
                ],
                [
                    FileDataValidatorTest::DATA_TO_VALIDATE_KEY => [
                        FileDataValidator::PRODUCT_NAME_COLUMN => 'name',
                        FileDataValidator::PRODUCT_DESCRIPTION_COLUMN => 'description',
                        FileDataValidator::PRODUCT_CODE_COLUMN => 'P00053',
                        FileDataValidator::PRODUCT_DISCONTINUED_COLUMN => 'yes',
                        FileDataValidator::PRODUCT_STOCK_COLUMN => 3,
                        FileDataValidator::PRODUCT_COST_COLUMN => 4
                    ],
                    FileDataValidatorTest::EXPECTED_RESULT_KEY => [false],
                    FileDataValidatorTest::EXPECTED_MESSAGE_KEY => [
                        'Stock and cost are less than '.FileDataValidator::PRODUCT_RULE_STOCK_MIN_RULE.' and '.FileDataValidator::PRODUCT_RULE_MIN_COST]
                ],
                [
                    FileDataValidatorTest::DATA_TO_VALIDATE_KEY => [
                        FileDataValidator::PRODUCT_NAME_COLUMN => 'name',
                        FileDataValidator::PRODUCT_DESCRIPTION_COLUMN => 'description',
                        FileDataValidator::PRODUCT_CODE_COLUMN => 'P00057',
                        FileDataValidator::PRODUCT_DISCONTINUED_COLUMN => 'yes',
                        FileDataValidator::PRODUCT_STOCK_COLUMN => 3,
                        FileDataValidator::PRODUCT_COST_COLUMN => 10000
                    ],
                    FileDataValidatorTest::EXPECTED_RESULT_KEY => [false],
                    FileDataValidatorTest::EXPECTED_MESSAGE_KEY => ['Cost is more than '.FileDataValidator::PRODUCT_RULE_MAX_COST]
                ],
                [
                    FileDataValidatorTest::DATA_TO_VALIDATE_KEY => [
                        FileDataValidator::PRODUCT_NAME_COLUMN => 'name0',
                        FileDataValidator::PRODUCT_DESCRIPTION_COLUMN => 'description 0',
                        FileDataValidator::PRODUCT_CODE_COLUMN => 'P00055',
                        FileDataValidator::PRODUCT_DISCONTINUED_COLUMN => 'yes',
                        FileDataValidator::PRODUCT_STOCK_COLUMN => 5,
                        FileDataValidator::PRODUCT_COST_COLUMN => 100
                    ],
                    FileDataValidatorTest::EXPECTED_RESULT_KEY => [true],
                    FileDataValidatorTest::EXPECTED_MESSAGE_KEY => [null]
                ],
                [
                    FileDataValidatorTest::DATA_TO_VALIDATE_KEY => [
                        FileDataValidator::PRODUCT_NAME_COLUMN => 'name3',
                        FileDataValidator::PRODUCT_DESCRIPTION_COLUMN => 'description 3',
                        FileDataValidator::PRODUCT_CODE_COLUMN => 'P00050',
                        FileDataValidator::PRODUCT_DISCONTINUED_COLUMN => '',
                        FileDataValidator::PRODUCT_STOCK_COLUMN => 5,
                        FileDataValidator::PRODUCT_COST_COLUMN => 100
                    ],
                    FileDataValidatorTest::EXPECTED_RESULT_KEY => [false],
                    FileDataValidatorTest::EXPECTED_MESSAGE_KEY => ['This product already exists']
                ],
        ];
    }
}
