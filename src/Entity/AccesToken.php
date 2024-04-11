<?php

namespace App\Entity;

use App\Repository\AccesTokenRepository;
use Doctrine\ORM\Mapping as ORM;
use GuzzleHttp\Client;

#[ORM\Entity(repositoryClass: AccesTokenRepository::class)]
class AccesToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $acces_token = null;

    #[ORM\Column(length: 255)]
    private ?string $client_id = null;

    #[ORM\Column(length: 255)]
    private ?string $client_secret = null;

    public function __construct() {
        $this->client_id = '20dae71bd2824e339248cd93062015be';
        $this->client_secret = '3e981600b5bb4502bbf7cb46dd958c4c';

        $client = new Client();
        $response = $client->request('POST', 'https://accounts.spotify.com/api/token', [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->client_id . ':' . $this->client_secret)
            ],
            'form_params' => [
                'grant_type' => 'client_credentials'
            ]
        ]);
        if ($response->getStatusCode() === 200)
            $this->acces_token = json_decode($response->getBody(), true)['access_token'];
        else { echo "Une erreur s'est produite lors de la récupération du token."; }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccesToken(): ?string
    {
        return $this->acces_token;
    }

    public function setAccesToken(?string $acces_token): static
    {
        $this->acces_token = $acces_token;

        return $this;
    }

    public function refreshToken(): static {
        /*$client = HttpClient::create();
        $response = $client->request('POST', 'https://accounts.spotify.com/api/token', [
            'body' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->acces_token,
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
            ],
        ]);
        $tokenData = $response->toArray();
        $this->acces_token = $tokenData['access_token'];*/
        return $this;
    }

    public function getClientId(): ?string
    {
        return $this->client_id;
    }

    public function setClientId(string $client_id): static
    {
        $this->client_id = $client_id;

        return $this;
    }

    public function getClientSecret(): ?string
    {
        return $this->client_secret;
    }

    public function setClientSecret(string $client_secret): static
    {
        $this->client_secret = $client_secret;

        return $this;
    }
}
