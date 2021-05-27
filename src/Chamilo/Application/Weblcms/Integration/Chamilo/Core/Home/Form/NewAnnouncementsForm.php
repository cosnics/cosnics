<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Form;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type\NewAnnouncements;
use Chamilo\Core\Home\Form\ConfigurationForm;
use Chamilo\Libraries\Translation\Translation;

class NewAnnouncementsForm extends ConfigurationForm
{

    /**
     *
     * @see \Chamilo\Core\Home\Form\ConfigurationForm::addSettings()
     */
    public function addSettings()
    {
        $this->addElement('checkbox', NewAnnouncements::CONFIGURATION_SHOW_CONTENT, Translation::get('ShowContent'));
    }

    public function setDefaults()
    {
        $defaults = [];
        
        $defaults[NewAnnouncements::CONFIGURATION_SHOW_CONTENT] = $this->getBlock()->getSetting(
            NewAnnouncements::CONFIGURATION_SHOW_CONTENT, 
            true);
        
        parent::setDefaults($defaults);
    }
}