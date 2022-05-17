<?php
namespace Chamilo\Core\Metadata\Vocabulary\Ajax\Component;

use Chamilo\Core\Metadata\Storage\DataClass\Element;
use Chamilo\Core\Metadata\Storage\DataClass\Vocabulary;
use Chamilo\Core\Metadata\Vocabulary\Ajax\Manager;
use Chamilo\Core\Metadata\Vocabulary\Table\Select\SelectTable;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Exception;
use stdClass;

/**
 *
 * @package Chamilo\Core\User\Ajax
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SelectComponent extends Manager implements TableSupport
{
    const PARAM_ELEMENT_ID = 'elementId';
    const PARAM_SCHEMA_ID = 'schemaId';
    const PARAM_SCHEMA_INSTANCE_ID = 'schemaInstanceId';

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     *
     * @var \Chamilo\Core\Metadata\Element\Storage\DataClass\Element
     */
    private $element;

    /**
     *
     * @var integer
     */
    private $elementId;

    public function run()
    {
        $elementId = $this->getPostDataValue(\Chamilo\Core\Metadata\Element\Manager::PARAM_ELEMENT_ID);

        if (!$this->getSelectedElementId())
        {
            throw new NoObjectSelectedException(Translation::get('Element', null, 'Chamilo\Core\Metadata\Element'));
        }

        if (!$this->getSelectedElement()->usesVocabulary())
        {
            throw new Exception(Translation::get('NoVocabularyAllowed'));
        }

        Page::getInstance()->setViewMode(Page::VIEW_MODE_HEADERLESS);

        $content = $this->getContent();

        $html = [];

        $html[] = $this->render_header();
        $html[] = $content;
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Builds the action bar
     *
     * @return ButtonToolBarRenderer
     */
    protected function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function getContent()
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
                $item->id = $vocabularyItem->get_id();
                $item->value = $vocabularyItem->get_value();

                $vocabularyItemValues[] = $item;
            }

            $resource_manager = ResourceManager::getInstance();
            $plugin_path =
                Path::getInstance()->getJavascriptPath('Chamilo\Core\Metadata', true) . 'Plugin/Bootstrap/Tagsinput/';

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
                Path::getInstance()->getJavascriptPath('Chamilo\Core\Metadata', true) . 'Selection.js'
            );
        }
        else
        {
            $table = new SelectTable($this);

            $html[] = $this->buttonToolbarRenderer->render();
            $html[] = $table->as_html();
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Get an array of parameters which should be set for this call to work
     *
     * @return array
     */
    public function getRequiredPostParameters()
    {
        return array(\Chamilo\Core\Metadata\Element\Manager::PARAM_ELEMENT_ID);
    }

    /**
     *
     * @return \Chamilo\Core\Metadata\Element\Storage\DataClass\Element
     */
    public function getSelectedElement()
    {
        if (!isset($this->element))
        {
            $this->element = DataManager::retrieve_by_id(
                Element::class, $this->getSelectedElementId()
            );
        }

        return $this->element;
    }

    /**
     *
     * @return integer
     */
    public function getSelectedElementId()
    {
        if (!isset($this->elementId))
        {
            $this->elementId = $this->getPostDataValue(\Chamilo\Core\Metadata\Element\Manager::PARAM_ELEMENT_ID);
        }

        return $this->elementId;
    }

    /**
     *
     * @return integer
     */
    public function getSelectedVocabularyId()
    {
        return (array) $this->getRequest()->get(\Chamilo\Core\Metadata\Vocabulary\Manager::PARAM_VOCABULARY_ID);
    }

    public function getVocabularyCondition()
    {
        $element = $this->getSelectedElement();

        $userConditions = [];

        if ($element->isVocabularyUserDefined())
        {
            $userConditions[] = new ComparisonCondition(
                new PropertyConditionVariable(Vocabulary::class, Vocabulary::PROPERTY_USER_ID),
                ComparisonCondition::EQUAL, new StaticConditionVariable($this->get_user_id())
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

    public function getVocabularyItems($vocabularyIds)
    {
        $conditions = [];
        $conditions[] = $this->getVocabularyCondition();
        $conditions[] = new InCondition(
            new PropertyConditionVariable(Vocabulary::class, Vocabulary::PROPERTY_ID), $vocabularyIds
        );

        $condition = new AndCondition($conditions);

        return DataManager::retrieves(Vocabulary::class, new DataClassRetrievesParameters($condition));
    }

    public function get_additional_parameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Core\Metadata\Element\Manager::PARAM_ELEMENT_ID;
        $additionalParameters[] = Manager::PARAM_ELEMENT_IDENTIFIER;

        return parent::get_additional_parameters($additionalParameters);
    }

    /**
     * Returns the condition
     *
     * @param string $table_class_name
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    public function get_table_condition($table_class_name)
    {
        $conditions = [];

        $searchCondition = $this->buttonToolbarRenderer->getConditions(
            array(new PropertyConditionVariable(Vocabulary::class, Vocabulary::PROPERTY_VALUE))
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
}