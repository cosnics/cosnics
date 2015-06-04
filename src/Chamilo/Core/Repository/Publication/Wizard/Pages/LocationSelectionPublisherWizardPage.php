<?php
namespace Chamilo\Core\Repository\Publication\Wizard\Pages;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\SortableTableFromArray;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;

/**
 * $Id: location_selection_publisher_wizard_page.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib.repository_manager.component.publication_wizard.pages
 */

/**
 * Class for application settings page Displays a form where the user can enter the installation settings regarding the
 * applications
 */
class LocationSelectionPublisherWizardPage extends PublisherWizardPage
{

    /**
     *
     * @var \core\repository\ContentObject[]
     */
    private $content_objects;

    /**
     *
     * @var string
     */
    private $type;

    private $applications;

    /**
     *
     * @param string $name
     * @param PublisherComponent $parent
     */
    public function __construct($name, $parent)
    {
        parent :: __construct($name, $parent);

        $ids = Request :: get(\Chamilo\Core\Repository\Manager :: PARAM_CONTENT_OBJECT_ID);

        if (empty($ids))
        {
            throw new NoObjectSelectedException(Translation :: get('ContentObject'));
        }

        if (! is_array($ids))
        {
            $ids = array($ids);
        }

        $this->content_objects = array();
        $this->type = null;

        // Check whether the selected objects exist and perform the necessary rights checks
        foreach ($ids as $id)
        {
            $content_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                ContentObject :: class_name(),
                $id);

            // fail if no object exists
            if (! $content_object instanceof ContentObject)
            {
                throw new NoObjectSelectedException(Translation :: get('ContentObject'));
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
                throw new \Exception(Translation :: get('ObjectsNotSameType'));
            }
        }
    }

    /**
     *
     * @return string
     */
    public function get_title()
    {
        return Translation :: get('LocationSelection');
    }

    /**
     *
     * @return string
     */
    public function add_selected_content_objects()
    {
        $category_title = htmlentities(
            Translation :: get(count($this->content_objects) > 1 ? 'LocationSelectionsInfo' : 'LocationSelectionInfo'));

        $category = Theme :: getInstance()->getImage(
            'Logo/22',
            'png',
            $category_title,
            null,
            ToolbarItem :: DISPLAY_ICON_AND_LABEL,
            false,
            \Chamilo\Core\Repository\Manager :: context());

        $this->addElement('category', $category, 'publication-location');

        Utilities :: order_content_objects_by_title($this->content_objects);

        $table_data = array();

        foreach ($this->content_objects as $content_object)
        {
            $table_data[] = array($content_object->get_icon_image(), $content_object->get_title());
        }

        $type_image = Theme :: getInstance()->getCommonImage(
            'Action/Category',
            'png',
            Translation :: get('Type'),
            null,
            ToolbarItem :: DISPLAY_ICON);

        $table = new SortableTableFromArray($table_data, 1, count($table_data), 'selected-content-objects');
        $table_header = $table->getHeader();
        $table->set_header(0, $type_image, false);
        $table_header->setColAttributes(0, 'class="action"');
        $table->set_header(1, Translation :: get('Title', null, \Chamilo\Core\Repository\Manager :: context()), false);

        $this->addElement('html', $table->as_html());

        $this->addElement('category');
    }

    public function buildForm()
    {
        $this->_formBuilt = true;

        $this->add_selected_content_objects();

        $registrations = \Chamilo\Configuration\Storage\DataManager :: get_integrating_contexts(
            'Chamilo\Core\Repository');

        $this->applications = array();

        $total_locations = 0;

        foreach ($registrations as $registration)
        {
            $manager_class = $registration->get_context() . '\Publication\Manager';
            $application = ClassnameUtilities :: getInstance()->getNamespaceParent($registration->get_context(), 4);

            $locations = $manager_class :: get_content_object_publication_locations(
                $this->content_objects[0],
                $this->get_parent()->get_user());

            $total_locations += $locations->size();

            if ($locations->size() > 0)
            {
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

            $prevnext[] = $this->createElement(
                'style_submit_button',
                $this->getButtonName('next'),
                Translation :: get('Publish', null, Utilities :: COMMON_LIBRARIES),
                'class="normal publish"');

            $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
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

        $this->setDefaultAction('next');
    }

    /**
     *
     * @param string $application
     * @param \core\repository\publication\Locations $locations
     */
    public function add_locations($locations)
    {
        $this->applications[] = $locations->get_context();

        $category = Theme :: getInstance()->getImage(
            'Logo/22',
            'png',
            Translation :: get('TypeName', null, $locations->get_application()),
            null,
            ToolbarItem :: DISPLAY_ICON_AND_LABEL,
            false,
            $locations->get_application());

        $this->addElement('category', $category, 'publication-location');

        $renderer_class = $locations->get_context() . '\LocationSelector';
        $renderer = new $renderer_class($this, $locations);
        $renderer->run();

        $manager_class = $locations->get_context() . '\Manager';
        $manager_class :: add_publication_attributes_elements($this);

        $this->addElement('category');
    }
}
