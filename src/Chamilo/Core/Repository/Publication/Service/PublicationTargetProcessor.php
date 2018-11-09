<?php
namespace Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Repository\Publication\Manager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @package Chamilo\Core\Repository\Publication\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationTargetProcessor
{
    /**
     * @var \Chamilo\Core\Repository\Publication\Service\PublicationTargetService
     */
    private $publicationTargetService;

    /**
     * @param \Chamilo\Core\Repository\Publication\Service\PublicationTargetService $publicationTargetService
     */
    public function __construct(PublicationTargetService $publicationTargetService)
    {
        $this->publicationTargetService = $publicationTargetService;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param string $modifierServiceIdentifier
     *
     * @return \Chamilo\Core\Repository\Publication\Service\PublicationModifierInterface
     */
    protected function getModifierService(ContainerInterface $container, string $modifierServiceIdentifier)
    {
        return $container->get($modifierServiceIdentifier);
    }

    /**
     * @return \Chamilo\Core\Repository\Publication\Service\PublicationTargetService
     */
    public function getPublicationTargetService(): PublicationTargetService
    {
        return $this->publicationTargetService;
    }

    /**
     * @param \Chamilo\Core\Repository\Publication\Service\PublicationTargetService $publicationTargetService
     */
    public function setPublicationTargetService(PublicationTargetService $publicationTargetService): void
    {
        $this->publicationTargetService = $publicationTargetService;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param array $contentObjects
     * @param array $selectedTargetValues
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function processSelectedTargetsFromValues(
        ContainerInterface $container, array $contentObjects, array $selectedTargetValues
    )
    {
        foreach ($selectedTargetValues as $modifierServiceKey => $targetData)
        {
            $modifierServiceIdentifier =
                $this->getPublicationTargetService()->getModifierServiceIdentifier($modifierServiceKey);

            $modifierService = $this->getModifierService($container, $modifierServiceIdentifier);
            $publicationOptions = $targetData[Manager::WIZARD_OPTION];

            foreach ($targetData[Manager::WIZARD_TARGET] as $publicationTargetKey => $selected)
            {
                $publicationTarget = $this->getPublicationTargetService()->getPublicationTarget($publicationTargetKey);

                foreach ($contentObjects as $contentObject)
                {
                    $modifierService->publishContentObject($contentObject, $publicationTarget, $publicationOptions);
                }
            }
        }
    }
}