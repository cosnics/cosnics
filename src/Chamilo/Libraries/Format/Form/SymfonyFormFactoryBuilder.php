<?php

namespace Chamilo\Libraries\Format\Form;

use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Hogent\Application\Weblcms\Tool\Implementation\Survey\Form\CustomCourseSurveyDates\FormType;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\FactoryRuntimeLoader;
use Twig_Environment;
use Twig_Loader_Filesystem;

/**
 * Builds the SymfonyFormFactory
 * More information can be found at the Symfony Form Component manual:
 *
 * @link http://symfony.com/doc/current/book/forms.html
 * @package Chamilo\Libraries\Format\Form
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SymfonyFormFactoryBuilder
{
    /**
     * @var FormTypeInterface[]
     */
    protected $chamiloFormTypes;

    /**
     * SymfonyFormFactoryBuilder constructor.
     */
    public function __construct()
    {
        $this->chamiloFormTypes = [];
    }

    /**
     * @param \Symfony\Component\Form\FormTypeInterface $formType
     */
    public function addFormType(FormTypeInterface $formType)
    {
        $this->chamiloFormTypes[] = $formType;
    }

    /**
     * Builds the FormFactory
     *
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator
     * @param CsrfTokenManagerInterface $csrfTokenManager
     *
     * @return \Symfony\Component\Form\FormFactoryInterface
     */
    public function buildFormFactory(ValidatorInterface $validator, CsrfTokenManagerInterface $csrfTokenManager)
    {
        return Forms::createFormFactoryBuilder()
            ->addExtension(new HttpFoundationExtension())
            ->addExtension(new ValidatorExtension($validator))
            ->addExtension(new CsrfExtension($csrfTokenManager))
            ->addTypes($this->chamiloFormTypes)
            ->getFormFactory();
    }
}
