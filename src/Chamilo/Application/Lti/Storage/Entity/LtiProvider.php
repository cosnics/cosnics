<?php

namespace Chamilo\Application\Lti\Storage\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

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
     * @var \Chamilo\Application\Lti\Storage\Entity\LtiProviderCustomParameter[] | \Doctrine\Common\Collections\ArrayCollection
     *
     * @OneToMany(targetEntity="\Chamilo\Application\Lti\Storage\Entity\LtiProviderCustomParameter", mappedBy="ltiProvider")
     */
    protected $customParameters;

    /**
     * LtiProvider constructor.
     */
    public function __construct()
    {
        $this->customParameters = new ArrayCollection();
    }

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
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getUuid(): ?string
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
    public function getLtiUrl(): ?string
    {
        return $this->ltiUrl;
    }

    /**
     * @return string
     */
    public function getKey(): ?string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getSecret(): ?string
    {
        return $this->secret;
    }

    /**
     * @return \Chamilo\Application\Lti\Storage\Entity\LtiProviderCustomParameter[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getCustomParameters()
    {
        return $this->customParameters;
    }

    /**
     * @param \Chamilo\Application\Lti\Storage\Entity\LtiProviderCustomParameter[]|\Doctrine\Common\Collections\ArrayCollection $customParameters
     */
    public function setCustomParameters($customParameters): void
    {
        $this->customParameters = $customParameters;
    }

    /**
     * @param \Chamilo\Application\Lti\Storage\Entity\LtiProviderCustomParameter $ltiProviderCustomParameter
     */
    public function addCustomParameter(LtiProviderCustomParameter $ltiProviderCustomParameter)
    {
        $this->customParameters->add($ltiProviderCustomParameter);
    }

    /**
     * Transforms the application to an OAuth consumer for further use
     *
     * @return \IMSGlobal\LTI\OAuth\OAuthConsumer
     */
    public function toOAuthConsumer(): OAuthConsumer
    {
        return new OAuthConsumer($this->getKey(), $this->getSecret());
    }
}