<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Forms\ConfigureQuestionForm;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Manager;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class ConfigureQuestionComponent extends Manager
{

    private $page_id;

    function run()
    {
       
        $this->page_id = $this->getRequest()->get(self :: PARAM_SURVEY_PAGE_ID);       
        $form = new ConfigureQuestionForm($this);

        if ($form->validate())
        {
            $created = $form->create_config();

            
            $message = $created ? Translation :: get('QuestionConfigurationCreated') : Translation :: get(
                'QuestionConfigurationNotCreated');
            $this->redirect(
                $message,
                (! $created),
                array(
                    self :: PARAM_ACTION => self :: ACTION_CONFIGURE_PAGE,
                    self :: PARAM_SURVEY_PAGE_ID => $this->page_id,
                    DynamicTabsRenderer :: PARAM_SELECTED_TAB => ConfigureComponent :: PAGE_CONFIGS_TAB));
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE)),
                Translation :: get('PageBuilderComponent')));
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_CONFIGURE_PAGE,
                        self :: PARAM_SURVEY_PAGE_ID => Request :: get(self :: PARAM_SURVEY_PAGE_ID))),
                Translation :: get('ConfigureComponent')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_SURVEY_PAGE_ID, self :: PARAM_COMPLEX_QUESTION_ITEM_ID);
    }
}

?>