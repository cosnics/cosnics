<?php
namespace Chamilo\Core\Repository\ContentObject\Blog\Display\Component\Viewer;

use Chamilo\Core\Repository\ContentObject\Blog\Storage\DataClass\Blog;
use Chamilo\Core\Repository\ContentObject\BlogItem\Storage\DataClass\ComplexBlogItem;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
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
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class BlogLayout
{
    use DependencyInjectionContainerTrait;

    /**
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $parent;

    /**
     * @var \Chamilo\Core\Repository\ContentObject\Blog\Storage\DataClass\Blog
     */
    private $blog;

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $parent
     * @param \Chamilo\Core\Repository\ContentObject\Blog\Storage\DataClass\Blog $blog
     */
    public function __construct($parent, Blog $blog)
    {
        $this->parent = $parent;
        $this->blog = $blog;

        $this->initializeContainer();
    }

    /**
     * @return string
     */
    public function render()
    {
        $html = array();

        $complexBlogItems = $this->retrieve_complex_blog_items();
        while ($complexBlogItem = $complexBlogItems->next_result())
        {
            $html[] = $this->renderBlogItem($complexBlogItem);
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @param $parent
     * @param \Chamilo\Core\Repository\ContentObject\Blog\Storage\DataClass\Blog $blog
     *
     * @return mixed
     * @throws \Exception
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

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Blog\Storage\DataClass\Blog
     */
    public function get_blog()
    {
        return $this->blog;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Blog\Storage\DataClass\Blog $blog
     */
    public function set_blog(Blog $blog)
    {
        $this->blog = $blog;
    }

    /**
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function get_parent()
    {
        return $this->parent;
    }

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $parent
     */
    public function set_parent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\BlogItem\Storage\DataClass\ComplexBlogItem $complexBlogItem
     *
     * @return boolean
     */
    public function hasBlogItemActions(ComplexBlogItem $complexBlogItem)
    {
        return $this->get_parent()->get_parent()->is_allowed_to_edit_content_object() ||
            $this->get_parent()->get_parent()->is_allowed_to_delete_child();
    }

    /**
     * @param $complexBlogItem ComplexBlogItem
     */
    abstract public function renderBlogItem(ComplexBlogItem $complexBlogItem);

    /**
     * Returns the actions for the blog item
     *
     * @param \Chamilo\Core\Repository\ContentObject\BlogItem\Storage\DataClass\ComplexBlogItem $complexBlogItem
     *
     * @return string
     */
    public function renderBlogItemActions(ComplexBlogItem $complexBlogItem)
    {
        $buttonToolBar = new ButtonToolBar();
        $buttonGroup = new ButtonGroup();

        if ($this->get_parent()->get_parent()->is_allowed_to_edit_content_object())
        {
            $buttonGroup->addButton(
                new Button(
                    Translation::get('Edit', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                    $this->get_parent()->get_complex_content_object_item_update_url($complexBlogItem),
                    ToolbarItem::DISPLAY_ICON, false, 'btn-link'
                )
            );
        }

        if ($this->get_parent()->get_parent()->is_allowed_to_delete_child())
        {
            $buttonGroup->addButton(
                new Button(
                    Translation::get('Delete', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('times'),
                    $this->get_parent()->get_complex_content_object_item_delete_url($complexBlogItem),
                    ToolbarItem::DISPLAY_ICON, true, 'btn-link'
                )
            );
        }

        $buttonToolBar->addButtonGroup($buttonGroup);
        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolbarRenderer->render();
    }

    /**
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
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
