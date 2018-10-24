<?php
namespace Chamilo\Libraries\Storage\Query\Variable;

/**
 * A case condition variable that describes a case in a select query
 *
 * @package Chamilo\Libraries\Storage\Query\Variable
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CaseConditionVariable extends ConditionVariable
{

    /**
     * The case_elements name of the DataClass object
     *
     * @var \Chamilo\Libraries\Storage\Query\Variable\CaseElementConditionVariable[]
     */
    private $caseElements;

    /**
     * The alias of the case
     *
     * @var string
     */
    private $alias;

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\CaseElementConditionVariable[] $caseElements
     * @param string $alias
     */
    public function __construct($caseElements = array(), $alias = null)
    {
        $this->caseElements = $caseElements;
        $this->alias = $alias;
    }

    /**
     * Get the case_elements
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\CaseElementConditionVariable[]
     */
    public function get_case_elements()
    {
        return $this->caseElements;
    }

    /**
     * Set the case_elements
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\CaseElementConditionVariable[] $caseElements
     */
    public function set_case_elements($caseElements)
    {
        $this->caseElements = $caseElements;
    }

    /**
     * Adds a case element to the case elements
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\CaseElementConditionVariable $case_element
     */
    public function add(CaseElementConditionVariable $case_element)
    {
        $this->caseElements[] = $case_element;
    }

    /**
     * Get the alias
     *
     * @return string
     */
    public function get_alias()
    {
        return $this->alias;
    }

    /**
     * Set the alias
     *
     * @param string $alias
     */
    public function set_alias($alias)
    {
        $this->alias = $alias;
    }

    /**
     *
     * @see \Chamilo\Libraries\Storage\Query\ConditionPart::getHashParts()
     */
    public function getHashParts()
    {
        $hashParts = ConditionVariable::getHashParts();

        foreach ($this->get_case_elements() as $case_element)
        {
            $hashParts[] = $case_element->getHashParts();
        }

        sort($hashParts);

        $hashParts[] = $this->get_alias();

        return $hashParts;
    }
}
