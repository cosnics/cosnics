<?php
namespace Chamilo\Core\Repository\Form;

use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\UserView\Storage\DataClass\UserView;
use Chamilo\Core\Repository\UserView\Storage\DataClass\UserViewRelContentObject;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: repository_filter_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.forms
 */
class RepositoryFilterForm extends FormValidator
{
    const FILTER_TYPE = 'filter_type';

    private $manager;

    private $renderer;

    /**
     * Creates a new search form
     *
     * @param RepositoryManager $manager The repository manager in which this search form will be displayed
     * @param string $url The location to which the search request should be posted.
     */
    public function __construct($manager, $url)
    {
        parent :: __construct('repository_filter_form', 'post', $url);

        $this->renderer = clone $this->defaultRenderer();
        $this->manager = $manager;

        $this->build_form();

        $this->accept($this->renderer);
    }

    /**
     * Build the simple search form.
     */
    private function build_form()
    {
        $this->renderer->setFormTemplate(
            '<form {attributes}><div class="filter_form">{content}</div><div class="clear">&nbsp;</div></form>');
        $this->renderer->setElementTemplate('<div class="row"><div class="formw">{label}&nbsp;{element}</div></div>');

        $select = $this->addElement('select', self :: FILTER_TYPE, null, array(), array('class' => 'postback'));

        $disabled_counter = 0;

        $select->addOption(Translation :: get('AllContentObjects'), 'disabled_' . $disabled_counter);
        $disabled_counter ++;

        $condition = new EqualityCondition(
            new PropertyConditionVariable(UserView :: class_name(), UserView :: PROPERTY_USER_ID),
            new StaticConditionVariable($this->manager->get_user_id()));
        $parameters = new DataClassRetrievesParameters($condition);
        $userviews = DataManager :: retrieves(UserView :: class_name(), $parameters);

        if ($userviews->size() > 0)
        {
            $select->addOption('--------------------------', 'disabled_' . $disabled_counter, array('disabled'));
            $disabled_counter ++;

            while ($userview = $userviews->next_result())
            {
                $select->addOption(
                    Translation :: get('View', null, Utilities :: COMMON_LIBRARIES) . ': ' . $userview->get_name(),
                    $userview->get_id());
            }
        }

        $select->addOption('--------------------------', 'disabled_' . $disabled_counter, array('disabled'));
        $disabled_counter ++;

        $type_selector = TypeSelector :: populate($this->get_allowed_content_object_types());
        $types = $type_selector->as_tree();
        unset($types[0]);

        foreach ($types as $key => $type)
        {
            if (is_integer($key))
            {
                $select->addOption($type, 'disabled_' . $disabled_counter, array('disabled'));
                $key = 'disabled_' . $disabled_counter;
                $disabled_counter ++;
            }
            else
            {

                $select->addOption($type, $key);
            }
        }

        $this->addElement(
            'style_submit_button',
            'submit',
            Translation :: get('Filter', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'normal filter'));

        $session_filter = Session :: retrieve('filter');
        $this->setDefaults(array(self :: FILTER_TYPE => $session_filter, 'published' => 1));

        $this->addElement(
            'html',
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath('Chamilo\Configuration', true) . 'Postback.js'));
    }

    public function get_filter_conditions()
    {
        $session_filter = Session :: retrieve('filter');
        if ($this->validate() || isset($session_filter))
        {
            $values = $this->exportValues();
            $filter = $values[self :: FILTER_TYPE];
            if (substr($filter, 0, 9) == 'disabled_')
                $filter = 0;

            if ($this->validate())
            {
                Session :: register('filter', $filter);
            }

            $filter_type = ! is_null($filter) ? $filter : $session_filter;

            if (is_numeric($filter_type))
            {
                if ($filter_type != '0')
                {

                    $parameters = new DataClassRetrievesParameters(
                        new EqualityCondition(
                            new PropertyConditionVariable(
                                UserViewRelContentObject :: class_name(),
                                UserViewRelContentObject :: PROPERTY_USER_VIEW_ID),
                            new StaticConditionVariable($filter_type)));
                    $content_objects = DataManager :: retrieves(UserViewRelContentObject :: class_name(), $parameters);
                    while ($lo = $content_objects->next_result())
                    {
                        if ($lo->get_visibility())
                        {
                            $visible_lo[] = $lo->get_content_object_type();
                        }
                    }

                    $condition = new InCondition(
                        new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_TYPE),
                        $visible_lo,
                        ContentObject :: get_table_name());
                }
                else
                {
                    $condition = null;
                }
            }
            else
            {
                $condition = new EqualityCondition(
                    new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_TYPE),
                    new StaticConditionVariable($filter_type));
            }

            return $condition;
        }
    }

    /**
     * Display the form
     */
    public function display()
    {
        $html = array();
        $html[] = '<div style="text-align: right;">';
        $html[] = $this->renderer->toHTML();
        $html[] = '</div>';
        return implode('', $html);
    }

    public function get_allowed_content_object_types()
    {
        return DataManager :: get_registered_types();
    }
}
