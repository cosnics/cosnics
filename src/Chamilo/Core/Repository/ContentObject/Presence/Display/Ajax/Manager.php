<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax;

use Chamilo\Core\Repository\ContentObject\Presence\Display\Component\AjaxComponent;
use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\Presence;
use Chamilo\Libraries\Architecture\AjaxManager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Exceptions\UserException;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Ajax
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
abstract class Manager extends AjaxManager
{
    const ACTION_LOAD_PRESENCE = 'LoadPresence';
    const ACTION_UPDATE_PRESENCE = 'UpdatePresence';

    const PARAM_ACTION = 'presence_display_ajax_action';

    /**
     * @var AjaxComponent
     */
    protected $ajaxComponent;

    /**
     * Manager constructor.
     *
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        if (!$applicationConfiguration->getApplication() instanceof AjaxComponent)
        {
            throw new \RuntimeException(
                'The ajax components from the presence display manager can only be called from ' .
                'within the AjaxComponent of the presence display application'
            );
        }

        $this->ajaxComponent = $applicationConfiguration->getApplication();

        parent::__construct($applicationConfiguration);
    }

    /**
     * @param string $json
     * @return array
     */
    protected function deserialize(string $json): array
    {
        return $this->getSerializer()->deserialize($json, 'array', 'json');
    }

    protected function serialize(array $array): string
    {
        return $this->getSerializer()->serialize($array, 'json');
    }

    protected function get_root_content_object()
    {
        return $this->get_application()->get_root_content_object();
    }

    /**
     * @return Presence
     * @throws UserException
     */
    protected function getPresence(): Presence
    {
        $presence = $this->get_root_content_object();

        if (!$presence instanceof Presence)
        {
            $this->throwUserException('PresenceNotFound');
        }

        return $presence;
    }

    /**
     * @throws UserException
     */
    protected function throwUserException(string $key)
    {
        $this->ajaxComponent->throwUserException($key);
    }
}