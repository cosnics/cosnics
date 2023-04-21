<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Processor\Ckeditor\Processor;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Format\Structure\PageConfiguration;
use Chamilo\Libraries\Platform\Session\Request;

class HtmlEditorFileComponent extends Manager
{
    public const PARAM_PLUGIN = 'plugin';

    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        $this->set_parameter('CKEditor', Request::get('CKEditor'));
        $this->set_parameter('CKEditorFuncNum', Request::get('CKEditorFuncNum'));
        $this->set_parameter('langCode', Request::get('langCode'));
    }

    public function run()
    {
        $plugin = $this->get_plugin();
        $this->set_parameter(self::PARAM_PLUGIN, $plugin);

        $this->getPageConfiguration()->setViewMode(PageConfiguration::VIEW_MODE_HEADERLESS);

        if (!\Chamilo\Core\Repository\Viewer\Manager::is_ready_to_be_published())
        {
            $component = $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\Repository\Viewer\Manager::context(),
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
            );
            $component->set_maximum_select(\Chamilo\Core\Repository\Viewer\Manager::SELECT_SINGLE);

            return $component->run();
        }
        else
        {
            $processor = new Processor($this, \Chamilo\Core\Repository\Viewer\Manager::get_selected_objects());

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
        return Request::get(self::PARAM_PLUGIN);
    }
}
