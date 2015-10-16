<?php
namespace Chamilo\Core\Admin\Announcement\Integration\Chamilo\Core\Home\Form;

use Chamilo\Core\Home\Form\ConfigurationForm;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Admin\Announcement\Integration\Chamilo\Core\Home\Type\SystemAnnouncements;

class SystemAnnouncementsForm extends ConfigurationForm
{

    /**
     *
     * @see \Chamilo\Core\Home\Form\ConfigurationForm::addSettings()
     */
    public function addSettings()
    {
        $group = array();
        $group[] = & $this->createElement(
            'radio',
            SystemAnnouncements :: CONFIGURATION_SHOW_EMPTY,
            null,
            Translation :: get('True'),
            1);
        $group[] = & $this->createElement(
            'radio',
            SystemAnnouncements :: CONFIGURATION_SHOW_EMPTY,
            null,
            Translation :: get('False'),
            0);

        $this->addGroup(
            $group,
            SystemAnnouncements :: CONFIGURATION_SHOW_EMPTY,
            Translation :: get('ShowWhenEmpty'),
            '<br/>',
            false);
    }

    public function setDefaults()
    {
        $defaults = array();

        $defaults[SystemAnnouncements :: CONFIGURATION_SHOW_EMPTY] = $this->getBlock()->getSetting(
            SystemAnnouncements :: CONFIGURATION_SHOW_EMPTY,
            1);

        parent :: setDefaults($defaults);
    }
}