<?php
namespace Chamilo\Core\Repository\Publication\Wizard\Pages;

use Chamilo\Core\Repository\Publication\Location\LocationResult;
use Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use HTML_QuickForm_Action;

/**
 *
 * @package core\repository\publication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PublisherWizardProcess extends HTML_QuickForm_Action
{

    /**
     * The repository tool in which the wizard runs.
     */
    private $parent;

    /**
     * Constructor
     *
     * @param Tool $parent The repository tool in which the wizard runs.
     */
    public function __construct($parent)
    {
        $this->parent = $parent;
    }

    public function perform($page, $actionName)
    {
        $values = $page->controller->exportValues();

        $content_object_ids = Request :: get(Manager :: PARAM_CONTENT_OBJECT_ID);
        if (! is_array($content_object_ids))
        {
            $content_object_ids = array($content_object_ids);
        }

        $condition = new InCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID),
            $content_object_ids);

        $order_by = array();
        $order_by[] = new OrderBy(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID));

        $content_objects = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_objects(
            ContentObject :: class_name(),
            new DataClassRetrievesParameters($condition, null, null, $order_by))->as_array();

        $html = array();

        $html[] = $this->parent->render_header();

        if (count($values[Manager :: WIZARD_LOCATION]) > 0)
        {
            foreach ($values[Manager :: WIZARD_LOCATION] as $registration_id => $locations)
            {
                $registration = \Chamilo\Configuration\Storage\DataManager :: retrieve_by_id(
                    \Chamilo\Configuration\Storage\DataClass\Registration :: class_name(),
                    $registration_id);

                $result_class = $registration->get_context() . '\LocationResult';
                $result = new $result_class($this, $registration->get_context());

                foreach ($locations as $encoded_location)
                {
                    $location = unserialize(base64_decode($encoded_location));

                    $manager_class = $registration->get_context() . '\Manager';

                    if (isset($values[Manager :: WIZARD_OPTION]) &&
                         isset($values[Manager :: WIZARD_OPTION][$registration_id]))
                    {
                        $options = $values[Manager :: WIZARD_OPTION][$registration_id];
                    }
                    else
                    {
                        $options = array();
                    }

                    foreach ($content_objects as $content_object)
                    {
                        $success = $manager_class :: publish_content_object($content_object, $location, $options);
                        $result->add($location, $content_object, $success);
                    }
                }

                $html[] = $this->process_result($result);
            }
        }
        else
        {
            $html[] = Display :: warning_message(Translation :: get('NoLocationsFound'), true);
        }

        $url = $this->parent->get_url(array(Manager :: PARAM_ACTION => null, Manager :: PARAM_ACTION => null));
        $html[] = '<a href="' . $url . '">' . Translation :: get('GoBack') . '</a>';

        $html[] = '<script type="text/javascript" src="' .
             Path :: getInstance()->namespaceToFullPath(__NAMESPACE__, true) . 'resources/javascript/visibility.js' .
             '"></script>';

        $page->controller->container(true);

        // Display the page footer
        $html[] = $this->parent->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function process_result(LocationResult $result)
    {
        $package_context = ClassnameUtilities :: getInstance()->getNamespaceParent($result->get_context(), 4);

        $category = Theme :: getInstance()->getImage(
            'Logo/22',
            'png',
            Translation :: get('TypeName', null, $package_context),
            null,
            ToolbarItem :: DISPLAY_ICON_AND_LABEL,
            false,
            $package_context);

        $html = array();
        $html[] = '<div class="configuration_form publication-location" >';
        $html[] = '<span class="category">' . $category . '</span>';
        $html[] = $result->as_html();
        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
