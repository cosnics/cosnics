<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPathItem\Storage\DataClass\LearningPathItem;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Menu\Library\HtmlMenu;
use Chamilo\Libraries\Format\Menu\Library\Renderer\HtmlMenuArrayRenderer;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Menu\TreeMenuRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * $Id: learning_path_tree.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.learning_path.component.learning_path_viewer
 */
/**
 * This class provides a navigation menu to allow a user to browse through his categories of learning objects.
 *
 * @author Sven Vanpoucke
 */
class LearningPathTree extends HtmlMenu
{
    const TREE_NAME = __CLASS__;

    private $current_step;

    private $lp_id;

    private $lp;

    private $lpi_tracker_data;

    /**
     * The string passed to sprintf() to format category URLs
     */
    private $urlFmt;

    private $current_object;

    private $current_cloi;

    private $current_tracker;

    private $current_parent;

    private $objects = array();

    private $translator;

    private $dm;

    /**
     * Creates a new category navigation menu.
     *
     * @param int $owner The ID of the owner of the categories to provide in this menu.
     * @param int $current_category The ID of the current category in the menu.
     * @param string $url_format The format to use for the URL of a category. Passed to sprintf(). Defaults to the
     *        string "?category=%s".
     * @param array $extra_items An array of extra tree items, added to the root.
     */
    public function __construct($lp_id, $current_step, $url_format, $lpi_tracker_data)
    {
        $this->current_step = $current_step;
        $this->lp_id = $lp_id;
        $this->lp = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
            ContentObject :: class_name(),
            $lp_id);
        $this->urlFmt = $url_format;
        $this->lpi_tracker_data = $lpi_tracker_data;
        $this->translator = new RuleConditionTranslator();

        $menu = $this->get_menu($lp_id);
        parent :: __construct($menu);
        $this->array_renderer = new HtmlMenuArrayRenderer();

        $this->clean_urls();

        if (! $current_step)
        {
            $this->forceCurrentUrl($this->get_progress_url());
        }
        else
        {
            $this->forceCurrentUrl($this->get_url($current_step));
        }
    }

    public function get_menu($lp_id)
    {
        $menu = array();
        $lo = $this->lp;
        $lp_item = array();
        $lp_item['title'] = $lo->get_title();
        // $menu_item['url'] = $this->get_url($lp_id);

        $sub_menu_items = $this->get_menu_items($lo);
        if (count($sub_menu_items) > 0)
        {
            $lp_item['sub'] = $sub_menu_items;
        }

        $lp_item['class'] = 'type_' .
             ClassnameUtilities :: getInstance()->getClassNameFromNamespace($lo->get_type(), true);

        // $menu_item['class'] = 'type_category';
        $lp_item[OptionsMenuRenderer :: KEY_ID] = - 1;

        $menu_item = array();
        $menu_item['title'] = Translation :: get('Progress');
        $menu_item['url'] = $this->get_progress_url();
        $menu_item['class'] = 'type_statistics';
        $menu_item[OptionsMenuRenderer :: KEY_ID] = $this->step;
        $lp_item['sub'] = array_merge($lp_item['sub'], array($menu_item));
        $lp_item['url'] = '#';
        $menu[] = $lp_item;

        return $menu;
    }

    /**
     * Returns the menu items.
     *
     * @param array $extra_items An array of extra tree items, added to the root.
     * @return array An array with all menu items. The structure of this array is the structure needed by
     *         PEAR::HTML_Menu, on which this class is based.
     */
    private $step = 1;

    private $step_urls = array();

    private $jump_urls = array();

    private function get_menu_items($parent)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem :: class_name(),
                ComplexContentObjectItem :: PROPERTY_PARENT),
            new StaticConditionVariable($parent->get_id()),
            ComplexContentObjectItem :: get_table_name());

        $order_by = array(
            new OrderBy(
                new PropertyConditionVariable(
                    ComplexContentObjectItem :: class_name(),
                    ComplexContentObjectItem :: PROPERTY_DISPLAY_ORDER)));

        $parameters = new DataClassRetrievesParameters($condition, null, null, $order_by);

        $objects = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_complex_content_object_items(
            ComplexContentObjectItem :: class_name(),
            $parameters);

        while (($object = $objects->next_result()))
        {
            $lo = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                ContentObject :: class_name(),
                $object->get_ref());
            $lpi_tracker_data = $this->lpi_tracker_data[$object->get_id()];

            if ($lo->get_type() == LearningPathItem :: class_name())
            {
                $lo = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                    ContentObject :: class_name(),
                    $lo->get_reference());
            }

            $menu_item = array();
            $menu_item['title'] = $lo->get_title();

            $menu_item['class'] = 'type_' .
                 ClassnameUtilities :: getInstance()->getClassNameFromNamespace($lo->get_type(), true);

            $menu_item[OptionsMenuRenderer :: KEY_ID] = - 1;

            $sub_menu_items = array();

            $control_mode = $parent->get_control_mode();

            if ($lo->get_type() == LearningPath :: class_name())
            {
                $sub_menu_items = $this->get_menu_items($lo);

                if (count($sub_menu_items) > 0)
                {
                    $menu_item['sub'] = $sub_menu_items;
                    if ($sub_menu_items[0]['url'])
                    {
                        $menu_item['url'] = $sub_menu_items[0]['url'];
                    }
                }
            }
            else
            {

                $this->step_urls[$this->step] = $this->get_url($this->step);
                $status = 'enabled';

                // in PHP5.3, getInstance returns the fully namespaced name so
                // we need this little hack
                if (strpos(get_class($lo), 'ScormItem') !== FALSE)
                {
                    if ($this->lp->get_version() == 'SCORM2004')
                    {
                        $this->jump_urls[$lo->get_identifier()] = $this->step_urls[$this->step];
                        $status = $this->translator->get_status_from_item($lo, $lpi_tracker_data);

                        switch ($status)
                        {
                            case 'skip' :
                                $this->step_urls[$this->step] = null;
                                break;
                            case 'disabled' :
                                $this->jump_urls[$lo->get_identifier()] = $this->step_urls[$this->step] = null;
                                break;
                            case 'hidden_from_choice' :
                                $this->jump_urls[$lo->get_identifier()] = null;
                                break;
                        }
                    }
                }

                $this->objects[$object->get_id()] = $lo;
                if ($lpi_tracker_data['completed'])
                {
                    $menu_item['title'] = $menu_item['title'] . Theme :: getInstance()->getCommonImage('Status/OkMini');
                    $this->taken_steps ++;
                }

                if ((! array_key_exists('choice', $control_mode) || $control_mode['choice'] != 0) &&
                     ($status != 'disabled' || $status != 'hidden_from_choice'))
                {
                    $menu_item['url'] = $this->get_url($this->step);
                    $menu_item[OptionsMenuRenderer :: KEY_ID] = $this->step;
                }

                if ($this->step == $this->current_step)
                {
                    $this->current_cloi = $object;
                    $this->current_object = $lo;
                    $this->current_tracker = $lpi_tracker_data['active_tracker'];
                    $this->current_parent = $parent;
                }

                $this->step ++;
            }

            $menu[] = $menu_item;
        }

        return $menu;
    }

    private function clean_urls()
    {
        if (! $this->get_current_parent())
            return;

        $control_mode = $this->get_current_parent()->get_control_mode();

        if ($control_mode['forwardOnly'] != 0)
        {
            for ($i = 1; $i <= $this->current_step; $i ++)
            {
                $this->step_urls[$i] = null;
            }
        }
    }

    private $continue_url = null;

    public function get_continue_url()
    {
        if (! $this->continue_url)
        {
            $step = $this->current_step + 1;
            while ($this->step_urls[$step] == null && $step <= $this->count_steps())
            {
                $step ++;
            }

            if ($step <= $this->count_steps())
            {
                $this->continue_url = $this->step_urls[$step];
                return $this->continue_url;
            }

            $this->continue_url = $this->get_progress_url();
        }
        return $this->continue_url;
    }

    private $previous_url = null;

    public function get_previous_url()
    {
        if (! $this->previous_url)
        {
            $step = $this->current_step - 1;
            while ($this->step_urls[$step] == null && $step > 0)
            {
                $step --;
            }

            if ($step > 0)
            {
                $this->previous_url = $this->step_urls[$step];
            }
        }

        return $this->previous_url;
    }

    public function get_jump_urls()
    {
        return $this->jump_urls;
    }

    public function get_objects()
    {
        return $this->objects;
    }

    public function get_current_object()
    {
        return $this->current_object;
    }

    public function get_current_cloi()
    {
        return $this->current_cloi;
    }

    public function get_current_tracker()
    {
        return $this->current_tracker;
    }

    public function get_current_parent()
    {
        return $this->current_parent;
    }

    private function get_url($current_step)
    {
        return sprintf($this->urlFmt, $current_step);
    }

    private function get_progress_url()
    {
        return str_replace('&step=%s', '', $this->urlFmt) . '&show_progress=true';
    }

    /**
     * Renders the menu as a tree
     *
     * @return string The HTML formatted tree
     */
    public function render_as_tree()
    {
        $renderer = new TreeMenuRenderer($this->get_tree_name());
        $this->render($renderer, 'sitemap');
        return $renderer->toHTML();
    }

    public static function get_tree_name()
    {
        return ClassnameUtilities :: getInstance()->getClassNameFromNamespace(self :: TREE_NAME, true);
    }

    public function get_progress()
    {
        return ($this->taken_steps / ($this->count_steps())) * 100;
    }

    public function count_steps()
    {
        return $this->step - 1;
    }

    public function get_breadcrumbs()
    {
        $this->render($this->array_renderer, 'urhere');
        $breadcrumbs = $this->array_renderer->toArray();
        $trail = BreadcrumbTrail :: getInstance();
        $used_urls = array();
        foreach ($breadcrumbs as $crumb)
        {
            if (! in_array($crumb['url'], $used_urls))
            {
                $trail->add(new Breadcrumb($crumb['url'], strip_tags($crumb['title'])));
                $used_urls[] = $crumb['url'];
            }
        }
        return $trail;
    }
}
