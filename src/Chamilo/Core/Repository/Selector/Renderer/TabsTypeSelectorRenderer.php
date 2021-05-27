<?php
namespace Chamilo\Core\Repository\Selector\Renderer;

use Chamilo\Core\Repository\Selector\TabsTypeSelectorSupport;
use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Core\Repository\Selector\TypeSelectorOption;
use Chamilo\Core\Repository\Selector\TypeSelectorRenderer;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Tabs\DynamicContentTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * Render content object type selection tabs based on their category
 *
 * @author Hans De Bisschop
 */
class TabsTypeSelectorRenderer extends TypeSelectorRenderer
{
    // Type selector tabs
    const TAB_EXTRA = 'extra';

    const TAB_MOST_USED = 'most_used';

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

    public function __construct(
        Application $parent, TypeSelector $type_selector, $additional_links = [], $use_general_statistics = false
    )
    {
        parent::__construct($parent, $type_selector);

        if (!$parent instanceof TabsTypeSelectorSupport)
        {
            throw new Exception(
                get_class($parent) .
                ' uses the TabsTypeSelectorRender, please implement the TabsTypeSelectorSupport interface'
            );
        }

        $this->additional_links = $additional_links;
        $this->use_general_statistics = $use_general_statistics;
    }

    /**
     * Render the tabs
     *
     * @return string
     */
    public function render()
    {
        $renderer_name = ClassnameUtilities::getInstance()->getClassnameFromObject($this, true);
        $tabs = new DynamicTabsRenderer($renderer_name);

        if ($this->get_type_selector()->count_options() > 15)
        {
            $most_used_content = $this->render_most_used();

            if ($most_used_content)
            {
                $glyph = new FontAwesomeGlyph(
                    'bookmark', array('fa-lg', 'fa-fw'), null, 'fas'
                );

                $tabs->add_tab(
                    new DynamicContentTab(
                        self::TAB_MOST_USED, Translation::get('MostUsed'), $glyph, $most_used_content
                    )
                );
            }
        }

        foreach ($this->get_type_selector()->get_categories() as $category)
        {
            $glyph = new NamespaceIdentGlyph(
                'Chamilo\Core\Repository\ContentObject\Category\\' . $category->get_type(), true, false, false,
                IdentGlyph::SIZE_SMALL, array('fa-fw')
            );

            $tabs->add_tab(
                new DynamicContentTab(
                    $category->get_type(), $category->get_name(), $glyph, $this->render_category($category)
                )
            );
        }

        if ($this->has_additional_links() > 0)
        {
            $glyph = new FontAwesomeGlyph(
                'magic', array('fa-lg', 'fa-fw'), null, 'fas'
            );

            $tabs->add_tab(
                new DynamicContentTab(
                    self::TAB_EXTRA, Translation::get('Extra'), $glyph, $this->render_additional_links()
                )
            );
        }

        return $tabs->render();
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
    public function get_use_general_statistics()
    {
        return $this->use_general_statistics;
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
     * Render any available additional links
     *
     * @return string
     */
    public function render_additional_links()
    {
        $html = [];

        foreach ($this->get_additional_links() as $link)
        {
            $html[] = $this->render_option($link, $link->get_url());
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Render one category / content object type tab
     *
     * @param TypeSelectorCategory $category
     *
     * @return string
     */
    public function render_category($category)
    {
        $html = [];

        foreach ($category->get_options() as $option)
        {
            $url = $this->get_parent()->get_content_object_type_creation_url($option->get_template_registration_id());
            $html[] = $this->render_option($option, $url);
        }

        $html[] = '<div class="clearfix"></div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Render the most used content object types tab
     *
     * @return string
     */
    public function render_most_used()
    {
        if (!$this->get_use_general_statistics())
        {
            $statistics_condition = new EqualityCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OWNER_ID),
                new StaticConditionVariable($this->get_parent()->get_user_id())
            );
        }
        else
        {
            $statistics_condition = null;
        }

        $content_object_type_counts = [];
        $most_used_type_count = 0;

        foreach ($this->get_type_selector()->get_categories() as $category)
        {
            foreach ($category->get_options() as $option)
            {
                $conditions = [];

                if (!is_null($statistics_condition))
                {
                    $conditions[] = $statistics_condition;
                }

                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObject::class, ContentObject::PROPERTY_TEMPLATE_REGISTRATION_ID
                    ), new StaticConditionVariable($option->get_template_registration_id())
                );
                $condition = new AndCondition($conditions);

                $context = $option->get_template_registration()->get_content_object_type();
                $package = ClassnameUtilities::getInstance()->getPackageNameFromNamespace($context);
                $type = $context . '\Storage\DataClass\\' .
                    (string) StringUtilities::getInstance()->createString($package)->upperCamelize();

                $parameters = new DataClassCountParameters($condition);
                $count = DataManager::count_active_content_objects($type, $parameters);

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
            $content_object_type_counts, function ($count_a, $count_b) {
            return $count_a < $count_b;
        }
        );

        $most_used_types = array_slice($content_object_type_counts, 0, 10);

        $html = [];

        foreach ($most_used_types as $type_option => $count)
        {
            $type_option = unserialize($type_option);
            $url = $this->get_parent()->get_content_object_type_creation_url(
                $type_option->get_template_registration_id()
            );

            $html[] = $this->render_option($type_option, $url);
        }

        $html[] = '<div class="clearfix"></div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param TypeSelectorOption $option
     *
     * @return string
     */
    public function render_option(TypeSelectorOption $option, $url)
    {
        $html = [];

        $html[] = '<a href="' . $url . '">';

        $imagePath = $option->get_image_path();

        if ($imagePath instanceof InlineGlyph)
        {
            $html[] = '<div>' . $imagePath->render();
        }
        else
        {
            $html[] = '<div class="create_block" style="background-image: url(' . $imagePath . ');">';
        }

        $html[] = $option->get_label();
        $html[] = '</div>';
        $html[] = '</a>';

        return implode(PHP_EOL, $html);
    }
}
