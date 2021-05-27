<?php
namespace Chamilo\Libraries\Calendar\Renderer;

use Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\VisibilitySupport;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\NotificationMessage\NotificationMessage;
use Chamilo\Libraries\Format\NotificationMessage\NotificationMessageManager;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Legend
{

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Renderer\Legend[]
     */
    protected static $instance = [];

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface
     */
    private $dataProvider;

    /**
     *
     * @var string[]
     */
    private $sources;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface $dataProvider
     */
    public function __construct(CalendarRendererProviderInterface $dataProvider)
    {
        $this->dataProvider = $dataProvider;
        $this->sources = [];
    }

    /**
     * Builds a color-based legend for the calendar to help users to see the origin of the the published events
     *
     * @return string
     */
    public function render()
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

                if ($this->getDataProvider() instanceof VisibilitySupport)
                {
                    $isSourceVisible = $this->getDataProvider()->isSourceVisible($source);
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

            if ($this->getDataProvider() instanceof VisibilitySupport)
            {
                $result[] = '<script>';
                $result[] =
                    'var calendarVisibilityContext = ' . json_encode($this->getDataProvider()->getVisibilityContext()) .
                    ';';
                $result[] = '</script>';

                $result[] = ResourceManager::getInstance()->getResourceHtml(
                    Path::getInstance()->getJavascriptPath(__NAMESPACE__, true) . 'Highlight.js'
                );

                if ($visibleSources == 0)
                {
                    $notificationMessageManager = new NotificationMessageManager();
                    $notificationMessageManager->addMessage(
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
     *
     * @param string $source
     *
     * @return integer
     * @throws \Exception
     */
    public function addSource($source)
    {
        if (!in_array($source, $this->getSources()))
        {
            $this->sources[] = $source;
        }

        return $this->getSourceKey($source);
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface
     */
    public function getDataProvider()
    {
        return $this->dataProvider;
    }

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface $dataProvider
     */
    public function setDataProvider(CalendarRendererProviderInterface $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * @param \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface $dataProvider
     *
     * @return \Chamilo\Libraries\Calendar\Renderer\Legend
     */
    static public function getInstance(CalendarRendererProviderInterface $dataProvider)
    {
        $dataProviderType = get_class($dataProvider);

        if (is_null(static::$instance[$dataProviderType]))
        {
            self::$instance[$dataProviderType] = new static($dataProvider);
        }

        return static::$instance[$dataProviderType];
    }

    /**
     * Determine the classes for a specific source
     *
     * @param string $source
     * @param boolean $fade
     *
     * @return string
     * @throws \Exception
     */
    public function getSourceClasses($source = null, $fade = false)
    {
        $classes = 'event-container-source event-container-source-' . $this->addSource($source);

        if ($fade)
        {
            $classes .= ' event-container-source-faded';
        }

        return $classes;
    }

    /**
     *
     * @param string $source
     *
     * @return integer
     * @throws \Exception
     */
    public function getSourceKey($source)
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
     *
     * @return string[]
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     *
     * @param string[] $sources
     */
    public function setSources($sources)
    {
        $this->sources = $sources;
    }

    /**
     *
     * @return boolean
     */
    public function hasMultipleSources()
    {
        return count($this->getSources()) > 1;
    }

    /**
     *
     * @return boolean
     */
    public function hasSources()
    {
        return count($this->getSources()) > 0;
    }
}