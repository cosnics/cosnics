<?php

namespace Chamilo\Core\Repository\ContentObject\ExternalTool\Display;

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

    const ACTION_VIEWER =  'Viewer';
    const DEFAULT_ACTION = self::ACTION_VIEWER;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\ExternalTool\Display\DisplayParameters
     */
    protected $displayParameters;

    /**
     * Manager constructor.
     *
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfiguration $applicationConfiguration
     */
    public function __construct(ApplicationConfiguration $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        $displayParameters = $applicationConfiguration->get(self::CONFIG_DISPLAY_PARAMETERS);
        if(!$displayParameters instanceof DisplayParameters || !$displayParameters->isValid())
        {
            throw new \RuntimeException('The given display parameters in the launch configuration are not valid');
        }

        $this->displayParameters = $displayParameters;
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\ExternalTool\Display\DisplayParameters|string
     */
    public function getDisplayParameters()
    {
        return $this->displayParameters;
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\ExternalTool\Storage\DataClass\ExternalTool
     */
    public function getExternalTool()
    {
        return $this->getDisplayParameters()->getExternalTool();
    }
}