<?php
namespace Chamilo\Libraries\Calendar\Service;

use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Service\Resource\ResourceManager;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertsManager;
use Exception;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LegendRenderer
{
    protected AlertsManager $notificationMessageManager;

    protected ResourceManager $resourceManager;

    /**
     * @var string[]
     */
    protected array $sources = [];

    protected Translator $translator;

    protected WebPathBuilder $webPathBuilder;

    public function __construct(
        AlertsManager $notificationMessageManager, Translator $translator, ResourceManager $resourceManager,
        WebPathBuilder $webPathBuilder
    )
    {
        $this->notificationMessageManager = $notificationMessageManager;
        $this->translator = $translator;
        $this->resourceManager = $resourceManager;
        $this->webPathBuilder = $webPathBuilder;
    }

    /**
     * Builds a colour-based legend for the calendar to help users to see the origin of the the published events
     *
     * @param \Chamilo\Libraries\Calendar\Architecture\Domain\Visibility[] $invisibleSources
     *
     * @throws \Exception
     */
    public function render(array $invisibleSources, ?string $invisibilityContext = null): string
    {
        $result = [];

        if ($this->hasSources()) {
            $visibleSources = 0;

            $result[] = '<div class="panel panel-default table-calendar-legend">';
            $result[] = '<div class="panel-heading">';
            $result[] =
                '<h4 class="panel-title">' . $this->translator->trans('Legend', [], 'Chamilo\Libraries\Calendar') .
                '</h4>';
            $result[] = '</div>';
            $result[] = '<ul class="list-group">';

            $sources = $this->getSources();

            sort($sources);

            foreach ($sources as $source) {
                $sourceClasses = $this->getSourceClasses($source);

                $isSourceVisible = $this->isSourceVisible($invisibleSources, $source);
                $eventClasses = !$isSourceVisible ? ' event-container-source-faded' : '';

                if ($isSourceVisible) {
                    $visibleSources ++;
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

            if ($invisibilityContext) {
                $result[] = '<script>';
                $result[] = 'var calendarVisibilityContext = ' . json_encode($invisibilityContext) . ';';
                $result[] = '</script>';

                $result[] = $this->resourceManager->getResourceHtml(
                    $this->webPathBuilder->getJavascriptPath() . 'Calendar/Highlight.js'
                );

                if ($visibleSources == 0) {
                    $this->notificationMessageManager->addAlert(
                        new Alert(
                            $this->translator->trans('AllEventSourcesHidden', [], 'Chamilo\Libraries\Calendar'),
                            AlertEnum::WARNING
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
        if (!in_array($source, $this->getSources())) {
            $this->sources[] = $source;
        }

        return $this->getSourceKey($source);
    }

    /**
     * @throws \Exception
     */
    public function getSourceClasses(?string $source = null, bool $fade = false): string
    {
        $classes = 'event-container-source event-container-source-' . $this->addSource($source);

        if ($fade) {
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

        if ($sourceKey === false) {
            throw new Exception($this->translator->trans('InvalidLegendSource', [], 'Chamilo\Libraries\Calendar'));
        }
        else {
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
    public function setSources(array $sources): static
    {
        $this->sources = $sources;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasSources(): bool
    {
        return count($this->getSources()) > 0;
    }

    public function isSourceVisible(array $invisibleSources, string $source): bool
    {
        return !array_key_exists($source, $invisibleSources);
    }
}