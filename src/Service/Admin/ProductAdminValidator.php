<?php

declare(strict_types=1);

namespace App\Service\Admin;

use App\Service\ImportTool\FileDataValidator;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\Form\Validator\ErrorElement;

class ProductAdminValidator
{
    /**
     * @param ErrorElement $errorElement
     * @param EntityManagerInterface $em
     * @throws \Exception
     */
    public function validate(ErrorElement $errorElement, EntityManagerInterface $em): void
    {
        if ($errorElement->getSubject()->getCost() < FileDataValidator::PRODUCT_RULE_MIN_COST &&
            $errorElement->getSubject()->getStock() < FileDataValidator::PRODUCT_RULE_STOCK_MIN_RULE
        ) {
            $errorElement->with('stock')
                ->addViolation(
                    'Stock is less than '.FileDataValidator::PRODUCT_RULE_STOCK_MIN_RULE
                    .' and cost is less than '. FileDataValidator::PRODUCT_RULE_MIN_COST
                )
                ->end()
            ;
        }

        $entityObject = $errorElement->getSubject();
        $entityName = get_class($entityObject);
        $foundedObject = $em->getRepository($entityName)
            ->findOneByCode($errorElement->getSubject()->getCode());

        if ($foundedObject) {
            if ($errorElement->getSubject()->getId() != $foundedObject->getId()) {
                $errorElement->with('code')
                    ->addViolation('Product with this code is already existing!')
                    ->end()
                ;
            }
        }

        if ($entityObject->getDiscontinuedAt() || $entityObject->getStock() === 0) {
            $entityObject->setDiscontinuedAt(new \DateTime());
        }
    }
}
