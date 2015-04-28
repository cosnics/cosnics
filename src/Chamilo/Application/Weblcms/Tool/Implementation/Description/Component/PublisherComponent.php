<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Description\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Description\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

/**
 * $Id: description_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.description.component
 */
class PublisherComponent extends Manager implements DelegateComponent
{

    public function get_additional_parameters()
    {
        return array(
            \Chamilo\Core\Repository\Viewer\Manager :: PARAM_ID,
            \Chamilo\Core\Repository\Viewer\Manager :: PARAM_ACTION);
    }
}
