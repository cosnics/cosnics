<?php
namespace Chamilo\Core\Repository\Common\Renderer;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarSearchForm;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Exception;

abstract class ContentObjectRenderer implements TableSupport
{
    const TYPE_TABLE = 'Table';
    const TYPE_GALLERY = 'GalleryTable';
    const TYPE_SLIDESHOW = 'Slideshow';

    /**
     *
     * @var RepositoryManagerBrowserComponent
     */
    protected $repository_browser;

    /**
     *
     * @param RepositoryManagerBrowserComponent $repository_browser
     */
    public function __construct($repository_browser)
    {
        $this->repository_browser = $repository_browser;
    }

    /**
     *
     * @return RepositoryManagerBrowserComponent
     */
    public function get_repository_browser()
    {
        return $this->repository_browser;
    }

    public static function factory($type, $repository_browser)
    {
        $class = __NAMESPACE__ . '\Type\\' . StringUtilities::getInstance()->createString($type)->upperCamelize() .
             'ContentObjectRenderer';
        
        if (! class_exists($class))
        {
            throw new Exception(Translation::get('ContentObjectRendererTypeDoesNotExist', array('type' => $type)));
        }
        
        return new $class($repository_browser);
    }

    abstract public function as_html();

    public function get_parameters($include_search = false)
    {
        $parameters = $this->get_repository_browser()->get_parameters();
        
        $selected_types = TypeSelector::get_selection();
        
        if (is_array($selected_types) && count($selected_types))
        {
            $parameters[TypeSelector::PARAM_SELECTION] = $selected_types;
        }
        
        $parameters[ActionBarSearchForm::PARAM_SIMPLE_SEARCH_QUERY] = $this->get_repository_browser()->getButtonToolbarRenderer()->getSearchForm()->getQuery();
        
        return $parameters;
    }

    public function get_user()
    {
        return $this->get_repository_browser()->get_user();
    }

    public function get_condition()
    {
        return $this->get_repository_browser()->get_condition();
    }

    public function count_content_objects($condition)
    {
        return DataManager::count_active_content_objects(ContentObject::class_name(), $condition);
    }

    public function count_categories($conditions = null)
    {
        return $this->get_repository_browser()->count_categories($conditions);
    }

    public function get_url($parameters = array (), $filter = array(), $encode_entities = false)
    {
        return $this->get_repository_browser()->get_url($parameters, $filter, $encode_entities);
    }

    public function get_content_object_viewing_url($object)
    {
        return $this->get_repository_browser()->get_content_object_viewing_url($object);
    }

    /**
     *
     * @param int $template_registration_id
     */
    public function get_type_filter_url($template_registration_id)
    {
        return $this->get_repository_browser()->get_type_filter_url($template_registration_id);
    }

    public function get_content_object_actions(ContentObject $content_object)
    {
        $buttonToolBar = new ButtonToolBar();
        $buttonGroup = new ButtonGroup();

        $user = $this->get_user();
        $workspace = $this->get_repository_browser()->getWorkspace();
        $rightsService = RightsService::getInstance();

        $canEditContentObject = $rightsService->canEditContentObject($user, $content_object, $workspace);
        $canDeleteContentObject = $rightsService->canDeleteContentObject($user, $content_object, $workspace);
        $canUseContentObject = $rightsService->canUseContentObject($user, $content_object, $workspace);
        $canCopyContentObject = $rightsService->canCopyContentObject($user, $content_object, $workspace);


        $dropdownButton = new DropdownButton(
            Translation::get('Actions'),
            new FontAwesomeGlyph('cog'),
            Button::DISPLAY_ICON,
            'btn-link');
        $dropdownButton->setDropdownClasses('dropdown-menu-right');

        if ($canEditContentObject)
        {
            $buttonGroup->addButton(
                new Button(
                    Translation::get('Edit', null, Utilities::COMMON_LIBRARIES),
                    new FontAwesomeGlyph('pencil'),
                    $this->get_repository_browser()->get_content_object_editing_url($content_object),
                    Button::DISPLAY_ICON,
                    false,
                    'btn-link'
                )
            );
        }
        
        if ($canCopyContentObject)
        {
            $dropdownButton->addSubButton(
                new SubButton(
                    Translation::get('Duplicate'),
                    Theme::getInstance()->getCommonImagePath('Action/Copy'),
                    $this->get_repository_browser()->get_copy_content_object_url($content_object->get_id()),
                    SubButton::DISPLAY_ICON_AND_LABEL
                )
            );
        }
        
        if ($this->get_repository_browser()->getWorkspace() instanceof PersonalWorkspace)
        {
            if ($url = $this->get_repository_browser()->get_content_object_recycling_url($content_object))
            {
                $dropdownButton->addSubButton(
                    new SubButton(
                        Translation::get('Remove', null, Utilities::COMMON_LIBRARIES),
                        Theme::getInstance()->getCommonImagePath('Action/RecycleBin'),
                        $url,
                        SubButton::DISPLAY_ICON_AND_LABEL,
                        true
                    )
                );
            }
            else
            {
                $dropdownButton->addSubButton(
                    new SubButton(
                        Translation::get('RemoveNotAvailable', null, Utilities::COMMON_LIBRARIES),
                        Theme::getInstance()->getCommonImagePath('Action/RecycleBinNa'),
                        null,
                        SubButton::DISPLAY_ICON_AND_LABEL
                    )
                );
            }
        }
        
        if ($canEditContentObject && DataManager::workspace_has_categories($this->get_repository_browser()->getWorkspace()))
        {
            $dropdownButton->addSubButton(
                new SubButton(
                    Translation::get('Move', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Move'),
                    $this->get_repository_browser()->get_content_object_moving_url($content_object),
                    SubButton::DISPLAY_ICON_AND_LABEL
                )
            );
        }
        
        if ($this->get_repository_browser()->getWorkspace() instanceof PersonalWorkspace)
        {
            $dropdownButton->addSubButton(
                new SubButton(
                    Translation::get('Share', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Rights'),
                    $this->get_repository_browser()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_WORKSPACE,
                            Manager::PARAM_CONTENT_OBJECT_ID => $content_object->get_id(),
                            \Chamilo\Core\Repository\Workspace\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Manager::ACTION_SHARE)),
                    SubButton::DISPLAY_ICON_AND_LABEL
                )
            );
        }
        elseif ($canDeleteContentObject)
        {
            $url = $this->get_repository_browser()->get_url(
                array(
                    Manager::PARAM_ACTION => Manager::ACTION_WORKSPACE,
                    \Chamilo\Core\Repository\Workspace\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Workspace\Manager::ACTION_UNSHARE,
                    Manager::PARAM_CONTENT_OBJECT_ID => $content_object->getId()));

            $dropdownButton->addSubButton(
                new SubButton(
                    Translation::get('Unshare', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Unshare'),
                    $url,
                    SubButton::DISPLAY_ICON_AND_LABEL,
                    true
                )
            );
        }
        
        if ($canCopyContentObject)
        {
            $dropdownButton->addSubButton(
                new SubButton(
                    Translation::get('Export', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Export'),
                    $this->get_repository_browser()->get_content_object_exporting_url($content_object),
                    SubButton::DISPLAY_ICON_AND_LABEL
                )
            );
        }
        
        if ($canUseContentObject)
        {
            $dropdownButton->addSubButton(
                new SubButton(
                    Translation::get('Publish', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Publish'),
                    $this->get_repository_browser()->get_publish_content_object_url($content_object),
                    SubButton::DISPLAY_ICON_AND_LABEL
                )
            );
        }
        
        // $actions[] = new ToolbarItem(
        // Translation :: get('ContentObjectAlternativeLinker'),
        // Theme :: getInstance()->getCommonImagePath('Action/ContentObjectAlternativeLinker'),
        // $this->get_repository_browser()->get_content_object_alternative_linker($content_object),
        // ToolbarItem :: DISPLAY_ICON);
        
        $preview_url = $this->get_repository_browser()->get_preview_content_object_url($content_object);

        if ($content_object instanceof ComplexContentObjectSupport)
        {
            if (\Chamilo\Core\Repository\Builder\Manager::exists($content_object->package()))
            {
                if ($canEditContentObject)
                {
                    $dropdownButton->addSubButton(
                        new SubButton(
                            Translation::get('BuildComplexObject', null, Utilities::COMMON_LIBRARIES),
                            Theme::getInstance()->getCommonImagePath('Action/Build'),
                            $this->get_repository_browser()->get_browse_complex_content_object_url($content_object),
                            SubButton::DISPLAY_ICON_AND_LABEL
                        )
                    );
                }

                $buttonGroup->addButton(
                    new Button(
                        Translation::get('Preview', null, Utilities::COMMON_LIBRARIES),
                        new FontAwesomeGlyph('caret-square-o-right'),
                        $preview_url,
                        Button::DISPLAY_ICON,
                        false,
                        'btn-link',
                        '_blank'
                    )
                );
            }
            elseif ($canEditContentObject)
            {
                $buttonGroup->addButton(
                    new Button(
                        Translation::get('BuildPreview', null, Utilities::COMMON_LIBRARIES),
                        new FontAwesomeGlyph('desktop'),
                        $preview_url,
                        Button::DISPLAY_ICON,
                        false,
                        'btn-link',
                        '_blank'
                    )
                );
            }
            else
            {
                $buttonGroup->addButton(
                    new Button(
                        Translation::get('Preview', null, Utilities::COMMON_LIBRARIES),
                        new FontAwesomeGlyph('caret-square-o-right'),
                        $preview_url,
                        Button::DISPLAY_ICON,
                        false,
                        'btn-link',
                        '_blank'
                    )
                );
            }
        }
        else
        {
            $buttonGroup->addButton(
                new Button(
                    Translation::get('Preview', null, Utilities::COMMON_LIBRARIES),
                    new FontAwesomeGlyph('caret-square-o-right'),
                    $preview_url,
                    Button::DISPLAY_ICON,
                    false,
                    'btn-link',
                    '_blank'
                )
            );
        }
        
        if ($content_object->get_type() == 'Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File')
        {
            $dropdownButton->addSubButton(
                new SubButton(
                    Translation::get('Download', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Download'),
                    $this->get_repository_browser()->get_document_downloader_url(
                        $content_object->get_id(),
                        $content_object->calculate_security_code()),
                    SubButton::DISPLAY_ICON_AND_LABEL
                )
            );
        }

        $buttonGroup->addButton($dropdownButton);
        $buttonToolBar->addItem($buttonGroup);
        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolbarRenderer->render();
    }

    public function get_table_condition($table_class_name)
    {
        return $this->get_condition();
    }
}
