<?php
namespace Chamilo\Core\Repository\Builder;

use Chamilo\Core\Repository\Builder\Interfaces\MenuSupport;
use Chamilo\Core\Repository\Selector\Renderer\BasicTypeSelectorRenderer;
use Chamilo\Core\Repository\Selector\TabsTypeSelectorSupport;
use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Core\Repository\Selector\TypeSelectorFactory;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Table\Complex\ComplexTable;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

abstract class Manager extends Application implements TabsTypeSelectorSupport, TableSupport
{
    // Parameters
    const ACTION_BROWSE = 'Browser';

    const ACTION_CHANGE_PARENT = 'ParentChanger';

    const ACTION_COPY_COMPLEX_CONTENT_OBJECT_ITEM = 'Copier';

    const ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM = 'Creator';

    const ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM = 'Deleter';

    const ACTION_MOVE_COMPLEX_CONTENT_OBJECT_ITEM = 'Mover';

    const ACTION_PREVIEW = 'Preview';

    const ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM = 'Updater';

    const ACTION_VIEW_ATTACHMENT = 'AttachmentViewer';

    // Actions

    const ACTION_VIEW_COMPLEX_CONTENT_OBJECT_ITEM = 'Viewer';

    const DEFAULT_ACTION = self::ACTION_BROWSE;

    const PARAM_ACTION = 'builder_action';

    const PARAM_ATTACHMENT_ID = 'attachment_id';

    const PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID = 'cloi';

    const PARAM_DELETE_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM = 'delete_selected_cloi';

    const PARAM_DIRECTION = 'direction';

    const PARAM_MOVE_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM = 'move_selected_cloi';

    const PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID = 'selected_cloi';

    const PARAM_TEMPLATE_ID = 'template_id';

    // Default action

    const PARAM_TYPE = 'type';

    private static $instance;

    protected $menu;

    /**
     * The current item in treemenu to determine where we are in the structure
     *
     * @var ComplexContentObjectItem
     */
    private $complex_content_object_item;

    /**
     * The selected parent content object
     *
     * @var ContentObject
     */
    private $parent_content_object;

    /**
     * The item we select to execute an action like update / delete / move etc
     *
     * @var ComplexContentObjectItem
     */
    private $selected_complex_content_object_item;

    // Singleton

    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        $complex_content_object_item_id = Request::get(self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        if ($complex_content_object_item_id)
        {
            $this->complex_content_object_item = $this->get_complex_content_object_by_id(
                $complex_content_object_item_id
            );
        }

        $selected_complex_content_object_item_id = Request::get(self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);

        if ($selected_complex_content_object_item_id)
        {
            $this->set_parameter(
                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID, $selected_complex_content_object_item_id
            );

            if (!is_array($selected_complex_content_object_item_id))
            {
                $this->selected_complex_content_object_item = $this->get_complex_content_object_by_id(
                    $selected_complex_content_object_item_id
                );
            }
            else
            {
                $this->selected_complex_content_object_item = [];

                foreach ($selected_complex_content_object_item_id as $id)
                {
                    $this->selected_complex_content_object_item[] = $this->get_complex_content_object_by_id($id);
                }
            }
        }
    }

    protected function build_complex_content_object_menu()
    {
        $this->menu = new Menu(
            $this->get_root_content_object(), $this->get_complex_content_object_item(),
            $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE))
        );
    }

    /**
     * Determine whether the content object type has a (complex) builder
     *
     * @param string $content_object_type
     *
     * @return boolean
     */
    public static function exists($content_object_type)
    {
        $class_name = $content_object_type . '\Builder\Manager';

        return class_exists($class_name);
    }

    public static function factory($type, $application)
    {
        $class = 'Chamilo\Core\Repository\ContentObject\\' .
            StringUtilities::getInstance()->createString($type)->upperCamelize() . '\Builder\Manager';

        return new $class($application->get_user(), $application);
    }

    public function getButtonToolbarRenderer(ContentObject $content_object = null)
    {
        return '';
    }

    /**
     *
     * @return LinkTypeSelectorOption[]
     */
    public function get_additional_links()
    {
        return [];
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID;

        return parent::getAdditionalParameters($additionalParameters);
    }

    public function get_browse_url()
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_BROWSE,
                self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()
            )
        );
    }

    public function get_complex_content_object_breadcrumbs()
    {
        if (is_null($this->menu))
        {
            $this->build_complex_content_object_menu();
        }

        return $this->menu->get_breadcrumbs();
    }

    /**
     * Retrieves and validates a complex content object with a given id
     *
     * @param $complex_content_object_item_id int
     *
     * @return ComplexContentObjectItem
     */
    protected function get_complex_content_object_by_id($complex_content_object_item_id)
    {
        $complex_content_object_item = DataManager::retrieve_by_id(
            ComplexContentObjectItem::class, $complex_content_object_item_id
        );

        if (is_null($complex_content_object_item))
        {
            throw new ObjectNotExistException(
                Translation::get('ComplexContentObjectItem'), $complex_content_object_item_id
            );
        }

        if (!DataManager::is_child_of_content_object(
            $this->get_root_content_object_id(), $complex_content_object_item->get_ref()
        ))
        {
            throw new NotAllowedException();
        }

        return $complex_content_object_item;
    }

    public function get_complex_content_object_item()
    {
        return $this->complex_content_object_item;
    }

    public function get_complex_content_object_item_copy_url($selected_content_object_item_id)
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_COPY_COMPLEX_CONTENT_OBJECT_ITEM,
                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_content_object_item_id,
                self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()
            )
        );
    }

    public function get_complex_content_object_item_delete_url($selected_content_object_item_id)
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_content_object_item_id,
                self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()
            )
        );
    }

    public function get_complex_content_object_item_edit_url($selected_content_object_item_id)
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_content_object_item_id,
                self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()
            )
        );
    }

    public function get_complex_content_object_item_id()
    {
        if ($this->complex_content_object_item)
        {
            return $this->complex_content_object_item->get_id();
        }
    }

    public function get_complex_content_object_item_move_url($selected_content_object_item_id, $direction)
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_MOVE_COMPLEX_CONTENT_OBJECT_ITEM,
                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_content_object_item_id,
                self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id(),
                self::PARAM_DIRECTION => $direction
            )
        );
    }

    // url building

    public function get_complex_content_object_item_view_url($selected_content_object_item_id)
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT_ITEM,
                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_content_object_item_id,
                self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()
            )
        );
    }

    public function get_complex_content_object_menu()
    {
        if (is_null($this->menu))
        {
            $this->build_complex_content_object_menu();
        }

        return $this->menu->render_as_tree();
    }

    public function get_complex_content_object_parent_changer_url($selected_content_object_item_id)
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_CHANGE_PARENT,
                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_content_object_item_id,
                self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()
            )
        );
    }

    public function get_complex_content_object_table_condition()
    {
        if ($this->get_complex_content_object_item())
        {
            return new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
                ), new StaticConditionVariable($this->get_complex_content_object_item()->get_ref()),
                ComplexContentObjectItem::getStorageUnitName()
            );
        }

        return new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
            ), new StaticConditionVariable($this->get_root_content_object_id()),
            ComplexContentObjectItem::getStorageUnitName()
        );
    }

    /**
     * Common functionality
     */
    public function get_complex_content_object_table_html()
    {
        $name_space = $this->get_root_content_object()->package();

        $class = $name_space . '\Builder\Component\Browser\ComplexTable';

        if (class_exists($class))
        {
            $table = new $class($this);
        }
        else
        {
            $table = new ComplexTable($this);
        }

        return $table->as_html();
    }

    public function get_content_object_display_attachment_url($attachment)
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_VIEW_ATTACHMENT,
                self::PARAM_ATTACHMENT_ID => $attachment->get_id()
            )
        );
    }

    public function get_content_object_type_creation_url($template_registration_id)
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM,
                TypeSelector::PARAM_SELECTION => $template_registration_id,
                self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()
            )
        );
    }

    public function get_create_complex_content_object_item_url()
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM,
                self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()
            )
        );
    }

    public function get_creation_links($content_object, $content_object_types = [])
    {
        if (count($content_object_types) == 0)
        {
            $content_object_types = $content_object->get_allowed_types();
        }

        $typeSelectorFactory = new TypeSelectorFactory($content_object_types, $this->get_user_id());
        $type_selector = $typeSelectorFactory->getTypeSelector();

        $type_selector_renderer = new BasicTypeSelectorRenderer(
            $this, $type_selector, $this->get_additional_links(), false, $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM,
                self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()
            )
        )
        );

        return $type_selector_renderer->render();
    }

    public function get_parent_content_object()
    {
        if (!$this->parent_content_object)
        {
            $this->parent_content_object = DataManager::retrieve_by_id(
                ContentObject::class, $this->get_parent_content_object_id()
            );
        }

        return $this->parent_content_object;
    }

    public function get_parent_content_object_id()
    {
        if ($this->complex_content_object_item)
        {
            return $this->get_complex_content_object_item()->get_ref();
        }

        return $this->get_root_content_object_id();
    }

    /**
     * Returns the url to the preview component
     *
     * @return string
     */
    public function get_preview_content_object_url()
    {
        return $this->get_url(array(self::PARAM_ACTION => self::ACTION_PREVIEW));
    }

    public function get_root_content_object()
    {
        return $this->get_parent()->get_root_content_object();
    }

    public function get_root_content_object_id()
    {
        return $this->get_parent()->get_root_content_object()->get_id();
    }

    public function get_selected_complex_content_object_item()
    {
        return $this->selected_complex_content_object_item;
    }

    public function get_selected_complex_content_object_item_id()
    {
        if ($this->selected_complex_content_object_item)
        {
            return $this->selected_complex_content_object_item->get_id();
        }
    }

    public function get_table_condition($table_class_name)
    {
        return $this->get_complex_content_object_table_condition();
    }

    public function redirect_away_from_complex_builder($message, $error_message)
    {
        $this->get_parent()->redirect_away_from_complex_builder($message, $error_message);
    }

    /**
     *
     * @return boolean
     * @deprecated Use instanceof MenuSupport directly from now on
     */
    public function show_menu()
    {
        return $this instanceof MenuSupport;
    }
}
