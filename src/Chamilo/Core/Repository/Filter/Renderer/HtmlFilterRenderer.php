<?php
namespace Chamilo\Core\Repository\Filter\Renderer;

use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Filter\FilterRenderer;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\UserView\Storage\DataClass\UserView;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class HtmlFilterRenderer extends FilterRenderer
{
    const CLEAR_ALL = 'all';

    public function get_parameter_name($filter_property)
    {
        return 'parameter_' . $filter_property;
    }

    public function render()
    {
        $html = array();

        if ($this->get_filter_data()->is_set())
        {
            $html[] = $this->add_header();
            $html[] = $this->add_properties();
            $html[] = $this->add_footer();
        }

        return implode(PHP_EOL, $html);
    }

    public function add_properties()
    {
        $filter_data = $this->get_filter_data();

        $html = array();

        // Text
        if ($filter_data->has_filter_property(FilterData :: FILTER_TEXT))
        {
            $html[] = '<div class="parameter" id="' . $this->get_parameter_name(FilterData :: FILTER_TEXT) . '">' .
                 $filter_data->get_filter_property(FilterData :: FILTER_TEXT) . '</div>';
        }

        // Category id
        $category_id = $filter_data->get_filter_property(FilterData :: FILTER_CATEGORY);

        if (isset($category_id) && $category_id >= 0)
        {
            $recursive = (boolean) $filter_data->get_filter_property(FilterData :: FILTER_CATEGORY_RECURSIVE);

            if ($recursive)
            {
                if ($category_id == 0)
                {
                    $html[] = '<div class="parameter" id="' . $this->get_parameter_name(FilterData :: FILTER_CATEGORY) .
                         '">' . Translation :: get(
                            'InCategoryAndChildren',
                            array('CATEGORY' => $this->get_workspace()->getTitle())) . '</div>';
                }
                else
                {
                    $category = DataManager :: retrieve_by_id(RepositoryCategory :: class_name(), $category_id);

                    if ($category instanceof RepositoryCategory)
                    {
                        $html[] = '<div class="parameter" id="' .
                             $this->get_parameter_name(FilterData :: FILTER_CATEGORY) . '">' .
                             Translation :: get('InCategoryAndChildren', array('CATEGORY' => $category->get_name())) .
                             '</div>';
                    }
                    else
                    {
                        $filter_data->set_filter_property(FilterData :: FILTER_CATEGORY, null);
                    }
                }
            }
            else
            {
                if ($category_id == 0)
                {
                    $html[] = '<div class="parameter" id="' . $this->get_parameter_name(FilterData :: FILTER_CATEGORY) .
                         '">' . Translation :: get(
                            'InCategory',
                            array('CATEGORY' => $this->get_workspace()->getTitle())) . '</div>';
                }
                else
                {
                    $category = DataManager :: retrieve_by_id(RepositoryCategory :: class_name(), $category_id);

                    if ($category instanceof RepositoryCategory)
                    {
                        $html[] = '<div class="parameter" id="' .
                             $this->get_parameter_name(FilterData :: FILTER_CATEGORY) . '">' .
                             Translation :: get('InCategory', array('CATEGORY' => $category->get_name())) . '</div>';
                    }
                    else
                    {
                        $filter_data->set_filter_property(FilterData :: FILTER_CATEGORY, null);
                    }
                }
            }
        }

        // Creation date
        if ($filter_data->has_date(FilterData :: FILTER_CREATION_DATE))
        {
            $html[] = '<div class="parameter" id="' . $this->get_parameter_name(FilterData :: FILTER_CREATION_DATE) .
                 '">' . Translation :: get(
                    'CreatedBetween',
                    array(
                        'FROM' => $filter_data->get_creation_date(FilterData :: FILTER_FROM_DATE),
                        'TO' => $filter_data->get_creation_date(FilterData :: FILTER_TO_DATE))) . '</div>';
        }
        else
        {
            if ($filter_data->get_creation_date(FilterData :: FILTER_FROM_DATE))
            {
                $html[] = '<div class="parameter" id="' . $this->get_parameter_name(FilterData :: FILTER_CREATION_DATE) .
                     '">' . Translation :: get(
                        'CreatedAfter',
                        array('FROM' => $filter_data->get_creation_date(FilterData :: FILTER_FROM_DATE))) . '</div>';
            }
            elseif ($filter_data->get_creation_date(FilterData :: FILTER_TO_DATE))
            {
                $html[] = '<div class="parameter" id="' . $this->get_parameter_name(FilterData :: FILTER_CREATION_DATE) .
                     '">' . Translation :: get(
                        'CreatedBefore',
                        array('TO' => $filter_data->get_creation_date(FilterData :: FILTER_TO_DATE))) . '</div>';
            }
        }

        // Modification date
        if ($filter_data->has_date(FilterData :: FILTER_MODIFICATION_DATE))
        {
            $html[] = '<div class="parameter" id="' . $this->get_parameter_name(FilterData :: FILTER_MODIFICATION_DATE) .
                 '">' . Translation :: get(
                    'ModifiedBetween',
                    array(
                        'FROM' => $filter_data->get_modification_date(FilterData :: FILTER_FROM_DATE),
                        'TO' => $filter_data->get_modification_date(FilterData :: FILTER_TO_DATE))) . '</div>';
        }
        else
        {
            if ($filter_data->get_modification_date(FilterData :: FILTER_FROM_DATE))
            {
                $html[] = '<div class="parameter" id="' .
                     $this->get_parameter_name(FilterData :: FILTER_MODIFICATION_DATE) . '">' .
                     Translation :: get(
                        'ModifiedAfter',
                        array('FROM' => $filter_data->get_modification_date(FilterData :: FILTER_FROM_DATE))) . '</div>';
            }
            elseif ($filter_data->get_modification_date(FilterData :: FILTER_TO_DATE))
            {
                $html[] = '<div class="parameter" id="' .
                     $this->get_parameter_name(FilterData :: FILTER_MODIFICATION_DATE) . '">' . Translation :: get(
                        'ModifiedBefore',
                        array('TO' => $filter_data->get_modification_date(FilterData :: FILTER_TO_DATE))) . '</div>';
            }
        }

        // Type
        if ($filter_data->has_filter_property(FilterData :: FILTER_TYPE))
        {
            $type = $filter_data->get_filter_property(FilterData :: FILTER_TYPE);

            // Category
            if (! is_numeric($type) && ! empty($type))
            {
                $type_selector = TypeSelector :: populate(DataManager :: get_registered_types());

                try
                {
                    $category_name = $type_selector->get_category_by_type($type)->get_name();
                }
                catch (\Exception $exception)
                {
                    $category_name = $type;
                }

                $html[] = '<div class="parameter" id="' . $this->get_parameter_name(FilterData :: FILTER_TYPE) . '">' .
                     $category_name . '</div>';
            }
            // Template id
            elseif (is_numeric($type) && ! empty($type))
            {
                $template_registration = \Chamilo\Core\Repository\Configuration :: registration_by_id($type);
                $template = $template_registration->get_template();
                $html[] = '<div class="parameter" id="' . $this->get_parameter_name(FilterData :: FILTER_TYPE) . '">' .
                     $template->translate('TypeName') . '</div>';
            }
        }

        // User view id
        if ($filter_data->has_filter_property(FilterData :: FILTER_USER_VIEW))
        {
            $user_view = DataManager :: retrieve_by_id(
                UserView :: class_name(),
                $filter_data->get_filter_property(FilterData :: FILTER_USER_VIEW));
            $html[] = '<div class="parameter" id="' . $this->get_parameter_name(FilterData :: FILTER_USER_VIEW) . '">' .
                 Translation :: get('UserViewFilter', array('VIEW' => $user_view->get_name())) . '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function add_header()
    {
        $html = array();

        $workspaceId = $this->get_workspace() instanceof PersonalWorkspace ? null : $this->get_workspace()->getId();

        $html[] = '<div id="search_parameters" data-current-workspace-id="' . $workspaceId . '"><h4>' .
             Translation :: get('SearchParameters') . '</h4>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function add_footer()
    {
        $html = array();

        $html[] = '<div class="parameter" id="' . $this->get_parameter_name(self :: CLEAR_ALL) . '">' .
             Translation :: get('ClearAllParameters') . '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        $html[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getJavascriptPath(Manager :: context(), true) . 'Search.js');

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param \core\repository\filter\FilterData $filter_data
     * @return \core\repository\filter\renderer\HtmlFilterRenderer
     */
    public static function factory(FilterData $filter_data, WorkspaceInterface $workspace)
    {
        $class_name = $filter_data->get_context() . '\Filter\Renderer\HtmlFilterRenderer';
        return new $class_name($filter_data, $workspace);
    }
}