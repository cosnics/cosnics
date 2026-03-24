<?php
namespace Chamilo\Core\User\UserInterface\Form;

use Chamilo\Core\User\UserInterface\Form\Service\UserFormDataMapper;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @package Chamilo\Core\User\UserInterface\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserFormType extends AbstractUserFormType
{
    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->buildPersonalDetailsForm($builder, $options);
        $this->buildSecurityForm($builder, $options);
        $this->buildPictureForm($builder, $options);
        $this->buildAccountForm($builder, $options);
        $this->buildMailForm($builder);

        if ($this->isAnythingChangeable($options['executingUser'], $options['user'])) {
            $this->getFormButtonTypeBuilder()->addSaveAndResetButton($builder);
        }

        $builder->setDataMapper(new UserFormDataMapper());
    }
}