<?php

namespace App\Domain;

use App\Reward\RewardStatus;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity() */
class Reward
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;
    
    /** @ORM\Column(name="product_code", type="string") */
    public string $productCode;
    /** @ORM\Column(name="product_name", type="string") */
    public string $productName;
    /** @ORM\Column(name="category", type="string") */
    public string $category;
    /** @ORM\Column(name="product_description", type="string", nullable=true) */
    private ?string $productDescription;
    /** @ORM\Column(name="status", type=RewardStatus::class, length=18) */
    private RewardStatus $status;
    /** @ORM\Column(name="image", type="string") */
    private string $image;
    /** @ORM\Column(type="decimal") */
    private float $price;
    /** @ORM\Column(type="decimal") */
    private float $tax;
    /** @ORM\Column(type="decimal") */
    private float $insurance;
    /** @ORM\Column(type="decimal") */
    private float $commission;
    /** @ORM\Column(name="delivery_charges", type="decimal") */
    private float $deliveryCharges;
    /** @ORM\Column(type="integer") */
     private int $credits;
    /** @ORM\Column(name="created_at", type="datetime") */
    private \DateTime $createdAt;
    /** @ORM\Column(name="updated_at", type="datetime") */
    private \DateTime $updatedAt;
    
    public function __construct(string $productCode, string $productName, string $category, string $productDescription, float $price,
    float $tax, float $insurance, float $commission, float $deliveryCharges, int $credits, string $image)
    {
        $this->productCode = $productCode;
        $this->productName = $productName;
        $this->category = $category;
        $this->productDescription = $productDescription;
        $this->status = RewardStatus::ACTIVE();
        $this->image = $image;
        $this->price = $price;
        $this->tax = $tax;
        $this->insurance = $insurance;
        $this->commission = $commission;
        $this->deliveryCharges = $deliveryCharges;
        $this->credits = $credits;
        $this->createdAt = Carbon::now();
        $this->updatedAt = Carbon::now();
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getProductCode(): string
    {
        return $this->productCode;
    }
    public function setProductCode($productCode)
    {
        $this->productCode = $productCode;
    }
    public function getProductName(): string
    {
        return $this->productName;
    }
    public function setProductName($productName)
    {
        $this->productName = $productName;
    
    }
    public function getCategory(): string
    {
        return $this->category;
    }    
    public function setCategory($category)
    {
        $this->category = $category;
    }    
    public function getProductDescription(): ?string
    {
        return $this->productDescription;
    }
    public function setProductDescription($productDescription)
    {
        $this->productDescription = $productDescription;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function setStatus($status)
    {
        if ($status == RewardStatus::INACTIVE()) {
            $this->status = RewardStatus::INACTIVE();
        } else {
            $this->status = RewardStatus::ACTIVE();
        }
        
    }
    public function getImage(): string
    {
        return $this->image;
    }
    public function setImage($image)
    {
        $this->image = $image;
    }
    public function getPrice(): float
    {
        return $this->price;
    }
    public function setPrice($price)
    {
        $this->price = $price;
    }
    public function getTax(): float
    {
        return $this->tax;
    }
    public function setTax($tax)
    {
        $this->tax = $tax;
    }
    public function getInsurance(): float
    {
        return $this->insurance;
    }
    public function setInsurance($insurance)
    {
        $this->insurance = $insurance;
    }
    public function getCommission(): float
    {
        return $this->commission;
    }
    public function setCommission($commission)
    {
        $this->commission = $commission;
    }
    public function getDeliveryCharges(): float
    {
        return $this->deliveryCharges;
    }
    public function setDeliveryCharges($deliveryCharges)
    {
        $this->deliveryCharges = $deliveryCharges;
    }
    public function getCredits(): int
    {
        return $this->credits;
    }
    public function setCredits($credits)
    {
        $this->credits = $credits;
    }
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }
    public function getCategoryDescription() {
        switch ($this->category) {
            case "CASH_CRYPTO":
                return "Cash & Crypto";
            case "SPORTS":
                return "Sports";
            case "ELECTRONICS":
                return "Electronics";
            case "ACCESSORIES":
                return "Accessories";
            case "TRAVEL":
                return "Travel";
            case "HOME_LEISURE":
                return "Home & Leisureessories";
            default:
                return null;
        }
    }
}
