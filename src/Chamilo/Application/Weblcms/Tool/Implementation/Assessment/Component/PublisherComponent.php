<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Publisher;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

class PublisherComponent extends Manager
{

    /**
     * Modified version of the default Publisher to allow for the feedback-functionality
     */
    public function run()
    {
        if (! ($this->get_course()->is_course_admin($this->get_user()) || $this->is_allowed(WeblcmsRights :: ADD_RIGHT)))
        {
            throw new NotAllowedException();
        }

        if (! \Chamilo\Core\Repository\Viewer\Manager :: is_ready_to_be_published())
        {
            $factory = new ApplicationFactory(
                \Chamilo\Core\Repository\Viewer\Manager :: context(),
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
            return $factory->run();
        }
        else
        {
            $objects = \Chamilo\Core\Repository\Viewer\Manager :: get_selected_objects();

            $mode = Request :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLISH_MODE);
            $publish_type = PlatformSetting :: get(
                'display_publication_screen',
                \Chamilo\Application\Weblcms\Manager :: context());
            $show_form = (($publish_type == \Chamilo\Application\Weblcms\Tool\Manager :: PUBLISH_TYPE_FORM) || ($publish_type ==
                 \Chamilo\Application\Weblcms\Tool\Manager :: PUBLISH_TYPE_BOTH &&
                 $mode != \Chamilo\Application\Weblcms\Tool\Manager :: PUBLISH_MODE_QUICK));

            $publisher = new Publisher($this, $objects, $show_form);

            if ($publisher->ready_to_publish())
            {
                $success = $publisher->publish();

                $message = Translation :: get(
                    ($success ? 'ObjectPublished' : 'ObjectNotPublished'),
                    array('OBJECT' => Translation :: get('Object')),
                    Utilities :: COMMON_LIBRARIES);

                $parameters = array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_BROWSE);

                if ($publisher->is_publish_and_build_submit())
                {
                    $parameters[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION] = \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_BUILD_COMPLEX_CONTENT_OBJECT;

                    $publications = $publisher->get_publications();
                    $parameters[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID] = $publications[0]->get_id();
                }

                $this->redirect($message, ! $success, $parameters);
            }
            else
            {
                $html = array();

                $html[] = $this->render_header();
                $html[] = $publisher->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
    }

    public function get_allowed_content_object_types()
    {
        return $this->get_allowed_types();
    }

    public function get_additional_parameters()
    {
        return array(
            \Chamilo\Core\Repository\Viewer\Manager :: PARAM_ID,
            \Chamilo\Core\Repository\Viewer\Manager :: PARAM_ACTION);
    }

    /**
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }
}
