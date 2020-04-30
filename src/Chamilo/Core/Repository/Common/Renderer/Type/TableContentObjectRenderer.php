<?php
namespace Chamilo\Core\Repository\Common\Renderer\Type;

use Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Service\TemplateRegistrationConsulter;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;

class TableContentObjectRenderer extends ContentObjectRenderer
{

    /**
     * Returns the HTML output of this renderer.
     *
     * @return string
     * @throws \ReflectionException
     */
    public function as_html()
    {
        if ($this->get_repository_browser()->has_filter_type())
        {
            $filter_type = $this->get_repository_browser()->get_filter_type();
            $template_registration =
                $this->getTemplateRegistrationConsulter()->getTemplateRegistrationByIdentifier($filter_type);

            $classname = $template_registration->get_content_object_type() . '\RepositoryTable';
            if (!class_exists($classname))
            {
                $classname = Manager::package() . '\Table\ContentObject\Table\RepositoryTable';
            }
        }
        else
        {
            $classname = Manager::package() . '\Table\ContentObject\Table\RepositoryTable';
        }

        $table = new $classname($this, $this->get_parameters(), $this->get_condition());

        return $table->as_html();
    }

    /**
     * @return \Chamilo\Core\Repository\Service\TemplateRegistrationConsulter
     * @throws \Exception
     */
    public function getTemplateRegistrationConsulter()
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            TemplateRegistrationConsulter::class
        );
    }
}
