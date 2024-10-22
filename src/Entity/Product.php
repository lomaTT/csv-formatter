<?php
// src/Entity/Product.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Doctrine\ORM\Mapping\Index;

/**
 * Product entity class mapped to the 'tblProductData' table.
 */
#[ORM\Entity]
#[ORM\Table(name: "tblProductData")]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "intProductDataId", type: "integer", options: ["unsigned" => true, "length" => 10])]
    private ?int $intProductDataId = null;

    #[ORM\Column(name: "strProductCode", type: "string", length: 10, unique: true)]
    private string $strProductCode;

    #[ORM\Column(name: "strProductName", type: "string", length: 50)]
    private string $strProductName;

    #[ORM\Column(name: "strProductDesc", type: "string", length: 255)]
    private string $strProductDesc;

    #[ORM\Column(name: "dtmAdded", type: "datetime", nullable: true)]
    private ?DateTime $dtmAdded = null;

    #[ORM\Column(name: "dtmDiscontinued", type: "datetime", nullable: true)]
    private ?DateTime $dtmDiscontinued = null;

    #[ORM\Column(name: "intStockLevel", type: "integer", nullable: true)]
    private ?int $intStockLevel = null;

    #[ORM\Column(name: "decPrice", type: "decimal", precision: 10, scale: 2, nullable: true)]
    private ?float $decPrice = null;

    #[ORM\Column(name: "stmTimestamp", type: "datetime", nullable: false, options: ["default" => "CURRENT_TIMESTAMP", "update" => "CURRENT_TIMESTAMP"])]
    private DateTime $stmTimestamp;

    public function getIntProductDataId(): ?int
    {
        return $this->intProductDataId;
    }

    public function getStrProductCode(): string
    {
        return $this->strProductCode;
    }

    public function setStrProductCode(string $strProductCode): self
    {
        $this->strProductCode = $strProductCode;
        return $this;
    }

    public function getStrProductName(): string
    {
        return $this->strProductName;
    }

    public function setStrProductName(string $strProductName): self
    {
        $this->strProductName = $strProductName;
        return $this;
    }

    public function getStrProductDesc(): string
    {
        return $this->strProductDesc;
    }

    public function setStrProductDesc(string $strProductDesc): self
    {
        $this->strProductDesc = $strProductDesc;
        return $this;
    }

    public function getDtmAdded(): ?DateTime
    {
        return $this->dtmAdded;
    }

    public function setDtmAdded(?DateTime $dtmAdded): self
    {
        $this->dtmAdded = $dtmAdded;
        return $this;
    }

    public function getDtmDiscontinued(): ?DateTime
    {
        return $this->dtmDiscontinued;
    }

    public function setDtmDiscontinued(?DateTime $dtmDiscontinued): self
    {
        $this->dtmDiscontinued = $dtmDiscontinued;
        return $this;
    }

    public function getIntStockLevel(): ?int
    {
        return $this->intStockLevel;
    }

    public function setIntStockLevel(?int $intStockLevel): self
    {
        $this->intStockLevel = $intStockLevel;
        return $this;
    }

    public function getDecPrice(): ?float
    {
        return $this->decPrice;
    }

    public function setDecPrice(?float $decPrice): self
    {
        $this->decPrice = $decPrice;
        return $this;
    }

    public function getDtmTimestamp(): DateTime
    {
        return $this->stmTimestamp;
    }

    public function setDtmTimestamp(DateTime $stmTimestamp): self
    {
        $this->stmTimestamp = $stmTimestamp;
        return $this;
    }
}
