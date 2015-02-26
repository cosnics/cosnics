<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component\QuestionBrowser\QuestionTable;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Forms\ConfigureQuestionForm;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;

class ConfigUpdaterComponent extends Manager
{

    private $page_id;

    function run()
    {
        $config_index = Request :: get(self :: PARAM_CONFIG_ID);
        // $this->page_id = $this->get_root_content_object_id();
        // $survey_page = \repository\DataManager :: retrieve_by_id(Page :: class_name(), $this->page_id);
        // $configs = $survey_page->get_config();
        // $complex_question = \repository\DataManager :: retrieve_by_id(
        // ComplexContentObjectItem :: class_name(),
        // $configs[$config_index][PageConfig :: PROPERTY_FROM_VISIBLE_QUESTION_ID]);

        $form = new ConfigureQuestionForm($this, $config_index);

        if ($form->validate())
        {
            $created = $form->update_config();
            $message = $created ? Translation :: get('QuestionConfigurationUpdated') : Translation :: get(
                'QuestionConfigurationNotUpdated');
            $this->redirect(
                $message,
                ! $created,
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

            return implode("\n", $html);
        }
    }

    private function get_table()
    {
        $parameters = $this->get_parameters();
        $table = new QuestionTable($this, $parameters, $this->get_condition());
        return $table->as_html();
    }

    function get_condition()
    {
        $page_id = Request :: get(self :: PARAM_SURVEY_PAGE_ID);
        $condition = new EqualityCondition(
            ComplexContentObjectItem :: PROPERTY_PARENT,
            $page_id,
            ComplexContentObjectItem :: get_table_name());
        return $condition;
    }

    function get_complex_content_object_table_html($show_subitems_column = true, $model = null, $renderer = null)
    {
        // return parent :: get_complex_content_object_table_html($show_subitems_column, $model, new
        // SurveyBrowserTableCellRenderer($this, $this->get_complex_content_object_table_condition()));
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
                Translation :: get('ConfigurePage')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_SURVEY_PAGE_ID, self :: PARAM_COMPLEX_QUESTION_ITEM_ID, self :: PARAM_CONFIG_ID);
    }
}

?>