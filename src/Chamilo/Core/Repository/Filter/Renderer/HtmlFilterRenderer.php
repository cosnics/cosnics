<?php
namespace Chamilo\Core\Repository\Filter\Renderer;

use Chamilo\Core\Repository\Configuration;
use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Filter\FilterRenderer;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Selector\TypeSelectorFactory;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\UserView\Storage\DataClass\UserView;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class HtmlFilterRenderer extends FilterRenderer
{
    const CLEAR_ALL = 'all';

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

    /**
     *
     * @return string
     */
    public function add_footer()
    {
        $html = array();

        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath(Manager::context(), true) . 'Search.js'
        );

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

        $html[] =
            '<div class="panel panel-default" id="search-parameters" data-current-workspace-id="' . $workspaceId . '">';
        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">';

        $html[] = '<span class="pull-right search-parameter" id="' . $this->get_parameter_name(self::CLEAR_ALL) . '">';
        $glyph = new FontAwesomeGlyph('times', array('text-muted', 'fas-ci-va'));
        $html[] = $glyph->render();
        $html[] = '</span>';

        $html[] = Translation::get('SearchParameters');
        $html[] = '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="list-group">';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string
     */
    public function add_properties()
    {
        $filter_data = $this->get_filter_data();

        $html = array();

        // Text
        if ($filter_data->has_filter_property(FilterData::FILTER_TEXT))
        {
            $html[] = $this->renderParameter(
                $this->get_parameter_name(FilterData::FILTER_TEXT),
                $filter_data->get_filter_property(FilterData::FILTER_TEXT)
            );
        }

        // Category id
        $category_id = $filter_data->get_filter_property(FilterData::FILTER_CATEGORY);

        if (isset($category_id) && $category_id >= 0)
        {
            $recursive = (boolean) $filter_data->get_filter_property(FilterData::FILTER_CATEGORY_RECURSIVE);

            if ($recursive)
            {
                if ($category_id == 0)
                {
                    $html[] = $this->renderParameter(
                        $this->get_parameter_name(FilterData::FILTER_CATEGORY), Translation::get(
                        'InCategoryAndChildren', array('CATEGORY' => $this->get_workspace()->getTitle())
                    )
                    );
                }
                else
                {
                    $category = DataManager::retrieve_by_id(RepositoryCategory::class_name(), $category_id);

                    if ($category instanceof RepositoryCategory)
                    {
                        $html[] = $this->renderParameter(
                            $this->get_parameter_name(FilterData::FILTER_CATEGORY),
                            Translation::get('InCategoryAndChildren', array('CATEGORY' => $category->get_name()))
                        );
                    }
                    else
                    {
                        $filter_data->set_filter_property(FilterData::FILTER_CATEGORY, null);
                    }
                }
            }
            else
            {
                if ($category_id == 0)
                {
                    $html[] = $this->renderParameter(
                        $this->get_parameter_name(FilterData::FILTER_CATEGORY),
                        Translation::get('InCategory', array('CATEGORY' => $this->get_workspace()->getTitle()))
                    );
                }
                else
                {
                    $category = DataManager::retrieve_by_id(RepositoryCategory::class_name(), $category_id);

                    if ($category instanceof RepositoryCategory)
                    {
                        $html[] = $this->renderParameter(
                            $this->get_parameter_name(FilterData::FILTER_CATEGORY),
                            Translation::get('InCategory', array('CATEGORY' => $category->get_name()))
                        );
                    }
                    else
                    {
                        $filter_data->set_filter_property(FilterData::FILTER_CATEGORY, null);
                    }
                }
            }
        }

        // Creation date
        if ($filter_data->has_date(FilterData::FILTER_CREATION_DATE))
        {
            $html[] = $this->renderParameter(
                $this->get_parameter_name(FilterData::FILTER_CREATION_DATE), Translation::get(
                'CreatedBetween', array(
                    'FROM' => $filter_data->get_creation_date(FilterData::FILTER_FROM_DATE),
                    'TO' => $filter_data->get_creation_date(FilterData::FILTER_TO_DATE)
                )
            )
            );
        }
        else
        {
            if ($filter_data->get_creation_date(FilterData::FILTER_FROM_DATE))
            {
                $html[] = $this->renderParameter(
                    $this->get_parameter_name(FilterData::FILTER_CREATION_DATE), Translation::get(
                    'CreatedAfter', array('FROM' => $filter_data->get_creation_date(FilterData::FILTER_FROM_DATE))
                )
                );
            }
            elseif ($filter_data->get_creation_date(FilterData::FILTER_TO_DATE))
            {
                $html[] = $this->renderParameter(
                    $this->get_parameter_name(FilterData::FILTER_CREATION_DATE), Translation::get(
                    'CreatedBefore', array('TO' => $filter_data->get_creation_date(FilterData::FILTER_TO_DATE))
                )
                );
            }
        }

        // Modification date
        if ($filter_data->has_date(FilterData::FILTER_MODIFICATION_DATE))
        {
            $html[] = $this->renderParameter(
                $this->get_parameter_name(FilterData::FILTER_MODIFICATION_DATE), Translation::get(
                'ModifiedBetween', array(
                    'FROM' => $filter_data->get_modification_date(FilterData::FILTER_FROM_DATE),
                    'TO' => $filter_data->get_modification_date(FilterData::FILTER_TO_DATE)
                )
            )
            );
        }
        else
        {
            if ($filter_data->get_modification_date(FilterData::FILTER_FROM_DATE))
            {
                $html[] = $this->renderParameter(
                    $this->get_parameter_name(FilterData::FILTER_MODIFICATION_DATE), Translation::get(
                    'ModifiedAfter', array('FROM' => $filter_data->get_modification_date(FilterData::FILTER_FROM_DATE))
                )
                );
            }
            elseif ($filter_data->get_modification_date(FilterData::FILTER_TO_DATE))
            {
                $html[] = $this->renderParameter(
                    $this->get_parameter_name(FilterData::FILTER_MODIFICATION_DATE), Translation::get(
                    'ModifiedBefore', array('TO' => $filter_data->get_modification_date(FilterData::FILTER_TO_DATE))
                )
                );
            }
        }

        // Type
        if ($filter_data->has_filter_property(FilterData::FILTER_TYPE))
        {
            $type = $filter_data->get_filter_property(FilterData::FILTER_TYPE);

            // Category
            if (!is_numeric($type) && !empty($type))
            {
                $typeSelectorFactory = new TypeSelectorFactory(DataManager::get_registered_types());
                $type_selector = $typeSelectorFactory->getTypeSelector();

                try
                {
                    $category_name = $type_selector->get_category_by_type($type)->get_name();
                }
                catch (Exception $exception)
                {
                    $category_name = $type;
                }

                $html[] = $this->renderParameter($this->get_parameter_name(FilterData::FILTER_TYPE), $category_name);
            }
            // Template id
            elseif (is_numeric($type) && !empty($type))
            {
                $template_registration = Configuration::registration_by_id($type);
                $template = $template_registration->get_template();
                $html[] = $this->renderParameter(
                    $this->get_parameter_name(FilterData::FILTER_TYPE), $template->translate('TypeName')
                );
            }
        }

        // User view id
        if ($filter_data->has_filter_property(FilterData::FILTER_USER_VIEW))
        {
            $user_view = DataManager::retrieve_by_id(
                UserView::class_name(), $filter_data->get_filter_property(FilterData::FILTER_USER_VIEW)
            );
            if (!empty($user_view))
            {
                $html[] = $this->renderParameter(
                    $this->get_parameter_name(FilterData::FILTER_USER_VIEW),
                    Translation::get('UserViewFilter', array('VIEW' => $user_view->get_name()))
                );
            }
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Core\Repository\Filter\FilterData $filter_data
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspace
     *
     * @return \Chamilo\Core\Repository\Filter\Renderer\HtmlFilterRenderer
     */
    public static function factory(FilterData $filter_data, WorkspaceInterface $workspace)
    {
        $class_name = $filter_data->get_context() . '\Filter\Renderer\HtmlFilterRenderer';

        return new $class_name($filter_data, $workspace);
    }

    /**
     * @param string $filter_property
     *
     * @return string
     */
    public function get_parameter_name($filter_property)
    {
        return 'parameter_' . $filter_property;
    }

    /**
     * @param string $parameterIdentifier
     * @param string $parameterText
     *
     * @return string
     */
    public function renderParameter($parameterIdentifier, $parameterText)
    {
        $html = array();

        $html[] = '<div class="list-group-item search-parameter" id="' . $parameterIdentifier . '">';
        $glyph = new FontAwesomeGlyph('times', array('pull-right', 'text-muted', 'fas-ci-va'));
        $html[] = $glyph->render();
        $html[] = $parameterText;
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}