<?php
namespace Chamilo\Core\Home\Ajax;

use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Libraries\Architecture\AjaxManager;

/**
 * @package Chamilo\Core\Home\Ajax
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends AjaxManager
{
    public const CONTEXT = __NAMESPACE__;

    public function getHomeService(): HomeService
    {
        return $this->getService(HomeService::class);
    }
}
