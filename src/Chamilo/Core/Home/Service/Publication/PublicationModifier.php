<?php
namespace Chamilo\Core\Home\Service\Publication;

use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Service\ContentObjectPublicationService;
use Chamilo\Core\Home\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Publication\Domain\PublicationResult;
use Chamilo\Core\Repository\Publication\Domain\PublicationTarget;
use Chamilo\Core\Repository\Publication\Service\PublicationModifierInterface;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Form\FormValidator;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Home\Integration\Chamilo\Core\Repository\Publication\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationModifier implements PublicationModifierInterface
{
    protected ContentObjectPublicationService $contentObjectPublicationService;

    private Translator $translator;

    public function __construct(ContentObjectPublicationService $contentObjectPublicationService, Translator $translator
    )
    {
        $this->translator = $translator;
        $this->contentObjectPublicationService = $contentObjectPublicationService;
    }

    public function addContentObjectPublicationAttributesElementsToForm(FormValidator $formValidator)
    {
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

    public function deleteContentObjectPublication(int $publicationIdentifier): bool
    {
        $this->getContentObjectPublicationService()->deleteContentObjectPublicationById($publicationIdentifier);

        return true;
    }

    public function getContentObjectPublicationAttributes(int $publicationIdentifier): Attributes
    {
        $publication =
            $this->getContentObjectPublicationService()->getContentObjectPublicationById($publicationIdentifier);

        return $this->createPublicationAttributesFromPublication($publication);
    }

    public function getContentObjectPublicationService(): ContentObjectPublicationService
    {
        return $this->contentObjectPublicationService;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function publishContentObject(
        ContentObject $contentObject, PublicationTarget $publicationTarget, array $options = []
    ): PublicationResult
    {
        return new PublicationResult(
            PublicationResult::STATUS_FAILURE, $this->getTranslator()->trans(
            'PublicationImpossible', ['%CONTENT_OBJECT%' => $contentObject->get_title()], 'Chamilo\Core\Home'
        )
        );
    }

    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @throws \Exception
     */
    public function updateContentObjectPublicationContentObjectIdentifier(Attributes $publicationAttributes): bool
    {
        $publication = $this->getContentObjectPublicationService()->getContentObjectPublicationById(
            $publicationAttributes->getId()
        );

        if ($publication instanceof ContentObjectPublication)
        {
            $publication->set_content_object_id($publicationAttributes->get_content_object_id());

            return $publication->update();
        }
        else
        {
            return false;
        }
    }
}