<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Assessment\Builder\Component\AssessmentMerger\ObjectTable;
use Chamilo\Core\Repository\ContentObject\Assessment\Builder\Manager;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Viewer\ViewerInterface;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package repository.lib.complex_builder.assessment.component
 */
class AssessmentMergerComponent extends Manager implements ViewerInterface, TableSupport
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    public function run()
    {
        $trail = BreadcrumbTrail::getInstance();
        $trail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE)),
                $this->get_root_content_object()->get_title()
            )
        );
        $trail->add(new Breadcrumb($this->get_url([]), Translation::get('MergeAssessment')));
        $assessment = $this->get_root_content_object();

        if (!\Chamilo\Core\Repository\Viewer\Manager::is_ready_to_be_published())
        {
            $component = $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\Repository\Viewer\Manager::context(),
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
            );
            $component->set_maximum_select(\Chamilo\Core\Repository\Viewer\Manager::SELECT_SINGLE);
            $component->set_parameter(
                \Chamilo\Core\Repository\Viewer\Manager::PARAM_ID,
                Request::get(\Chamilo\Core\Repository\Viewer\Manager::PARAM_ID)
            );

            return $component->run();
        }
        else
        {
            $selected_assessment = DataManager::retrieve_by_id(
                Assessment::class, \Chamilo\Core\Repository\Viewer\Manager::get_selected_objects()
            );
            $display = ContentObjectRenditionImplementation::launch(
                $selected_assessment, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_FULL, $this
            );

            $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer($selected_assessment);

            $html = [];

            $html[] = $this->render_header();
            $html[] = $display;
            $html[] = '<br />';
            $html[] = $this->buttonToolbarRenderer->render();
            $html[] = '<h3>' . Translation::get('SelectQuestions') . '</h3>';

            $params = array(
                \Chamilo\Core\Repository\Viewer\Manager::PARAM_ID => Request::get(
                    \Chamilo\Core\Repository\Viewer\Manager::PARAM_ID
                )
            );
            $table = new ObjectTable($this);

            $html[] = $table->as_html();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function getButtonToolbarRenderer($selected_assessment = null)
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            $commonActions->addButton(
                new Button(
                    Translation::get('AddAllQuestions'), new FontAwesomeGlyph('plus'),
                    $this->get_question_selector_url(null, $selected_assessment->get_id())
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function get_allowed_content_object_types()
    {
        return array(Assessment::class);
    }

    public function get_condition($selected_assessment)
    {
        $sub_condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
            ), new StaticConditionVariable($selected_assessment->get_id())
        );
        $condition = new SubselectCondition(

            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID),
            new PropertyConditionVariable(
                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_REF
            ), $sub_condition
        );

        return $condition;
    }

    public function get_question_selector_url($question_id, $assessment_id)
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_SELECT_QUESTIONS,
                self::PARAM_QUESTION_ID => $question_id,
                self::PARAM_ASSESSMENT_ID => $assessment_id,
                \Chamilo\Core\Repository\Viewer\Manager::PARAM_ID => Request::get(
                    \Chamilo\Core\Repository\Viewer\Manager::PARAM_ID
                )
            )
        );
    }

    public function get_table_condition($table_class_name)
    {
        $selected_assessment = DataManager::retrieve_by_id(
            Assessment::class, \Chamilo\Core\Repository\Viewer\Manager::get_selected_objects()
        );

        return $this->get_condition($selected_assessment);
    }
}
