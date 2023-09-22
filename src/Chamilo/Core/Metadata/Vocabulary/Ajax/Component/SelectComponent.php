<?php
namespace Chamilo\Core\Metadata\Vocabulary\Ajax\Component;

use Chamilo\Core\Metadata\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Storage\DataClass\Vocabulary;
use Chamilo\Core\Metadata\Vocabulary\Ajax\Manager;
use Chamilo\Core\Metadata\Vocabulary\Table\SelectTableRenderer;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\PageConfiguration;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use stdClass;

/**
 * @package Chamilo\Core\Metadata\Vocabulary\Ajax
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class SelectComponent extends Manager
{
    public const PARAM_ELEMENT_ID = 'elementId';
    public const PARAM_SCHEMA_ID = 'schemaId';
    public const PARAM_SCHEMA_INSTANCE_ID = 'schemaInstanceId';

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    private $element;

    private $elementId;

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Exception
     */
    public function run()
    {
        $translator = $this->getTranslator();
        $elementId = $this->getPostDataValue(\Chamilo\Core\Metadata\Element\Manager::PARAM_ELEMENT_ID);

        if (!$this->getSelectedElementId())
        {
            throw new NoObjectSelectedException($translator->trans('Element', [], 'Chamilo\Core\Metadata\Element'));
        }

        if (!$this->getSelectedElement()->usesVocabulary())
        {
            throw new Exception($translator->trans('NoVocabularyAllowed', [], self::CONTEXT));
        }

        $this->getPageConfiguration()->setViewMode(PageConfiguration::VIEW_MODE_HEADERLESS);

        $content = $this->getContent();

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $content;
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Core\Metadata\Element\Manager::PARAM_ELEMENT_ID;
        $additionalParameters[] = Manager::PARAM_ELEMENT_IDENTIFIER;

        return parent::getAdditionalParameters($additionalParameters);
    }

    protected function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    public function getContent(): string
    {
        $html = [];
        $vocabularyIds = $this->getSelectedVocabularyId();
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        if (count($vocabularyIds) > 0)
        {
            $vocabularyItems = $this->getVocabularyItems($vocabularyIds);
            $vocabularyItemValues = [];

            foreach ($vocabularyItems as $vocabularyItem)
            {
                $item = new stdClass();
                $item->id = $vocabularyItem->getId();
                $item->value = $vocabularyItem->get_value();

                $vocabularyItemValues[] = $item;
            }

            $resource_manager = ResourceManager::getInstance();
            $plugin_path =
                $this->getWebPathBuilder()->getJavascriptPath('Chamilo\Core\Metadata') . 'Plugin/Bootstrap/Tagsinput/';

            $html[] = '<script>';
            $html[] = 'var selectedVocabularyItems = ' . json_encode($vocabularyItemValues) . ';';
            $html[] = 'var elementIdentifier = ' . json_encode(
                    $this->getRequest()->query->get(
                        Manager::PARAM_ELEMENT_IDENTIFIER
                    )
                ) . ';';
            $html[] = '</script>';
            $html[] = $resource_manager->getResourceHtml($plugin_path . 'bootstrap-typeahead.js');
            $html[] = $resource_manager->getResourceHtml($plugin_path . 'bootstrap-tagsinput.js');
            $html[] = $resource_manager->getResourceHtml(
                $this->getWebPathBuilder()->getJavascriptPath('Chamilo\Core\Metadata') . 'Selection.js'
            );
        }
        else
        {
            $html[] = $this->buttonToolbarRenderer->render();
            $html[] = $this->renderTable();
        }

        return implode(PHP_EOL, $html);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    public function getRequiredPostParameters(array $postParameters = []): array
    {
        return [\Chamilo\Core\Metadata\Element\Manager::PARAM_ELEMENT_ID];
    }

    /**
     * @throws \Exception
     */
    public function getSelectCondition(): AndCondition
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

        $conditions[] = $this->getVocabularyCondition();

        return new AndCondition($conditions);
    }

    public function getSelectTableRenderer(): SelectTableRenderer
    {
        return $this->getService(SelectTableRenderer::class);
    }

    public function getSelectedElement()
    {
        if (!isset($this->element))
        {
            $this->element = DataManager::retrieve_by_id(
                Element::class, (int) $this->getSelectedElementId()
            );
        }

        return $this->element;
    }

    public function getSelectedElementId()
    {
        if (!isset($this->elementId))
        {
            $this->elementId = $this->getPostDataValue(\Chamilo\Core\Metadata\Element\Manager::PARAM_ELEMENT_ID);
        }

        return $this->elementId;
    }

    public function getSelectedVocabularyId()
    {
        return (array) $this->getRequest()->getFromRequestOrQuery(\Chamilo\Core\Metadata\Vocabulary\Manager::PARAM_VOCABULARY_ID);
    }

    public function getVocabularyCondition()
    {
        $element = $this->getSelectedElement();

        $userConditions = [];

        if ($element->isVocabularyUserDefined())
        {
            $userConditions[] = new ComparisonCondition(
                new PropertyConditionVariable(Vocabulary::class, Vocabulary::PROPERTY_USER_ID),
                ComparisonCondition::EQUAL, new StaticConditionVariable($this->getUser()->getId())
            );
        }

        if ($element->isVocabularyPredefined())
        {
            $userConditions[] = new ComparisonCondition(
                new PropertyConditionVariable(Vocabulary::class, Vocabulary::PROPERTY_USER_ID),
                ComparisonCondition::EQUAL, new StaticConditionVariable(0)
            );
        }

        return new OrCondition($userConditions);
    }

    /**
     * @param string[] $vocabularyIds
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Metadata\Storage\DataClass\Vocabulary>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getVocabularyItems(array $vocabularyIds = []): ArrayCollection
    {
        $conditions = [];
        $conditions[] = $this->getVocabularyCondition();
        $conditions[] = new InCondition(
            new PropertyConditionVariable(Vocabulary::class, DataClass::PROPERTY_ID), $vocabularyIds
        );

        $condition = new AndCondition($conditions);

        return DataManager::retrieves(Vocabulary::class, new DataClassRetrievesParameters($condition));
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Exception
     */
    protected function renderTable(): string
    {
        $totalNumberOfItems = \Chamilo\Core\Metadata\Vocabulary\Storage\DataManager::count(
            Vocabulary::class, new DataClassCountParameters($this->getSelectCondition())
        );
        $selectTableRenderer = $this->getSelectTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $selectTableRenderer->getParameterNames(), $selectTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $vocabularies = DataManager::retrieves(
            Vocabulary::class, new DataClassRetrievesParameters(
                $this->getSelectCondition(), $tableParameterValues->getNumberOfItemsPerPage(),
                $tableParameterValues->getOffset(), $selectTableRenderer->determineOrderBy($tableParameterValues)
            )
        );

        return $selectTableRenderer->render($tableParameterValues, $vocabularies);
    }
}