<?php

declare(strict_types=1);

namespace App\Entity;

use App\Form\DataTransferObject\ProductDTO;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * Product.
 * @ORM\Table(name="tblProductData", uniqueConstraints={@ORM\UniqueConstraint(name="tblProductData", columns={"intProductDataId"})})
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    /**
     * @var int
     * @ORM\Column(name="intProductDataId", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="strProductName", type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="strProductDesc", type="string", length=255, nullable=false)
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(name="strProductCode", type="string", length=10, nullable=false, unique=true)
     */
    private $code;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="dtmAdded", type="datetime", nullable=true)
     */
    private $addedAt;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="dtmDiscontinued", type="datetime", nullable=true)
     */
    private $discontinuedAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="stmTimestamp", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $timestamp;

    /**
     * @ORM\Column(name="intStock", type="integer", nullable=true)
     *
     * @var int
     */
    private $stock;

    /**
     * @ORM\Column(name="intCostInGBP", type="integer")
     *
     * @var int
     */
    private $cost;

    /**
     * @param object $productDTO
     * @return Product
     * @throws Exception
     */
    public static function createProductFromDTO(object $productDTO):? Product
    {
        if ($productDTO->getStock() === 0) {
            $isDiscontinued = true;
        } else {
            $isDiscontinued = ($productDTO->isDiscontinued() === true)? true : false;
        }

        return new Product(
            $productDTO->getName(),
            $productDTO->getDescription(),
            $productDTO->getCode(),
            $productDTO->getStock(),
            $productDTO->getCost(),
            $isDiscontinued
        );
    }

    /**
     * @param Product $product
     * @param ProductDTO $productDTO
     * @throws Exception
     */
    public static function updateProductFromDTO(Product $product, ProductDTO $productDTO): void
    {
        $product->setName($productDTO->getName());
        $product->setDescription($productDTO->getDescription());
        $product->setCode($productDTO->getCode());
        $product->setCost($productDTO->getCost());
        $product->setStock($productDTO->getStock());

        if (true === $productDTO->isDiscontinued() || 0 === $productDTO->getStock()) {
            if (null === $product->getDiscontinuedAt()) {
                $product->setDiscontinuedAt(new \DateTime());
            }
        } else {
            $product->setDiscontinuedAt(null);
        }
    }

    /**
     * Product constructor.
     *
     * @param string $name
     * @param string $description
     * @param string $code
     * @param int $stock
     * @param int $cost
     * @param bool $isDiscontinued
     * @throws Exception
     */
    public function __construct(
        string $name,
        string $description,
        string $code,
        int $stock,
        int $cost,
        bool $isDiscontinued
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->code = $code;
        $this->addedAt = new \DateTime();
        $this->timestamp = new \DateTime();
        $this->stock = $stock;
        $this->cost = $cost;

        if (true === $isDiscontinued) {
            $this->discontinuedAt = new \DateTime();
        } else {
            $this->discontinuedAt = null;
        }
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
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
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
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
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
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
     * @return $this
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getAddedAt(): ?\DateTimeInterface
    {
        return $this->addedAt;
    }

    /**
     * @param \DateTimeInterface|null $addedAt
     * @return $this
     */
    public function setAddedAt(?\DateTimeInterface $addedAt): self
    {
        $this->addedAt = $addedAt;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDiscontinuedAt(): ?\DateTimeInterface
    {
        return $this->discontinuedAt;
    }

    /**
     * @param \DateTimeInterface|null $discontinuedAt
     * @return $this
     */
    public function setDiscontinuedAt(?\DateTimeInterface $discontinuedAt): self
    {
        $this->discontinuedAt = $discontinuedAt;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    /**
     * @param \DateTimeInterface $timestamp
     * @return $this
     */
    public function setTimestamp(\DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getStock(): ?int
    {
        return $this->stock;
    }

    /**
     * @param int|null $Stock
     * @return $this
     */
    public function setStock(?int $Stock): self
    {
        $this->stock = $Stock;

        return $this;
    }

    public function getCost(): ?int
    {
        return $this->cost;
    }

    /**
     * @param int $Cost
     * @return $this
     */
    public function setCost(int $Cost): self
    {
        $this->cost = $Cost;

        return $this;
    }
}
