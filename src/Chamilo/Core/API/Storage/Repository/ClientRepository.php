<?php

namespace Chamilo\Core\API\Storage\Repository;

use Chamilo\Core\API\Entities\Oauth2\Client;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use function password_hash;

class ClientRepository extends \Doctrine\ORM\EntityRepository implements ClientRepositoryInterface
{
    public function getClientEntity($clientIdentifier): ?Client
    {
        return $this->findOneBy(['identifier' => $clientIdentifier]);
    }

    public function createClient($clientIdentifier, $clientSecret, $clientName): Client
    {
        $client = new Client();

        $client->setIdentifier($clientIdentifier);
        $client->setSecret($clientSecret);
        $client->setName($clientName);
        $client->setConfidential();

        $this->_em->persist($client);
        $this->_em->flush();

        return $client;
    }

    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        $client = $this->getClientEntity($clientIdentifier);
        if(!$client instanceof Client)
        {
            return false;
        }

        return $client->isValidPassword($clientSecret);
    }
}