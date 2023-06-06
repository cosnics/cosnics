<?php
namespace Chamilo\Application\Calendar\Integration\Chamilo\Core\Home\Form;

use Chamilo\Application\Calendar\Service\Home\DayBlockRenderer;
use Chamilo\Core\Home\Form\ConfigurationForm;
use Chamilo\Libraries\Translation\Translation;

class DayForm extends ConfigurationForm
{

    /**
     *
     * @see \Chamilo\Core\Home\Form\ConfigurationForm::addSettings()
     */
    public function addSettings()
    {
        $this->add_textfield(DayBlockRenderer::CONFIGURATION_HOUR_STEP, Translation::get('HourStep'), true);
        $this->add_textfield(DayBlockRenderer::CONFIGURATION_TIME_START, Translation::get('TimeStart'), true);
        $this->add_textfield(DayBlockRenderer::CONFIGURATION_TIME_END, Translation::get('TimeEnd'), true);
        $this->addElement('checkbox', DayBlockRenderer::CONFIGURATION_TIME_HIDE, Translation::get('TimeHide'));
    }

    public function setDefaults($defaults = [], $filter = null)
    {
        $defaults = [];

        $defaults[DayBlockRenderer::CONFIGURATION_HOUR_STEP] = $this->getBlock()->getSetting(DayBlockRenderer::CONFIGURATION_HOUR_STEP, 1);
        $defaults[DayBlockRenderer::CONFIGURATION_TIME_START] = $this->getBlock()->getSetting(DayBlockRenderer::CONFIGURATION_TIME_START, 8);
        $defaults[DayBlockRenderer::CONFIGURATION_TIME_END] = $this->getBlock()->getSetting(DayBlockRenderer::CONFIGURATION_TIME_END, 17);
        $defaults[DayBlockRenderer::CONFIGURATION_TIME_HIDE] = $this->getBlock()->getSetting(DayBlockRenderer::CONFIGURATION_TIME_HIDE, 0);
        
        parent::setDefaults($defaults);
    }
}