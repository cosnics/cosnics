<?php
namespace Chamilo\Configuration\Form;

/**
 *
 * @package configuration\form
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
use Chamilo\Configuration\Form\Form\ExecuteForm;
use Chamilo\Configuration\Form\Storage\DataClass\Instance;
use Chamilo\Configuration\Form\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

class Executer
{

    private $application;

    private $name;

    private $title;

    public function __construct($application, $name, $title = null)
    {
        $this->application = $application;
        $this->name = $name;
        $this->title = $title ?: Translation::get(
            (string) StringUtilities::getInstance()->createString($name)->upperCamelize(), 
            $application->context());
    }

    public function run()
    {
        $form = new ExecuteForm(
            $this->get_form(), 
            $this->application->get_url(), 
            $this->application->get_user(), 
            $this->title);
        
        if ($form->validate())
        {
            $success = $form->update_values();
            $this->application->redirectWithMessage(
                Translation::get($success ? 'DynamicFormExecuted' : 'DynamicFormNotExecuted'), !$success,
                []);
        }
        else
        {
            $html = [];
            
            $html[] = $this->application->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->application->render_footer();
            
            return implode(PHP_EOL, $html);
        }
    }

    public function get_form()
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance::class, Instance::PROPERTY_APPLICATION),
            new StaticConditionVariable($this->application->context()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance::class, Instance::PROPERTY_NAME),
            new StaticConditionVariable($this->name));
        $condition = new AndCondition($conditions);
        
        return DataManager::retrieve(Instance::class, new DataClassRetrieveParameters($condition));
    }
}
