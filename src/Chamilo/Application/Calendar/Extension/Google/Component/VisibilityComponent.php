<?php
namespace Chamilo\Application\Calendar\Extension\Google\Component;

use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Application\Calendar\Extension\Google\Manager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Application\Calendar\Extension\Google\Storage\DataClass\Visibility;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Google\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class VisibilityComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        $form = $this->getForm();

        if ($form->validate())
        {
            $values = $form->exportValues();

            foreach ($values['visible'] as $visibleCalendarId => $value)
            {
                $visibility = new Visibility();
                $visibility->setCalendarId($visibleCalendarId);
                $visibility->setVisibility(1);
                $visibility->create();
            }

            $redirect = new Redirect(
                array(Application :: PARAM_CONTEXT => \Chamilo\Application\Calendar\Manager :: context()));
            $redirect->toUrl();
        }
        else
        {

            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     *
     * @return \Google_Service_Calendar_CalendarListEntry
     */
    private function getCalendarList()
    {
        $configuration = Configuration :: get_instance();
        $configurationContext = \Chamilo\Application\Calendar\Extension\Google\Manager :: context();

        $googleClient = new \Google_Client();
        $googleClient->setDeveloperKey($configuration->get_setting(array($configurationContext, 'developer_key')));

        $calendarClient = new \Google_Service_Calendar($googleClient);

        $googleClient->setClientId($configuration->get_setting(array($configurationContext, 'client_id')));
        $googleClient->setClientSecret($configuration->get_setting(array($configurationContext, 'client_secret')));
        $googleClient->setScopes('https://www.googleapis.com/auth/calendar.readonly');

        $googleClient->setAccessToken(LocalSetting :: get('token', $configurationContext));

        return $calendarClient->calendarList->listCalendarList(array('minAccessRole' => 'owner'))->getItems();
    }

    private function getForm()
    {
        $form = new FormValidator('visibility', 'post', $this->get_url());

        foreach ($this->getCalendarList() as $calendarListEntry)
        {
            $form->addElement('checkbox', 'visible[' . $calendarListEntry->id . ']', $calendarListEntry->summary);
        }

        $form->addSaveResetButtons();

        return $form;
    }
}
