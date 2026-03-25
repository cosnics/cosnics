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
    protected string $defaultLanguage;

    protected FormTypeBuilder $formTypeBuilder;

    protected ItemRendererRegistry $itemRendererRegistry;

    protected LanguageConsulter $languageConsulter;

    protected Translator $translator;

    public function __construct(
        FormTypeBuilder $formTypeBuilder, string $defaultLanguage, ItemRendererRegistry $itemRendererRegistry,
        LanguageConsulter $languageConsulter, Translator $translator
    )
    {
        $this->defaultLanguage = $defaultLanguage;
        $this->itemRendererRegistry = $itemRendererRegistry;
        $this->languageConsulter = $languageConsulter;
        $this->translator = $translator;
        $this->formTypeBuilder = $formTypeBuilder;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $translator = $this->getTranslator();
        $formTypeBuilder = $this->getFormTypeBuilder();
        $defaultLanguage = $this->getDefaultLanguage();

        $formTypeBuilder->addCategory(
            $builder, 'category_titles', $translator->trans('Titles', [], Manager::CONTEXT)
        );

        $activeLanguages = $this->getLanguageConsulter()->getLanguages();

        foreach ($activeLanguages as $isocode => $language) {
            $formTypeBuilder->addText($builder, $isocode, $language, $isocode == $defaultLanguage);
        }
    }

    public function getDefaultLanguage(): string
    {
        return $this->defaultLanguage;
    }

    public function getFormTypeBuilder(): FormTypeBuilder
    {
        return $this->formTypeBuilder;
    }

    public function getItemRendererRegistry(): ItemRendererRegistry
    {
        return $this->itemRendererRegistry;
    }

    public function getLanguageConsulter(): LanguageConsulter
    {
        return $this->languageConsulter;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}