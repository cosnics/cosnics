<?php
namespace Chamilo\Application\Calendar\Extension\Personal;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;

class Actions extends \Chamilo\Application\Calendar\Actions
{

    /**
     *
     * @see \application\calendar\ActionRenderer::get()
     */
    public function get()
    {
        $tabs = array();

        $parameters = array();
        $parameters[Application :: PARAM_CONTEXT] = __NAMESPACE__;
        $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_CREATE;

        $redirect = new Redirect($parameters);
        $link = $redirect->getUrl();

        $tabs[] = new DynamicVisualTab(
            'Publish',
            Translation :: get('Publish', null, Utilities :: COMMON_LIBRARIES),
            Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Tab/Publish'),
            $link,
            false,
            false,
            DynamicVisualTab :: POSITION_RIGHT,
            DynamicVisualTab :: DISPLAY_BOTH_SELECTED);

        $parameters = array();
        $parameters[Application :: PARAM_CONTEXT] = __NAMESPACE__;
        $parameters[Application :: PARAM_ACTION] = Manager :: ACTION_IMPORT;

        $redirect = new Redirect($parameters);
        $link = $redirect->getUrl();

        $tabs[] = new DynamicVisualTab(
            'ImportIcal',
            Translation :: get('ImportIcal'),
            Theme :: getInstance()->getFileExtension('ics', Theme :: ICON_SMALL),
            $link,
            false,
            false,
            DynamicVisualTab :: POSITION_RIGHT,
            DynamicVisualTab :: DISPLAY_BOTH_SELECTED);

        return $tabs;
    }
}