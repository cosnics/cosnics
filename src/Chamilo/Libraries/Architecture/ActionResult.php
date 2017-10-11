<?php
namespace Chamilo\Libraries\Architecture;

use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Libraries\Architecture\Exceptions
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ActionResult
{

    /**
     *
     * @var integer
     */
    private $totalActions;

    /**
     *
     * @var integer
     */
    private $failedActions;

    /**
     *
     * @var string
     */
    private $context;

    /**
     *
     * @var string
     */
    private $actionType;

    /**
     *
     * @var string
     */
    private $entityType;

    /**
     *
     * @param integer $totalActions
     * @param integer $failedActions
     * @param string $context
     * @param string $actionType
     * @param string $entityType
     */
    public function __construct($totalActions, $failedActions, $context, $actionType, $entityType)
    {
        $this->totalActions = $totalActions;
        $this->failedActions = $failedActions;
        $this->context = $context;
        $this->actionType = $actionType;
        $this->entityType = $entityType;

        if ($this->hasFailed())
        {
            throw new \Exception($this->getMessage());
        }
    }

    /**
     *
     * @return integer
     */
    public function getTotalActions()
    {
        return $this->totalActions;
    }

    /**
     *
     * @param integer $totalActions
     */
    public function setTotalActions($totalActions)
    {
        $this->totalActions = $totalActions;
    }

    /**
     *
     * @return integer
     */
    public function getFailedActions()
    {
        return $this->failedActions;
    }

    /**
     *
     * @param integer $failedActions
     */
    public function setFailedActions($failedActions)
    {
        $this->failedActions = $failedActions;
    }

    /**
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     *
     * @param string $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     *
     * @return string
     */
    public function getActionType()
    {
        return $this->actionType;
    }

    /**
     *
     * @param string $actionType
     */
    public function setActionType($actionType)
    {
        $this->actionType = $actionType;
    }

    /**
     *
     * @return string
     */
    public function getEntityType()
    {
        return $this->entityType;
    }

    /**
     *
     * @param string $entityType
     */
    public function setEntityType($entityType)
    {
        $this->entityType = $entityType;
    }

    /**
     *
     * @return string
     */
    public function getMessage()
    {
        $parameters = array();
        $parameters['ACTION'] = Translation::get('ActionResultAction' . $this->getActionType(), array(), $this->getContext());

        if ($this->isSingleAction())
        {
            $parameters['OBJECT'] = Translation::get(
                'ActionResultSingleEntity' . $this->getEntityType(),
                array(),
                $this->getContext());

            if ($this->hasFailed())
            {
                return Translation::get('ActionResultSingleFailureMessage', $parameters);
            }
            else
            {
                return Translation::get('ActionResultSingleSuccessMessage', $parameters);
            }
        }
        else
        {
            $parameters['OBJECT'] = Translation::get(
                'ActionResultMultipleEntity' . $this->getEntityType(),
                array(),
                $this->getContext());

            if ($this->hasSucceeded())
            {
                return Translation::get('ActionResultMultipleSuccessMessage', $parameters);
            }
            elseif ($this->hasFailedCompletely())
            {
                return Translation::get('ActionResultMultipleFailureMessage', $parameters);
            }
            else
            {
                return Translation::get('ActionResultSomeFailureMessage', $parameters);
            }
        }
    }

    /**
     *
     * @return boolean
     */
    public function hasFailed()
    {
        return $this->getFailedActions() > 0;
    }

    /**
     *
     * @return boolean
     */
    public function hasSucceeded()
    {
        return ! $this->hasFailed();
    }

    /**
     *
     * @return boolean
     */
    public function hasFailedCompletely()
    {
        return $this->hasFailed() && $this->getFailedActions() == $this->getTotalActions();
    }

    /**
     *
     * @return boolean
     */
    public function isSingleAction()
    {
        return $this->getTotalActions() == 1;
    }
}