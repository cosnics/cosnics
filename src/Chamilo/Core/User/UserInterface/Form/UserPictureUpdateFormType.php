<?php
namespace Chamilo\Core\User\UserInterface\Form;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * @package Chamilo\Core\User\UserInterface\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserPictureUpdateFormType extends AbstractUserFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->buildPictureForm($builder, $options);

        if ($this->isPictureChangeable($options['executingUser'])) {
            $this->getFormButtonTypeBuilder()->addSaveAndResetButton($builder);
        }
    }
}