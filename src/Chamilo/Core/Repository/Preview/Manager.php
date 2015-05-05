<?php
namespace Chamilo\Core\Repository\Preview;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Format\Structure\Page;

abstract class Manager extends Application
{
    // Parameters
    const PARAM_CONTENT_OBJECT_ID = 'content_object_id';

    // Available actions
    const ACTION_DISPLAY = 'Display';
    const ACTION_RENDITION = 'Rendition';
    const ACTION_REPORTING = 'Reporting';
    const ACTION_RESET = 'Reset';

    // The Default action
    const DEFAULT_ACTION = self :: ACTION_DISPLAY;

    /**
     *
     * @var \core\repository\ContentObject
     */
    private $content_object;

    /**
     *
     * @param \core\user\storage\data_class\User $user
     * @param \libraries\architecture\application\Application,null $application
     */
    public function __construct(\Symfony\Component\HttpFoundation\Request $request, $user, $application = null)
    {
        parent :: __construct($request, $user, $application);

        $content_object_id = Request :: get(self :: PARAM_CONTENT_OBJECT_ID);
        $this->content_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
            ContentObject :: class_name(),
            $content_object_id);

        if (! $this->content_object instanceof ContentObject)
        {
            throw new NoObjectSelectedException(Translation :: get('ContentObject'));
        }

        $this->set_parameter(self :: PARAM_CONTENT_OBJECT_ID, $this->content_object->get_id());
    }

    /*
     * (non-PHPdoc) @see \libraries\architecture\application\Application::render_header()
     */
    public function render_header()
    {
        $html = array();

        $page = Page :: getInstance();
        $page->setViewMode(Page :: VIEW_MODE_HEADERLESS);

        $html[] = $page->getHeader()->toHtml();
        $html[] = '<div class="warning-banner">';

        $translation = Translation :: get(
            'PreviewModeWarning',
            null,
            ClassnameUtilities :: getInstance()->getNamespaceFromObject($this->get_content_object()));

        if ($translation == 'PreviewModeWarning')
        {
            $translation = Translation :: get('PreviewModeWarning');
        }

        $html[] = $translation;
        $html[] = '<div class="actions">';

        if ($this->get_action() != self :: ACTION_DISPLAY && $this->get_content_object()->is_complex_content_object())
        {
            $html[] = Theme :: getInstance()->getImage(
                'Action/Display',
                'png',
                Translation :: get('DisplayPreview'),
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DISPLAY)),
                ToolbarItem :: DISPLAY_ICON);
        }

        $is_complex_object = $this->get_content_object()->is_complex_content_object();
        $is_display_action = $this->get_action() == self :: ACTION_DISPLAY;

        if ($is_display_action && $is_complex_object && $this->getPreview()->getComponent()->supports_reset())
        {
            $html[] = Theme :: getInstance()->getImage(
                'Action/Reset',
                'png',
                Translation :: get('ResetDisplayPreview'),
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_RESET)),
                ToolbarItem :: DISPLAY_ICON);
        }

        if ($this->get_action() != self :: ACTION_RENDITION)
        {
            $html[] = Theme :: getInstance()->getImage(
                'Action/Rendition',
                'png',
                Translation :: get('RenditionPreview'),
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_RENDITION)),
                ToolbarItem :: DISPLAY_ICON);
        }

        if ($this->get_action() != self :: ACTION_REPORTING && $this->has_reporting())
        {
            $html[] = Theme :: getInstance()->getImage(
                'Action/Reporting',
                'png',
                Translation :: get('ReportingPreview'),
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_REPORTING)),
                ToolbarItem :: DISPLAY_ICON);
        }

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return \core\repository\ContentObject
     */
    public function get_content_object()
    {
        return $this->content_object;
    }

    /**
     *
     * @return boolean
     */
    public function has_reporting()
    {
        return self :: reporting($this->get_content_object());
    }

    /**
     *
     * @param \core\repository\ContentObject $content_object
     * @return boolean
     */
    public static function reporting($content_object)
    {
        $package = $content_object->package();
        $reporting_manager_class = $package . '\Integration\Chamilo\Core\Reporting\Preview\Manager';

        return class_exists($reporting_manager_class);
    }

    /**
     *
     * @param \core\repository\ContentObject,int $content_object
     */
    public static function get_content_object_default_action_link($content_object)
    {
        if (! $content_object instanceof ContentObject && is_numeric($content_object))
        {
            $content_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                ContentObject :: class_name(),
                $content_object);
        }

        if (! $content_object instanceof ContentObject)
        {
            throw new NoObjectSelectedException(Translation :: get('ContentObject'));
        }

        $action = $content_object->is_complex_content_object() ? self :: ACTION_DISPLAY : self :: ACTION_RENDITION;

        $redirect = new Redirect(
            array(
                self :: PARAM_CONTEXT => self :: context(),
                self :: PARAM_ACTION => $action,
                self :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));

        return $redirect->getUrl();
    }

    public function getPreview()
    {
        $package = $this->get_content_object()->package();
        $context = $package . '\Display\Preview';
        $factory = new ApplicationFactory($this->getRequest(), $context, $this->get_user(), $this);

        return $factory;
    }
}