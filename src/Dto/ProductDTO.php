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
    private ?string $intProductDataId;

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

    public function getIntProductDataId(): ?string
    {
        return $this->intProductDataId;
    }

    public function setIntProductDataId(string $intProductDataId): void
    {
        $this->intProductDataId = $intProductDataId;
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
}