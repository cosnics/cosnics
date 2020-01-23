<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Form;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass\Rubric;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_Environment;

/**
 *
 * @package repository.lib.content_object.rubric
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */

/**
 * A form to create/update a rubric
 */
class RubricForm extends ContentObjectForm
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    // Inherited
    public function create_content_object()
    {
        $object = new Rubric();
        $this->set_content_object($object);

        return parent::create_content_object();
    }

    public function build_creation_form($htmleditor_options = array(), $in_tab = false)
    {
        parent::build_creation_form($htmleditor_options, $in_tab);

        $this->addElement(
            'html', $this->getTwig()->render('Chamilo\Core\Repository\ContentObject\Rubric:RubricBuilder.html.twig')
        );
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        if (!isset($this->container))
        {
            $this->container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
        }

        return $this->container;
    }

    /**
     * @return Twig_Environment
     */
    protected function getTwig()
    {
        return $this->getContainer()->get('twig.environment');
    }
}
