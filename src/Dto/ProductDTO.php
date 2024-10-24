<?php

namespace App\Dto;

use League\Csv\Serializer\MapCell;
use League\Csv\Serializer;

class ProductDTO
{
    #[MapCell(
        column: 'Product Code',
        convertEmptyStringToNull: true
    )]
    private ?string $strProductCode;

    #[MapCell(
        column: 'Product Name',
        convertEmptyStringToNull: true
    )]
    private ?string $strProductName;

    #[MapCell(
        column: 'Product Description',
        convertEmptyStringToNull: true
    )]
    private ?string $strProductDescription;

    #[MapCell(
        column: 'Stock',
        cast: Serializer\CastToInt::class,
        convertEmptyStringToNull: true
    )]
    private ?int $intStockLevel;

    #[MapCell(
        column: 'Cost in GBP',
        cast: Serializer\CastToFloat::class,
        convertEmptyStringToNull: true,
    )]
    private ?float $decPrice;

    #[MapCell(
        column: 'Discontinued',
        cast: Serializer\CastToString::class,
        convertEmptyStringToNull: true,
    )]
    private ?string $isDiscontinued;

    public function getStrProductCode(): ?string
    {
        return $this->strProductCode;
    }

    public function setStrProductCode(string $strProductCode): void
    {
        $this->strProductCode = $strProductCode;
    }

    public function getStrProductName(): ?string
    {
        return $this->strProductName;
    }

    public function setStrProductName(string $strProductName): void
    {
        $this->strProductName = $strProductName;
    }

    public function getStrProductDescription(): ?string
    {
        return $this->strProductDescription;
    }

    public function setStrProductDescription(string $strProductDescription): void
    {
        $this->strProductDescription = $strProductDescription;
    }

    public function getIntStockLevel(): ?int
    {
        return $this->intStockLevel;
    }

    public function setIntStockLevel(int $intStockLevel): void
    {
        $this->intStockLevel = $intStockLevel;
    }

    public function getDecPrice(): ?float
    {
        return $this->decPrice;
    }

    public function setDecPrice(float $decPrice): void
    {
        $this->decPrice = $decPrice;
    }

    public function getIsDiscontinued(): ?string
    {
        return $this->isDiscontinued;
    }

    public function setIsDiscontinued(string $isDiscontinued): void
    {
        $this->isDiscontinued = $isDiscontinued;
    }

    /**
     * @throws \Exception
     */
    public function validate(): void
    {
        if (!isset($this->strProductCode)) {
            throw new \Exception('Product data id is required');
        }

        if (!isset($this->strProductName) || !is_string($this->strProductName)) {
            throw new \Exception('Problem with product name. Possibly empty or not string.');
        }

        if (!isset($this->strProductDescription) || !is_string($this->strProductDescription)) {
            throw new \Exception('Problem with product description. Possibly empty or not string.');
        }

        if (!isset($this->intStockLevel) || !is_int($this->intStockLevel)) {
            throw new \Exception('Problem with stock level. Possibly empty or not integer.');
        }

        if (!isset($this->decPrice) || !is_float($this->decPrice)) {
            throw new \Exception('Problem with dec_price. Possibly empty or not single decimal.');
        }
    }
}