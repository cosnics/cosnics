<?php

namespace Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Repository\Publication\Domain\PublicationTarget;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Form\FormValidator;

/**
 * @package Chamilo\Core\Repository\Publication\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface PublicationModifierInterface
{
    /**
     * @param \Chamilo\Libraries\Format\Form\FormValidator $formValidator
     *
     * @see PublicationInterface::add_publication_attributes_elements()
     */
    public function addContentObjectPublicationAttributesElementsToForm(FormValidator $formValidator);

    /**
     * @param $publicationIdentifier
     *
     * @return bool
     * @see PublicationInterface::delete_content_object_publication()
     */
    public function deleteContentObjectPublication(int $publicationIdentifier);

    /**
     * @param integer $publicationIdentifier
     *
     * @return \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes
     * @throws \Exception
     * @see PublicationInterface::get_content_object_publication_attribute()
     */
    public function getContentObjectPublicationAttributes(int $publicationIdentifier);

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param \Chamilo\Core\Repository\Publication\Domain\PublicationTarget $publicationTarget
     * @param array $options
     *
     * @return \Chamilo\Core\Repository\Publication\Domain\PublicationResult
     * @throws \Exception
     * @see PublicationModifierInterface::publishContentObject()
     */
    public function publishContentObject(
        ContentObject $contentObject, PublicationTarget $publicationTarget, $options = []
    );

    /**
     * @param \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes $publicationAttributes
     *
     * @return boolean
     * @see PublicationInterface::update_content_object_publication_id()
     */
    public function updateContentObjectPublicationContentObjectIdentifier(Attributes $publicationAttributes);
}