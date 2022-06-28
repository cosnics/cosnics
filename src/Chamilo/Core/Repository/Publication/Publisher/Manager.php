<?php
namespace Chamilo\Core\Repository\Publication\Publisher;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    public const ACTION_PUBLISHER = 'publisher';

    public const DEFAULT_ACTION = self::ACTION_PUBLISHER;
    public const PARAM_ACTION = 'publisher_action';

    /**
     * Returns the parent application
     *
     * @return Application | PublisherSupport
     */
    public function getParentApplication()
    {
        return parent::get_application();
    }

    /**
     * Returns the publication form
     *
     * @param array $selectedContentObjects
     *
     * @return \Chamilo\Libraries\Format\Form\FormValidator
     */
    public function getPublicationForm($selectedContentObjects = [])
    {
        return $this->getParentApplication()->getPublicationForm($selectedContentObjects);
    }

    /**
     * Returns the publication Handler
     *
     * @return Interfaces\PublicationHandlerInterface
     */
    public function getPublicationHandler()
    {
        return $this->getParentApplication()->getPublicationHandler();
    }

    /**
     * Returns the allowed content object types
     *
     * @return array
     */
    public function get_allowed_content_object_types()
    {
        return $this->getParentApplication()->get_allowed_content_object_types();
    }
}