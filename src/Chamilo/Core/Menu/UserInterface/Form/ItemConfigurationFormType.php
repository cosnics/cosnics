<?php
namespace Chamilo\Core\Menu\UserInterface\Form;

use Chamilo\Core\Menu\Architecture\Domain\ItemRendererRegistry;
use Chamilo\Core\Menu\Architecture\Interface\ConfigurableItemInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @package Chamilo\Core\Group\UserInterface\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemConfigurationFormType extends AbstractType
{
    protected ItemRendererRegistry $itemRendererRegistry;

    public function __construct(ItemRendererRegistry $itemRendererRegistry)
    {
        $this->itemRendererRegistry = $itemRendererRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $itemType = $options['itemType'];

        try {
            $itemRenderer = $this->getItemRendererRegistry()->getItemRenderer($itemType);

            if ($itemRenderer instanceof ConfigurableItemInterface &&
                count($itemRenderer->getConfigurationPropertyNames()) > 0) {
                $itemRenderer->addConfigurationToForm($builder, $options);
            }
        }
        catch (NoSuchClassException) {
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['itemType' => null]);

        $normalizer = static function (Options $options, $itemType) {
            if (!is_string($itemType)) {
                throw new LogicException('$itemType should be a string.');
            }

            return $itemType;
        };

        $resolver->setNormalizer('itemType', $normalizer);
    }

    public function getItemRendererRegistry(): ItemRendererRegistry
    {
        return $this->itemRendererRegistry;
    }
}