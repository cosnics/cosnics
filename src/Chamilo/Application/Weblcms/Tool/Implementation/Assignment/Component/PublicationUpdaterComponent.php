<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

/**
 *
 * @package application.weblcms.tool.assignment.php.component
 *          Publication updater for assignments
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class PublicationUpdaterComponent extends Manager implements DelegateComponent
{

    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);
    }
}
