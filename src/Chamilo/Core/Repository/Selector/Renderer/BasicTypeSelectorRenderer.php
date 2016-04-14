<?php
namespace Chamilo\Core\Repository\Selector\Renderer;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Selector\TabsTypeSelectorSupport;
use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Core\Repository\Selector\TypeSelectorOption;
use Chamilo\Core\Repository\Selector\TypeSelectorRenderer;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Core\Repository\Selector
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class BasicTypeSelectorRenderer extends TypeSelectorRenderer
{
    // Type selector tabs
    const TAB_MOST_USED = 'most_used';
    const TAB_EXTRA = 'extra';

    /**
     *
     * @var string[][]
     */
    private $additional_links;

    /**
     *
     * @var boolean
     */
    private $use_general_statistics;

    /**
     *
     * @param Application $parent
     * @param TypeSelector $type_selector
     */
    public function __construct(Application $parent, TypeSelector $type_selector, $additional_links = array(),
        $use_general_statistics = false)
    {
        parent :: __construct($parent, $type_selector);

        if (! $parent instanceof TabsTypeSelectorSupport)
        {
            throw new \Exception(
                get_class($parent) .
                     ' uses the TabsTypeSelectorRender, please implement the TabsTypeSelectorSupport interface');
        }

        $this->additional_links = $additional_links;
        $this->use_general_statistics = $use_general_statistics;
    }

    /**
     *
     * @return LinkTypeSelectorOption[]
     */
    public function get_additional_links()
    {
        return $this->additional_links;
    }

    /**
     *
     * @return boolean
     */
    public function has_additional_links()
    {
        return count($this->get_additional_links()) > 0;
    }

    /**
     *
     * @return boolean
     */
    public function get_use_general_statistics()
    {
        return $this->use_general_statistics;
    }

    /**
     * Render the tabs
     *
     * @return string
     */
    public function render()
    {
        $html = array();

        if ($this->get_type_selector()->count_options() > 15)
        {
            $mostUsedButtons = $this->getMostUsedButtons();

            if (count($mostUsedButtons) > 0)
            {
                $html[] = $this->renderPanel(
                    Translation :: get('MostUsed'),
                    Theme :: getInstance()->getImagePath(Manager :: context(), 'TypeSelector/Tab/MostUsed'),
                    $mostUsedButtons);
            }
        }

        $html[] = '<div class="panel panel-default">';
        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">';
        $html[] = '</h3>';
        $html[] = '</div>';
        $html[] = '<div class="panel-body">';

        foreach ($this->get_type_selector()->get_categories() as $category)
        {
            $html[] = $this->renderCategory($category);
        }

        $html[] = '</div>';
        $html[] = '</div>';

        if ($this->has_additional_links() > 0)
        {
            $html[] = $this->renderPanel(
                Translation :: get('Extra'),
                Theme :: getInstance()->getImagePath(Manager :: context(), 'TypeSelector/Tab/Extra'),
                $this->getAdditionalButtons());
        }

        return implode(PHP_EOL, $html);
    }

    public function renderPanel($title, $imagePath, $buttons)
    {
        $html = array();

        $html[] = '<div class="panel panel-default">';
        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">';
        $html[] = '<img src="' . $imagePath . '" /> ' . $title;
        $html[] = '</h3>';
        $html[] = '</div>';
        $html[] = '<div class="panel-body">';

        $html[] = $this->renderToolBar($buttons);

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function renderToolBar($buttons)
    {
        $buttonToolBar = new ButtonToolBar();
        $buttonGroup = new ButtonGroup();

        foreach ($buttons as $button)
        {
            $buttonGroup->addButton($button);
        }

        $buttonToolBar->addButtonGroup($buttonGroup);
        $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolBarRenderer->render();
    }

    /**
     * Render the most used content object types tab
     *
     * @return string
     */
    public function getMostUsedButtons()
    {
        if (! $this->get_use_general_statistics())
        {
            $statistics_condition = new EqualityCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_OWNER_ID),
                new StaticConditionVariable($this->get_parent()->get_user_id()));
        }
        else
        {
            $statistics_condition = null;
        }

        $content_object_type_counts = array();
        $most_used_type_count = 0;

        foreach ($this->get_type_selector()->get_categories() as $category)
        {
            foreach ($category->get_options() as $option)
            {
                $conditions = array();

                if (! is_null($statistics_condition))
                {
                    $conditions[] = $statistics_condition;
                }

                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObject :: class_name(),
                        ContentObject :: PROPERTY_TEMPLATE_REGISTRATION_ID),
                    new StaticConditionVariable($option->get_template_registration_id()));
                $condition = new AndCondition($conditions);

                $context = $option->get_template_registration()->get_content_object_type();
                $package = ClassnameUtilities :: getInstance()->getPackageNameFromNamespace($context);
                $type = $context . '\Storage\DataClass\\' .
                     (string) StringUtilities :: getInstance()->createString($package)->upperCamelize();

                $parameters = new DataClassCountParameters($condition);
                $count = \Chamilo\Core\Repository\Storage\DataManager :: count_active_content_objects(
                    $type,
                    $parameters);

                if ($count > 0)
                {
                    $content_object_type_counts[serialize($option)] = $count;

                    if ($count > $most_used_type_count)
                    {
                        $most_used_type_count = $count;
                    }
                }
            }
        }

        uasort(
            $content_object_type_counts,
            function ($count_a, $count_b) {
                return $count_a < $count_b;
            });

        $mostUsedTypes = array_slice($content_object_type_counts, 0, 10);

        $buttons = array();

        foreach ($mostUsedTypes as $typeOption => $count)
        {
            $typeOption = unserialize($typeOption);
            $url = $this->get_parent()->get_content_object_type_creation_url(
                $typeOption->get_template_registration_id());

            $buttons[] = $this->getButton($typeOption, $url);
        }

        return $buttons;
    }

    /**
     * Render one category / content object type tab
     *
     * @param TypeSelectorCategory $category
     * @return string
     */
    public function renderCategory($category)
    {
        $buttons = array();

        foreach ($category->get_options() as $option)
        {
            $url = $this->get_parent()->get_content_object_type_creation_url($option->get_template_registration_id());
            $buttons[] = $this->getButton($option, $url);
        }

        return $this->renderToolBar($buttons);

        return $this->renderPanel(
            $category->get_name(),
            Theme :: getInstance()->getImagePath(Manager :: context(), 'TypeSelector/Tab/' . $category->get_type()),
            $buttons);
    }

    /**
     *
     * @param TypeSelectorOption $option
     * @return string
     */
    public function getButton(TypeSelectorOption $option, $url)
    {
        return new Button($option->get_label(), $option->get_image_path(), $url);
    }

    /**
     * Render any available additional links
     *
     * @return string
     */
    public function getAdditionalSubButtons()
    {
        $buttons = array();

        foreach ($this->get_additional_links() as $link)
        {
            $buttons[] = $this->getButton($link, $link->get_url());
        }

        return $buttons;
    }
}
