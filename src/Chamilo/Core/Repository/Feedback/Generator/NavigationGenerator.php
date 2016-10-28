<?php
namespace Chamilo\Core\Repository\Feedback\Generator;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Core\Repository\Feedback\Generator
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
abstract class NavigationGenerator
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
    private $baseParameters;

    /**
     *
     * @var boolean
     */
    private $isAllowedToViewFeedback;

    /**
     *
     * @var integer
     */
    private $feedbackCount;

    /**
     *
     * @var boolean
     */
    private $hasNotification;

    /**
     *
     * @var boolean
     */
    private $isActive;

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param string[] $baseParameters
     * @param boolean $isAllowedToViewFeedback
     * @param boolean $hasNotification
     * @param integer $feedbackCount
     * @param boolean $isActive
     */
    public function __construct(Application $application, $baseParameters, $isAllowedToViewFeedback = false,
        $feedbackCount = 0, $hasNotification = false, $isActive = false)
    {
        $this->application = $application;
        $this->baseParameters = $baseParameters;
        $this->isAllowedToViewFeedback = $isAllowedToViewFeedback;
        $this->feedbackCount = $feedbackCount;
        $this->hasNotification = $hasNotification;
        $this->isActive = $isActive;
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
    public function getBaseParameters()
    {
        return $this->baseParameters;
    }

    /**
     *
     * @param string[] $baseParameters
     */
    public function setBaseParameters($baseParameters)
    {
        $this->baseParameters = $baseParameters;
    }

    /**
     *
     * @return boolean
     */
    public function getIsAllowedToViewFeedback()
    {
        return $this->isAllowedToViewFeedback;
    }

    /**
     *
     * @param boolean $isAllowedToViewFeedback
     */
    public function setIsAllowedToViewFeedback($isAllowedToViewFeedback)
    {
        $this->isAllowedToViewFeedback = $isAllowedToViewFeedback;
    }

    /**
     * Helper method
     *
     * @return boolean
     */
    public function isAllowedToViewFeedback()
    {
        return $this->getIsAllowedToViewFeedback();
    }

    /**
     *
     * @return integer
     */
    public function getFeedbackCount()
    {
        return $this->feedbackCount;
    }

    /**
     *
     * @param integer $feedbackCount
     */
    public function setFeedbackCount($feedbackCount)
    {
        $this->feedbackCount = $feedbackCount;
    }

    /**
     * Helper method
     *
     * @return boolean
     */
    public function hasFeedback()
    {
        return $this->getFeedbackCount() > 0;
    }

    /**
     *
     * @return boolean
     */
    public function getHasNotification()
    {
        return $this->hasNotification;
    }

    /**
     *
     * @param boolean $hasNotification
     */
    public function setNotification($hasNotification)
    {
        $this->hasNotification = $hasNotification;
    }

    /**
     * Helper method
     *
     * @return boolean
     */
    public function hasNotification()
    {
        return $this->getHasNotification();
    }

    /**
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     *
     * @param boolean $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * Helper method
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->getIsActive();
    }

    abstract public function run();
}