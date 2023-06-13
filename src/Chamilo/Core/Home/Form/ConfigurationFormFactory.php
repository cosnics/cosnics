<?php
namespace Chamilo\Core\Home\Form;

use Chamilo\Core\Home\Architecture\Interfaces\ConfigurableBlockRendererInterface;
use Chamilo\Core\Home\Storage\DataClass\Element;

/**
 * @package Chamilo\Core\Home\Form
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ConfigurationFormFactory
{
    /**
     * @throws \QuickformException
     */
    public function buildConfigurationForm(
        ConfigurableBlockRendererInterface $blockRenderer, Element $block, bool $hasStaticTitle = true
    ): ConfigurationForm
    {
        $configurationForm = new ConfigurationForm();

        $configurationForm->addTitleField($block, $hasStaticTitle);
        $blockRenderer->addConfigurationFieldsToForm($configurationForm, $block);
        $configurationForm->addSubmitButtons($block);

        return $configurationForm;
    }
}