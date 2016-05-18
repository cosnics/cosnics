<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Manager;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\BootstrapGlyph;
use Chamilo\Libraries\Platform\Translation;

class BrowserComponent extends Manager
{

    public function get_tool_actions()
    {
        $tool_actions = array();

        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {

            $tool_actions[] = new Button(
                Translation :: get('Reporting'),
                new BootstrapGlyph('stats'),
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Manager :: PARAM_TOOL => 'reporting',
                        \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_VIEW,
                        \Chamilo\Core\Reporting\Viewer\Manager :: PARAM_BLOCK_ID => 2)),
                Button :: DISPLAY_ICON_AND_LABEL);
        }

        return $tool_actions;
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_BROWSE_PUBLICATION_TYPE);
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    }
}
