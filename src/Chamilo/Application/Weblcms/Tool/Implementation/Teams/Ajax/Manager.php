<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Ajax;

use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Component\AjaxComponent;
use Chamilo\Application\Weblcms\Tool\Implementation\Teams\Service\PlatformGroupTeamService;
use Chamilo\Libraries\Architecture\AjaxManager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Teams\Ajax
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends AjaxManager
{
    const PARAM_ACTION = 'AjaxAction';
    const ACTION_UPDATE_LOCAL_TEAM_NAME = 'UpdateLocalTeamName';

    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        if (!$this->get_application() instanceof AjaxComponent)
        {
            throw new \RuntimeException(
                'This ajax call can only be called through the weblcms context. The parent of this component should therefore be the' .
                ' AjaxComponent from the teams tool.'
            );
        }
    }

    /**
     * @return PlatformGroupTeamService
     */
    protected function getPlatformGroupTeamService()
    {
        return $this->getService(PlatformGroupTeamService::class);
    }
}
