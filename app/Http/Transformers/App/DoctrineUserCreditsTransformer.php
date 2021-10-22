<?php
namespace App\Http\Transformers\App;

use App\Domain\UserCredits;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class DoctrineUserCreditsTransformer extends TransformerAbstract
{
    public function transform(UserCredits $credit)
    {
        return [
            "id" => $credit->getId(),
            "credits" => $credit->getCredits(),
            "reason" => $credit->getReason(),
            "paidDate" => (new Carbon($credit->getPaidDate()))->toAtomString(),
        ];
    }
}
