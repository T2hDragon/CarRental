<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\CarSorting;

use App\Entity\Person;
use App\Form\ChangePasswordType;
use App\Form\PersonType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Logout\LogoutUrlGenerator;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CarInfoHelper
{
    public function __construct(private DataRequest $dataRequest)
    {
    }

    public function getCheapestCars(string $location, string $pickupTime, string $dropOffTime): array
    {
        $data = $this->dataRequest->getAll($location, $pickupTime, $dropOffTime);
        $result = [];
        $supplierData = $data['branches'];
        foreach ($data['rates'] as $carData) {
            $result[] = self::getCar($carData, $supplierData);
        };
        usort($result, static function($a, $b): int
        {
            $aPrice = $a['price'];
            $bPrice = $b['price'];
            if ($aPrice === $bPrice) {
                return 0;
            }
            return ($aPrice < $bPrice) ? -1 : 1;
        });
        return $result;
    }

    private static function getCar($carData, $supplierData): array
    {
        $car = [
            'vehicleName' => null,
            'supplierName' => null,
            'price' => null,
        ];
        $vehicle = $carData['vehicle'];
        $car['vehicleName'] = $vehicle['name'];
        $car['supplierName'] = $supplierData[$carData['pickUpBranchId']]['supplier']['name'];
        $car['price'] = self::getCarLowestPrice($carData['packages']);
        return $car;
    }

    private static function getCarLowestPrice(array $pricesData): float
    {
        $prices = [];
        foreach ($pricesData as $priceData) {
            $prices[] = $priceData['payments']['estimatedTotal']['total']['payment']['amount'];
        }
        return min($prices);
    }

}
