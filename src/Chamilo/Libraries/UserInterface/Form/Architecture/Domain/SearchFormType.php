<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture\Domain;

use Chamilo\Core\Admin\Manager;
use Chamilo\Libraries\UserInterface\Form\Service\FormButtonTypeBuilder;
use Chamilo\Libraries\UserInterface\Form\Service\FormTypeBuilder;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Group\UserInterface\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SearchFormType extends AbstractType
{
    public const string PARAM_SIMPLE_SEARCH_QUERY = 'query';

    public function __construct(
        protected readonly FormTypeBuilder $formTypeBuilder,
        protected readonly FormButtonTypeBuilder $formButtonTypeBuilder, protected readonly Translator $translator
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $searchLabel = $this->translator->trans('Search', [], Manager::CONTEXT);

        $builder->add(
            $this->formTypeBuilder->createText(
                $builder, self::PARAM_SIMPLE_SEARCH_QUERY, $searchLabel, false, [], ['row_attr' => ['class' => '']]
            )
        );

        $builder->add(
            $this->formButtonTypeBuilder->createButton(
                $builder, 'submit', null, new FontAwesomeGlyph('magnifying-glass', ['me-1'], $searchLabel, 'fa-solid'),
                ['btn', 'btn-outline-primary'], SubmitType::class
            )
        );

        $builder->add(
            $this->formButtonTypeBuilder->createButton(
                $builder, 'cancel', null, new FontAwesomeGlyph('arrow-rotate-left', ['me-1'], $searchLabel, 'fa-solid'),
                ['btn', 'btn-outline-secondary'], SubmitType::class
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['hasData' => false]);

        $resolver->setAllowedTypes('hasData', ['bool']);
    }
}