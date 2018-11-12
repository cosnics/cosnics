<?php

namespace Chamilo\Core\Admin\Announcement\Integration\Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Admin\Announcement\Integration\Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Core\Repository\Publication\Domain\PublicationResult;
use Chamilo\Core\Repository\Publication\Domain\PublicationTarget;
use Chamilo\Core\Repository\Publication\Service\PublicationModifierInterface;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication;

/**
 * @package Chamilo\Core\Admin\Announcement\Integration\Chamilo\Core\Repository\Publication\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationModifier implements PublicationModifierInterface
{
    /**
     * @param \Chamilo\Libraries\Format\Form\FormValidator $formValidator
     *
     * @see PublicationInterface::add_publication_attributes_elements()
     */
    public function addContentObjectPublicationAttributesElementsToForm(FormValidator $formValidator)
    {
        return Manager::add_publication_attributes_elements($formValidator);
    }

    /**
     * @param integer $publicationIdentifier
     *
     * @return bool
     */
    public function deleteContentObjectPublication(int $publicationIdentifier)
    {
        return Manager::delete_content_object_publication($publicationIdentifier);
    }

    /**
     * @param integer $publicationIdentifier
     *
     * @return \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes
     * @throws \Exception
     */
    public function getContentObjectPublicationAttributes(int $publicationIdentifier)
    {
        return Manager::get_content_object_publication_attribute($publicationIdentifier);
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param \Chamilo\Core\Repository\Publication\Domain\PublicationTarget $publicationTarget
     * @param array $options
     *
     * @return \Chamilo\Core\Repository\Publication\Domain\PublicationResult
     * @throws \Exception
     * @see PublicationInterface::publish_content_object()
     */
    public function publishContentObject(
        ContentObject $contentObject, PublicationTarget $publicationTarget, $options = array()
    )
    {
        $publication = new Publication();
        $publication->set_content_object_id($contentObject->get_id());
        $publication->set_publisher($contentObject->get_owner_id());

        if (!$publication->create())
        {
            $failureMessage = $this->getTranslator()->trans(
                'PublicationFailure', [
                '%CONTENT_OBJECT%' => $contentObject->get_title()
            ], 'Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository'
            );

            return new PublicationResult(PublicationResult::STATUS_FAILURE, $failureMessage);
        }
        else
        {
            $successMessage = $this->getTranslator()->trans(
                'PublicationSuccess', [
                '%CONTENT_OBJECT%' => $contentObject->get_title()
            ], 'Chamilo\Application\Portfolio\Integration\Chamilo\Core\Repository'
            );

            $publicationUrl = new Redirect(
                array(
                    Application::PARAM_CONTEXT => \Chamilo\Core\Admin\Manager::package(),
                    Application::PARAM_ACTION => \Chamilo\Core\Admin\Manager::ACTION_SYSTEM_ANNOUNCEMENTS,
                    \Chamilo\Core\Admin\Announcement\Manager::PARAM_ACTION => \Chamilo\Core\Admin\Announcement\Manager::ACTION_VIEW,
                    \Chamilo\Core\Admin\Announcement\Manager::PARAM_SYSTEM_ANNOUNCEMENT_ID => $publication->getId()
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
        return Manager::update_content_object_publication_id($publicationAttributes);
    }
}