<?php
namespace Chamilo\Libraries\Format\Form;

use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Forms;
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
     * Builds the FormFactory
     *
     * @param \Twig_Environment $twig
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator
     *
     * @return \Symfony\Component\Form\FormFactoryInterface
     */
    public function buildFormFactory(\Twig_Environment $twig)
    {
        $chamilo_form_templates_path = __DIR__ . '/../../Resources/Templates/Form';

        $this->addTwigLoader($twig, $chamilo_form_templates_path);
        $this->addTwigExtension($twig, $chamilo_form_templates_path);

        return Forms::createFormFactoryBuilder()->addExtension(new HttpFoundationExtension())->getFormFactory();
    }

    /**
     * Adds the twig loaders that are necessary for the form templates
     *
     * @param \Twig_Environment $twig
     * @param string $chamiloFormTemplatesPath
     * @throws \InvalidArgumentException
     */
    protected function addTwigLoader(\Twig_Environment $twig, $chamiloFormTemplatesPath)
    {
        $vendorTwigBridgeDir = Path::getInstance()->getBasePath() . '../vendor/symfony/twig-bridge/';

        $form_loader = new Twig_Loader_Filesystem(
            array($chamiloFormTemplatesPath, $vendorTwigBridgeDir . '/Resources/views/Form'));

        $loader = $twig->getLoader();
        if (! $loader instanceof \Twig_Loader_Chain)
        {
            throw new \InvalidArgumentException('The given Twig_Environment must use a chain loader');
        }

        $loader->addLoader($form_loader);
    }

    /**
     * Adds the twig extension for the forms
     *
     * @param \Twig_Environment $twig
     * @param string $chamiloFormTemplatesPath
     */
    protected function addTwigExtension(\Twig_Environment $twig, $chamiloFormTemplatesPath)
    {
        $chamilo_files = Filesystem::get_directory_content($chamiloFormTemplatesPath, Filesystem::LIST_FILES, false);
        $twig_rendering_files = array_merge(array('form_div_layout.html.twig'), $chamilo_files);

        $formEngine = new TwigRendererEngine($twig_rendering_files);
        $formEngine->setEnvironment($twig);

        $twig->addExtension(new FormExtension(new TwigRenderer($formEngine)));
    }
}