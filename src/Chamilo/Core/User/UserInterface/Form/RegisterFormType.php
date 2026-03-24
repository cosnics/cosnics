<?php
namespace Chamilo\Core\User\UserInterface\Form;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * @package Chamilo\Core\User\UserInterface\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RegisterFormType extends AbstractUserFormType
{
    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->buildPersonalDetailsForm($builder, $options);
        $this->buildSecurityForm($builder, $options);
        $this->buildPictureForm($builder, $options);
        $this->buildMailForm($builder);

        if ($this->isAnythingChangeable($options['executingUser'], $options['user'])) {
            $this->getFormButtonTypeBuilder()->addSaveAndResetButton($builder);
        }
    }
}