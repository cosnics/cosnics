<?php

namespace Chamilo\Core\Repository\Form;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Core\User\Storage\DataClass\User;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Shortcut form for shortcut component
 * @see \Chamilo\Core\Repository\Component\CourseExporterComponent
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseExporterFormType extends \Symfony\Component\Form\AbstractType
{
    const ELEMENT_COURSE = 'course';

    /**
     * @var \Chamilo\Application\Weblcms\Service\CourseService
     */
    protected $courseService;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * CourseExporterFormType constructor.
     *
     * @param \Chamilo\Application\Weblcms\Service\CourseService $courseService
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(
        \Chamilo\Application\Weblcms\Service\CourseService $courseService,
        \Symfony\Component\Translation\Translator $translator
    )
    {
        $this->courseService = $courseService;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];
        if (!$user instanceof User)
        {
            throw new \RuntimeException('The given user is not a valid User object');
        }

        $courses = $this->courseService->getCoursesWhereUserIsTeacher($user);

        $courseLabel = function (Course $course) {
            return $course->get_title() . ' (' . $course->get_visual_code() . ')';
        };

        $builder->add(
            self::ELEMENT_COURSE, ChoiceType::class, [
                'choices' => $courses,
                'choices_as_values' => true,
                'choice_label' => $courseLabel,
                'label' => $this->translator->trans('SelectCourse', [], 'Chamilo\Core\Repository')
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'user' => null
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'course_exporter';
    }
}