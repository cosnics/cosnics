<?php
namespace Chamilo\Core\Menu\Architecture\Interface;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @package Chamilo\Core\Menu\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface ConfigurableItemInterface
{
    public function addConfigurationToForm(FormBuilderInterface $builder, array $options): void;

    /**
     * @return string[]
     */
    public function getConfigurationPropertyNames(): array;

    public function mapDataToForms(array $viewData, FormInterface $form): void;

    public function mapFormsToData(FormInterface $form, mixed &$viewData): void;
}