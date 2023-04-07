<?php
namespace Chamilo\Core\Admin\Announcement\Integration\Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Admin\Announcement\Service\PublicationService;
use Chamilo\Core\Repository\ContentObject\SystemAnnouncement\Storage\DataClass\SystemAnnouncement;
use Chamilo\Core\Repository\Publication\Domain\PublicationTarget;
use Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface;
use Chamilo\Core\Repository\Publication\Service\PublicationTargetRenderer;
use Chamilo\Core\Repository\Publication\Service\PublicationTargetService;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Admin\Announcement\Integration\Chamilo\Core\Repository\Publication\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationAggregator implements PublicationAggregatorInterface
{
    private PublicationAttributesGenerator $publicationAttributesGenerator;

    private PublicationService $publicationService;

    private PublicationTargetRenderer $publicationTargetRenderer;

    private PublicationTargetService $publicationTargetService;

    private Translator $translator;

    public function __construct(
        PublicationService $publicationService, Translator $translator,
        PublicationAttributesGenerator $publicationAttributesGenerator,
        PublicationTargetService $publicationTargetService, PublicationTargetRenderer $publicationTargetRenderer
    )
    {
        $this->publicationService = $publicationService;
        $this->translator = $translator;
        $this->publicationAttributesGenerator = $publicationAttributesGenerator;
        $this->publicationTargetService = $publicationTargetService;
        $this->publicationTargetRenderer = $publicationTargetRenderer;
    }

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function addPublicationTargetsToFormForContentObjectAndUser(
        FormValidator $form, ContentObject $contentObject, User $user
    )
    {
        if ($user->is_platform_admin() && $contentObject instanceof SystemAnnouncement)
        {
            $publicationTargetKey = $this->getPublicationTargetService()->addPublicationTargetAndGetKey(
                new PublicationTarget(PublicationModifier::class)
            );
            $modifierServiceKey =
                $this->getPublicationTargetService()->addModifierServiceIdentifierAndGetKey(PublicationModifier::class);

            $publicationTargetName = $this->getTranslator()->trans('TypeName', [], 'Chamilo\Core\Admin\Announcement');

            $this->getPublicationTargetRenderer()->addSinglePublicationTargetToForm(
                $form, $modifierServiceKey, $publicationTargetKey, $publicationTargetName
            );
        }
    }

    /**
     * @param int[] $contentObjectIdentifiers
     */
    public function areContentObjectsPublished(array $contentObjectIdentifiers): bool
    {
        $publicationCount =
            $this->getPublicationService()->countPublicationsForContentObjectIdentifiers($contentObjectIdentifiers);

        return $publicationCount > 0;
    }

    public function canContentObjectBeEdited(int $contentObjectIdentifier): bool
    {
        return true;
    }

    public function canContentObjectBeUnlinked(ContentObject $contentObject): bool
    {
        return true;
    }

    public function countPublicationAttributes(
        int $type, int $objectIdentifier, ?Condition $condition = null
    ): int
    {
        return $this->getPublicationService()->countPublicationsForTypeAndIdentifier(
            $type, $objectIdentifier, $condition
        );
    }

    public function deleteContentObjectPublications(ContentObject $contentObject): bool
    {
        return $this->getPublicationService()->deletePublicationsForContentObject($contentObject);
    }

    /**
     * @param int $type
     * @param int $objectIdentifier
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param ?int $count
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes>
     */
    public function getContentObjectPublicationsAttributes(
        int $type, int $objectIdentifier, ?Condition $condition = null, ?int $count = null, ?int $offset = null,
        ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $publicationRecords = $this->getPublicationService()->findPublicationRecordsForTypeAndIdentifier(
            $type, $objectIdentifier, $condition, $count, $offset, $orderBy
        );

        $publicationAttributes = [];

        foreach ($publicationRecords as $publicationRecord)
        {
            $publicationAttributes[] =
                $this->getPublicationAttributesGenerator()->createAttributesFromRecord($publicationRecord);
        }

        return new ArrayCollection($publicationAttributes);
    }

    public function getPublicationAttributesGenerator(): PublicationAttributesGenerator
    {
        return $this->publicationAttributesGenerator;
    }

    public function getPublicationService(): PublicationService
    {
        return $this->publicationService;
    }

    public function getPublicationTargetRenderer(): PublicationTargetRenderer
    {
        return $this->publicationTargetRenderer;
    }

    public function getPublicationTargetService(): PublicationTargetService
    {
        return $this->publicationTargetService;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function isContentObjectPublished(int $contentObjectIdentifier): bool
    {
        $publicationCount =
            $this->getPublicationService()->countPublicationsForContentObjectIdentifier($contentObjectIdentifier);

        return $publicationCount > 0;
    }

    public function setPublicationAttributesGenerator(PublicationAttributesGenerator $publicationAttributesGenerator
    ): void
    {
        $this->publicationAttributesGenerator = $publicationAttributesGenerator;
    }

    public function setPublicationService(PublicationService $publicationService): void
    {
        $this->publicationService = $publicationService;
    }

    public function setPublicationTargetRenderer(PublicationTargetRenderer $publicationTargetRenderer): void
    {
        $this->publicationTargetRenderer = $publicationTargetRenderer;
    }

    public function setPublicationTargetService(PublicationTargetService $publicationTargetService): void
    {
        $this->publicationTargetService = $publicationTargetService;
    }

    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }
}