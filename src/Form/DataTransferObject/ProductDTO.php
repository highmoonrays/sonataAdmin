<?php

declare(strict_types=1);

namespace App\Form\DataTransferObject;

use App\Validator as AcmeAssert;
use App\Entity\Product;
use Symfony\Component\Validator\Constraints as Assert;
use App\Service\ImportTool\FileDataValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @AcmeAssert\CustomUniqueEntity(
 *     fields={"code"},
 *     message="Product with this code already exists!",
 *     className=Product::class,
 *     fieldToFireError="code",
 *     primaryKey="id"
 * )
 */
class ProductDTO
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Regex(
     *    FileDataValidator::REGULAR_EXPRESSION_TO_AVOID_SPECIAL_CHARACTERS,
     *    message="Please, enter valid name"
     * )
     */
    private $name;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Regex(
     *     FileDataValidator::REGULAR_EXPRESSION_TO_AVOID_SPECIAL_CHARACTERS,
     *     message="Please, enter valid description"
     * )
     */
    private $description;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Regex(
     *     FileDataValidator::REGULAR_EXPRESSION_TO_AVOID_SPECIAL_CHARACTERS,
     *     message="Please, enter valid code"
     * )
     */
    private $code;

    /**
     * @var bool
     */
    private $isDiscontinued;

    /**
     * @var int
     * @Assert\NotBlank
     */
    private $stock;

    /**
     * @var int
     * @Assert\NotBlank
     * @Assert\LessThan(1000)
     */
    private $cost;

    /**
     * @param object $product
     * @return ProductDTO|null
     */
    public static function createDTOFromProduct(object $product): ?ProductDTO
    {
        $productDTO = new ProductDTO();
        $productDTO->setId($product->getId());
        $productDTO->setName($product->getName());
        $productDTO->setDescription($product->getDescription());
        $productDTO->setCode($product->getCode());
        $productDTO->setStock($product->getStock());
        $productDTO->setCost($product->getCost());

        if ($product->getDiscontinuedAt() != null || $product->getStock() === 0) {
            $productDTO->setIsDiscontinued(true);
        } else {
            $productDTO->setIsDiscontinued(false);
        }

        return $productDTO;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return int|null
     */
    public function getStock(): ?int
    {
        return $this->stock;
    }

    /**
     * @param int $stock
     */
    public function setStock(int $stock): void
    {
        $this->stock = $stock;
    }

    /**
     * @return int|null
     */
    public function getCost(): ?int
    {
        return $this->cost;
    }

    /**
     * @param int $cost
     */
    public function setCost(int $cost): void
    {
        $this->cost = $cost;
    }

    /**
     * @return bool|null
     */
    public function isDiscontinued(): ?bool
    {
        return $this->isDiscontinued;
    }

    /**
     * @param bool $isDiscontinued
     */
    public function setIsDiscontinued(bool $isDiscontinued): void
    {
        $this->isDiscontinued = $isDiscontinued;
    }

    /**
     * @param ExecutionContextInterface $context
     * @Assert\Callback()
     */
    public function isRules(ExecutionContextInterface $context): void
    {
        if ($this->cost < FileDataValidator::PRODUCT_RULE_MIN_COST &&
            $this->stock < FileDataValidator::PRODUCT_RULE_STOCK_MIN_RULE
        ) {
            $context->buildViolation(
                'Cost is less than '
                .FileDataValidator::PRODUCT_RULE_MIN_COST
                .' and stock less than '
                .FileDataValidator::PRODUCT_RULE_STOCK_MIN_RULE
            )
                ->atPath('stock')
                ->addViolation();

            $context->buildViolation("")
                ->atPath('cost')
                ->addViolation();
        }
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
