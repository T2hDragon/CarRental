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


use Symfony\Contracts\HttpClient\HttpClientInterface;

class DataRequest
{
    private const REQUEST_URL = 'https://api.carrentalgateway.com/web/availability';

    public function __construct(private HttpClientInterface $client)
    {
    }

    public function getAll(string $location, string $pickupTime, string $dropOffTime): array
    {
        $payload = [
            'lang' => 'en-us',
            'source' => 'wc-test',
            'pickUp' => [
                'dateTime' => $pickupTime,
                'location' => ['iata' => $location],
            ],
            'dropOff' => [
                'dateTime' => $dropOffTime,
                'location' => ['iata' => $location],
            ],
            'driverAge' => 35,
            'residenceCountry' => ['code' => 'US'],
        ];
        $response = $this->client->request(
            'POST',
            self::REQUEST_URL,
            [
                'json' => $payload,
                'auth_basic' => ['wc-test', 'Ul08winZtWf8guIl'],
                'headers' => [
                    'content-type' => 'application/json',
                ],
            ]
        );

        return $response->toArray();
    }
}
