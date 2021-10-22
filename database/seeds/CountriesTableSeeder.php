<?php

use App\Domain\Country;
use Doctrine\ORM\EntityManager;
use Illuminate\Database\Seeder;

class CountriesTableSeeder extends Seeder
{
    
    private array $countries = array(
        array('code' => 'USA','name' => 'UNITED STATES', 'phone_code' => '1'),
        array('code' => 'CAN','name' => 'CANADA', 'phone_code' => '1')
    );
    
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function run()
    {
        $existing_countries = $this->entityManager->getRepository(Country::class)->findBy(array('code' => 'USA'));

        if (count($existing_countries)) {
            return;
        }
        foreach ($this->countries as $country) {
            $this->entityManager->persist(Country::create($country['code'], $country['name'], $country['phone_code']));
        }
        $this->entityManager->flush();
    }
}