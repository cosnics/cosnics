<?php
namespace Chamilo\Core\Home\Service\Publication;

use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Service\ContentObjectPublicationService;
use Chamilo\Core\Home\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Home\Integration\Chamilo\Core\Repository\Publication\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationAggregator implements PublicationAggregatorInterface
{
    protected ContentObjectPublicationService $contentObjectPublicationService;

    protected Translator $translator;

    public function __construct(ContentObjectPublicationService $contentObjectPublicationService, Translator $translator
    )
    {
        $this->contentObjectPublicationService = $contentObjectPublicationService;
        $this->translator = $translator;
    }

    public function addPublicationTargetsToFormForContentObjectAndUser(
        FormValidator $form, ContentObject $contentObject, User $user
    )
    {
    }

    /**
     * @param int[] $contentObjectIdentifiers
     */
    public function areContentObjectsPublished(array $contentObjectIdentifiers): bool
    {
        return $this->getContentObjectPublicationService()->countContentObjectPublicationsByContentObjectIds(
                $contentObjectIdentifiers
            ) > 0;
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
        switch ($type)
        {
            case PublicationAggregatorInterface::ATTRIBUTES_TYPE_OBJECT :
                return $this->getContentObjectPublicationService()->countContentObjectPublicationsByContentObjectIds(
                    [$objectIdentifier]
                );
            case PublicationAggregatorInterface::ATTRIBUTES_TYPE_USER :
                return $this->getContentObjectPublicationService()
                    ->countContentObjectPublicationsByContentObjectOwnerId(
                        $objectIdentifier
                    );
            default :
                return 0;
        }
    }

    protected function createPublicationAttributesFromPublication(ContentObjectPublication $publication): Attributes
    {
        $attributes = new Attributes();

        $attributes->setId($publication->getId());
        $attributes->set_publisher_id($publication->getContentObject()->get_owner_id());
        $attributes->set_date($publication->getContentObject()->get_creation_date());
        $attributes->set_application(Manager::CONTEXT);
        $attributes->set_location($this->getTranslator()->trans('TypeName', [], Manager::CONTEXT));
        $attributes->set_url('index.php');

        $attributes->set_title($publication->getContentObject()->get_title());
        $attributes->set_content_object_id($publication->get_content_object_id());
        $attributes->setModifierServiceIdentifier(PublicationModifier::class);

        return $attributes;
    }

    public function deleteContentObjectPublications(ContentObject $contentObject): bool
    {
        $this->getContentObjectPublicationService()->deleteContentObjectPublicationsByContentObjectId(
            $contentObject->getId()
        );

        return true;
    }

    public function getContentObjectPublicationService(): ContentObjectPublicationService
    {
        return $this->contentObjectPublicationService;
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
        int $type, int $objectIdentifier, Condition $condition = null, int $count = null, int $offset = null,
        ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $publicationAttributes = new ArrayCollection();

        switch ($type)
        {
            case PublicationAggregatorInterface::ATTRIBUTES_TYPE_OBJECT :
                $publications =
                    $this->getContentObjectPublicationService()->getContentObjectPublicationsByContentObjectId(
                        $objectIdentifier
                    );
                break;
            case PublicationAggregatorInterface::ATTRIBUTES_TYPE_USER :
                $publications =
                    $this->getContentObjectPublicationService()->getContentObjectPublicationsByContentObjectOwnerId(
                        $objectIdentifier
                    );
                break;
            default :
                return $publicationAttributes;
        }

        foreach ($publications as $publication)
        {
            $publicationAttributes->add($this->createPublicationAttributesFromPublication($publication));
        }

        return $publicationAttributes;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function isContentObjectPublished(int $contentObjectIdentifier): bool
    {
        return $this->getContentObjectPublicationService()->countContentObjectPublicationsByContentObjectId(
                $contentObjectIdentifier
            ) > 0;
    }
}