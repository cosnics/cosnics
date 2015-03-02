<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class BrowserComponent extends Manager implements DelegateComponent
{

    public function get_tool_actions()
    {
        if ($this->is_allowed(WeblcmsRights :: ADD_RIGHT))
        {
            $actions[] = new ToolbarItem(
                Translation :: get('ImportScorm'),
                Theme :: getInstance()->getCommonImagePath('action_import'),
                $this->get_url(
                    array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_IMPORT_SCORM)));
        }

        return $actions;
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_BROWSE_PUBLICATION_TYPE);
    }
}
