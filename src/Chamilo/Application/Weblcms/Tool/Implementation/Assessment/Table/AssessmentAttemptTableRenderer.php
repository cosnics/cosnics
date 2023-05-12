<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Table;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Component\AttemptResultViewerComponent;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Storage\DataClass\Publication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Attempt\AbstractAttempt;
use Chamilo\Core\Repository\ContentObject\Hotpotatoes\Storage\DataClass\Hotpotatoes;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordListTableRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableParameterValues;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Table
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AssessmentAttemptTableRenderer extends RecordListTableRenderer implements TableRowActionsSupport
{
    public const DEFAULT_ORDER_COLUMN_INDEX = 1;

    /**
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     */
    protected Application $application;

    protected DatetimeUtilities $datetimeUtilities;

    public function __construct(
        DatetimeUtilities $datetimeUtilities, Translator $translator, UrlGenerator $urlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager
    )
    {
        $this->datetimeUtilities = $datetimeUtilities;

        parent::__construct($translator, $urlGenerator, $htmlTableRenderer, $pager);
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    /**
     * @throws \ReflectionException
     */
    protected function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_FIRSTNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_LASTNAME));
        $this->addColumn(new DataClassPropertyTableColumn(User::class, User::PROPERTY_OFFICIAL_CODE));

        $this->addColumn(
            new DataClassPropertyTableColumn(AssessmentAttempt::class, AbstractAttempt::PROPERTY_START_TIME)
        );

        $this->addColumn(
            new DataClassPropertyTableColumn(AssessmentAttempt::class, AbstractAttempt::PROPERTY_END_TIME)
        );

        $this->addColumn(
            new DataClassPropertyTableColumn(AssessmentAttempt::class, AbstractAttempt::PROPERTY_TOTAL_TIME)
        );

        $publication = $this->application->get_publication();
        $parameters = new DataClassRetrieveParameters(
            new EqualityCondition(
                new PropertyConditionVariable(Publication::class, Publication::PROPERTY_PUBLICATION_ID),
                new StaticConditionVariable($publication->get_id())
            )
        );
        $assessment_publication = DataManager::retrieve(Publication::class, $parameters);

        if ($this->application->is_allowed(WeblcmsRights::EDIT_RIGHT, $publication) ||
            $assessment_publication->get_configuration()->show_score())
        {
            $this->addColumn(
                new DataClassPropertyTableColumn(
                    AssessmentAttempt::class, AbstractAttempt::PROPERTY_TOTAL_SCORE
                )
            );
        }

        $this->addColumn(
            new DataClassPropertyTableColumn(AssessmentAttempt::class, AbstractAttempt::PROPERTY_STATUS)
        );
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     */
    public function legacyRender(
        Application $application, TableParameterValues $parameterValues, ArrayCollection $tableData,
        ?string $tableName = null
    ): string
    {
        $this->application = $application;

        return parent::render($parameterValues, $tableData, $tableName); // TODO: Change the autogenerated stub
    }

    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $assessmentAttempt): string
    {
        $translator = $this->getTranslator();
        $datetimeUtilities = $this->getDatetimeUtilities();

        switch ($column->get_name())
        {
            case AbstractAttempt::PROPERTY_START_TIME :
                return $datetimeUtilities->formatLocaleDate(
                    null, (int) $assessmentAttempt[AbstractAttempt::PROPERTY_START_TIME]
                );
            case AbstractAttempt::PROPERTY_END_TIME :
                if ($assessmentAttempt[AbstractAttempt::PROPERTY_END_TIME])
                {
                    return $datetimeUtilities->formatLocaleDate(
                        null, (int) $assessmentAttempt[AbstractAttempt::PROPERTY_END_TIME]
                    );
                }

                return '';
            case AbstractAttempt::PROPERTY_TOTAL_TIME :
                if ($assessmentAttempt[AbstractAttempt::PROPERTY_STATUS] == AbstractAttempt::STATUS_COMPLETED)
                {
                    return $datetimeUtilities->convertSecondsToHours(
                        (int) $assessmentAttempt[AbstractAttempt::PROPERTY_TOTAL_TIME]
                    );
                }

                return '';
            case AbstractAttempt::PROPERTY_TOTAL_SCORE :
                if ($assessmentAttempt[AbstractAttempt::PROPERTY_STATUS] == AbstractAttempt::STATUS_COMPLETED)
                {
                    $total = $assessmentAttempt[AbstractAttempt::PROPERTY_TOTAL_SCORE];

                    return $total . '%';
                }

                return '';
            case AbstractAttempt::PROPERTY_STATUS :
                return $translator->trans(
                    ($assessmentAttempt[AbstractAttempt::PROPERTY_STATUS] == AbstractAttempt::STATUS_COMPLETED ?
                        'Completed' : 'NotCompleted'), [],
                    'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking'
                );
        }

        return parent::renderCell($column, $resultPosition, $assessmentAttempt);
    }

    /**
     * @throws \ReflectionException
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $assessmentAttempt): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        $pub = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class, $assessmentAttempt[AssessmentAttempt::PROPERTY_ASSESSMENT_ID]
        );

        $assessment_attempt_status = $assessmentAttempt[AbstractAttempt::PROPERTY_STATUS];
        $assessment_attempt_id = $assessmentAttempt[DataClass::PROPERTY_ID];

        $assessment = $pub->get_content_object();

        $parameters = new DataClassRetrieveParameters(
            new EqualityCondition(
                new PropertyConditionVariable(Publication::class, Publication::PROPERTY_PUBLICATION_ID),
                new StaticConditionVariable($pub->getId())
            )
        );
        $assessment_publication = DataManager::retrieve(Publication::class, $parameters);

        if ($assessment->getType() != Hotpotatoes::class &&
            (($assessment_attempt_status == AbstractAttempt::STATUS_COMPLETED &&
                    $assessment_publication->get_configuration()->show_feedback()) ||
                $this->application->is_allowed(WeblcmsRights::EDIT_RIGHT)))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('ViewResults', [], Manager::CONTEXT), new FontAwesomeGlyph('chart-line'),
                    $urlGenerator->fromRequest(
                        [
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_ATTEMPT_RESULT_VIEWER,
                            Manager::PARAM_USER_ASSESSMENT => $assessment_attempt_id,
                            AttemptResultViewerComponent::PARAM_SHOW_FULL => 1
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('ViewResultsNA', [], Manager::CONTEXT),
                    new FontAwesomeGlyph('chart-line', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($this->application->is_allowed(WeblcmsRights::DELETE_RIGHT))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('DeleteResult', [], Manager::CONTEXT), new FontAwesomeGlyph('times'),
                    $urlGenerator->fromRequest(
                        [
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_DELETE_RESULTS,
                            Manager::PARAM_USER_ASSESSMENT => $assessment_attempt_id
                        ]
                    ), ToolbarItem::DISPLAY_ICON, true
                )
            );
        }

        return $toolbar->render();
    }
}
