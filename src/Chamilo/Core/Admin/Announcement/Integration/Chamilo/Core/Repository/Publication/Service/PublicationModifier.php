<?php
namespace Chamilo\Core\Admin\Announcement\Integration\Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Admin\Announcement\Manager;
use Chamilo\Core\Admin\Announcement\Service\PublicationService;
use Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication;
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
 * @package Chamilo\Core\Admin\Announcement\Integration\Chamilo\Core\Repository\Publication\Service
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

    public function deleteContentObjectPublication(int $publicationIdentifier): bool
    {
        return $this->getPublicationService()->deletePublicationByIdentifier($publicationIdentifier);
    }

    /**
     * @throws \Exception
     */
    public function getContentObjectPublicationAttributes(int $publicationIdentifier): Attributes
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

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function publishContentObject(
        ContentObject $contentObject, PublicationTarget $publicationTarget, array $options = []
    ): PublicationResult
    {
        $publication =
            $this->getPublicationService()->createPublicationForUserIdentifierAndContentObjectIdentifierFromValues(
                $publicationTarget->getUserIdentifier(), (int) $contentObject->getId(), []
            );

        if (!$publication instanceof Publication)
        {
            $failureMessage = $this->getTranslator()->trans(
                'PublicationFailure', [
                '%CONTENT_OBJECT%' => $contentObject->get_title()
            ], 'Chamilo\Core\Admin\Announcement\Integration\Chamilo\Core\Repository'
            );

            return new PublicationResult(PublicationResult::STATUS_FAILURE, $failureMessage);
        }
        else
        {
            $successMessage = $this->getTranslator()->trans(
                'PublicationSuccess', [
                '%CONTENT_OBJECT%' => $contentObject->get_title()
            ], 'Chamilo\Core\Admin\Announcement\Integration\Chamilo\Core\Repository'
            );

            $publicationUrl = $this->getUrlGenerator()->fromParameters(
                [
                    Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::package(),
                    Application::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_SYSTEM_ANNOUNCEMENTS,
                    Manager::PARAM_ACTION => Manager::ACTION_VIEW,
                    Manager::PARAM_SYSTEM_ANNOUNCEMENT_ID => $publication->getId()
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