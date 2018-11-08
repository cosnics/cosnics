<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Service;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Core\Repository\Publication\LocationSupport;
use Chamilo\Core\Repository\Publication\Service\PublicationModifierInterface;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Form\FormValidator;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Service
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
     * @param \Chamilo\Core\Repository\Publication\LocationSupport $location
     * @param array $options
     *
     * @return \Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode
     * @throws \Exception
     * @see PublicationInterface::publish_content_object()
     */
    public function publishContentObject(ContentObject $contentObject, LocationSupport $location, $options = array())
    {
        return Manager::publish_content_object($contentObject, $location, $options);
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