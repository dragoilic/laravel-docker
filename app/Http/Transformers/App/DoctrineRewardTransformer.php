<?php
namespace App\Http\Transformers\App;

use App\Domain\Reward;
use App\Reward\RewardStatus;
use League\Fractal\TransformerAbstract;

class DoctrineRewardTransformer extends TransformerAbstract
{
    public function transform(Reward $reward)
    {
        return [
            "productCode" => $reward->getProductCode(),
            "productName" => $reward->getProductName(),
            "productDescription" => $reward->getProductDescription(),
            "category" => $reward->getCategoryDescription(),
            "price" => $reward->getPrice(),
            "tax" => $reward->getTax(),
            "insurance" => $reward->getInsurance(),
            "deliveryCharges" => $reward->getDeliveryCharges(),
            "commission" => $reward->getCommission(),
            "credits" => $reward->getCredits(),
            "image" => $reward->getImage(),
            "image_path" => env('IMAGE_PATH') . $reward->getImage(),
        ];
    }
}
