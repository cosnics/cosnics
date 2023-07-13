<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Viewer\Architecture\Traits\ViewerTrait;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Format\Structure\PageConfiguration;

/**
 * @package repository.lib.repository_manager.component
 */

/**
 * Repository manager component to restore objects.
 * This means moving objects from the recycle bin to there original
 * location.
 */
class RepositoryViewerComponent extends Manager
{
    use ViewerTrait;

    public const PARAM_ELEMENT_NAME = 'element_name';

    public function run()
    {
        $element_name = $this->get_element_name();
        $this->set_parameter(self::PARAM_ELEMENT_NAME, $element_name);
        $this->getPageConfiguration()->setViewMode(PageConfiguration::VIEW_MODE_HEADERLESS);

        if (!$this->isAnyObjectSelectedInViewer())
        {
            return $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\Repository\Viewer\Manager::CONTEXT,
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
            )->run();
        }
        else
        {
            $html = [];

            $html[] = $this->render_header();
            
            $object = DataManager::retrieve_by_id(File::class, $this->getObjectsSelectedInviewer());

            $html[] = '<script>';
            $html[] = 'window.opener.$("input[name=' . $element_name . '_title]").val("' . addslashes(
                    $object->get_title()
                ) . '");';
            $html[] =
                'window.opener.$("input[name=' . $element_name . ']").val("' . addslashes($object->get_id()) . '");';
            $html[] = 'window.close();';
            $html[] = '</script>';

            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function get_allowed_content_object_types()
    {
        return [File::class];
    }

    public function get_element_name()
    {
        return $this->getRequest()->query->get(self::PARAM_ELEMENT_NAME);
    }
}
