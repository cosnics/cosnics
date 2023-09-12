<?php
namespace Chamilo\Core\Repository\Viewer;

use Chamilo\Core\Admin\Service\BreadcrumbGenerator;
use Chamilo\Core\Repository\Viewer\Architecture\Traits\ViewerTrait;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Format\Structure\BreadcrumbGeneratorInterface;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;

/**
 * @package Chamilo\Core\Repository\Viewer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    use ViewerTrait;

    public const ACTION_BROWSER = 'Browser';
    public const ACTION_CREATOR = 'Creator';
    public const ACTION_IMPORTER = 'Importer';
    public const ACTION_VIEWER = 'Viewer';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_CREATOR;

    public const PARAM_ACTION = 'viewer_action';
    public const PARAM_CONTENT_OBJECT_TEMPLATE_REGISTRATION_ID = 'template_id';
    public const PARAM_CONTENT_OBJECT_TYPE = 'content_object_type';
    public const PARAM_EDIT = 'edit';
    public const PARAM_EDIT_ID = 'viewer_edit_id';
    public const PARAM_ID = 'viewer_publish_id';
    public const PARAM_IMPORTED_CONTENT_OBJECT_IDS = 'imported_content_object_ids';
    public const PARAM_IMPORT_TYPE = 'import_type';
    public const PARAM_IN_WORKSPACES = 'in_workspaces';
    public const PARAM_PUBLISH_SELECTED = 'viewer_selected';
    public const PARAM_QUERY = 'query';
    public const PARAM_TAB = 'tab';
    public const PARAM_VIEW_ID = 'viewer_view_id';
    public const PARAM_WORKSPACE_ID = 'workspace_id';

    public const SELECT_MULTIPLE = 0;
    public const SELECT_SINGLE = 1;

    public const TAB_BROWSER = 'Browser';
    public const TAB_CREATOR = 'Creator';
    public const TAB_IMPORTER = 'Importer';
    public const TAB_VIEWER = 'Viewer';
    public const TAB_WORKSPACE_BROWSER = 'WorkspaceBrowser';

    /**
     * @var string[]
     */
    private $actions;

    private $creation_defaults;

    private $excluded_objects;

    private $maximum_select;

    private $parameters;

    /**
     * @var \Chamilo\Libraries\Format\Tabs\Link\LinkTabsRenderer
     */
    private $tabs;

    /**
     * @var string[]
     */
    private $types;

    /**
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);
        $this->maximum_select = self::SELECT_MULTIPLE;
        $this->default_content_objects = [];
        $this->parameters = [];
        $this->excluded_objects = [];

        $this->set_parameter(
            self::PARAM_ACTION, $this->getRequest()->query->get(self::PARAM_ACTION, self::ACTION_CREATOR)
        );
    }

    public function getBreadcrumbGenerator(): BreadcrumbGeneratorInterface
    {
        return $this->getService(BreadcrumbGenerator::class);
    }

    public function getCurrentWorkspace(): Workspace
    {
        return $this->getService('Chamilo\Core\Repository\CurrentWorkspace');
    }

    /**
     * Returns a list of the available tabs
     *
     * @return array
     */
    protected function getTabs()
    {
        $tabs = [];

        $tabs[self::TAB_CREATOR] = [
            'url' => $this->get_url(
                [self::PARAM_TAB => self::TAB_CREATOR, self::PARAM_ACTION => self::ACTION_CREATOR]
            ),
            'glyph' => new FontAwesomeGlyph('plus', ['fa-lg'], null, 'fas')
        ];

        $tabs[self::TAB_BROWSER] = [
            'url' => $this->get_url(
                [
                    self::PARAM_TAB => self::TAB_BROWSER,
                    self::PARAM_ACTION => self::ACTION_BROWSER,
                    self::PARAM_IN_WORKSPACES => false
                ]
            ),
            'glyph' => new FontAwesomeGlyph('folder', ['fa-lg'], null, 'fas')
        ];

        $tabs[self::TAB_WORKSPACE_BROWSER] = [
            'url' => $this->get_url(
                [
                    self::PARAM_TAB => self::TAB_WORKSPACE_BROWSER,
                    self::PARAM_ACTION => self::ACTION_BROWSER,
                    self::PARAM_IN_WORKSPACES => true
                ]
            ),
            'glyph' => new FontAwesomeGlyph('users', ['fa-lg'], null, 'fas')
        ];

        if ($this->get_maximum_select() > 1)
        {
            $tabs[self::TAB_IMPORTER] = [
                'url' => $this->get_url(
                    [self::PARAM_TAB => self::TAB_IMPORTER, self::PARAM_ACTION => self::ACTION_IMPORTER]
                ),
                'glyph' => new FontAwesomeGlyph('upload', ['fa-lg'], null, 'fas')
            ];
        }

        if ($this->get_action() == self::ACTION_VIEWER)
        {
            $tabs[self::TAB_VIEWER] = [
                'url' => $this->get_url(
                    [
                        self::PARAM_TAB => self::TAB_VIEWER,
                        self::PARAM_ACTION => self::ACTION_VIEWER,
                        self::PARAM_ID => $this->getRequest()->getFromRequestOrQuery(self::PARAM_ID)
                    ]
                ),
                'glyph' => new FontAwesomeGlyph('desktop', ['fa-lg'], null, 'fas')
            ];
        }

        return $tabs;
    }

    /**
     * @return string[]
     */
    public function get_actions()
    {
        return $this->actions;
    }

    /**
     * @return int
     */
    public function get_excluded_objects()
    {
        return $this->excluded_objects;
    }

    /**
     * @return int
     */
    public function get_maximum_select()
    {
        return $this->maximum_select;
    }

    /**
     * Returns the types of content object that the viewer can use.
     *
     * @return string[]
     */
    public function get_types()
    {
        return $this->get_application()->get_allowed_content_object_types();
    }

    /**
     * @param string[] $actions
     */
    public function set_actions($actions)
    {
        $this->actions = $actions;
    }

    /**
     * @param int $excluded_objects
     */
    public function set_excluded_objects($excluded_objects)
    {
        $this->excluded_objects = $excluded_objects;
    }

    /**
     * @param int $maximum_select
     */
    public function set_maximum_select($maximum_select)
    {
        $this->maximum_select = $maximum_select;
    }
}
