<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Manager;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\Page;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class ConfigDeleterComponent extends Manager
{

    function run()
    {
        $config_index = Request :: get(self :: PARAM_CONFIG_ID);

        if (! empty($config_index))
        {
            if (! is_array($config_index))
            {
                $config_index = array($config_index);
            }

            // $page_id = Request :: get(self :: PARAM_SURVEY_PAGE_ID);
            $page_id = $this->get_root_content_object_id();

            $survey_page = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                Page :: class_name(),
                $page_id);

            $configs = $survey_page->get_config();

            $new_config = array();
            foreach ($configs as $key => $config)
            {
                if (! in_array($key, $config_index))
                {
                    $new_config[$key] = $config;
                }
            }

            $survey_page->set_config($new_config);
            $deleted = $survey_page->update();
            $message = ($deleted ? Translation :: get('QuestionConfigurationDeleted') : Translation :: get(
                'QuestionConfigurationNotDeleted'));

            $this->redirect(
                $message,
                ! $deleted,
                array(self :: PARAM_ACTION => self :: ACTION_CONFIGURE_PAGE, self :: PARAM_SURVEY_PAGE_ID => $page_id));
        }
        else
        {
            return $this->display_error_page(htmlentities(Translation :: get('NoConfigsSelected')));
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE)),
                Translation :: get('BrowseSurvey')));
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_CONFIGURE_PAGE,
                        self :: PARAM_SURVEY_PAGE_ID => Request :: get(self :: PARAM_SURVEY_PAGE_ID))),
                Translation :: get('ConfigurePage')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_SURVEY_PAGE_ID, self :: PARAM_COMPLEX_QUESTION_ITEM_ID);
    }
}

?>