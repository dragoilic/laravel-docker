<?php
namespace App\Http\Transformers\App;

use App\Domain\UserReward;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class DoctrineUserRewardTransformer extends TransformerAbstract
{
    public function transform(UserReward $userReward)
    {
        $reward = $userReward->getReward();
        return [
            "id" => $userReward->getId(),
            "claimedDate" => (new Carbon($userReward->getClaimedDate()))->toAtomString(),
            "productCode" => $reward->getProductCode(),
            "productDescription" => $reward->getProductDescription(),
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
