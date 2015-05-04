<?php
namespace Chamilo\Core\Repository\Share\Component;

use Chamilo\Core\Repository\Share\Form\ContentObjectShareForm;
use Chamilo\Core\Repository\Share\Manager;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Creator for share rights
 *
 * @author Pieterjan Broekaert
 * @author Sven Vanpoucke
 */
class CreateComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $ids = $this->get_content_object_ids();
        if ($ids)
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            $share_form = ContentObjectShareForm :: factory(
                ContentObjectShareForm :: TYPE_CREATE,
                $ids,
                $this->get_user(),
                $this->get_url());

            if ($share_form->validate())
            {
                $succes = $share_form->create_content_object_share();

                $message = $succes ? Translation :: get('ObjectShared') : Translation :: get(
                    'ObjectNotShared',
                    array('OBJECT' => Translation :: get('ContentObject')),
                    Utilities :: COMMON_LIBRARIES);

                $this->redirect($message, ! $succes, array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
            }
            else
            {
                $html = array();

                $html[] = $this->render_header();
                $html[] = $this->display_content_objects();
                $html[] = $share_form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            return $this->display_error_page(
                Translation :: get(
                    'NoObjectsSelected',
                    array('OBJECTS' => Translation :: get('ContentObjects')),
                    Utilities :: COMMON_LIBRARIES));
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE)),
                Translation :: get('ShareManagerBrowserComponent')));

        $breadcrumbtrail->add_help('repository_content_object_share_rights_creator');
    }

    public function get_additional_parameters()
    {
        return array(\Chamilo\Core\Repository\Manager :: PARAM_CONTENT_OBJECT_ID);
    }
}
