<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Service\Publication;

use Chamilo\Application\Calendar\Extension\Personal\Manager as PersonalCalendarManager;
use Chamilo\Application\Calendar\Extension\Personal\Service\PublicationService;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Core\Repository\Publication\Domain\PublicationResult;
use Chamilo\Core\Repository\Publication\Domain\PublicationTarget;
use Chamilo\Core\Repository\Publication\Service\PublicationModifierInterface;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Form\FormValidator;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Calendar\Extension\Personal\Service\Publication
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationModifier implements PublicationModifierInterface
{

    protected UrlGenerator $urlGenerator;

    private PublicationAttributesGenerator $publicationAttributesGenerator;

    private PublicationService $publicationService;

    private Translator $translator;

    public function __construct(
        PublicationService $publicationService, PublicationAttributesGenerator $publicationAttributesGenerator,
        Translator $translator, UrlGenerator $urlGenerator
    )
    {
        $this->publicationService = $publicationService;
        $this->publicationAttributesGenerator = $publicationAttributesGenerator;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
    }

    public function addContentObjectPublicationAttributesElementsToForm(FormValidator $formValidator)
    {
    }

    public function deleteContentObjectPublication(string $publicationIdentifier): bool
    {
        return $this->getPublicationService()->deletePublicationByIdentifier($publicationIdentifier);
    }

    /**
     * @throws \Exception
     */
    public function getContentObjectPublicationAttributes(string $publicationIdentifier): Attributes
    {
        return $this->getPublicationAttributesGenerator()->createAttributesFromRecord(
            $this->getPublicationService()->findPublicationRecordByIdentifier($publicationIdentifier)
        );
    }

    public function getPublicationAttributesGenerator(): PublicationAttributesGenerator
    {
        return $this->publicationAttributesGenerator;
    }

    public function getPublicationService(): PublicationService
    {
        return $this->publicationService;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function publishContentObject(
        ContentObject $contentObject, PublicationTarget $publicationTarget, array $options = []
    ): PublicationResult
    {
        $publication = $this->getPublicationService()->getPublicationInstance();
        $publication->set_content_object_id($contentObject->getId());
        $publication->set_publisher($publicationTarget->getUserIdentifier());

        if (!$this->getPublicationService()->createPublication($publication))
        {
            $failureMessage = $this->getTranslator()->trans(
                'PublicationFailure', ['%CONTENT_OBJECT%' => $contentObject->get_title()],
                'Chamilo\Application\Calendar\Extension\Personal'
            );

            return new PublicationResult(PublicationResult::STATUS_FAILURE, $failureMessage);
        }
        else
        {
            $successMessage = $this->getTranslator()->trans(
                'PublicationSuccess', ['%CONTENT_OBJECT%' => $contentObject->get_title()],
                'Chamilo\Application\Calendar\Extension\Personal'
            );

            $publicationUrl = $this->getUrlGenerator()->fromParameters(
                [
                    Application::PARAM_CONTEXT => PersonalCalendarManager::CONTEXT,
                    Application::PARAM_ACTION => PersonalCalendarManager::ACTION_VIEW,
                    PersonalCalendarManager::PARAM_PUBLICATION_ID => $publication->getId()
                ]
            );

            return new PublicationResult(
                PublicationResult::STATUS_SUCCESS, $successMessage, $publicationUrl
            );
        }
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

    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    public function updateContentObjectPublicationContentObjectIdentifier(Attributes $publicationAttributes): bool
    {
        $publication =
            $this->getPublicationService()->findPublicationByIdentifier((int) $publicationAttributes->getId());

        if ($publication instanceof Publication)
        {
            $publication->set_content_object_id($publicationAttributes->get_content_object_id());

            return $this->getPublicationService()->updatePublication($publication);
        }
        else
        {
            return false;
        }
    }
}