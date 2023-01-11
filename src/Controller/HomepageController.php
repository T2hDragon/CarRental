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

use App\Entity\Person;
use App\Form\ChangePasswordType;
use App\Form\PersonType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Logout\LogoutUrlGenerator;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/')]
class HomepageController extends AbstractController
{

    public function __construct(private HttpClientInterface $client)
    {
    }

    #[Route('/', methods: ['GET'], name: 'homepage')]
    public function homepage(): Response {
        $response = $this->client->request(
            'GET',
            'https://pokeapi.co/api/v2/berry/3',
        );
        $contents = $response->toArray();
        return $this->render('default/homepage.html.twig', [
        ]);
    }
}
