<?php
namespace Chamilo\Core\Repository\Common\Renderer\Type;

use Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Table\ContentObject\Table\RepositoryTable;

class TableContentObjectRenderer extends ContentObjectRenderer
{

    /**
     * Returns the HTML output of this renderer.
     *
     * @return string The HTML output
     */
    public function as_html()
    {
        $class = RepositoryTable::class_name(false);

        if ($this->get_repository_browser()->has_filter_type())
        {
            $filter_type = $this->get_repository_browser()->get_filter_type();
            $template_registration = \Chamilo\Core\Repository\Configuration::registration_by_id($filter_type);

            $classname = $template_registration->get_content_object_type() . '\\' . $class;
            if (!class_exists($classname))
            {
                $classname = Manager::package() . '\Table\ContentObject\Table\\' . $class;
            }
        }
        else
        {
            $classname = Manager::package() . '\Table\ContentObject\Table\\' . $class;
        }

        $table = new $classname($this, $this->get_parameters(), $this->get_condition());

        return $table->as_html();
    }
}
