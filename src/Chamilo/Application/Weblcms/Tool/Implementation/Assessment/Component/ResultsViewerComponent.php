<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager as WeblcmsTrackingDataManager;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Table\AssessmentAttemptTableRenderer;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Attempt\AbstractAttempt;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Views the results of an assessment
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ResultsViewerComponent extends Manager
{
    private ButtonToolBarRenderer $buttonToolbarRenderer;

    private ContentObjectPublication $publication;

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    public function run()
    {
        $pid = $this->getRequest()->query->get(self::PARAM_ASSESSMENT);

        $this->set_parameter(self::PARAM_ASSESSMENT, $pid);
        $this->publication = DataManager::retrieve_by_id(
            ContentObjectPublication::class, $pid
        );

        $assessment = $this->publication->get_content_object();

        $this->add_assessment_title_breadcrumb($assessment);

        if (!$this->is_allowed(WeblcmsRights::VIEW_RIGHT, $this->publication))
        {
            throw new NotAllowedException();
        }

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->getButtonToolbarRenderer($assessment)->render();

        $html[] = '<div class="panel panel-default">';

        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">';

        $glyph = new NamespaceIdentGlyph(
            'Chamilo\Core\Repository\ContentObject\Assessment', false, false, false, IdentGlyph::SIZE_MINI
        );

        $html[] = $glyph->render() . ' ' . $assessment->get_title();
        $html[] = '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body">';
        $html[] = $assessment->get_description();
        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = $this->renderTable();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }

    protected function add_assessment_title_breadcrumb(Assessment $assessment)
    {
        $breadcrumb_trail = BreadcrumbTrail::getInstance();
        $breadcrumbs = $breadcrumb_trail->getBreadcrumbs();

        $breadcrumbs[$breadcrumb_trail->size() - 1] = new Breadcrumb(
            $this->get_url([self::PARAM_ACTION => self::ACTION_VIEW_RESULTS]),
            $this->getTranslator()->trans('ViewResultsForAssessment', ['TITLE' => $assessment->get_title()])
        );

        $breadcrumb_trail->set($breadcrumbs);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Exception
     */
    public function getAssessmentAttemptCondition(): AndCondition
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(AssessmentAttempt::class, AssessmentAttempt::PROPERTY_ASSESSMENT_ID),
            new StaticConditionVariable($this->getRequest()->query->get(self::PARAM_ASSESSMENT))
        );

        if (!$this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(AssessmentAttempt::class, AbstractAttempt::PROPERTY_USER_ID),
                new StaticConditionVariable($this->get_user_id())
            );
        }

        if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $search_properties = [];

            $search_properties[] = new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME);
            $search_properties[] = new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME);
            $search_properties[] = new PropertyConditionVariable(User::class, User::PROPERTY_OFFICIAL_CODE);

            $search_conditions = $this->getButtonToolbarRenderer()->getConditions($search_properties);

            if ($search_conditions)
            {
                $conditions[] = $search_conditions;
            }
        }

        return new AndCondition($conditions);
    }

    public function getAssessmentAttemptTableRenderer(): AssessmentAttemptTableRenderer
    {
        return $this->getService(AssessmentAttemptTableRenderer::class);
    }

    /**
     * @throws NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function getButtonToolbarRenderer(?Assessment $assessment = null): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $translator = $this->getTranslator();

            if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
            {
                $aid = $this->getRequest()->query->get(self::PARAM_ASSESSMENT);

                $commonActions = new ButtonGroup();

                $commonActions->addButton(
                    new Button(
                        $translator->trans('ShowAll', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('folder'),
                        $this->get_url(), ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );

                $commonActions->addButton(
                    new Button(
                        $translator->trans('DeleteAllResults', [], Manager::CONTEXT), new FontAwesomeGlyph('times'),
                        $this->get_url(
                            [
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DELETE_RESULTS,
                                self::PARAM_ASSESSMENT => $aid
                            ]
                        ), ToolbarItem::DISPLAY_ICON_AND_LABEL,
                        $translator->trans('ConfirmChosenAction', [], StringUtilities::LIBRARIES)
                    )
                );

                if ($assessment instanceof Assessment)
                {
                    $commonActions->addButton(
                        new Button(
                            $translator->trans('DownloadDocuments', [], Manager::CONTEXT),
                            new FontAwesomeGlyph('download'), $this->get_url(
                            [
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_SAVE_DOCUMENTS,
                                self::PARAM_ASSESSMENT => $aid
                            ]
                        ), ToolbarItem::DISPLAY_ICON_AND_LABEL
                        )
                    );

                    $commonActions->addButton(
                        new Button(
                            $translator->trans('RawExportResults', [], Manager::CONTEXT),
                            new FontAwesomeGlyph('download'), $this->get_url(
                            [
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_RAW_EXPORT_RESULTS,
                                self::PARAM_ASSESSMENT => $this->getRequest()->query->get(self::PARAM_ASSESSMENT)
                            ]
                        ), ToolbarItem::DISPLAY_ICON_AND_LABEL
                        )
                    );
                }

                $buttonToolbar->addButtonGroup($commonActions);
            }

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    public function get_publication(): ContentObjectPublication
    {
        return $this->publication;
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Exception
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems =
            WeblcmsTrackingDataManager::count_assessment_attempts_with_user($this->getAssessmentAttemptCondition());
        $assessmentAttemptTableRenderer = $this->getAssessmentAttemptTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $assessmentAttemptTableRenderer->getParameterNames(),
            $assessmentAttemptTableRenderer->getDefaultParameterValues(), $totalNumberOfItems
        );

        $assessmentAttempts = WeblcmsTrackingDataManager::retrieve_assessment_attempts_with_user(
            $this->getAssessmentAttemptCondition(), $tableParameterValues->getOffset(),
            $tableParameterValues->getNumberOfItemsPerPage(),
            $assessmentAttemptTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $assessmentAttemptTableRenderer->legacyRender($this, $tableParameterValues, $assessmentAttempts);
    }
}
