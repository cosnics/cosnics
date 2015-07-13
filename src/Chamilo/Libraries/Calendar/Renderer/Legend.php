<?php
namespace Chamilo\Libraries\Calendar\Renderer;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\NotificationMessage;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface;

class Legend
{

    /**
     *
     * @var \Chamilo\Libraries\Calendar\Renderer\Legend
     */
    protected static $instance = array();

    /**
     *
     * @var CalendarRendererProviderInterface
     */
    private $dataProvider;

    /**
     *
     * @var string[]
     */
    private $sources;

    /**
     * Return 'this' as singleton
     *
     * @return \Chamilo\Libraries\Calendar\Renderer\Legend
     */
    static public function getInstance(CalendarRendererProviderInterface $dataProvider)
    {
        $dataProviderType = get_class($dataProvider);

        if (is_null(static :: $instance[$dataProviderType]))
        {
            self :: $instance[$dataProviderType] = new static($dataProvider);
        }

        return static :: $instance[$dataProviderType];
    }

    /**
     *
     * @param CalendarRendererProviderInterface $dataProvider
     */
    public function __construct(CalendarRendererProviderInterface $dataProvider)
    {
        $this->dataProvider = $dataProvider;
        $this->sources = array();
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
     * @return boolean
     */
    public function hasSources()
    {
        return count($this->getSources()) > 0;
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
     * @param string $source
     */
    public function addSource($source)
    {
        if (! in_array($source, $this->getSources()))
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
     *
     * @param string $source
     * @throws \Exception
     * @return integer
     */
    public function getSourceKey($source)
    {
        $sourceKey = array_search($source, $this->getSources());

        if ($sourceKey === false)
        {
            throw new \Exception(Translation :: get('InvalidLegendSource'));
        }
        else
        {
            return $sourceKey;
        }
    }

    /**
     * Determine the classes for a specific source
     *
     * @param string $key
     * @param boolean $fade
     * @return string
     */
    public function getSourceClasses($source = null, $fade = false)
    {
        $classes = 'event-source-identifier event-source-identifier-' . $this->addSource($source);

        if ($fade)
        {
            $classes .= ' event-source-identifier-faded';
        }

        return $classes;
    }

    /**
     * Builds a color-based legend for the calendar to help users to see the origin of the the published events
     *
     * @return string
     */
    public function render()
    {
        $result = array();

        if ($this->hasSources())
        {
            $visibleSources = 0;

            $result[] = '<fieldset class="event-legend-container" name="test">';
            $result[] = '<legend class="event-legend-label">' . Translation :: get('Legend') . '</legend>';
            $result[] = '<div class="event-legend">';

            foreach ($this->getSources() as $source)
            {
                $isSourceVisible = $this->getDataProvider()->isSourceVisible($source);

                $result[] = '<div class="event">';
                $result[] = '<div data-source="' . $source . '" class="' .
                     $this->getSourceClasses($source, ! $isSourceVisible) . '">';

                $result[] = $source;

                $result[] = '</div>';
                $result[] = '</div>';

                if ($isSourceVisible)
                {
                    $visibleSources ++;
                }
            }

            $result[] = '</div>';
            $result[] = '<div class="clear"><</div>';
            $result[] = '</fieldset>';

            if ($this->getDataProvider()->supportsVisibility())
            {
                $result[] = '<script type="text/javascript">';
                $result[] = 'var calendarVisibilityContext = ' .
                     json_encode($this->getDataProvider()->getVisibilityContext()) . ';';
                $result[] = 'var calendarVisibilityData = ' . json_encode($this->getDataProvider()->getVisibilityData()) .
                     ';';
                $result[] = '</script>';

                $result[] = ResourceManager :: get_instance()->get_resource_html(
                    Path :: getInstance()->getJavascriptPath(__NAMESPACE__, true) . 'Highlight.js');

                if ($visibleSources == 0)
                {
                    $messages = Session :: retrieve(Application :: PARAM_MESSAGES);
                    $messages[Application :: PARAM_MESSAGE_TYPE][] = NotificationMessage :: TYPE_WARNING;
                    $messages[Application :: PARAM_MESSAGE][] = Translation :: get('AllEventSourcesHidden');

                    Session :: register(Application :: PARAM_MESSAGES, $messages);
                }
            }
        }

        return implode(PHP_EOL, $result);
    }
}