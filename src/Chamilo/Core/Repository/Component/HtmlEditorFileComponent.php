<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Processor\Ckeditor\Processor;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Viewer\Architecture\Traits\ViewerTrait;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Format\Structure\PageConfiguration;

class HtmlEditorFileComponent extends Manager
{
    use ViewerTrait;

    public const PARAM_PLUGIN = 'plugin';

    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        $this->set_parameter('CKEditor', $this->getRequest()->query->get('CKEditor'));
        $this->set_parameter('CKEditorFuncNum', $this->getRequest()->query->get('CKEditorFuncNum'));
        $this->set_parameter('langCode', $this->getRequest()->query->get('langCode'));
    }

    public function run()
    {
        $plugin = $this->get_plugin();
        $this->set_parameter(self::PARAM_PLUGIN, $plugin);

        $this->getPageConfiguration()->setViewMode(PageConfiguration::VIEW_MODE_HEADERLESS);

        if (!$this->isAnyObjectSelectedInViewer())
        {
            $component = $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\Repository\Viewer\Manager::CONTEXT,
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
            );
            $component->set_maximum_select(\Chamilo\Core\Repository\Viewer\Manager::SELECT_SINGLE);

            return $component->run();
        }
        else
        {
            $processor = new Processor($this, $this->getObjectsSelectedInviewer());

            $html = [];

            $html[] = $this->render_header();
            $html[] = $processor->run();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function get_allowed_content_object_types()
    {
        $types = [];

        $active_types = DataManager::get_registered_types(true);

        foreach ($active_types as $active_type)
        {
            if (in_array(
                'Chamilo\Libraries\Architecture\Interfaces\Includeable', (array) class_implements($active_type)
            ))
            {
                $types[] = $active_type;
            }
        }

        return $types;
    }

    public function get_plugin()
    {
        return $this->getRequest()->query->get(self::PARAM_PLUGIN);
    }
}
