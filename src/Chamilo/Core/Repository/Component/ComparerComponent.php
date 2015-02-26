<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Table\ContentObject\Version\VersionTable;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: comparer.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which can be used to compare a content object.
 */
class ComparerComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        // $trail = BreadcrumbTrail :: get_instance();
        $object_ids = Request :: post(VersionTable :: DEFAULT_NAME . VersionTable :: CHECKBOX_NAME_SUFFIX);

        if ($object_ids)
        {
            $object_id = $object_ids[0];
            $version_id = $object_ids[1];
        }
        else
        {
            $object_id = Request :: get(self :: PARAM_COMPARE_OBJECT);
            $version_id = Request :: get(self :: PARAM_COMPARE_VERSION);
        }

        if ($object_id && $version_id)
        {
            $object = $this->retrieve_content_object($object_id);

            if ($object->get_state() == ContentObject :: STATE_RECYCLED)
            {
                $this->force_menu_url($this->get_recycle_bin_url());
            }

            $html = array();

            $html[] = $this->render_header();
            $html[] = $object->get_difference($version_id)->render();
            $html[] = $this->render_footer();

            return implode("\n", $html);
        }
        else
        {
            return $this->display_warning_page(
                htmlentities(
                    Translation :: get(
                        'NoObjectSelected',
                        array('OBJECT' => Translation :: get('ContentObject')),
                        Utilities :: COMMON_LIBRARIES)));
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTENT_OBJECTS)),
                Translation :: get('RepositoryManagerBrowserComponent')));
        $breadcrumbtrail->add_help('repository_comparer');
    }
}
