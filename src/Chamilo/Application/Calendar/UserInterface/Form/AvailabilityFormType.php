<?php
namespace Chamilo\Application\Calendar\UserInterface\Form;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Service\AvailabilityService;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\UserInterface\Form\Service\FormButtonTypeBuilder;
use Chamilo\Libraries\UserInterface\Form\Service\FormTypeBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Group\UserInterface\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AvailabilityFormType extends AbstractType
{
    public function __construct(
        protected readonly FormTypeBuilder $formTypeBuilder,
        protected readonly FormButtonTypeBuilder $formButtonTypeBuilder, protected readonly Translator $translator,
        protected AvailabilityService $availabilityService
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            $this->formTypeBuilder->createMessage(
                $builder, 'message_availability',
                $this->translator->trans('CalendarAvailabilityInformation', [], Manager::CONTEXT)
            )
        );

        $availableCalendars = $this->availabilityService->getAvailableCalendars($options['user']);

        foreach ($availableCalendars as $ownedCalendarType => $ownedCalendars) {
            $builder->add(
                $this->formTypeBuilder->createCategory(
                    $builder, 'category_' . md5($ownedCalendarType),
                    $this->translator->trans('TypeName', [], $ownedCalendarType)
                )
            );

            foreach ($ownedCalendars as $ownedCalendar) {
                $builder->add(
                    $this->formTypeBuilder->createCheckbox(
                        $builder, $ownedCalendar->getUniqueIdentifier(), $ownedCalendar->name
                    )
                );
            }
        }

        $this->formButtonTypeBuilder->addSaveAndResetButton($builder);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['user' => null]);

        $resolver->setAllowedTypes('user', [User::class]);
    }
}