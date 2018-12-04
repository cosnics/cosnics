<?php
namespace Chamilo\Core\Rights;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;

/**
 * @package Chamilo\Core\Rights
 *
 * @deprecated Should be removed eventually in favour of \Chamilo\Libraries\Rights
 */
abstract class Manager extends Application
{
    const ACTION_BROWSE = 'Browser';
    const DEFAULT_ACTION = self::ACTION_BROWSE;

    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        $this->checkAuthorization(Manager::context());
    }
}
