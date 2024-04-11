<?php

namespace App\Controller;

use App\Entity\AccesToken;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use GuzzleHttp\Client;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MostListenedController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }

    #[Route('/mostlistened', name: 'app_most_listened')]
    public function index(): Response {
        $artImg = $artLink = $artistName = "";
        $user = $this->getUser();
        $client = HttpClient::create();

        $accessToken = $this->doctrine->getManager()->getRepository(AccesToken::class)->findOneBy([]);
        if ($accessToken) {
            $token = $accessToken->getAccesToken();
            if ($user) {
                if (!$user->getSpotifyId()) {
                    /*$data = [
                        'grant_type' => 'password',
                        'username' => $user->getSpotifyUsername(),
                        'password' => $user->getSpotifyPassword(),
                        'client_id' => '20dae71bd2824e339248cd93062015be',
                        'client_secret' => '3e981600b5bb4502bbf7cb46dd958c4c',
                    ];
                    $response = $client->request('POST', 'https://accounts.spotify.com/api/token', [
                        'body' => $data,
                    ]);
                    $tokenData = $response->toArray();
                    $accessToken = $tokenData['access_token'];*/
                }
                else {
                    try {
                        if (strpos($user->getSpotifyId(), '/') == true) {
                            $parts = explode('/', $user->getSpotifyId());
                            $user->setSpotifyId(end($parts));
                            $this->doctrine->getManager()->persist($user);
                            $this->doctrine->getManager()->flush();
                        }
                        $url = 'https://api.spotify.com/v1/users/' . $user->getSpotifyId();
                        $client = new Client();
                        $response = $client->request('GET', $url, [
                            'headers' => [
                                'Authorization' => 'Bearer ' . $token,
                                'Accept' => 'application/json',
                            ],
                        ]);

                        if ($response->getStatusCode() === 200) {
                            $responseData = json_decode($response->getBody(), true);
                            $artistName = $responseData['display_name'];
                            $artLink = $responseData['external_urls']['spotify'];
                            try { $artImg = $responseData['images'][1]['url']; }
                            catch(Exception $e) { $artImg = ""; }
                        } else echo "Une erreur s'est produite lors de la récupération des informations de l'utilisateur.";
                    } catch(Exception $e) {
                        echo "error: ".$e;
                        $accessToken->refreshToken();
                        $this->redirectToRoute('app_most_listened');
                    }
                }
            }
        } else {
            $accesToken = new AccesToken();
            $this->doctrine->getManager()->persist($accesToken);
            $this->doctrine->getManager()->flush();
            return $this->redirectToRoute('app_most_listened');
        }
        
        return $this->render('most_listened/index.html.twig', [
            'art_img' => $artImg,
            'art_link' => $artLink,
            'art_name' => $artistName
        ]);
    }
}
