<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component\Merger\MergerTable;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Manager;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\Page;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Viewer\Component\ViewerComponent;
use Chamilo\Core\Repository\Viewer\ViewerInterface;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;

/**
 * $Id: survey_page_merger.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_builder.survey_page.component
 */
class MergerComponent extends Manager implements ViewerInterface
{

    function run()
    {
        $survey_page = $this->get_root_content_object();

        if (! ViewerComponent :: is_ready_to_be_published())
        {
            $repo_viewer = ViewerComponent :: construct($this);
            $repo_viewer->set_maximum_select(ViewerComponent :: SELECT_SINGLE);
            $repo_viewer->set_parameter(ViewerComponent :: PARAM_ID, Request :: get(ViewerComponent :: PARAM_ID));
            return $repo_viewer->run();
        }
        else
        {
            $selected_survey_page = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object(
                ViewerComponent :: get_selected_objects());

            $bar = $this->get_action_bar($selected_survey_page);

            $html = array();

            $html[] = $this->render_header();
            $html[] = ContentObjectRenditionImplementation :: launch(
                $selected_survey_page,
                ContentObjectRendition :: FORMAT_HTML,
                ContentObjectRendition :: VIEW_FULL,
                $this);
            $html[] = '<br />';
            $html[] = $bar->as_html();
            $html[] = '<h3>' . Translation :: get('Selects') . '</h3>';

            $params = array(ViewerComponent :: PARAM_ID => Request :: get(ViewerComponent :: PARAM_ID));
            $table = new MergerTable(
                $this,
                array_merge($params, $this->get_parameters()),
                $this->get_condition($selected_survey_page));

            $html[] = $table->as_html();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    function get_condition($selected_survey_page)
    {
        $sub_condition = new EqualityCondition(
            ComplexContentObjectItem :: PROPERTY_PARENT,
            $selected_survey_page->get_id());
        $condition = new SubselectCondition(
            ContentObject :: PROPERTY_ID,
            ComplexContentObjectItem :: PROPERTY_REF,
            ComplexContentObjectItem :: get_table_name(),
            $sub_condition,
            ContentObject :: get_table_name());

        return $condition;
    }

    function get_question_selector_url($question_id, $survey_page_id)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_SELECT_QUESTIONS,
                self :: PARAM_QUESTION_ID => $question_id,
                self :: PARAM_SURVEY_PAGE_ID => $survey_page_id,
                ViewerComponent :: PARAM_ID => Request :: get(ViewerComponent :: PARAM_ID)));
    }

    function get_action_bar($selected_survey_page)
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('AddAllQuestions'),
                Theme :: getInstance()->getCommonImagePath('action_add'),
                $this->get_question_selector_url(null, $selected_survey_page->get_id())));

        return $action_bar;
    }

    function get_allowed_content_object_types()
    {
        return array(Page :: class_name());
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE)),
                Translation :: get('PageBuilderComponent')));
    }
}

?>