<?php
namespace Chamilo\Core\Home\Architecture\Interfaces;

use Chamilo\Core\Home\Form\ConfigurationForm;
use Chamilo\Core\Home\Storage\DataClass\Element;

/**
 * @package Chamilo\Core\Home\Architecture\Interfaces
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
interface ConfigurableBlockRendererInterface
{

    public function addConfigurationFieldsToForm(ConfigurationForm $configurationForm, Element $block): void;

    /**
     * @return string[]
     */
    public function getConfigurationVariables(): array;
}