<?php
namespace Chamilo\Core\Metadata\Vocabulary\Component;

use Chamilo\Core\Metadata\Storage\DataClass\Vocabulary;
use Chamilo\Core\Metadata\Vocabulary\Manager;
use Chamilo\Core\Metadata\Vocabulary\Storage\DataManager;
use Chamilo\Core\Metadata\Vocabulary\Table\VocabularyTableRenderer;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Metadata\Vocabulary\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class BrowserComponent extends Manager
{

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     * @throws \ReflectionException
     * @throws \QuickformException
     */
    public function run()
    {
        if (!$this->getUser()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        if (!$this->getSelectedElementId())
        {
            throw new NoObjectSelectedException(
                $this->getTranslator()->trans('Element', [], 'Chamilo\Core\Metadata\Element')
            );
        }

        $content = $this->getContent();

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $content;
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    protected function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $commonActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    $this->getTranslator()->trans('Create', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('plus'), $this->get_url(
                    [
                        self::PARAM_ACTION => self::ACTION_CREATE,
                        \Chamilo\Core\Metadata\Element\Manager::PARAM_ELEMENT_ID => $this->getSelectedElementId(),
                        self::PARAM_USER_ID => $this->getSelectedUserId()
                    ]
                )
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    public function getContent(): string
    {
        $userId = $this->getSelectedUserId();
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        if ($userId != 0)
        {
            $user = $this->getUserService()->findUserByIdentifier($userId);
            $breadcrumbTitle = $user->get_fullname();
        }
        else
        {
            $breadcrumbTitle =
                $this->getTranslator()->trans('ValueTypePredefined', [], 'Chamilo\Core\Metadata\Element');
        }

        BreadcrumbTrail::getInstance()->add(
            new Breadcrumb($this->get_url([Manager::PARAM_USER_ID => $userId]), $breadcrumbTitle)
        );

        $html = [];

        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = $this->renderTable();

        return implode(PHP_EOL, $html);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \Exception
     */
    public function getVocabularyCondition(): AndCondition
    {
        $conditions = [];

        $searchCondition = $this->buttonToolbarRenderer->getConditions(
            [new PropertyConditionVariable(Vocabulary::class, Vocabulary::PROPERTY_VALUE)]
        );

        if ($searchCondition)
        {
            $conditions[] = $searchCondition;
        }

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(Vocabulary::class, Vocabulary::PROPERTY_ELEMENT_ID),
            ComparisonCondition::EQUAL, new StaticConditionVariable($this->getSelectedElementId())
        );

        $userId = $this->getSelectedUserId();

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable(Vocabulary::class, Vocabulary::PROPERTY_USER_ID), ComparisonCondition::EQUAL,
            new StaticConditionVariable($userId)
        );

        return new AndCondition($conditions);
    }

    public function getVocabularyTableRenderer(): VocabularyTableRenderer
    {
        return $this->getService(VocabularyTableRenderer::class);
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Exception
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems =
            DataManager::count(Vocabulary::class, new DataClassCountParameters($this->getVocabularyCondition()));
        $vocabularyTableRenderer = $this->getVocabularyTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $vocabularyTableRenderer->getParameterNames(), $vocabularyTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $vocabularies = DataManager::retrieves(
            Vocabulary::class, new DataClassRetrievesParameters(
                $this->getVocabularyCondition(), $tableParameterValues->getOffset(),
                $tableParameterValues->getNumberOfItemsPerPage(),
                $vocabularyTableRenderer->determineOrderBy($tableParameterValues)
            )
        );

        return $vocabularyTableRenderer->render($tableParameterValues, $vocabularies);
    }
}
