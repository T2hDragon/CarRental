<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\CarSorting\CarInfoHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class ByByLocationController extends AbstractController
{
    public function __construct(
        private CarInfoHelper $carInfoHelper,
    )
    {
    }

    #[Route('/best-price', methods: ['GET'], name: 'best-price')]
    public function bestPrice(Request $request): JsonResponse {
        $iata = $request->get('iata');
        $pickUpDateTime = $request->get('pickUpDateTime');
        $dropOffDateTime = $request->get('dropOffDateTime');
        $contents = $this->carInfoHelper->getCheapestCars($iata, $pickUpDateTime, $dropOffDateTime);
        $response = new JsonResponse();
        $response->setContent(json_encode($contents));
        return $response;
    }
}
