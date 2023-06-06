<?php
namespace Chamilo\Core\Admin\Announcement\Integration\Chamilo\Core\Home\Form;

use Chamilo\Core\Admin\Announcement\Service\Home\SystemAnnouncementsBlockRenderer;
use Chamilo\Core\Home\Form\ConfigurationForm;
use Chamilo\Libraries\Translation\Translation;

class SystemAnnouncementsForm extends ConfigurationForm
{

    /**
     * @see \Chamilo\Core\Home\Form\ConfigurationForm::addSettings()
     */
    public function addSettings()
    {
        $group = [];
        $group[] = &$this->createElement(
            'radio', SystemAnnouncementsBlockRenderer::CONFIGURATION_SHOW_EMPTY, null, Translation::get('True'), 1
        );
        $group[] = &$this->createElement(
            'radio', SystemAnnouncementsBlockRenderer::CONFIGURATION_SHOW_EMPTY, null, Translation::get('False'), 0
        );

        $this->addGroup(
            $group, SystemAnnouncementsBlockRenderer::CONFIGURATION_SHOW_EMPTY, Translation::get('ShowWhenEmpty'), '', false
        );
    }

    public function setDefaults($defaults = [], $filter = null)
    {
        $defaults = [];

        $defaults[SystemAnnouncementsBlockRenderer::CONFIGURATION_SHOW_EMPTY] = $this->getBlock()->getSetting(
            SystemAnnouncementsBlockRenderer::CONFIGURATION_SHOW_EMPTY, 1
        );

        parent::setDefaults($defaults, $filter);
    }
}