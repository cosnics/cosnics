<?php
namespace Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Repository\Publication\Domain\PublicationTarget;
use Chamilo\Core\Repository\Publication\Storage\Repository\PublicationTargetRepository;

/**
 * @package Chamilo\Core\Repository\Publication\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationTargetService
{
    private PublicationTargetRepository $publicationTargetRepository;

    public function __construct(PublicationTargetRepository $publicationTargetRepository)
    {
        $this->publicationTargetRepository = $publicationTargetRepository;
    }

    public function addModifierServiceIdentifierAndGetKey(string $modifierServiceIdentifier): string
    {
        $modifierServiceIdentifierKey = $this->determineModifierServiceIdentifierKey($modifierServiceIdentifier);
        $this->addModifierServiceIdentifierForKey($modifierServiceIdentifierKey, $modifierServiceIdentifier);

        return $modifierServiceIdentifierKey;
    }

    public function addModifierServiceIdentifierForKey(
        string $modifierServiceIdentifierKey, string $modifierServiceIdentifier
    ): bool
    {
        return $this->getPublicationTargetRepository()->addModifierServiceIdentifier(
            $modifierServiceIdentifierKey, $modifierServiceIdentifier
        );
    }

    public function addPublicationTargetAndGetKey(PublicationTarget $publicationTarget): string
    {
        $publicationTargetKey = $this->determinePublicationTargetKey($publicationTarget);
        $this->addPublicationTargetForKey($publicationTargetKey, $publicationTarget);

        return $publicationTargetKey;
    }

    public function addPublicationTargetForKey(string $publicationTargetKey, PublicationTarget $publicationTarget): bool
    {
        return $this->getPublicationTargetRepository()->addPublicationTarget(
            $publicationTargetKey, $publicationTarget
        );
    }

    protected function determineModifierServiceIdentifierKey(string $modifierServiceIdentifier): string
    {
        return md5($modifierServiceIdentifier);
    }

    protected function determinePublicationTargetKey(PublicationTarget $publicationTarget): string
    {
        return md5(serialize($publicationTarget));
    }

    public function getModifierServiceIdentifier(string $key): string
    {
        return $this->getPublicationTargetRepository()->getModifierServiceIdentifier($key);
    }

    public function getPublicationTarget(string $key): PublicationTarget
    {
        return $this->getPublicationTargetRepository()->getPublicationTarget($key);
    }

    public function getPublicationTargetRepository(): PublicationTargetRepository
    {
        return $this->publicationTargetRepository;
    }

    public function setPublicationTargetRepository(PublicationTargetRepository $publicationTargetRepository): void
    {
        $this->publicationTargetRepository = $publicationTargetRepository;
    }

}