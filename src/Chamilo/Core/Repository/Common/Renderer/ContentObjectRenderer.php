<?php
namespace Chamilo\Core\Repository\Common\Renderer;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonSearchForm;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Exception;

abstract class ContentObjectRenderer implements TableSupport
{
    const TYPE_GALLERY = 'GalleryTable';

    const TYPE_SLIDESHOW = 'Slideshow';

    const TYPE_TABLE = 'Table';

    /**
     *
     * @var \Chamilo\Core\Repository\Component\BrowserComponent
     */
    protected $repository_browser;

    /**
     *
     * @param \Chamilo\Core\Repository\Component\BrowserComponent $repository_browser
     */
    public function __construct($repository_browser)
    {
        $this->repository_browser = $repository_browser;
    }

    abstract public function as_html();

    public function count_categories($conditions = null)
    {
        return $this->get_repository_browser()->count_categories($conditions);
    }

    public function count_content_objects($condition)
    {
        return DataManager::count_active_content_objects(ContentObject::class, $condition);
    }

    public static function factory($type, $repository_browser)
    {
        $class = __NAMESPACE__ . '\Type\\' . StringUtilities::getInstance()->createString($type)->upperCamelize() .
            'ContentObjectRenderer';

        if (!class_exists($class))
        {
            throw new Exception(Translation::get('ContentObjectRendererTypeDoesNotExist', array('type' => $type)));
        }

        return new $class($repository_browser);
    }

    public function get_condition()
    {
        return $this->get_repository_browser()->get_condition();
    }

    public function get_content_object_actions(ContentObject $content_object)
    {
        $actions = [];

        $rightsService = RightsService::getInstance();

        $canEditContentObject = $rightsService->canEditContentObject(
            $this->get_user(), $content_object, $this->get_repository_browser()->getWorkspace()
        );

        $canDeleteContentObject = $rightsService->canDeleteContentObject(
            $this->get_user(), $content_object, $this->get_repository_browser()->getWorkspace()
        );

        $canUseContentObject = $rightsService->canUseContentObject(
            $this->get_user(), $content_object, $this->get_repository_browser()->getWorkspace()
        );

        $canCopyContentObject = $rightsService->canCopyContentObject(
            $this->get_user(), $content_object, $this->get_repository_browser()->getWorkspace()
        );

        if ($canEditContentObject)
        {
            $actions[] = new ToolbarItem(
                Translation::get('Edit', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $this->get_repository_browser()->get_content_object_editing_url($content_object),
                ToolbarItem::DISPLAY_ICON
            );
        }

        if ($canCopyContentObject)
        {
            $actions[] = new ToolbarItem(
                Translation::get('Duplicate'), new FontAwesomeGlyph('copy'),
                $this->get_repository_browser()->get_copy_content_object_url($content_object->get_id()),
                ToolbarItem::DISPLAY_ICON
            );
        }

        if ($this->get_repository_browser()->getWorkspace() instanceof PersonalWorkspace)
        {
            if ($url = $this->get_repository_browser()->get_content_object_recycling_url($content_object))
            {
                $actions[] = new ToolbarItem(
                    Translation::get('Remove', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('trash-alt'), $url,
                    ToolbarItem::DISPLAY_ICON, true
                );
            }
            else
            {
                $actions[] = new ToolbarItem(
                    Translation::get('RemoveNotAvailable', null, Utilities::COMMON_LIBRARIES),
                    new FontAwesomeGlyph('trash-alt', array('text-muted')), null, ToolbarItem::DISPLAY_ICON
                );
            }
        }

        if (DataManager::workspace_has_categories($this->get_repository_browser()->getWorkspace()))
        {
            if ($canEditContentObject)
            {
                $actions[] = new ToolbarItem(
                    Translation::get('Move', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('folder-open'),
                    $this->get_repository_browser()->get_content_object_moving_url($content_object),
                    ToolbarItem::DISPLAY_ICON
                );
            }
        }

        if ($this->get_repository_browser()->getWorkspace() instanceof PersonalWorkspace)
        {
            $actions[] = new ToolbarItem(
                Translation::get('Share', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('lock'),
                $this->get_repository_browser()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_WORKSPACE,
                        Manager::PARAM_CONTENT_OBJECT_ID => $content_object->get_id(),
                        \Chamilo\Core\Repository\Workspace\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Manager::ACTION_SHARE
                    )
                ), ToolbarItem::DISPLAY_ICON
            );
        }
        else
        {
            if ($canDeleteContentObject)
            {
                $url = $this->get_repository_browser()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_WORKSPACE,
                        \Chamilo\Core\Repository\Workspace\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Manager::ACTION_UNSHARE,
                        Manager::PARAM_CONTENT_OBJECT_ID => $content_object->getId()
                    )
                );

                $actions[] = new ToolbarItem(
                    Translation::get('Unshare', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('unlock'),
                    $url, ToolbarItem::DISPLAY_ICON, true
                );
            }
        }

        if ($canCopyContentObject)
        {
            $actions[] = new ToolbarItem(
                Translation::get('Export', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('download'),
                $this->get_repository_browser()->get_content_object_exporting_url($content_object),
                ToolbarItem::DISPLAY_ICON
            );
        }

        if ($canUseContentObject)
        {
            $actions[] = new ToolbarItem(
                Translation::get('Publish', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('share-square'),
                $this->get_repository_browser()->get_publish_content_object_url($content_object),
                ToolbarItem::DISPLAY_ICON
            );
        }

        $preview_url = $this->get_repository_browser()->get_preview_content_object_url($content_object);
        $onclick = '" onclick="javascript:openPopup(\'' . $preview_url . '\'); return false;';

        if ($content_object instanceof ComplexContentObjectSupport)
        {
            if (\Chamilo\Core\Repository\Builder\Manager::exists($content_object->package()))
            {
                if ($canEditContentObject)
                {
                    $actions[] = new ToolbarItem(
                        Translation::get('BuildComplexObject', null, Utilities::COMMON_LIBRARIES),
                        new FontAwesomeGlyph('cubes'),
                        $this->get_repository_browser()->get_browse_complex_content_object_url($content_object),
                        ToolbarItem::DISPLAY_ICON
                    );
                }

                $actions[] = new ToolbarItem(
                    Translation::get('Preview', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('desktop'),
                    $preview_url, ToolbarItem::DISPLAY_ICON, false, $onclick, '_blank'
                );
            }
            else
            {
                if ($canEditContentObject)
                {
                    $actions[] = new ToolbarItem(
                        Translation::get('BuildPreview', null, Utilities::COMMON_LIBRARIES),
                        new FontAwesomeGlyph('cubes'), $preview_url, ToolbarItem::DISPLAY_ICON, false, $onclick,
                        '_blank'
                    );
                }
                else
                {
                    $actions[] = new ToolbarItem(
                        Translation::get('Preview', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('desktop'),
                        $preview_url, ToolbarItem::DISPLAY_ICON, false, $onclick, '_blank'
                    );
                }
            }
        }
        else
        {
            $actions[] = new ToolbarItem(
                Translation::get('Preview', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('desktop'),
                $preview_url, ToolbarItem::DISPLAY_ICON, false, $onclick, '_blank'
            );
        }

        if ($content_object->getType() == 'Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File')
        {
            $actions[] = new ToolbarItem(
                Translation::get('Download', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('download'),
                $this->get_repository_browser()->get_document_downloader_url(
                    $content_object->get_id(), $content_object->calculate_security_code()
                ), ToolbarItem::DISPLAY_ICON
            );
        }

        return $actions;
    }

    public function get_content_object_viewing_url($object)
    {
        return $this->get_repository_browser()->get_content_object_viewing_url($object);
    }

    public function get_parameters($include_search = false)
    {
        $parameters = $this->get_repository_browser()->get_parameters();

        $selected_types = TypeSelector::get_selection();

        if (is_array($selected_types) && count($selected_types))
        {
            $parameters[TypeSelector::PARAM_SELECTION] = $selected_types;
        }

        $parameters[ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY] =
            $this->get_repository_browser()->getButtonToolbarRenderer()->getSearchForm()->getQuery();

        return $parameters;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Component\BrowserComponent
     */
    public function get_repository_browser()
    {
        return $this->repository_browser;
    }

    public function get_table_condition($table_class_name)
    {
        return $this->get_condition();
    }

    /**
     *
     * @param int $template_registration_id
     */
    public function get_type_filter_url($template_registration_id)
    {
        return $this->get_repository_browser()->get_type_filter_url($template_registration_id);
    }

    public function get_url($parameters = [], $filter = [], $encode_entities = false)
    {
        return $this->get_repository_browser()->get_url($parameters, $filter, $encode_entities);
    }

    public function get_user()
    {
        return $this->get_repository_browser()->get_user();
    }
}
