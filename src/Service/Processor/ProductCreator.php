<?php

declare(strict_types=1);

namespace App\Service\Processor;

use App\Entity\Product;
use App\Service\ImportTool\FileDataValidator;
use App\Service\Reporter\FileImportReporter;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class ProductCreator
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
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ProductCreator constructor.
     * @param FileDataValidator $validator
     * @param EntityManagerInterface $em
     * @param FileImportReporter $reporter
     */
    public function __construct(
        FileDataValidator $validator,
        EntityManagerInterface $em,
        FileImportReporter $reporter
    ) {
        $this->validator = $validator;
        $this->em = $em;
        $this->reporter = $reporter;
    }

    /**
     * @param $rowsWithKeys
     * @return bool
     * @throws Exception
     */
    public function createProducts(array $rowsWithKeys): bool
    {
        $isValid = false;
        foreach ($rowsWithKeys as $row) {
            $isValid = $this->validator->validate($row);

            if (true === $isValid) {
                $this->reporter->setNumberCreatedProducts($this->reporter->getNumberCreatedProducts() + 1);
                $this->setProduct($row);
            } else {
                $this->reporter->addInvalidProducts(implode(' ', $row));
            }
        }
        return $isValid;
    }

    /**
     * @param $row
     *
     * @throws Exception
     */
    public function setProduct(array $row): void
    {
        $isDiscontinued = false;

        if ('yes' === $row[FileDataValidator::PRODUCT_DISCONTINUED_COLUMN]) {
            $isDiscontinued = true;
        }
        $product = new Product(
            $row[FileDataValidator::PRODUCT_NAME_COLUMN],
            $row[FileDataValidator::PRODUCT_DESCRIPTION_COLUMN],
            $row[FileDataValidator::PRODUCT_CODE_COLUMN],
            (int) $row[FileDataValidator::PRODUCT_STOCK_COLUMN],
            (int) $row[FileDataValidator::PRODUCT_COST_COLUMN],
            $isDiscontinued
        );
        $this->em->persist($product);
    }
}
