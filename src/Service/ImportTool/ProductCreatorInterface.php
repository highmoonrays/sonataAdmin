<?php

declare(strict_types=1);

namespace App\Service\ImportTool;

use Doctrine\ORM\EntityManagerInterface;

interface ProductCreatorInterface
{
    /**
     * ProductCreatorInterface constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em);

    /**
     * @param array $productData
     * @return mixed
     */
    public function create(array $productData);
}
