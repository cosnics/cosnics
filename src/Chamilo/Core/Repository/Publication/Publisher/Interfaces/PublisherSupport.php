<?php
namespace Chamilo\Core\Repository\Publication\Publisher\Interfaces;

use Chamilo\Libraries\Format\Form\FormValidator;

/**
 * Interface that a component needs to implement to provide the necessary functionality for the publisher application
 *
 * @package Chamilo\Core\Repository\Publication\Publisher\Interfaces
 */
interface PublisherSupport
{

    /**
     * Returns the publication form
     *
     * @param ContentObject[] $selectedContentObjects
     *
     * @return FormValidator
     */
    public function getPublicationForm($selectedContentObjects = array());

    /**
     * Returns the publication handler
     *
     * @return PublicationHandlerInterface
     */
    public function getPublicationHandler();

    /**
     * Returns the allowed content object types
     *
     * @return array
     */
    public function get_allowed_content_object_types();
}