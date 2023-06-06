<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Form;

use Chamilo\Application\Weblcms\Service\Home\NewAnnouncementsBlockRenderer;
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
        $this->addElement('checkbox', NewAnnouncementsBlockRenderer::CONFIGURATION_SHOW_CONTENT, Translation::get('ShowContent'));
    }

    public function setDefaults($defaults = [], $filter = null)
    {
        $defaults = [];

        $defaults[NewAnnouncementsBlockRenderer::CONFIGURATION_SHOW_CONTENT] = $this->getBlock()->getSetting(
            NewAnnouncementsBlockRenderer::CONFIGURATION_SHOW_CONTENT,
            true);
        
        parent::setDefaults($defaults, $filter);
    }
}