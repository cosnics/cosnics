<?php
namespace Chamilo\Core\Repository\Feedback\Generator;

use Chamilo\Core\Repository\Feedback\FeedbackNotificationSupport;
use Chamilo\Core\Repository\Feedback\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\TabsRenderer;
use Chamilo\Libraries\Format\Tabs\Link\LinkTab;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Portfolio\Feedback\Generator
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class TabsGenerator extends NavigationGenerator
{

    /**
     *
     * @var \Chamilo\Libraries\Format\Tabs\TabsRenderer
     */
    private $tabsRenderer;

    /**
     *
     * @param \Chamilo\Libraries\Format\Tabs\TabsRenderer $tabsRenderer
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param string[] $baseParameters
     * @param boolean $isAllowedToViewFeedback
     * @param boolean $hasNotification
     * @param integer $feedbackCount
     * @param boolean $isActive
     */
    public function __construct(
        TabsRenderer $tabsRenderer, Application $application, $baseParameters, $isAllowedToViewFeedback = false,
        $feedbackCount = 0, $hasNotification = false, $isActive = false
    )
    {
        parent::__construct(
            $application, $baseParameters, $isAllowedToViewFeedback, $feedbackCount, $hasNotification, $isActive
        );

        $this->tabsRenderer = $tabsRenderer;
    }

    public function run()
    {
        $application = $this->getApplication();
        $baseParameters = $this->getBaseParameters();

        $title = Translation::get('FeedbackComponent', null, $application::package());

        if ($this->hasFeedback())
        {
            $title .= ' [' . $this->getFeedbackCount() . ']';
        }

        $this->getTabsRenderer()->addTab(
            new LinkTab(
                $application::ACTION_FEEDBACK, $title, new FontAwesomeGlyph('comments', array('fa-lg'), null, 'fas'),
                $application->get_url($baseParameters), $this->getIsActive(), false, LinkTab::POSITION_LEFT,
                LinkTab::DISPLAY_BOTH_SELECTED
            )
        );

        if ($this instanceof FeedbackNotificationSupport)
        {
            if ($this->isAllowedToViewFeedback())
            {
                if ($this->hasNotification())
                {

                    $baseParameters[Manager::PARAM_ACTION] = Manager::ACTION_UNSUBSCRIBER;

                    $this->getTabsRenderer()->addTab(
                        new LinkTab(
                            Manager::ACTION_UNSUBSCRIBER, Translation::get(
                            'StopReceivingNotifications', null,
                            'Chamilo\Core\Repository\ContentObject\Portfolio\Feedback'
                        ), new FontAwesomeGlyph('times', array('fa-lg'), null, 'fas'),
                            $application->get_url($baseParameters), false, false, LinkTab::POSITION_LEFT,
                            LinkTab::DISPLAY_BOTH_SELECTED
                        )
                    );
                }
                else
                {
                    $baseParameters = $this->getBaseParameters();
                    $baseParameters[Manager::PARAM_ACTION] = Manager::ACTION_SUBSCRIBER;

                    $this->getTabsRenderer()->addTab(
                        new LinkTab(
                            Manager::ACTION_SUBSCRIBER, Translation::get(
                            'ReceiveNotifications', null, 'Chamilo\Core\Repository\ContentObject\Portfolio\Feedback'
                        ), new FontAwesomeGlyph('envelope', array('fa-lg'), null, 'fas'),
                            $application->get_url($baseParameters), false, false, LinkTab::POSITION_LEFT,
                            LinkTab::DISPLAY_BOTH_SELECTED
                        )
                    );
                }
            }
        }
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Tabs\TabsRenderer
     */
    public function getTabsRenderer()
    {
        return $this->tabsRenderer;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Tabs\TabsRenderer $tabsRenderer
     */
    public function setTabsRenderer($tabsRenderer)
    {
        $this->tabsRenderer = $tabsRenderer;
    }
}