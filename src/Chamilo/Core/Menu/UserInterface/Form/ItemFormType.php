<?php
namespace Chamilo\Core\Menu\UserInterface\Form;

use Chamilo\Core\Menu\Architecture\Domain\ItemRendererRegistry;
use Chamilo\Core\Menu\Architecture\Interface\TranslatableItemInterface;
use Chamilo\Core\Menu\Implementation\Menu\CategoryItemRenderer;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\UserInterface\Form\Service\ItemFormDataMapper;
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
    public function __construct(
        protected readonly FormTypeBuilder $formTypeBuilder,
        protected readonly FormButtonTypeBuilder $formButtonTypeBuilder, protected readonly Translator $translator,
        protected readonly OptionsTreeRenderer $optionsTreeRenderer,
        protected readonly ItemRendererRegistry $itemRendererRegistry,
        protected readonly ItemFormDataMapper $itemFormDataMapper
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setDataMapper($this->itemFormDataMapper);

        $itemType = $options['itemType'];
        $translator = $this->translator;

        $builder->add(
            $this->formTypeBuilder->createCategory(
                $builder, 'category_general', $translator->trans('General', [], Manager::CONTEXT)
            )
        );

        if ($itemType !== CategoryItemRenderer::class) {
            $builder->add(
                $this->formTypeBuilder->createSelect(
                    $builder, Item::PROPERTY_PARENT, $translator->trans('Parent', [], Manager::CONTEXT), true,
                    $this->optionsTreeRenderer->getOptions()->toArray()
                )
            );
        }

        $builder->add(
            $this->formTypeBuilder->createCheckbox(
                $builder, Item::PROPERTY_HIDDEN, $translator->trans('Hidden', [], Manager::CONTEXT)
            )
        );

        $builder->add(
            $this->formTypeBuilder->createText(
                $builder, Item::PROPERTY_ICON_CLASS, $translator->trans('IconClass', [], Manager::CONTEXT), false
            )
        );

        $builder->add(
            $builder->create(
                Item::PROPERTY_CONFIGURATION, ItemConfigurationFormType::class,
                ['itemType' => $itemType, 'label' => false]
            )
        );

        try {
            $itemRenderer = $this->itemRendererRegistry->getItemRenderer($itemType);

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

        $this->formButtonTypeBuilder->addSaveAndResetButton($builder);
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
}