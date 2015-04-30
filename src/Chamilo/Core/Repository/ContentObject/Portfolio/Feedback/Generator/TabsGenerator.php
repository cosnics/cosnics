<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Feedback\Generator;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Core\Repository\ContentObject\Portfolio\Feedback\Storage\DataClass\AbstractNotification;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Core\Repository\ContentObject\Portfolio\Feedback\Manager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Application\Portfolio\Storage\DataClass\Notification;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Portfolio\Feedback\Generator
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class TabsGenerator
{

    /**
     *
     * @var \Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer
     */
    private $tabsRenderer;

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
     * @param \Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer $tabsRenderer
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param string[] $baseParameters
     * @param boolean $isAllowedToViewFeedback
     * @param boolean $hasNotification
     * @param integer $feedbackCount
     * @param boolean $isActive
     */
    public function __construct(DynamicTabsRenderer $tabsRenderer, Application $application, $baseParameters,
        $isAllowedToViewFeedback = false, $feedbackCount = 0, $hasNotification = false, $isActive = false)
    {
        $this->tabsRenderer = $tabsRenderer;
        $this->application = $application;
        $this->baseParameters = $baseParameters;
        $this->isAllowedToViewFeedback = $isAllowedToViewFeedback;
        $this->feedbackCount = $feedbackCount;
        $this->hasNotification = $hasNotification;
        $this->isActive = $isActive;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer
     */
    public function getTabsRenderer()
    {
        return $this->tabsRenderer;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer $tabsRenderer
     */
    public function setTabsRenderer($tabsRenderer)
    {
        $this->tabsRenderer = $tabsRenderer;
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

    public function run()
    {
        $application = $this->getApplication();
        $baseParameters = $this->getBaseParameters();

        $title = Translation :: get('FeedbackComponent', null, $application :: package());

        if ($this->hasFeedback())
        {
            $title .= ' [' . $this->getFeedbackCount() . ']';
        }

        $this->getTabsRenderer()->add_tab(
            new DynamicVisualTab(
                $application :: ACTION_FEEDBACK,
                $title,
                Theme :: getInstance()->getImagePath($application :: package(), 'Tab/Feedback'),
                $application->get_url($baseParameters),
                $this->getIsActive(),
                false,
                DynamicVisualTab :: POSITION_LEFT,
                DynamicVisualTab :: DISPLAY_BOTH_SELECTED));

        if ($this->isAllowedToViewFeedback())
        {
            if ($this->hasNotification())
            {

                $baseParameters[Manager :: PARAM_ACTION] = Manager :: ACTION_UNSUBSCRIBER;

                $this->getTabsRenderer()->add_tab(
                    new DynamicVisualTab(
                        Manager :: ACTION_UNSUBSCRIBER,
                        Translation :: get(
                            'StopReceivingNotifications',
                            null,
                            'Chamilo\Core\Repository\ContentObject\Portfolio\Feedback'),
                        Theme :: getInstance()->getImagePath(Manager :: package(), 'Tab/Unsubscribe'),
                        $application->get_url($baseParameters),
                        false,
                        false,
                        DynamicVisualTab :: POSITION_LEFT,
                        DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
            }
            else
            {
                $baseParameters = $this->getBaseParameters();
                $baseParameters[Manager :: PARAM_ACTION] = Manager :: ACTION_SUBSCRIBER;

                $this->getTabsRenderer()->add_tab(
                    new DynamicVisualTab(
                        Manager :: ACTION_SUBSCRIBER,
                        Translation :: get(
                            'ReceiveNotifications',
                            null,
                            'Chamilo\Core\Repository\ContentObject\Portfolio\Feedback'),
                        Theme :: getInstance()->getImagePath(Manager :: package(), 'Tab/Subscribe'),
                        $application->get_url($baseParameters),
                        false,
                        false,
                        DynamicVisualTab :: POSITION_LEFT,
                        DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
            }
        }
    }
}