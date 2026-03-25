<?php
namespace Chamilo\Core\Menu\Architecture\Interface;

use Chamilo\Core\Menu\Storage\DataClass\Item;
use Symfony\Component\Form\FormBuilderInterface;

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

    public function getDefaultFormConfigurationData(Item $item);

    public function handleConfigurationData(mixed $submittedData): mixed;
}