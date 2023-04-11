<?php
namespace Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Repository\Publication\Manager;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Repository\Publication\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationTargetProcessor
{
    /**
     * @var \Chamilo\Core\Repository\Publication\Service\PublicationModifierInterface[]
     */
    protected array $publicationModifiers;

    private PublicationTargetService $publicationTargetService;

    public function __construct(PublicationTargetService $publicationTargetService)
    {
        $this->publicationTargetService = $publicationTargetService;
        $this->publicationModifiers = [];
    }

    public function addPublicationModifier(PublicationModifierInterface $publicationModifier)
    {
        $this->publicationModifiers[get_class($publicationModifier)] = $publicationModifier;
    }

    public function getPublicationModifier(string $modifierServiceIdentifier): PublicationModifierInterface
    {
        return $this->publicationModifiers[$modifierServiceIdentifier];
    }

    /**
     * @return \Chamilo\Core\Repository\Publication\Service\PublicationModifierInterface[]
     */
    public function getPublicationModifiers(): array
    {
        return $this->publicationModifiers;
    }

    public function getPublicationTargetService(): PublicationTargetService
    {
        return $this->publicationTargetService;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Storage\DataClass\ContentObject> $contentObjects
     * @param array $selectedTargetValues
     *
     * @return \Chamilo\Core\Repository\Publication\Domain\PublicationResult[]
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function processSelectedTargetsFromValues(ArrayCollection $contentObjects, array $selectedTargetValues
    ): array
    {
        $publicationResults = [];

        foreach ($selectedTargetValues as $modifierServiceKey => $targetData)
        {
            $modifierServiceIdentifier =
                $this->getPublicationTargetService()->getModifierServiceIdentifier($modifierServiceKey);

            $publicationModifier = $this->getPublicationModifier($modifierServiceIdentifier);
            $publicationOptions = $targetData[Manager::WIZARD_OPTION];

            foreach ($targetData[Manager::WIZARD_TARGET] as $publicationTargetKey => $selected)
            {
                $publicationTarget = $this->getPublicationTargetService()->getPublicationTarget($publicationTargetKey);

                foreach ($contentObjects as $contentObject)
                {
                    $publicationResults[] = $publicationModifier->publishContentObject(
                        $contentObject, $publicationTarget, $publicationOptions
                    );
                }
            }
        }

        return $publicationResults;
    }

    public function setPublicationTargetService(PublicationTargetService $publicationTargetService): void
    {
        $this->publicationTargetService = $publicationTargetService;
    }
}