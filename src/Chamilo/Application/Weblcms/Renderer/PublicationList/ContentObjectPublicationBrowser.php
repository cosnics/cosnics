<?php
namespace Chamilo\Application\Weblcms\Renderer\PublicationList;

/**
 * $Id: content_object_publication_browser.class.php 218 2009-11-13 14:21:26Z
 * kariboe $
 * 
 * @package application.lib.weblcms
 */

/**
 * ==============================================================================
 * This class allows the user to browse through learning object publications.
 * 
 * @author Tim De Pauw
 *         ==============================================================================
 */
abstract class ContentObjectPublicationBrowser
{

    /**
     * The types of learning objects for which publications need to be
     * displayed.
     */
    private $types;

    /**
     * The ID of the category that is currently active.
     */
    private $category;

    /**
     * The list renderer used to display objects.
     */
    protected $listRenderer;

    /**
     * The tree view used to display categories.
     */
    private $categoryTree;

    /**
     * The tool that instantiated this browser.
     */
    private $parent;

    private $publication_id;

    /**
     * Constructor.
     * 
     * @param $parent Tool The tool that instantiated this browser.
     * @param $types mixed The types of learning objects for which
     *        publications need to be displayed.
     */
    public function __construct($parent, $types)
    {
        $this->parent = $parent;
        $this->types = is_array($types) ? $types : array($types);
    }

    /**
     * Returns the publication browser's content in HTML format.
     * 
     * @return string The HTML.
     */
    public function as_html()
    {
        if (! isset($this->categoryTree))
        {
            return $this->listRenderer->as_html();
        }
        return '<div style="float: left; width: 18%; overflow: auto;">' . $this->categoryTree->as_html() . '</div>' .
             '<div style="float: right; width: 80%">' . $this->listRenderer->as_html() . '</div>' .
             '<div class="clear">&nbsp;</div>';
    }

    /**
     * Returns the learning object publication list renderer associated with
     * this object.
     * 
     * @return ContentObjectPublicationRenderer The renderer.
     */
    public function get_publication_list_renderer()
    {
        return $this->listRenderer;
    }

    /**
     * Sets the renderer for the publication list.
     * 
     * @param $renderer ContentObjectPublicationRenderer The renderer.
     */
    public function set_publication_list_renderer($renderer)
    {
        $this->listRenderer = $renderer;
    }

    /**
     * Gets the publication category tree.
     * 
     * @return ContentObjectPublicationCategoryTree The category tree.
     */
    public function get_publication_category_tree()
    {
        return $this->categoryTree;
    }

    public function get_publication_id()
    {
        return $this->publication_id;
    }

    public function set_publication_id($publication_id)
    {
        $this->publication_id = $publication_id;
    }

    /**
     * Sets the publication category tree.
     * 
     * @param $tree ContentObjectPublicationCategoryTree The category tree.
     */
    public function set_publication_category_tree($tree)
    {
        $this->categoryTree = $tree;
    }

    /**
     * Returns the repository tool that this browser is associated with.
     * 
     * @return Tool The tool.
     */
    public function get_parent()
    {
        return $this->parent;
    }

    /**
     * Returns the ID of the current category.
     * 
     * @return int The category ID.
     */
    public function get_category()
    {
        return $this->category;
    }

    public function set_category($category)
    {
        $this->category = $category;
    }

    /**
     *
     * @see Tool :: get_user_id()
     */
    public function get_user_id()
    {
        return $this->parent->get_user_id();
    }

    public function get_user_info($user_id)
    {
        return $this->parent->get_user_info($user_id);
    }

    /**
     *
     * @see Tool :: get_course_groups()
     */
    public function get_course_groups()
    {
        return $this->parent->get_course_groups();
    }

    /**
     *
     * @see Tool :: get_course_id()
     */
    public function get_course_id()
    {
        return $this->parent->get_course_id();
    }

    /**
     *
     * @see Tool :: get_categories()
     */
    public function get_categories($list = false)
    {
        return $this->parent->get_categories($list);
    }

    /**
     *
     * @see Tool :: get_url()
     */
    public function get_url($parameters = array(), $filter = array(), $encode_entities = false)
    {
        return $this->parent->get_url($parameters, $filter, $encode_entities);
    }

    /**
     *
     * @see Tool :: get_parameters()
     */
    public function get_parameters()
    {
        return $this->parent->get_parameters();
    }

    /**
     *
     * @see Tool :: get_parameter()
     */
    public function get_parameter($name)
    {
        return $this->parent->get_parameter($name);
    }

    /**
     *
     * @see Tool :: is_allowed()
     */
    public function is_allowed($right)
    {
        return $this->parent->is_allowed($right);
    }

    /**
     *
     * @see WeblcmsManager :: get_last_visit_date()
     */
    public function get_last_visit_date()
    {
        return $this->parent->get_last_visit_date();
    }

    /**
     * Returns the learning object publications to display.
     * 
     * @param $from int The index of the first publication to return.
     * @param $count int The maximum number of publications to return.
     * @param $column int The index of the column to sort the table on.
     * @param $direction int The sorting direction; either SORT_ASC or
     *        SORT_DESC.
     * @return array The learning object publications.
     */
    abstract public function get_publications($from, $count, $column, $direction);

    /**
     * Returns the number of learning object publications to display.
     * 
     * @return int The number of publications.
     */
    abstract public function get_publication_count();
}
