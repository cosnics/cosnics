<?php

namespace Chamilo\Core\Repository\ContentObject\ExternalTool\Display;

use Chamilo\Core\Repository\ContentObject\ExternalTool\Display\Bridge\Interfaces\ExternalToolServiceBridgeInterface;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;

/**
 * @package Chamilo\Core\Repository\ContentObject\ExternalTool\Display
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends Application
{
    const CONFIG_DISPLAY_PARAMETERS = 'display_parameters';

    const PARAM_ACTION = 'ExternalToolAction';

    const ACTION_VIEWER = 'Viewer';
    const DEFAULT_ACTION = self::ACTION_VIEWER;

    /**
     * @var ExternalToolServiceBridgeInterface
     */
    protected $externalToolServiceBridge;

    /**
     * Manager constructor.
     *
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfiguration $applicationConfiguration
     */
    public function __construct(ApplicationConfiguration $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        $this->externalToolServiceBridge =
            $this->getBridgeManager()->getBridgeByInterface(ExternalToolServiceBridgeInterface::class);
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\ExternalTool\Storage\DataClass\ExternalTool
     */
    public function getExternalTool()
    {
        return $this->getExternalToolServiceBridge()->getExternalTool();
    }

    /**
     * @return ExternalToolServiceBridgeInterface
     */
    public function getExternalToolServiceBridge()
    {
        return $this->externalToolServiceBridge;
    }
}