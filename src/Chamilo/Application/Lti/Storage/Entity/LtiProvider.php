<?php

namespace Chamilo\Application\Lti\Storage\Entity;

use IMSGlobal\LTI\OAuth\OAuthConsumer;
use Doctrine\ORM\Mapping as ORM;

/**
 * @package Chamilo\Application\Lti\Domain
 * @author - Sven Vanpoucke - Hogeschool Gent
 *
 * @ORM\Entity(repositoryClass="Chamilo\Application\Lti\Storage\Repository\LtiProviderRepository")
 * @ORM\Table(
 *     name="lti_provider",
 *     indexes={
 *          @ORM\Index(name="lp_uuid", columns={"uuid"})
 *     }
 * )
 */
class LtiProvider
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=50)
     */
    protected $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="lti_url", type="string", length=255)
     */
    protected $ltiUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="consumer_key", type="string", length=255)
     */
    protected $key;

    /**
     * @var string
     *
     * @ORM\Column(name="consumer_secret", type="string", length=255)
     */
    protected $secret;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     */
    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @param string $ltiUrl
     */
    public function setLtiUrl(string $ltiUrl): void
    {
        $this->ltiUrl = $ltiUrl;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * @param string $secret
     */
    public function setSecret(string $secret): void
    {
        $this->secret = $secret;
    }


    /**
     * @return string
     */
    public function getLtiUrl(): string
    {
        return $this->ltiUrl;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * Transforms the application to an OAuth consumer for further use
     *
     * @return \IMSGlobal\LTI\OAuth\OAuthConsumer
     */
    public function toOAuthConsumer()
    {
        return new OAuthConsumer($this->getKey(), $this->getSecret());
    }
}