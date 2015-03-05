<?php
namespace Chamilo\Application\Calendar\Extension\Personal;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class Actions extends \Chamilo\Application\Calendar\Actions
{

    /**
     *
     * @see \application\calendar\ActionRenderer::get()
     */
    public function get()
    {
        $actions = array();

        $parameters = array();
        $parameters[Application :: PARAM_CONTEXT] = __NAMESPACE__;
        $parameters[Application :: PARAM_ACTION] = Manager :: ACTION_CREATE;
        $link = Redirect :: get_link($parameters);

        $actions[] = new ToolbarItem(
            Translation :: get('Publish', null, Utilities :: COMMON_LIBRARIES),
            Theme :: getInstance()->getCommonImagePath('Action/Publish'),
            $link);

        $parameters = array();
        $parameters[Application :: PARAM_CONTEXT] = __NAMESPACE__;
        $parameters[Application :: PARAM_ACTION] = Manager :: ACTION_IMPORT;
        $link = Redirect :: get_link($parameters);

        $actions[] = new ToolbarItem(
            Translation :: get('ImportIcal'),
            Theme :: getInstance()->getCommonImagePath('Action/Import'),
            $link);

        return $actions;
    }
}