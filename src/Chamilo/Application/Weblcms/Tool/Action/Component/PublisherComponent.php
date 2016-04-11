<?php
namespace Chamilo\Application\Weblcms\Tool\Action\Component;

use Chamilo\Application\Weblcms\ContentObjectPublisher;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Action\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: announcement_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.announcement.component
 */
class PublisherComponent extends Manager implements \Chamilo\Core\Repository\Viewer\ViewerInterface, DelegateComponent
{

    public function run()
    {
        if (! ($this->get_course()->is_course_admin($this->get_user()) || $this->is_allowed(WeblcmsRights :: ADD_RIGHT)))
        {
            throw new NotAllowedException();
        }

        if (! \Chamilo\Core\Repository\Viewer\Manager :: any_object_selected())
        {
            $applicationConfiguration = new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this);
            $applicationConfiguration->set(\Chamilo\Core\Repository\Viewer\Manager :: SETTING_TABS_DISABLED, true);

            $factory = new ApplicationFactory(
                \Chamilo\Core\Repository\Viewer\Manager :: context(),
                $applicationConfiguration);

            return $factory->run();
        }
        else
        {
            $objects = \Chamilo\Core\Repository\Viewer\Manager :: get_selected_objects();

            $mode = Request :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLISH_MODE);

            $this->set_parameter(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLISH_MODE, $mode);
            $publish_type = PlatformSetting :: get('display_publication_screen', 'Chamilo\Application\Weblcms');

            $show_form = (($publish_type == \Chamilo\Application\Weblcms\Tool\Manager :: PUBLISH_TYPE_FORM) || ($publish_type ==
                 \Chamilo\Application\Weblcms\Tool\Manager :: PUBLISH_TYPE_BOTH &&
                 $mode != \Chamilo\Application\Weblcms\Tool\Manager :: PUBLISH_MODE_QUICK));

            $publisher = new ContentObjectPublisher($this, $objects, $show_form);

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

                if ($publisher->is_publish_and_view_submit())
                {
                    $parameters[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION] = \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT;

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

    /**
     * Overwrite render header to add the wizard
     *
     * @return string
     */
    public function render_header()
    {
        $html = array();
        $html[] = parent :: render_header();

        $html[] = '<ul class="nav nav-wizard publication-wizard">';

        if (! \Chamilo\Core\Repository\Viewer\Manager :: any_object_selected())
        {
            $stepOneClass = 'active';
            $stepTwoClass = 'disabled';
        }
        else
        {
            $stepOneClass = 'done';
            $stepTwoClass = 'active';
        }

        $html[] = '<li class="' . $stepOneClass . '"><a href="#">' . $this->getWizardFirstStepTitle() . '</a></li>';

        $html[] = '<li class="' . $stepTwoClass . '"><a href="#">' . $this->getTranslation('SecondStepPublish') .
             '</a></li>';

        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the title for the first step wizard
     *
     * @return string
     */
    protected function getWizardFirstStepTitle()
    {
        $action = Request :: get(\Chamilo\Core\Repository\Viewer\Manager :: PARAM_ACTION);
        switch ($action)
        {
            case \Chamilo\Core\Repository\Viewer\Manager :: ACTION_CREATOR :
                return $this->getTranslation('FirstStepCreate');
            case \Chamilo\Core\Repository\Viewer\Manager :: ACTION_BROWSER :
                return $this->getTranslation('FirstStepBrowseInWorkspaces');
            case \Chamilo\Core\Repository\Viewer\Manager :: ACTION_IMPORTER :
                return $this->getTranslation('FirstStepImport');
        }
    }

    /**
     * Helper functionality
     *
     * @param string $variable
     * @param array $parameters
     *
     * @return string
     */
    protected function getTranslation($variable, $parameters = array())
    {
        return Translation :: getInstance()->get($variable, $parameters, Manager :: context());
    }
}
