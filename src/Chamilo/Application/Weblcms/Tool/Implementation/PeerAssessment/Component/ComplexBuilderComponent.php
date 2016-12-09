<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\PeerAssessment\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\PeerAssessment\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 * Description
 * 
 * @author Renaat De Muynck
 */
class ComplexBuilderComponent extends Manager implements DelegateComponent
{

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        // $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)),
        // Translation :: get('PeerAssessmentToolBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
    }
}