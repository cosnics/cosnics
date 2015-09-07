<?php
namespace Chamilo\Application\Calendar\Extension\Google;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;

class Actions extends \Chamilo\Application\Calendar\Actions
{

    /**
     *
     * @see \Chamilo\Application\Calendar\Actions::get()
     */
    public function get()
    {
        $tabs = array();

        $configurationContext = \Chamilo\Application\Calendar\Extension\Google\Manager :: context();
        $accessToken = LocalSetting :: get('token', $configurationContext);

        if (! $accessToken)
        {
            $parameters = array();
            $parameters[Application :: PARAM_CONTEXT] = __NAMESPACE__;
            $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_LOGIN;

            $redirect = new Redirect($parameters);
            $link = $redirect->getUrl();

            $tabs[] = new DynamicVisualTab(
                'GoogleCalendarLogin',
                Translation :: get('GoogleCalendarLogin'),
                Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Tab/' . Manager :: ACTION_LOGIN),
                $link,
                false,
                false,
                DynamicVisualTab :: POSITION_RIGHT,
                DynamicVisualTab :: DISPLAY_BOTH_SELECTED);
        }
        else
        {
            $parameters = array();
            $parameters[Application :: PARAM_CONTEXT] = __NAMESPACE__;
            $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_LOGOUT;

            $redirect = new Redirect($parameters);
            $link = $redirect->getUrl();

            $tabs[] = new DynamicVisualTab(
                'GoogleCalendarLogout',
                Translation :: get('GoogleCalendarLogout'),
                Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Tab/' . Manager :: ACTION_LOGOUT),
                $link,
                false,
                false,
                DynamicVisualTab :: POSITION_RIGHT,
                DynamicVisualTab :: DISPLAY_BOTH_SELECTED);
        }

        return $tabs;
    }
}