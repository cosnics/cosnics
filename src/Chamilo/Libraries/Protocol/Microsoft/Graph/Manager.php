<?php
namespace Chamilo\Libraries\Protocol\Microsoft\Graph;

use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService;

/**
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    public const ACTION_AUTHENTICATE = 'Authentication';
    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_AUTHENTICATE;

    /**
     * @return \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService
     */
    protected function getGraphService(): UserService
    {
        return $this->getService(UserService::class);
    }
}