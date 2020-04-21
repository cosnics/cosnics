<?php
namespace Chamilo\Libraries\Format\Form;

use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Hogent\Application\Weblcms\Tool\Implementation\Survey\Form\CustomCourseSurveyDates\FormType;
use InvalidArgumentException;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig_Environment;
use Twig_FactoryRuntimeLoader;
use Twig_Loader_Chain;
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
     * @param \Twig_Environment $twig
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator
     *
     * @return \Symfony\Component\Form\FormFactoryInterface
     */
    public function buildFormFactory(Twig_Environment $twig, ValidatorInterface $validator)
    {
        $chamiloFormTemplatesPath = __DIR__ . '/../../Resources/Templates/Form';

        $this->createTwigLoader($twig, $chamiloFormTemplatesPath);
        $this->createTwigExtension($twig, $chamiloFormTemplatesPath);

        return Forms::createFormFactoryBuilder()
            ->addExtension(new HttpFoundationExtension())
            ->addExtension(new ValidatorExtension($validator))
            ->addTypes($this->chamiloFormTypes)
            ->getFormFactory();

    }

    /**
     * Adds the twig loaders that are necessary for the form templates
     *
     * @param \Twig_Environment $twig
     * @param string $chamiloFormTemplatesPath
     * @throws \InvalidArgumentException
     */
    protected function createTwigLoader(Twig_Environment $twig, $chamiloFormTemplatesPath)
    {
        $vendorTwigBridgeDir = Path::getInstance()->getBasePath() . '../vendor/symfony/twig-bridge/';

        $form_loader = new Twig_Loader_Filesystem(
            array($chamiloFormTemplatesPath, $vendorTwigBridgeDir . '/Resources/views/Form'));

        $loader = $twig->getLoader();
        if (! $loader instanceof Twig_Loader_Chain)
        {
            throw new InvalidArgumentException('The given Twig_Environment must use a chain loader');
        }

        $loader->addLoader($form_loader);
    }

    /**
     * Adds the twig extension for the forms
     *
     * @param \Twig_Environment $twig
     * @param string $chamiloFormTemplatesPath
     */
    protected function createTwigExtension(Twig_Environment $twig, $chamiloFormTemplatesPath)
    {
        $chamilo_files = Filesystem::get_directory_content($chamiloFormTemplatesPath, Filesystem::LIST_FILES, false);
        $twig_rendering_files = array_merge(array('form_div_layout.html.twig'), $chamilo_files);

        $formEngine = new TwigRendererEngine($twig_rendering_files, $twig);

        $twig->addRuntimeLoader(new Twig_FactoryRuntimeLoader(array(
            FormRenderer::class => function () use ($formEngine) {
                return new FormRenderer($formEngine);
            },
        )));

        $twig->addExtension(new FormExtension());
    }
}