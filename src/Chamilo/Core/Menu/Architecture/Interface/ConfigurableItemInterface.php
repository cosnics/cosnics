<?php
namespace Chamilo\Core\Menu\Architecture\Interface;

use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\FormValidator;

/**
 * @package Chamilo\Core\Menu\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ConfigurableItemInterface
{
    public function addConfigurationToForm(FormValidator $formValidator): void;

    /**
     * @return string[]
     */
    public function getConfigurationPropertyNames(): array;
}