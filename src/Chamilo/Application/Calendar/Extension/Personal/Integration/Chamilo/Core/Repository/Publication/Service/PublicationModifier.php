<?php

namespace Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Core\Repository\Publication\Service;

use Chamilo\Application\Calendar\Extension\Personal\Manager as PersonalCalendarManager;
use Chamilo\Application\Calendar\Extension\Personal\Service\PublicationService;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Core\Repository\Publication\Domain\PublicationResult;
use Chamilo\Core\Repository\Publication\Domain\PublicationTarget;
use Chamilo\Core\Repository\Publication\Service\PublicationModifierInterface;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\FormValidator;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Core\Repository\Publication\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationModifier implements PublicationModifierInterface
{

    /**
     * @var \Chamilo\Application\Calendar\Extension\Personal\Service\PublicationService
     */
    private $publicationService;

    /**
     * @var \Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Core\Repository\Publication\Service\PublicationAttributesGenerator
     */
    private $publicationAttributesGenerator;

    /**
     *
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @param \Chamilo\Application\Portfolio\Service\PublicationService $publicationService
     * @param \Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Core\Repository\Publication\Service\PublicationAttributesGenerator $publicationAttributesGenerator
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(
        PublicationService $publicationService, PublicationAttributesGenerator $publicationAttributesGenerator,
        Translator $translator
    )
    {
        $this->publicationService = $publicationService;
        $this->publicationAttributesGenerator = $publicationAttributesGenerator;
        $this->translator = $translator;
    }

    /**
     * @param \Chamilo\Libraries\Format\Form\FormValidator $formValidator
     */
    public function addContentObjectPublicationAttributesElementsToForm(FormValidator $formValidator)
    {
    }

    /**
     * @param integer $publicationIdentifier
     *
     * @return bool
     */
    public function deleteContentObjectPublication(int $publicationIdentifier)
    {
        return $this->getPublicationService()->deletePublicationByIdentifier($publicationIdentifier);
    }

    /**
     * @param integer $publicationIdentifier
     *
     * @return \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes
     * @throws \Exception
     */
    public function getContentObjectPublicationAttributes(int $publicationIdentifier)
    {
        return $this->getPublicationAttributesGenerator()->createAttributesFromRecord(
            $this->getPublicationService()->findPublicationRecordByIdentifier($publicationIdentifier)
        );
    }

    /**
     * @return \Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Core\Repository\Publication\Service\PublicationAttributesGenerator
     */
    public function getPublicationAttributesGenerator(): PublicationAttributesGenerator
    {
        return $this->publicationAttributesGenerator;
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Core\Repository\Publication\Service\PublicationAttributesGenerator $publicationAttributesGenerator
     */
    public function setPublicationAttributesGenerator(PublicationAttributesGenerator $publicationAttributesGenerator
    ): void
    {
        $this->publicationAttributesGenerator = $publicationAttributesGenerator;
    }

    /**
     * @return \Chamilo\Application\Calendar\Extension\Personal\Service\PublicationService
     */
    public function getPublicationService(): PublicationService
    {
        return $this->publicationService;
    }

    /**
     * @param \Chamilo\Application\Calendar\Extension\Personal\Service\PublicationService $publicationService
     */
    public function setPublicationService(PublicationService $publicationService): void
    {
        $this->publicationService = $publicationService;
    }

    /**
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param \Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Core\Repository\Publication\Domain\PublicationTarget $publicationTarget
     * @param array $options
     *
     * @return \Chamilo\Core\Repository\Publication\Domain\PublicationResult
     * @throws \Exception
     * @see PublicationModifierInterface::publishContentObject()
     */
    public function publishContentObject(
        ContentObject $contentObject, PublicationTarget $publicationTarget, $options = []
    )
    {
        $publication = $this->getPublicationService()->getPublicationInstance();
        $publication->set_content_object_id($contentObject->getId());
        $publication->set_publisher($publicationTarget->getUserIdentifier());

        if (!$this->getPublicationService()->createPublication($publication))
        {
            $failureMessage = $this->getTranslator()->trans(
                'PublicationFailure', ['%CONTENT_OBJECT%' => $contentObject->get_title()],
                'Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Core\Repository'
            );

            return new PublicationResult(PublicationResult::STATUS_FAILURE, $failureMessage);
        }
        else
        {
            $successMessage = $this->getTranslator()->trans(
                'PublicationSuccess', ['%CONTENT_OBJECT%' => $contentObject->get_title()],
                'Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Core\Repository'
            );

            $publicationUrl = new Redirect(
                array(
                    Application::PARAM_CONTEXT => PersonalCalendarManager::package(),
                    Application::PARAM_ACTION => PersonalCalendarManager::ACTION_VIEW,
                    PersonalCalendarManager::PARAM_PUBLICATION_ID => $publication->getId()
                )
            );

            return new PublicationResult(
                PublicationResult::STATUS_SUCCESS, $successMessage, $publicationUrl->getUrl()
            );
        }
    }

    /**
     * @param \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes $publicationAttributes
     *
     * @return boolean
     */
    public function updateContentObjectPublicationContentObjectIdentifier(Attributes $publicationAttributes)
    {
        $publication = $this->getPublicationService()->findPublicationByIdentifier($publicationAttributes->getId());

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