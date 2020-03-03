<?php

declare(strict_types=1);

namespace App\Service\ImportTool;

use App\Repository\ProductRepository;
use App\Service\Reporter\FileImportReporter;

class FileDataValidator
{
    /**
     * @var string
     */
    public const REGULAR_EXPRESSION_TO_AVOID_SPECIAL_CHARACTERS = '/[a-zA-Z0-9]/';
    /**
     * @var string
     */
    public const PRODUCT_NAME_COLUMN = 'Product Name';

    /**
     * @var string
     */
    public const PRODUCT_DESCRIPTION_COLUMN = 'Product Description';

    /**
     * @var string
     */
    public const PRODUCT_CODE_COLUMN = 'Product Code';

    /**
     * @var string
     */
    public const PRODUCT_COST_COLUMN = 'Cost in GBP';

    /**
     * @var string
     */
    public const PRODUCT_STOCK_COLUMN = 'Stock';

    /**
     * @var string
     */
    public const PRODUCT_DISCONTINUED_COLUMN = 'Discontinued';

    /**
     * @var int
     */
    public const PRODUCT_RULE_MIN_COST = 5;

    /**
     * @var int
     */
    public const PRODUCT_RULE_MAX_COST = 1000;

    /**
     * @var int
     */
    public const PRODUCT_RULE_STOCK_MIN_RULE = 10;

    /**
     * @var FileImportReporter
     */
    private $reporter;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * FileDataValidator constructor.
     * @param FileImportReporter $reporter
     * @param ProductRepository $productRepository
     */
    public function __construct(FileImportReporter $reporter, ProductRepository $productRepository)
    {
        $this->reporter = $reporter;
        $this->productRepository = $productRepository;
    }

    /**
     * @param $row
     *
     * @return bool
     */
    public function validate($row): bool
    {
        $isValid = false;

        if (!preg_match(self::REGULAR_EXPRESSION_TO_AVOID_SPECIAL_CHARACTERS, $row[self::PRODUCT_NAME_COLUMN])) {
            $this->reporter->addMessage('Invalid product name');
        } elseif ($this->productRepository->findOneBy(['code' => $row[self::PRODUCT_CODE_COLUMN]])) {
            $this->reporter->addMessage('Product with this code is already exists!');
        } elseif (!preg_match(self::REGULAR_EXPRESSION_TO_AVOID_SPECIAL_CHARACTERS, $row[self::PRODUCT_DESCRIPTION_COLUMN])) {
            $this->reporter->addMessage('Invalid product description');
        } elseif (!preg_match(self::REGULAR_EXPRESSION_TO_AVOID_SPECIAL_CHARACTERS, $row[self::PRODUCT_CODE_COLUMN])) {
            $this->reporter->addMessage('Invalid product code');
        } elseif (!is_numeric($row[self::PRODUCT_COST_COLUMN])) {
            $this->reporter->addMessage('Invalid product cost');
        } elseif (!is_numeric($row[self::PRODUCT_STOCK_COLUMN])) {
            $this->reporter->addMessage('Invalid product stock');
        } elseif ($row[self::PRODUCT_STOCK_COLUMN] < self::PRODUCT_RULE_STOCK_MIN_RULE
            && (int) $row[self::PRODUCT_COST_COLUMN] < self::PRODUCT_RULE_MIN_COST) {
            $this->reporter->addMessage('Stock and cost are less than '.self::PRODUCT_RULE_STOCK_MIN_RULE.' and '.self::PRODUCT_RULE_MIN_COST);
        } elseif ($row[self::PRODUCT_COST_COLUMN] > self::PRODUCT_RULE_MAX_COST) {
            $this->reporter->addMessage('Cost is more than '.self::PRODUCT_RULE_MAX_COST);
        } else {
            $isValid = true;
        }

        return $isValid;
    }
}
