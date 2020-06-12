<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Teams\Form\Type
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class QuickUsersSubscribeFormType extends AbstractType
{
    const ELEMENT_USER_IDENTIFIERS = 'user_identifiers';

    const TRANSLATION_CONTEXT = 'Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup';

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(\Symfony\Component\Translation\Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            self::ELEMENT_USER_IDENTIFIERS, TextareaType::class
        );
    }
}
