<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Common\Import\ContentObjectImportService;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * $Id: importer.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib.repository_manager.component
 */
class ImporterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! RightsService :: getInstance()->canAddContentObjects($this->get_user(), $this->getWorkspace()))
        {
            throw new NotAllowedException();
        }

        $type = $this->getRequest()->query->get(self :: PARAM_IMPORT_TYPE);
        $contentObjectImportService = new ContentObjectImportService($type, $this->getWorkspace(), $this);

        $type = Request :: get(self :: PARAM_IMPORT_TYPE);

        if ($type)
        {
            if ($contentObjectImportService->hasFinished())
            {
                // Session :: register(self :: PARAM_MESSAGES, $controller->get_messages_for_url());
                $this->simple_redirect(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTENT_OBJECTS));
            }
            else
            {
                BreadcrumbTrail :: get_instance()->add(
                    new Breadcrumb(
                        $this->get_url(),
                        Translation :: get(
                            'ImportType',
                            array(
                                'TYPE' => Translation :: get(
                                    'ImportType' . StringUtilities :: getInstance()->createString($type)->upperCamelize())))));

                $html = array();

                $html[] = $this->render_header();
                $html[] = $contentObjectImportService->renderForm();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            BreadcrumbTrail :: get_instance()->add(
                new Breadcrumb($this->get_url(), Translation :: get('ChooseImportFormat')));

            $html = array();

            $html[] = $this->render_header();
            $html[] = $contentObjectImportService->renderTypeSelector($this->get_types());
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function get_types()
    {
        $types_folder = Path :: getInstance()->namespaceToFullPath('Chamilo\Core\Repository\Common\Import');
        $folders = Filesystem :: get_directory_content($types_folder, Filesystem :: LIST_DIRECTORIES, false);
        $folder_types = array();
        $sort_types = array();

        foreach ($folders as $folder)
        {
            $class = '\Chamilo\Core\Repository\Common\Import\\' . $folder . '\\' . $folder .
                 'ContentObjectImportController';

            if (class_exists($class) && $class :: is_available())
            {
                $folder_types[$folder] = Translation :: get('ImportType' . $folder);
                $sort_types[$folder] = strtolower(Translation :: get('ImportType' . $folder));
            }
        }

        asort($sort_types);

        $types = array();

        foreach ($sort_types as $key => $value)
        {
            $types[$key] = $folder_types[$key];
        }

        return $types;
    }
}
