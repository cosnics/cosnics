<?php
namespace Chamilo\Core\Repository\Publication;

use Chamilo\Core\Repository\Publication\Location\LocationResult;
use Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 *
 * @package Chamilo\Core\Repository\Publication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class PublicationProcessor
{

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    /**
     *
     * @var string[]
     */
    private $submittedValues;

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param string[] $submittedValues
     */
    public function __construct(Application $application, $submittedValues)
    {
        $this->application = $application;
        $this->submittedValues = $submittedValues;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    /**
     *
     * @return string[]
     */
    public function getSubmittedValues()
    {
        return $this->submittedValues;
    }

    /**
     *
     * @param string[] $submittedValues
     */
    public function setSubmittedValues($submittedValues)
    {
        $this->submittedValues = $submittedValues;
    }

    public function run()
    {
        $values = $this->getSubmittedValues();
        
        $content_object_ids = $this->getApplication()->getRequest()->query->get(
            \Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID);
        
        if (! is_array($content_object_ids))
        {
            $content_object_ids = array($content_object_ids);
        }
        
        $condition = new InCondition(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_ID), 
            $content_object_ids);
        
        $order_by = array();
        $order_by[] = new OrderBy(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_ID));
        
        $content_objects = \Chamilo\Core\Repository\Storage\DataManager::retrieve_content_objects(
            ContentObject::class_name(), 
            new DataClassRetrievesParameters($condition, null, null, $order_by))->as_array();
        
        $html = array();
        
        $html[] = $this->getApplication()->render_header();
        
        $html[] = '<div class="alert alert-info" style="margin-top: 15px; margin-bottom: 35px;">' .
             Translation::getInstance()->getTranslation('PublishInformationMessage', null, Manager::context()) . '</div>';
        
        if (count($values[Manager::WIZARD_LOCATION]) > 0)
        {
            foreach ($values[Manager::WIZARD_LOCATION] as $registration_id => $locations)
            {
                $registration = \Chamilo\Configuration\Storage\DataManager::retrieve_by_id(
                    \Chamilo\Configuration\Storage\DataClass\Registration::class_name(), 
                    $registration_id);
                
                $result_class = $registration->get_context() . '\Publication\LocationResult';
                $result = new $result_class($this, $registration->get_context());
                
                foreach ($locations as $encoded_location)
                {
                    $location = unserialize(base64_decode($encoded_location));
                    
                    $manager_class = $registration->get_context() . '\Publication\Manager';
                    
                    if (isset($values[Manager::WIZARD_OPTION]) &&
                         isset($values[Manager::WIZARD_OPTION][$registration_id]))
                    {
                        $options = $values[Manager::WIZARD_OPTION][$registration_id];
                    }
                    else
                    {
                        $options = array();
                    }
                    
                    foreach ($content_objects as $content_object)
                    {
                        $success = $manager_class::publish_content_object($content_object, $location, $options);
                        $result->add($location, $content_object, $success);
                    }
                }
                
                $html[] = $this->process_result($result);
            }
        }
        else
        {
            $html[] = Display::warning_message(Translation::get('NoLocationsFound'), true);
        }
        
        $html[] = '<script type="text/javascript" src="' .
             Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository\Publication', true) . 'Visibility.js' .
             '"></script>';
        
        // Display the page footer
        $html[] = $this->getApplication()->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    public function process_result(LocationResult $result)
    {
        $package_context = ClassnameUtilities::getInstance()->getNamespaceParent($result->get_context(), 4);
        
        $category = Theme::getInstance()->getImage(
            'Logo/22', 
            'png', 
            Translation::get('TypeName', null, $package_context), 
            null, 
            ToolbarItem::DISPLAY_ICON_AND_LABEL, 
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
