<?php
namespace App\Http\Controllers\Backstage\View;

use App\Betting\TimeStatus;
use App\Domain\Reward;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManager;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use JavaScript;

class PrizeController extends Controller
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function index()
    {
        JavaScript::put([
        ]);
        $rewards = $this->entityManager->getRepository(Reward::class)->findAll();

        return view('backstage.prizes.index')
            ->with('prizes', $rewards)
            ->with('prize', null);
    }

    public function show(String $id)
    {
        $reward = $this->entityManager->getRepository(Reward::class)->find($id);
        JavaScript::put([
            'productCode' => $reward->getProductCode(),
            'productName' => $reward->getProductName(),
            'category' => $reward->getCategory(),
            'productDescription' => $reward->getProductDescription(),
            'status' => $reward->getStatus(),
            'price' => $reward->getPrice(),
            'tax' => $reward->getTax(),
            'insurance' => $reward->getInsurance(),
            'commission' => $reward->getCommission(),
            'deliveryCharges' => $reward->getDeliveryCharges(),
            'credits' => $reward->getCredits(),
            'image' => $reward->getImage(),
        ]);

        return view('backstage.prizes.show')
            ->with('prize', $reward);
    }

    public function edit(String $id)
    {
        // JavaScript::put([
        //     'name' => $admin->name,
        // ]);
        $reward = $this->entityManager->getRepository(Reward::class)->find($id);
        JavaScript::put([
            'productCode' => $reward->getProductCode(),
            'productName' => $reward->getProductName(),
            'category' => $reward->getCategory(),
            'productDescription' => $reward->getProductDescription(),
            'status' => $reward->getStatus(),
            'price' => $reward->getPrice(),
            'tax' => $reward->getTax(),
            'insurance' => $reward->getInsurance(),
            'commission' => $reward->getCommission(),
            'deliveryCharges' => $reward->getDeliveryCharges(),
            'credits' => $reward->getCredits(),
            'image' => $reward->getImage(),
        ]);
        return view('backstage.prizes.edit')
            ->with('prize', $reward);
    }

    public function create(Request $request)
    {
        JavaScript::put([
            'productCode' => "",
            'productName' => "",
            'category' => "",
            'productDescription' => "",
            'status' => "Active",
            'price' => 0,
            'tax' => 0,
            'insurance' => 0,
            'commission' => 0,
            'deliveryCharges' => 0,
            'credits' => 0,
            'image' => "",
            'file' => null,
        ]);

        return view('backstage.prizes.create')
            ->with('prizes', null)
            ->with('prize', null);
    }

    public function store(Request $request, Dispatcher $dispatcher) {
        
        $fields = json_decode($request->fields);
        if($request->hasFile('file')) {
            $file = $request->file;
        }

        $productCode = $fields->productCode;
        $productName = $fields->productName;
        $category = $fields->category;
        $productDescription = $fields->productDescription;
        $status = $fields->status;
        $price = $fields->price;
        $tax = $fields->tax;
        $insurance = $fields->insurance;
        $commission = $fields->commission;
        $deliveryCharges = $fields->deliveryCharges;
        $credits = $fields->credits;
        $image = $fields->image;

        if ($file != null) {
            $fileName = $file->getClientOriginalName();
            $fileFullPath = 'prizes/'.$fileName;
            if (env('STORAGE_DISK') === "GCS") {
                $disk = Storage::disk('gcs');
            } else {
                $disk = Storage::disk('public');
            }
            if ($disk->exists($fileFullPath)) {
                $disk->delete($fileFullPath);
            }
         
            $filepath =$disk->put($fileFullPath, file_get_contents($file));
         }

        $reward = new Reward($productCode, $productName, $category, $productDescription, $price, $tax, $insurance, $commission, $deliveryCharges, $credits,  $image);
        $reward->setStatus($status);

        $this->entityManager->persist($reward);
        $this->entityManager->flush();

        return 'Data Saved Successfully';
    }

    public function update(Request $request, String $rewardId)
    {
        $fields = json_decode($request->fields);
        $file = $request->file;
        $reward = $this->entityManager->getRepository(Reward::class)->find($rewardId);
        $reward->setProductCode( $fields->productCode);
        $reward->setProductName( $fields->productName);
        $reward->setCategory( $fields->category);
        $reward->setProductDescription($fields->productDescription);
        $reward->setImage($fields->image);
        $reward->setStatus($fields->status);
        $reward->setPrice($fields->price);
        $reward->setTax($fields->tax);
        $reward->setInsurance($fields->insurance);
        $reward->setCommission($fields->commission);
        $reward->setDeliveryCharges($fields->deliveryCharges);
        $reward->setCredits($fields->credits);
        
        if ($file != null) {
           $fileName = $file->getClientOriginalName();
           $fileFullPath = 'prizes/'.$fileName;
           if (env('STORAGE_DISK') == "GCS") {
               $disk = Storage::disk('gcs');
           } else {
               $disk = Storage::disk('public');
           }
           
           if ($disk->exists($fileFullPath)) {
               $disk->delete($fileFullPath);
           }
        
           $filepath =$disk->put($fileFullPath, file_get_contents($file));
        }

        $this->entityManager->persist($reward);
        $this->entityManager->flush();

        return 'Data Updated Successfully';
    }
}
