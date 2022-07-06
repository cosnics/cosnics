<?php
namespace Chamilo\Libraries\Calendar\Service;

use Chamilo\Libraries\Calendar\Architecture\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Architecture\Interfaces\VisibilitySupport;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Format\NotificationMessage\NotificationMessage;
use Chamilo\Libraries\Format\NotificationMessage\NotificationMessageManager;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Exception;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LegendRenderer
{

    protected NotificationMessageManager $notificationMessageManager;

    protected PathBuilder $pathBuilder;

    protected ResourceManager $resourceManager;

    /**
     * @var string[]
     */
    protected array $sources = [];

    protected Translator $translator;

    public function __construct(
        NotificationMessageManager $notificationMessageManager, Translator $translator,
        ResourceManager $resourceManager, PathBuilder $pathBuilder
    )
    {
        $this->notificationMessageManager = $notificationMessageManager;
        $this->translator = $translator;
        $this->resourceManager = $resourceManager;
        $this->pathBuilder = $pathBuilder;
    }

    /**
     * Builds a color-based legend for the calendar to help users to see the origin of the the published events
     * @throws \Exception
     */
    public function render(CalendarRendererProviderInterface $dataProvider): string
    {
        $translator = $this->getTranslator();

        $result = [];

        if ($this->hasSources())
        {
            $visibleSources = 0;

            $result[] = '<div class="panel panel-default table-calendar-legend">';
            $result[] = '<div class="panel-heading">';
            $result[] =
                '<h4 class="panel-title">' . $translator->trans('Legend', [], 'Chamilo\Libraries\Calendar') . '</h4>';
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

                $result[] = $this->getResourceManager()->getResourceHtml(
                    $this->getPathBuilder()->getJavascriptPath('Chamilo\Libraries\Calendar', true) . 'Highlight.js'
                );

                if ($visibleSources == 0)
                {
                    $this->getNotificationMessageManager()->addMessage(
                        new NotificationMessage(
                            $translator->trans('AllEventSourcesHidden', [], 'Chamilo\Libraries\Calendar'),
                            NotificationMessage::TYPE_WARNING
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

    public function getPathBuilder(): PathBuilder
    {
        return $this->pathBuilder;
    }

    public function getResourceManager(): ResourceManager
    {
        return $this->resourceManager;
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
            throw new Exception($this->getTranslator()->trans('InvalidLegendSource', [], 'Chamilo\Libraries\Calendar'));
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

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @return bool
     */
    public function hasSources(): bool
    {
        return count($this->getSources()) > 0;
    }
}