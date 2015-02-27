<?php
namespace Chamilo\Application\Weblcms\Renderer\PublicationList\Type;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Pager;

/**
 * $Id: list_content_object_publication_list_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.browser.list_renderer
 */

/**
 * Renderer to display a list of learning object publications
 */
class ListContentObjectPublicationListRenderer extends ContentObjectPublicationListRenderer
{

    /**
     * The page to display
     */
    private $page_nr;

    /**
     * Number of items to display per page
     */
    private $per_page;

    /**
     * The pager object to split the data in several pages
     */
    private $pager;

    /**
     * A prefix for the URL-parameters, can be used on pages with multiple Pagers
     */
    private $param_prefix;

    /**
     * The total number of items in the list
     */
    private $total_number_of_items;
    
    /**
     * The default number of objects per page
     */
    const DEFAULT_PER_PAGE = 5;

    /**
     * Returns the HTML output of this renderer.
     * 
     * @return string The HTML output
     */
    public function as_html()
    {
        // prepares the pager
        $this->prepare_pager();
        
        $publications = $this->get_page_publications();
        
        if (count($publications) == 0)
        {
            return Display :: normal_message(
                Translation :: get('NoPublications', null, Utilities :: COMMON_LIBRARIES), 
                true);
        }
        
        $html[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->namespaceToFullPath('Chamilo\Configuration', true) .
                 'Resources/Javascript/PublicationsList.js');
        
        if ($this->get_actions() && $this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $html[] = '<div style="clear: both;">';
            $html[] = '<form class="publication_list" name="publication_list" action="' . $this->get_url() .
                 '" method="POST" >';
        }
        
        // add top page navigation
        $html[] = $this->get_navigation_html();
        
        foreach ($publications as $index => $publication)
        {
            $first = ($index == 0);
            $last = ($index == count($publications) - 1);
            $html[] = $this->render_publication($publication, $first, $last, $index);
        }
        
        if ($this->get_actions() && count($publications) > 0 && $this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $table_name = ClassnameUtilities :: getInstance()->getClassNameFromNamespace(__CLASS__, true);
            foreach ($_GET as $parameter => $value)
            {
                if ($parameter == 'message')
                {
                    continue;
                }
                
                $html[] = '<input type="hidden" name="' . $parameter . '" value="' . $value . '" />';
            }
            $html[] = '<script type="text/javascript">
							/* <![CDATA[ */
							function setCheckbox(formName, value) {
								var d = document[formName];
								for (counter = 0; counter < d.elements.length; counter++) {
									if (d.elements[counter].type == "checkbox") {
									     d.elements[counter].checked = value;
									}
								}
							}
							/* ]]> */
							</script>';
            
            $html[] = '<div style="text-align: right;">';
            $html[] = '<a href="?" onclick="setCheckbox(\'publication_list\', true); return false;">' .
                 Translation :: get('SelectAll', null, Utilities :: COMMON_LIBRARIES) . '</a>';
            $html[] = '- <a href="?" onclick="setCheckbox(\'publication_list\', false); return false;">' .
                 Translation :: get('UnselectAll', null, Utilities :: COMMON_LIBRARIES) . '</a><br />';
            $html[] = '<select id="tool_actions" name="' . $table_name . '_action_value">';
            foreach ($this->get_actions()->get_form_actions() as $form_action)
            {
                if ($form_action instanceof TableFormAction)
                {
                    $html[] = '<option value="' . base64_encode(serialize($form_action->get_action())) . '" class="' .
                         ($form_action->get_confirm() ? 'confirm' : '') . '">' . $form_action->get_title() . '</option>';
                }
            }
            $html[] = '</select>';
            $html[] = '<input type="hidden" name="table_name" value="' . $table_name . '"/>';
            $html[] = '<input type="hidden" name="' . $table_name . '_namespace" value="' . __NAMESPACE__ . '"/>';
            $html[] = ' <input type="submit" value="' . Translation :: get('Ok', null, Utilities :: COMMON_LIBRARIES) .
                 '"/>';
            $html[] = '</div>';
            
            // add bottom page navigation
            $html[] = $this->get_navigation_html();
            $html[] = '</form>';
            $html[] = '</div>';
        }
        
        return implode(PHP_EOL, $html);
    }

    public static function handle_table_action()
    {
        $selected_ids = Request :: post(Manager :: PARAM_PUBLICATION);
        
        if (empty($selected_ids))
        {
            $selected_ids = array();
        }
        elseif (! is_array($selected_ids))
        {
            $selected_ids = array($selected_ids);
        }
        Request :: set_get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID, $selected_ids);
    }

    /**
     * Renders a single publication.
     * 
     * @param $publication ContentObjectPublication The publication.
     * @param $first boolean True if the publication is the first in the list it is a part of.
     * @param $last boolean True if the publication is the last in the list it is a part of.
     * @return string The rendered HTML.
     */
    public function render_publication($publication, $first = false, $last = false, $position = 0)
    {
        // TODO: split into separate overrideable methods
        $html = array();
        $last_visit_date = $this->get_tool_browser()->get_last_visit_date();
        $icon_suffix = '';
        if ($publication[ContentObjectPublication :: PROPERTY_HIDDEN])
        {
            $icon_suffix = '_na';
        }
        else
        {
            if ($publication[ContentObjectPublication :: PROPERTY_PUBLICATION_DATE] >= $last_visit_date)
            {
                $icon_suffix = '_new';
            }
            // else
            // {
            // $feedbacks = \core\admin\storage\DataManager :: retrieve_feedback_publications(
            // $publication[ContentObjectPublication :: PROPERTY_ID],
            // null,
            // Manager :: APPLICATION_NAME);
            
            // while ($feedback = $feedbacks->next_result())
            // {
            // if ($feedback->get_modification_date() >= $last_visit_date)
            // {
            // $icon_suffix = '_new';
            // break;
            // }
            // }
            // }
        }
        
        $left = $position % 2;
        switch ($left)
        {
            case 0 :
                $level = 'level_1';
                break;
            case 1 :
                $level = 'level_2';
                break;
        }
        
        if ($this->get_content_object_from_publication($publication) instanceof ComplexContentObjectSupport)
        {
            $title_url = $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $publication[ContentObjectPublication :: PROPERTY_ID], 
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT));
        }
        else
        {
            $title_url = $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $publication[ContentObjectPublication :: PROPERTY_ID], 
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_VIEW), 
                array(), 
                true);
        }
        
        $html[] = '<div class="announcements ' . $level . '" style="background-image: url(' . str_replace(
            '.png', 
            $icon_suffix . '.png', 
            $this->get_content_object_from_publication($publication)->get_icon_path()) . ');">';
        
        $html[] = '<div class="title' . ($this->is_visible_for_target_users($publication) ? '' : ' invisible') . '">';
        $html[] = '<a href="' . $title_url . '">' . $this->render_title($publication) . '</a>';
        $html[] = '</div>';
        $html[] = '<div class="topactions' . ($this->is_visible_for_target_users($publication) ? '' : ' invisible') .
             '">';
        $html[] = $this->render_top_action($publication);
        $html[] = '</div><div class="clear">&nbsp;</div>';
        $html[] = $this->render_description($publication);
        // $html[] = '</div>';
        $html[] = '<div class="publication_info' . ($this->is_visible_for_target_users($publication) ? '' : ' invisible') .
             '">';
        $html[] = $this->render_publication_information($publication);
        $html[] = '</div>';
        $html[] = '<div class="publication_actions">';
        if ($this->get_actions() && $this->is_allowed(WeblcmsRights :: EDIT_RIGHT, $publication))
        {
            $html[] = '<input style="display: inline; float: right;" class="pid" type="checkbox" name="' .
                 Manager :: PARAM_PUBLICATION . '[]" value="' . $publication[ContentObjectPublication :: PROPERTY_ID] .
                 '"/>';
        }
        $html[] = $this->get_publication_actions($publication, false)->as_html();
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        $html[] = '</div><br />';
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Prepares the pager (counts objects, sets page, etc)
     */
    public function prepare_pager()
    {
        // set the prefix
        $this->param_prefix = ContentObjectPublication :: get_table_name() . '_';
        
        // count the total number of objects
        $this->total_number_of_items = $this->get_publication_count();
        
        // set the page number
        $this->page_nr = isset($_SESSION[$this->param_prefix . 'page_nr']) ? $_SESSION[$this->param_prefix . 'page_nr'] : 1;
        $this->page_nr = Request :: get($this->param_prefix . 'page_nr') ? Request :: get(
            $this->param_prefix . 'page_nr') : $this->page_nr;
        $_SESSION[$this->param_prefix . 'page_nr'] = $this->page_nr;
        
        // set the number of objects per page
        $this->per_page = self :: DEFAULT_PER_PAGE;
    }

    /**
     * Get the Pager object to split the showed data in several pages
     */
    public function get_pager()
    {
        if (is_null($this->pager))
        {
            $total_number_of_items = $this->total_number_of_items;
            $params['mode'] = 'Sliding';
            $params['perPage'] = $this->per_page;
            $params['totalItems'] = $total_number_of_items;
            $params['urlVar'] = $this->param_prefix . 'page_nr';
            $params['prevImg'] = '<img src="' . Theme :: getInstance()->getCommonImagesPath() .
                 'action_prev.png"  style="vertical-align: middle;"/>';
            $params['nextImg'] = '<img src="' . Theme :: getInstance()->getCommonImagesPath() .
                 'action_next.png"  style="vertical-align: middle;"/>';
            $params['firstPageText'] = '<img src="' . Theme :: getInstance()->getCommonImagesPath() .
                 'action_first.png"  style="vertical-align: middle;"/>';
            $params['lastPageText'] = '<img src="' . Theme :: getInstance()->getCommonImagesPath() .
                 'action_last.png"  style="vertical-align: middle;"/>';
            $params['firstPagePre'] = '';
            $params['lastPagePre'] = '';
            $params['firstPagePost'] = '';
            $params['lastPagePost'] = '';
            $params['spacesBeforeSeparator'] = '';
            $params['spacesAfterSeparator'] = '';
            $params['currentPage'] = $this->page_nr;
            
            $params['extraVars'] = $this->get_tool_browser()->get_parameters();
            $params['excludeVars'] = array('message');
            
            $this->pager = Pager :: factory($params);
        }
        return $this->pager;
    }

    /**
     * Get the HTML-code with the navigational buttons to browse through the data-pages.
     */
    public function get_navigation_html()
    {
        $pager = $this->get_pager();
        $pager_links = $pager->getLinks();
        return $pager_links['first'] . ' ' . $pager_links['back'] . ' ' . $pager->getCurrentPageId() . ' / ' .
             $pager->numPages() . ' ' . $pager_links['next'] . ' ' . $pager_links['last'];
    }

    public function get_page_publications()
    {
        $pager = $this->get_pager();
        if ($pager)
        {
            $offset = $pager->getOffsetByPageId();
            $from = $offset[0] - 1;
            $count = $this->per_page;
        }
        else
        {
            $from = 0;
            $count = - 1;
        }
        
        return $this->get_publications($from, $count);
    }
}
