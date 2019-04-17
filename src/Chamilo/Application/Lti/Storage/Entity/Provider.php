<?php

namespace Chamilo\Application\Lti\Storage\Entity;

use Chamilo\Application\Lti\Domain\Provider\ProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @package Chamilo\Application\Lti\Domain
 * @author - Sven Vanpoucke - Hogeschool Gent
 *
 * @ORM\Entity(repositoryClass="Chamilo\Application\Lti\Storage\Repository\ProviderRepository")
 * @ORM\Table(
 *     name="lti_provider",
 *     indexes={
 *          @ORM\Index(name="lp_uuid", columns={"uuid"})
 *     }
 * )
 */
class Provider implements ProviderInterface
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
    protected $launchUrl;

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
     * @var \Chamilo\Application\Lti\Storage\Entity\ProviderCustomParameter[] | \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\Chamilo\Application\Lti\Storage\Entity\ProviderCustomParameter", mappedBy="provider")
     */
    protected $customParameters;

    /**
     * Provider constructor.
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
     * @param string $launchUrl
     */
    public function setLaunchUrl(string $launchUrl): void
    {
        $this->launchUrl = $launchUrl;
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
    public function getLaunchUrl(): ?string
    {
        return $this->launchUrl;
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
     * @return \Chamilo\Application\Lti\Storage\Entity\ProviderCustomParameter[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getCustomParameters()
    {
        return $this->customParameters;
    }

    /**
     * @param \Chamilo\Application\Lti\Storage\Entity\ProviderCustomParameter[]|\Doctrine\Common\Collections\ArrayCollection $customParameters
     */
    public function setCustomParameters($customParameters): void
    {
        $this->customParameters = $customParameters;
    }

    /**
     * @param \Chamilo\Application\Lti\Storage\Entity\ProviderCustomParameter $providerCustomParameter
     */
    public function addCustomParameter(ProviderCustomParameter $providerCustomParameter)
    {
        $this->customParameters->add($providerCustomParameter);
        $providerCustomParameter->setProvider($this);
    }

    /**
     * @param \Chamilo\Application\Lti\Storage\Entity\ProviderCustomParameter $providerCustomParameter
     */
    public function removeCustomParameter(ProviderCustomParameter $providerCustomParameter)
    {
        $this->customParameters->removeElement($providerCustomParameter);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function cloneCustomParameters()
    {
        $clonedCustomParameters = new ArrayCollection();
        foreach($this->customParameters as $customParameter)
        {
            $clonedCustomParameters->add($customParameter);
        }

        return $clonedCustomParameters;
    }

    /**
     * @return string
     */
    public function getUniqueId()
    {
        return $this->getUuid();
    }
}