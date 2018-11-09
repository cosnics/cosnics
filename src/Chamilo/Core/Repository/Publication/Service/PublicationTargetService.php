<?php
namespace Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Repository\Publication\Domain\PublicationTarget;
use Chamilo\Core\Repository\Publication\Storage\Repository\PublicationTargetRepository;

/**
 * @package Chamilo\Core\Repository\Publication\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationTargetService
{
    /**
     * @var \Chamilo\Core\Repository\Publication\Storage\Repository\PublicationTargetRepository
     */
    private $publicationTargetRepository;

    /**
     * @param \Chamilo\Core\Repository\Publication\Storage\Repository\PublicationTargetRepository $publicationTargetRepository
     */
    public function __construct(PublicationTargetRepository $publicationTargetRepository)
    {
        $this->publicationTargetRepository = $publicationTargetRepository;
    }

    /**
     * @param string $modifierServiceIdentifier
     *
     * @return string
     */
    public function addModifierServiceIdentifierAndGetKey(string $modifierServiceIdentifier)
    {
        $modifierServiceIdentifierKey = $this->determineModifierServiceIdentifierKey($modifierServiceIdentifier);
        $this->addModifierServiceIdentifierForKey($modifierServiceIdentifierKey, $modifierServiceIdentifier);

        return $modifierServiceIdentifierKey;
    }

    /**
     * @param string $modifierServiceIdentifierKey
     * @param string $modifierServiceIdentifier
     *
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function addModifierServiceIdentifierForKey(
        string $modifierServiceIdentifierKey, string $modifierServiceIdentifier
    )
    {
        return $this->getPublicationTargetRepository()->addModifierServiceIdentifier(
            $modifierServiceIdentifierKey, $modifierServiceIdentifier
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Publication\Domain\PublicationTarget $publicationTarget
     *
     * @return string
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function addPublicationTargetAndGetKey(PublicationTarget $publicationTarget)
    {
        $publicationTargetKey = $this->determinePublicationTargetKey($publicationTarget);
        $this->addPublicationTargetForKey($publicationTargetKey, $publicationTarget);

        return $publicationTargetKey;
    }

    /**
     * @param string $publicationTargetKey
     * @param \Chamilo\Core\Repository\Publication\Domain\PublicationTarget $publicationTarget
     *
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function addPublicationTargetForKey(string $publicationTargetKey, PublicationTarget $publicationTarget)
    {
        return $this->getPublicationTargetRepository()->addPublicationTarget(
            $publicationTargetKey, $publicationTarget
        );
    }

    /**
     * @param string $modifierServiceIdentifier
     *
     * @return string
     */
    protected function determineModifierServiceIdentifierKey(string $modifierServiceIdentifier)
    {
        return md5($modifierServiceIdentifier);
    }

    /**
     * @param \Chamilo\Core\Repository\Publication\Domain\PublicationTarget $publicationTarget
     *
     * @return string
     */
    protected function determinePublicationTargetKey(PublicationTarget $publicationTarget)
    {
        return md5(serialize($publicationTarget));
    }

    /**
     * @param string $key
     *
     * @return \Chamilo\Core\Repository\Publication\Domain\PublicationTarget
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getPublicationTarget(string $key)
    {
        return $this->getPublicationTargetRepository()->getPublicationTarget($key);
    }

    /**
     * @return \Chamilo\Core\Repository\Publication\Storage\Repository\PublicationTargetRepository
     */
    public function getPublicationTargetRepository(): PublicationTargetRepository
    {
        return $this->publicationTargetRepository;
    }

    /**
     * @param \Chamilo\Core\Repository\Publication\Storage\Repository\PublicationTargetRepository $publicationTargetRepository
     */
    public function setPublicationTargetRepository(PublicationTargetRepository $publicationTargetRepository): void
    {
        $this->publicationTargetRepository = $publicationTargetRepository;
    }

}