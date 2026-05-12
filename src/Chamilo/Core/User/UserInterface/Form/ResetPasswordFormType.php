<?php
namespace Chamilo\Core\User\UserInterface\Form;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Form\Service\FormButtonTypeBuilder;
use Chamilo\Libraries\UserInterface\Form\Service\FormTypeBuilder;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @package Chamilo\Core\User\UserInterface\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ResetPasswordFormType extends AbstractType
{
    public function __construct(
        protected FormTypeBuilder $formTypeBuilder, protected FormButtonTypeBuilder $formButtonTypeBuilder,
        protected Translator $translator
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $emailLabel = $this->translator->trans('Email', [], Manager::CONTEXT);

        $builder->add(
            $this->formTypeBuilder->createEmail(
                builder: $builder, name: User::PROPERTY_EMAIL, label: $emailLabel, constraints: [new Assert\Email()]
            )
        );

        $saveText = $this->translator->trans('Ok', [], StringUtilities::LIBRARIES);
        $saveGlyph = new FontAwesomeGlyph('check', ['me-1'], $saveText, 'fas');

        $this->formButtonTypeBuilder->addSubmitAndResetButton($builder, $saveText, $saveGlyph);
    }
}