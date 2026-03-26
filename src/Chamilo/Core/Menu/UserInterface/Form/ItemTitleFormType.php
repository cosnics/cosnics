<?php
namespace Chamilo\Core\Menu\UserInterface\Form;

use Chamilo\Core\Admin\Service\Consulter\LanguageConsulter;
use Chamilo\Core\Menu\Architecture\Domain\ItemRendererRegistry;
use Chamilo\Core\Menu\Manager;
use Chamilo\Libraries\UserInterface\Form\Service\FormTypeBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Group\UserInterface\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemTitleFormType extends AbstractType
{
    public function __construct(
        protected FormTypeBuilder $formTypeBuilder, protected string $defaultLanguage,
        protected ItemRendererRegistry $itemRendererRegistry, protected LanguageConsulter $languageConsulter,
        protected Translator $translator
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            $this->formTypeBuilder->createCategory(
                $builder, 'category_titles', $this->translator->trans('Titles', [], Manager::CONTEXT)
            )
        );

        $activeLanguages = $this->languageConsulter->getLanguages();

        foreach ($activeLanguages as $isocode => $language) {
            $builder->add(
                $this->formTypeBuilder->createText($builder, $isocode, $language, $isocode == $this->defaultLanguage)
            );
        }
    }
}