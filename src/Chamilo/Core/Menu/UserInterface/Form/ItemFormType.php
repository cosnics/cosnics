<?php
namespace Chamilo\Core\Menu\UserInterface\Form;

use Chamilo\Core\Menu\Architecture\Domain\ItemRendererRegistry;
use Chamilo\Core\Menu\Architecture\Interface\TranslatableItemInterface;
use Chamilo\Core\Menu\Implementation\Menu\CategoryItemRenderer;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException;
use Chamilo\Libraries\UserInterface\Form\Service\FormButtonTypeBuilder;
use Chamilo\Libraries\UserInterface\Form\Service\FormTypeBuilder;
use Chamilo\Libraries\UserInterface\Tree\Service\OptionsTreeRenderer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Group\UserInterface\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemFormType extends AbstractType
{
    protected FormButtonTypeBuilder $formButtonTypeBuilder;

    protected FormTypeBuilder $formTypeBuilder;

    protected ItemRendererRegistry $itemRendererRegistry;

    protected OptionsTreeRenderer $optionsTreeRenderer;

    protected Translator $translator;

    public function __construct(
        FormTypeBuilder $formTypeBuilder, FormButtonTypeBuilder $formButtonTypeBuilder, Translator $translator,
        OptionsTreeRenderer $optionsTreeRenderer, ItemRendererRegistry $itemRendererRegistry
    )
    {
        $this->translator = $translator;
        $this->optionsTreeRenderer = $optionsTreeRenderer;
        $this->formTypeBuilder = $formTypeBuilder;
        $this->formButtonTypeBuilder = $formButtonTypeBuilder;
        $this->itemRendererRegistry = $itemRendererRegistry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $formTypeBuilderHelper = $this->formTypeBuilder;
        $itemType = $options['itemType'];
        $translator = $this->getTranslator();

        $formTypeBuilderHelper->addCategory(
            $builder, 'category_general', $translator->trans('General', [], Manager::CONTEXT)
        );

        if ($itemType !== CategoryItemRenderer::class) {
            $formTypeBuilderHelper->addSelect(
                $builder, Item::PROPERTY_PARENT, $translator->trans('Parent', [], Manager::CONTEXT), true,
                $this->getOptionsTreeRenderer()->getOptions()->toArray()
            );
        }

        $formTypeBuilderHelper->addCheckbox(
            $builder, Item::PROPERTY_HIDDEN, $translator->trans('Hidden', [], Manager::CONTEXT)
        );

        $formTypeBuilderHelper->addText(
            $builder, Item::PROPERTY_ICON_CLASS, $translator->trans('IconClass', [], Manager::CONTEXT), false
        );

        $builder->add(
            $builder->create(
                Item::PROPERTY_CONFIGURATION, ItemConfigurationFormType::class,
                ['itemType' => $itemType, 'label' => false]
            )
        );

        try {
            $itemRenderer = $this->getItemRendererRegistry()->getItemRenderer($itemType);

            if ($itemRenderer instanceof TranslatableItemInterface) {
                $builder->add(
                    $builder->create(
                        Item::PROPERTY_TITLES, ItemTitleFormType::class, ['label' => false]
                    )
                );
            }
        }
        catch (NoSuchClassException) {
        }

        $this->getFormButtonTypeBuilder()->addSaveAndResetButton($builder);
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

    public function getFormButtonTypeBuilder(): FormButtonTypeBuilder
    {
        return $this->formButtonTypeBuilder;
    }

    public function getFormTypeBuilder(): FormTypeBuilder
    {
        return $this->formTypeBuilder;
    }

    public function getItemRendererRegistry(): ItemRendererRegistry
    {
        return $this->itemRendererRegistry;
    }

    public function getOptionsTreeRenderer(): OptionsTreeRenderer
    {
        return $this->optionsTreeRenderer;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}