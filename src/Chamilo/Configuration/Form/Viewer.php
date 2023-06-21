<?php
namespace Chamilo\Configuration\Form;

use Chamilo\Configuration\Form\Storage\DataClass\Element;
use Chamilo\Configuration\Form\Storage\DataClass\Instance;
use Chamilo\Configuration\Form\Storage\DataClass\Value;
use Chamilo\Configuration\Form\Storage\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;
use HTML_Table;

/**
 * @package Chamilo\Configuration\Form
 * @author  Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Viewer
{

    private string $context;

    private string $name;

    private ?string $title;

    private string $user_id;

    public function __construct(string $context, string $name, string $user_id, ?string $title = null)
    {
        $this->context = $context;
        $this->name = $name;
        $this->user_id = $user_id;
        $this->title = $title;
    }

    public function render()
    {
        $values = $this->get_form_values();

        if ($values->count() != 0)
        {

            $table = new HTML_Table(['class' => 'table table-striped table-bordered table-hover table-data']);
            $table->setHeaderContents(0, 0, $this->title);
            $table->setCellAttributes(0, 0, ['colspan' => 2, 'style' => 'text-align: center;']);

            $counter = 1;

            foreach ($values as $value)
            {
                $condition = new EqualityCondition(
                    new PropertyConditionVariable(Element::class, Element::PROPERTY_ID),
                    new StaticConditionVariable($value->get_dynamic_form_element_id())
                );
                $element = DataManager::retrieve_dynamic_form_elements($condition)->current();

                $table->setCellContents($counter, 0, $element->get_name());
                $table->setCellAttributes($counter, 0, ['style' => 'width: 150px;']);

                $table->setCellContents($counter, 1, $value->get_value());

                $counter ++;
            }

            return $table->toHtml();
        }

        return '';
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Configuration\Form\Storage\DataClass\Value>
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     */
    public function get_form_values()
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance::class, Instance::PROPERTY_APPLICATION),
            new StaticConditionVariable($this->context)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Instance::class, Instance::PROPERTY_NAME),
            new StaticConditionVariable($this->name)
        );
        $condition = new AndCondition($conditions);

        $form = DataManager::retrieve(Instance::class, new DataClassRetrieveParameters($condition));

        if (!$form)
        {
            return new ArrayCollection([]);
        }

        $subcondition = new EqualityCondition(
            new PropertyConditionVariable(Element::class, Element::PROPERTY_DYNAMIC_FORM_ID),
            new StaticConditionVariable($form->get_id())
        );

        $conditions = [];
        $conditions[] = new SubselectCondition(
            new PropertyConditionVariable(Value::class, Value::PROPERTY_DYNAMIC_FORM_ELEMENT_ID),
            new PropertyConditionVariable(Element::class, Element::PROPERTY_ID), $subcondition
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Value::class, Value::PROPERTY_USER_ID),
            new StaticConditionVariable($this->user_id)
        );
        $condition = new AndCondition($conditions);

        return DataManager::retrieves(Value::class, new DataClassRetrievesParameters($condition));
    }
}
