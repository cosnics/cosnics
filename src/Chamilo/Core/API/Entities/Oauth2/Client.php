<?php

namespace Chamilo\Core\API\Entities\Oauth2;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Chamilo\Core\API\Storage\Repository\ClientRepository")
 * @ORM\Table(
 *      name="api_oauth2_client",
 *      indexes={@ORM\Index(name="aoc_client_id", columns={"identifier"})}
 *  )
 */
class Client implements ClientEntityInterface
{
    use EntityTrait;
    use ClientTrait;

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
     * @var string
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="secret", type="string", nullable=false)
     */
    protected string $secret;

    /**
     * @var string
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="redirect_uri", type="string", nullable=true)
     */
    protected $redirectUri;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_confidential", type="boolean")
     */
    protected $isConfidential = false;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Client
    {
        $this->id = $id;
        return $this;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setRedirectUri(string|array $uri): void
    {
        $this->redirectUri = json_encode($uri);
    }

    public function setSecret(string $secret): void
    {
        $this->secret = password_hash($secret, PASSWORD_BCRYPT);
    }

    public function isValidPassword(string $password): bool
    {
        return password_verify($password, $this->secret);
    }

    public function getRedirectUri(): string|array
    {
        return json_decode($this->redirectUri, true);
    }

    public function setConfidential(): void
    {
        $this->isConfidential = true;
    }
}