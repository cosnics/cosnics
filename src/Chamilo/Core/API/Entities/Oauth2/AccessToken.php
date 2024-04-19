<?php

namespace Chamilo\Core\API\Entities\Oauth2;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Chamilo\Core\API\Storage\Repository\AccessTokenRepository")
 * @ORM\Table(
 *      name="api_oauth2_access_token",
 *      indexes={@ORM\Index(name="aoc_access_token_identifier", columns={"identifier"})}
 *  )
 */
class AccessToken implements AccessTokenEntityInterface
{
    use AccessTokenTrait;
    use TokenEntityTrait;
    use EntityTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id
     */
    protected int $id;

    /**
     * @var string
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="identifier", type="string", nullable=false)
     */
    protected $identifier;

    /**
     * @var array
     *
     * @ORM\Column(name="scopes", type="json", nullable=true)
     */
    protected $scopes = [];

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="expiry_date_time", type="datetime_immutable", nullable=true)
     */
    protected $expiryDateTime;

    /**
     * @var string|int|null
     *
     * @ORM\Column(name="user_identifier", type="string", nullable=true)
     */
    protected $userIdentifier;

    /**
     * @var ClientEntityInterface
     *
     * @ORM\ManyToOne(targetEntity="Chamilo\Core\API\Entities\Oauth2\Client")
     * @ORM\JoinColumn(name="client_id", nullable=false)
     */
    protected $client;
}