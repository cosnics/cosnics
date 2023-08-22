<?php
namespace Chamilo\Configuration\Form;

use Chamilo\Configuration\Form\Service\FormService;
use Chamilo\Configuration\Form\Storage\DataClass\Element;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
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
    protected FormService $formService;

    public function __construct(FormService $formService)
    {
        $this->formService = $formService;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \TableException
     */
    public function render(string $context, string $name, string $userIdentifier, ?string $title = null): string
    {
        $values = $this->getFormValues($context, $name, $userIdentifier);

        if ($values->count() != 0)
        {
            $table = new HTML_Table(['class' => 'table table-striped table-bordered table-hover table-data']);

            $table->setHeaderContents(0, 0, $title);
            $table->setCellAttributes(0, 0, ['colspan' => 2, 'style' => 'text-align: center;']);

            $counter = 1;

            foreach ($values as $value)
            {
                $condition = new EqualityCondition(
                    new PropertyConditionVariable(Element::class, DataClass::PROPERTY_ID),
                    new StaticConditionVariable($value->get_dynamic_form_element_id())
                );

                $element = $this->getFormService()->retrieveDynamicFormElements($condition)->current();

                $table->setCellContents($counter, 0, $element->get_name());
                $table->setCellAttributes($counter, 0, ['style' => 'width: 150px;']);

                $table->setCellContents($counter, 1, $value->get_value());

                $counter ++;
            }

            return $table->toHtml();
        }

        return '';
    }

    public function getFormService(): FormService
    {
        return $this->formService;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Configuration\Form\Storage\DataClass\Value>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getFormValues(string $context, string $name, string $userIdentifier): ArrayCollection
    {
        $form = $this->getFormService()->retrieveInstanceForContextAndName($context, $name);

        if (!$form)
        {
            return new ArrayCollection([]);
        }

        return $this->getFormService()->retrieveDynamicFormElementValuesForFormIdentifierAndUserIdentifier(
            $form->getId(), $userIdentifier
        );
    }
}
