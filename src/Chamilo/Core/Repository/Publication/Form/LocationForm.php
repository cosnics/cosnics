<?php
namespace Chamilo\Core\Repository\Publication\Form;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\SortableTableFromArray;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 * $Id: location_selection_publisher_wizard_page.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib.repository_manager.component.publication_wizard.pages
 */

/**
 * Class for application settings page Displays a form where the user can enter the installation settings regarding the
 * applications
 */
class LocationForm extends FormValidator
{

    /**
     *
     * @var \core\repository\ContentObject[]
     */
    private $content_objects;

    /**
     *
     * @var integer[]
     */
    private $contentObjectIdentifiers;

    /**
     *
     * @var string
     */
    private $type;

    private $applications;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @throws NoObjectSelectedException
     * @throws NotAllowedException
     * @throws \Exception
     */
    public function __construct(Application $application, $action)
    {
        $this->application = $application;
        parent :: __construct('page_locations', 'post', $action);

        $this->contentObjectIdentifiers = $this->getApplication()->getRequest()->get(
            \Chamilo\Core\Repository\Manager :: PARAM_CONTENT_OBJECT_ID);

        if (empty($this->contentObjectIdentifiers))
        {
            throw new NoObjectSelectedException(Translation :: get('ContentObject'));
        }

        if (! is_array($this->contentObjectIdentifiers))
        {
            $this->contentObjectIdentifiers = array($this->contentObjectIdentifiers);
        }

        $this->content_objects = array();
        $this->type = null;

        // Check whether the selected objects exist and perform the necessary rights checks
        foreach ($this->contentObjectIdentifiers as $id)
        {
            $content_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                ContentObject :: class_name(),
                $id);

            // fail if no object exists
            if (! $content_object instanceof ContentObject)
            {
                throw new NoObjectSelectedException(Translation :: get('ContentObject'));
            }

            // Check the USE-right
            if (! RightsService :: getInstance()->canUseContentObject(
                $this->getApplication()->get_user(),
                $content_object))
            {
                throw new NotAllowedException();
            }

            // Don't allow publication is the content object is in the RECYCLED
            // state
            if ($content_object->get_state() == ContentObject :: STATE_RECYCLED)
            {
                throw new NotAllowedException();
            }

            $this->content_objects[] = $content_object;

            if ($this->type == null)
            {
                $this->type = $content_object->get_type();
            }
            elseif ($this->type != $content_object->get_type())
            {
                throw new UserException(Translation :: get('ObjectsNotSameType'));
            }
        }

        $this->buildForm();
    }

    public function getApplication()
    {
        return $this->application;
    }

    /**
     *
     * @return string
     */
    public function add_selected_content_objects()
    {
        $category_title = htmlentities(
            Translation :: get(count($this->content_objects) > 1 ? 'LocationSelectionsInfo' : 'LocationSelectionInfo'));

        $this->addElement('category', $category_title, 'publication-location');

        Utilities :: order_content_objects_by_title($this->content_objects);

        $table_data = array();

        foreach ($this->content_objects as $content_object)
        {
            $table_data[] = array($content_object->get_icon_image(Theme :: ICON_MINI), $content_object->get_title());
        }

        $type_image = Theme :: getInstance()->getCommonImage(
            'Action/Category',
            'png',
            Translation :: get('Type'),
            null,
            ToolbarItem :: DISPLAY_ICON);

        $header = array();
        $header[] = new StaticTableColumn('category', $type_image, 'cell-stat-x2');
        $header[] = new StaticTableColumn(
            Translation :: get('Title', null, \Chamilo\Core\Repository\Manager :: context()));

        $table = new SortableTableFromArray(
            $table_data,
            $header,
            array(),
            1,
            count($table_data),
            SORT_ASC,
            'selected-content-objects');
        $this->addElement('html', $table->toHtml());

        $this->addElement('category');
    }

    public function buildForm()
    {
        $this->_formBuilt = true;

        $this->add_selected_content_objects();

        $registrations = Configuration :: getInstance()->getIntegrationRegistrations('Chamilo\Core\Repository');

        $this->applications = array();

        $total_locations = 0;

        foreach ($registrations as $registration)
        {
            $manager_class = $registration[Registration :: PROPERTY_CONTEXT] . '\Publication\Manager';

            $locations = $manager_class :: get_content_object_publication_locations(
                $this->content_objects[0],
                $this->getApplication()->get_user());

            if (! is_null($locations) && $locations->size() > 0)
            {
                $total_locations += $locations->size();
                $this->add_locations($locations);
            }
        }

        if ($total_locations > 0)
        {
            $html = array();
            $html[] = '<div style="padding: 5px 0px;">';
            $html[] = '<a href="#" class="select-all-checkboxes">';
            $html[] = Translation :: get('SelectAll', null, Utilities :: COMMON_LIBRARIES);
            $html[] = '</a>';
            $html[] = ' - ';
            $html[] = '<a href="#" class="select-no-checkboxes">';
            $html[] = Translation :: get('UnselectAll', null, Utilities :: COMMON_LIBRARIES);
            $html[] = '</a>';
            $html[] = '</div>';

            $this->addElement('html', implode('', $html));

            $this->addElement('html', '<br /><br />');

            $this->addElement(
                'style_submit_button',
                'publish',
                Translation :: get('Publish', null, Utilities :: COMMON_LIBRARIES),
                null,
                null,
                'ok-sign');
        }
        else
        {
            $this->addElement(
                'html',
                '<div class="warning-message">' . Translation :: get('NoLocationsFound') . '</div>');
        }

        $this->addElement(
            'html',
            '<script type="text/javascript" src="' .
                 Path :: getInstance()->getJavascriptPath('Chamilo\Core\Repository\Publication', true) . 'Visibility.js' .
                 '"></script>');
    }

    /**
     *
     * @param string $application
     * @param \core\repository\publication\Locations $locations
     */
    public function add_locations($locations)
    {
        $this->applications[] = $locations->get_context();

        $this->addElement(
            'category',
            Translation :: get('TypeName', null, $locations->get_application()),
            'publication-location');

        $renderer_class = $locations->get_context() . '\LocationSelector';
        $renderer = new $renderer_class($this, $locations);
        $renderer->run();

        $manager_class = $locations->get_context() . '\Manager';
        $manager_class :: add_publication_attributes_elements($this);

        $this->addElement('category');
    }
}
