<?php
namespace Chamilo\Application\Calendar\Extension\Google;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Platform\Translation;

class Actions extends \Chamilo\Application\Calendar\Actions
{

    /**
     *
     * @see \Chamilo\Application\Calendar\Actions::get()
     */
    public function get()
    {
        $buttonGroup = new ButtonGroup();

        $configurationContext = \Chamilo\Application\Calendar\Extension\Google\Manager :: context();
        $accessToken = LocalSetting :: getInstance()->get('token', $configurationContext);

        if (! $accessToken)
        {
            $parameters = array();
            $parameters[Application :: PARAM_CONTEXT] = __NAMESPACE__;
            $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_LOGIN;

            $redirect = new Redirect($parameters);
            $link = $redirect->getUrl();

            $buttonGroup->addButton(
                new Button(
                    Translation :: get('GoogleCalendarLogin'),
                    Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Tab/' . Manager :: ACTION_LOGIN),
                    $link,
                    Button :: DISPLAY_ICON));
        }
        else
        {
            $parameters = array();
            $parameters[Application :: PARAM_CONTEXT] = __NAMESPACE__;
            $parameters[Manager :: PARAM_ACTION] = Manager :: ACTION_LOGOUT;

            $redirect = new Redirect($parameters);
            $link = $redirect->getUrl();

            $buttonGroup->addButton(
                new Button(
                    Translation :: get('GoogleCalendarLogout'),
                    Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Tab/' . Manager :: ACTION_LOGOUT),
                    $link,
                    Button :: DISPLAY_ICON));
        }

        return array($buttonGroup);
    }
}