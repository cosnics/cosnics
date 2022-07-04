<?php
namespace Chamilo\Libraries\Calendar\Service;

use Chamilo\Libraries\Calendar\Architecture\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Architecture\Interfaces\VisibilitySupport;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\NotificationMessage\NotificationMessage;
use Chamilo\Libraries\Format\NotificationMessage\NotificationMessageManager;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 * @package Chamilo\Libraries\Calendar\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LegendRenderer
{

    private NotificationMessageManager $notificationMessageManager;

    /**
     * @var string[]
     */
    private array $sources = [];

    public function __construct(NotificationMessageManager $notificationMessageManager)
    {
        $this->notificationMessageManager = $notificationMessageManager;
    }

    /**
     * Builds a color-based legend for the calendar to help users to see the origin of the the published events
     * @throws \Exception
     */
    public function render(CalendarRendererProviderInterface $dataProvider): string
    {
        $result = [];

        if ($this->hasSources())
        {
            $visibleSources = 0;

            $result[] = '<div class="panel panel-default table-calendar-legend">';
            $result[] = '<div class="panel-heading">';
            $result[] = '<h4 class="panel-title">' . Translation::get('Legend') . '</h4>';
            $result[] = '</div>';
            $result[] = '<ul class="list-group">';

            $sources = $this->getSources();

            sort($sources);

            foreach ($sources as $source)
            {
                $sourceClasses = $this->getSourceClasses($source);

                if ($dataProvider instanceof VisibilitySupport)
                {
                    $isSourceVisible = $dataProvider->isSourceVisible($source);
                    $eventClasses = !$isSourceVisible ? ' event-container-source-faded' : '';

                    if ($isSourceVisible)
                    {
                        $visibleSources ++;
                    }
                }
                else
                {

                    $eventClasses = '';
                }

                $result[] = '<li class="list-group-item">';
                $result[] =
                    '<div class="event-source' . $eventClasses . '" data-source-key="' . $this->addSource($source) .
                    '" data-source="' . $source . '">';
                $result[] = '<span class="event-container ' . $sourceClasses . '"></span>';
                $result[] = $source;
                $result[] = '</div>';
                $result[] = '</li>';
            }

            $result[] = '</ul>';
            $result[] = '</div>';

            if ($dataProvider instanceof VisibilitySupport)
            {
                $result[] = '<script>';
                $result[] =
                    'var calendarVisibilityContext = ' . json_encode($dataProvider->getVisibilityContext()) . ';';
                $result[] = '</script>';

                $result[] = ResourceManager::getInstance()->getResourceHtml(
                    Path::getInstance()->getJavascriptPath('Chamilo\Libraries\Calendar', true) . 'Highlight.js'
                );

                if ($visibleSources == 0)
                {
                    $this->getNotificationMessageManager()->addMessage(
                        new NotificationMessage(
                            Translation::get('AllEventSourcesHidden'), NotificationMessage::TYPE_WARNING
                        )
                    );
                }
            }
        }

        return implode(PHP_EOL, $result);
    }

    /**
     * @throws \Exception
     */
    public function addSource(string $source): int
    {
        if (!in_array($source, $this->getSources()))
        {
            $this->sources[] = $source;
        }

        return $this->getSourceKey($source);
    }

    public function getNotificationMessageManager(): NotificationMessageManager
    {
        return $this->notificationMessageManager;
    }

    /**
     * @param \Chamilo\Libraries\Format\NotificationMessage\NotificationMessageManager $notificationMessageManager
     *
     * @return LegendRenderer
     */
    public function setNotificationMessageManager(NotificationMessageManager $notificationMessageManager
    ): LegendRenderer
    {
        $this->notificationMessageManager = $notificationMessageManager;

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function getSourceClasses(?string $source = null, bool $fade = false): string
    {
        $classes = 'event-container-source event-container-source-' . $this->addSource($source);

        if ($fade)
        {
            $classes .= ' event-container-source-faded';
        }

        return $classes;
    }

    /**
     * @throws \Exception
     */
    public function getSourceKey(string $source): int
    {
        $sourceKey = array_search($source, $this->getSources());

        if ($sourceKey === false)
        {
            throw new Exception(Translation::get('InvalidLegendSource'));
        }
        else
        {
            return $sourceKey;
        }
    }

    /**
     * @return string[]
     */
    public function getSources(): array
    {
        return $this->sources;
    }

    /**
     * @param string[] $sources
     */
    public function setSources(array $sources)
    {
        $this->sources = $sources;
    }

    /**
     * @return bool
     */
    public function hasMultipleSources(): bool
    {
        return count($this->getSources()) > 1;
    }

    /**
     * @return bool
     */
    public function hasSources(): bool
    {
        return count($this->getSources()) > 0;
    }
}