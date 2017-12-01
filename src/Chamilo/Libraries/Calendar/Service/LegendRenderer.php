<?php
namespace Chamilo\Libraries\Calendar\Service;

use Chamilo\Libraries\Calendar\CalendarSources;
use Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Format\NotificationMessage\NotificationMessage;
use Chamilo\Libraries\Format\NotificationMessage\NotificationMessageManager;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Symfony\Component\Translation\Translator;

/**
 *
 * @package Chamilo\Libraries\Calendar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LegendRenderer
{

    /**
     *
     * @var \Chamilo\Libraries\Calendar\CalendarSources
     */
    private $calendarSources;

    /**
     *
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     *
     * @var \Chamilo\Libraries\File\PathBuilder
     */
    private $pathBuilder;

    /**
     *
     * @var \Chamilo\Libraries\Format\Utilities\ResourceManager
     */
    private $resourceManager;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\CalendarSources $calendarSources
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Libraries\File\PathBuilder $pathBuilder
     * @param \Chamilo\Libraries\Format\Utilities\ResourceManager $resourceManager
     */
    public function __construct(CalendarSources $calendarSources, Translator $translator, PathBuilder $pathBuilder,
        ResourceManager $resourceManager)
    {
        $this->calendarSources = $calendarSources;
        $this->translator = $translator;
        $this->pathBuilder = $pathBuilder;
        $this->resourceManager = $resourceManager;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\CalendarSources
     */
    protected function getCalendarSources()
    {
        return $this->calendarSources;
    }

    /**
     *
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     *
     * @return \Chamilo\Libraries\File\PathBuilder
     */
    protected function getPathBuilder()
    {
        return $this->pathBuilder;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Utilities\ResourceManager
     */
    protected function getResourceManager()
    {
        return $this->resourceManager;
    }

    /**
     * Builds a color-based legend for the calendar to help users to see the origin of the the published events
     *
     * @param \Chamilo\Libraries\Calendar\Interfaces\CalendarRendererProviderInterface $dataProvider
     * @return string
     */
    public function render(CalendarRendererProviderInterface $dataProvider)
    {
        $result = [];

        if ($this->getCalendarSources()->hasSources())
        {
            $visibleSources = 0;

            $result[] = '<div class="panel panel-default table-calendar-legend">';
            $result[] = '<div class="panel-heading">';
            $result[] = '<h4 class="panel-title">' .
                 $this->getTranslator()->trans('Legend', [], 'Chamilo\Libraries\Calendar') . '</h4>';
            $result[] = '</div>';
            $result[] = '<ul class="list-group">';

            $sources = $this->getSources();

            sort($sources);

            foreach ($sources as $source)
            {
                $sourceClasses = $this->getCalendarSources()->getSourceClasses($source);

                if ($dataProvider->supportsVisibility())
                {
                    $isSourceVisible = $dataProvider->isSourceVisible($source);
                    $eventClasses = ! $isSourceVisible ? ' event-container-source-faded' : '';
                }
                else
                {

                    $eventClasses = '';
                }

                $result[] = '<li class="list-group-item">';
                $result[] = '<div class="event-source' . $eventClasses . '" data-source-key="' .
                     $this->addSource($source) . '" data-source="' . $source . '">';
                $result[] = '<span class="event-container ' . $sourceClasses . '"></span>';
                $result[] = $source;
                $result[] = '</div>';
                $result[] = '</li>';

                if ($dataProvider->supportsVisibility())
                {
                    if ($isSourceVisible)
                    {
                        $visibleSources ++;
                    }
                }
            }

            $result[] = '</ul>';
            $result[] = '</div>';

            if ($dataProvider->supportsVisibility())
            {
                $result[] = '<script type="text/javascript">';
                $result[] = 'var calendarVisibilityContext = ' . json_encode($dataProvider->getVisibilityContext()) . ';';
                $result[] = '</script>';

                $result[] = $this->getResourceManager()->get_resource_html(
                    $this->getPathBuilder()->getJavascriptPath(__NAMESPACE__, true) . 'Highlight.js');

                if ($visibleSources == 0)
                {
                    $notificationMessageManager = new NotificationMessageManager();
                    $notificationMessageManager->addMessage(
                        new NotificationMessage(
                            $this->getTranslator()->trans('AllEventSourcesHidden', [], 'Chamilo\Libraries\Calendar'),
                            NotificationMessage::TYPE_WARNING));
                }
            }
        }

        return implode(PHP_EOL, $result);
    }
}

