<?php
namespace Chamilo\Core\Repository\ContentObject\Blog\Display\Component\Viewer;

use Chamilo\Core\Repository\ContentObject\Blog\Storage\DataClass\Blog;
use Chamilo\Core\Repository\ContentObject\BlogItem\Storage\DataClass\ComplexBlogItem;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Exception;

/**
 * Abstract class to define a blog layout so users are able to define new blog layouts and choose between them in the
 * local settings
 *
 * @author Sven Vanpoucke
 */
abstract class BlogLayout
{
    use DependencyInjectionContainerTrait;

    /**
     * The parent on which this blog layout is rendering
     */
    private $parent;

    /**
     * The blog which needs to be rendered
     *
     * @var Blog
     */
    private $blog;

    /**
     * Constructor
     *
     * @param $parent
     * @param $blog Blog
     */
    public function __construct($parent, Blog $blog)
    {
        $this->parent = $parent;
        $this->blog = $blog;

        $this->initializeContainer();
    }

    public function as_html()
    {
        $html = array();

        $complex_blog_items = $this->retrieve_complex_blog_items();
        while ($complex_blog_item = $complex_blog_items->next_result())
        {
            $html[] = $this->display_blog_item($complex_blog_item);
        }

        return implode(PHP_EOL, $html);
    }

    // Getters and setters

    /**
     * Displays a given blog item
     *
     * @param $complex_blog_item ComplexBlogItem
     */
    abstract public function display_blog_item(ComplexBlogItem $complex_blog_item);

    /**
     * Factory
     *
     * @param $parent
     * @param $blog Blog
     */
    public function factory($parent, Blog $blog)
    {
        $type = $blog->get_blog_layout();
        $class = __NAMESPACE__ . '\BlogLayout\\' . $type . 'BlogLayout';

        if (!class_exists($class))
        {
            throw new Exception(Translation::get('BlogLayoutNotExists', array('BLOGLAYOUT' => $type)));
        }

        return new $class($parent, $blog);
    }

    public function get_blog()
    {
        return $this->blog;
    }

    public function set_blog(Blog $blog)
    {
        $this->blog = $blog;
    }

    /**
     * Returns the actions for the blog item
     *
     * @param $complex_blog_item ComplexBlogItem
     */
    public function get_blog_item_actions($complex_blog_item)
    {
        $toolbar = new Toolbar();
        if ($this->get_parent()->get_parent()->is_allowed_to_edit_content_object())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Edit', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('pencil'),
                    $this->get_parent()->get_complex_content_object_item_update_url($complex_blog_item),
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($this->get_parent()->get_parent()->is_allowed_to_delete_child())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Delete', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('times'),
                    $this->get_parent()->get_complex_content_object_item_delete_url($complex_blog_item),
                    ToolbarItem::DISPLAY_ICON, true
                )
            );
        }

        return $toolbar->as_html();
    }

    public function get_parent()
    {
        return $this->parent;
    }

    // Helper methods

    public function set_parent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Retrieves the children of the current blog by date
     */
    public function retrieve_complex_blog_items()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class_name(), ComplexContentObjectItem::PROPERTY_PARENT
            ), new StaticConditionVariable($this->get_blog()->get_id())
        );

        $parameters = new DataClassRetrievesParameters(
            $condition, null, null, array(
                new OrderBy(
                    new PropertyConditionVariable(
                        ComplexContentObjectItem::class_name(), ComplexContentObjectItem::PROPERTY_ADD_DATE
                    )
                )
            )
        );

        return DataManager::retrieves(
            ComplexContentObjectItem::class_name(), $parameters
        );
    }
}
