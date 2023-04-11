<?php
namespace Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Repository\Publication\Domain\PublicationResult;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Publication\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationResultsRenderer
{

    private Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function getStatusGlyph(int $status): string
    {
        switch ($status)
        {
            case PublicationResult::STATUS_FAILURE:
                $type = 'times-circle';
                $titleVariable = 'PublicationFailed';
                break;
            case PublicationResult::STATUS_SUCCESS:
                $type = 'arrow-circle-right';
                $titleVariable = 'ViewPublication';
                break;
            default:
                $type = 'exclamation-triangle';
                $titleVariable = 'ViewPublication';
                break;
        }

        $glyph = new FontAwesomeGlyph(
            $type, ['fa-lg'], $this->getTranslator()->trans($titleVariable, [], 'Chamilo\Core\Repository\Publication')
        );

        return $glyph->render();
    }

    public function getStatusHeading(int $status): string
    {
        $translator = $this->getTranslator();

        switch ($status)
        {
            case PublicationResult::STATUS_FAILURE:
                return $translator->trans('PublicationFailures', [], 'Chamilo\Core\Repository\Publication');
            case PublicationResult::STATUS_SUCCESS:
                return $translator->trans('PublicationSuccesses', [], 'Chamilo\Core\Repository\Publication');
        }

        return '';
    }

    /**
     * @return int[]
     */
    protected function getStatusOrder(): array
    {
        return [PublicationResult::STATUS_FAILURE, PublicationResult::STATUS_SUCCESS];
    }

    public function getStatusType(int $status): string
    {
        switch ($status)
        {
            case PublicationResult::STATUS_FAILURE:
                return 'danger';
            case PublicationResult::STATUS_SUCCESS:
                return 'success';
            default:
                return 'warning';
        }
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param \Chamilo\Core\Repository\Publication\Domain\PublicationResult[] $publicationResults
     *
     * @return \Chamilo\Core\Repository\Publication\Domain\PublicationResult[][]
     */
    public function groupPublicationResultsByType(array $publicationResults): array
    {
        $groupedPublicationResults = [];

        foreach ($publicationResults as $publicationResult)
        {
            $status = $publicationResult->getStatus();

            if (!array_key_exists($status, $groupedPublicationResults))
            {
                $groupedPublicationResults[$status] = [];
            }

            $groupedPublicationResults[$status][] = $publicationResult;
        }

        return $groupedPublicationResults;
    }

    /**
     * @param \Chamilo\Core\Repository\Publication\Domain\PublicationResult[] $publicationResults
     *
     * @return string
     */
    public function renderPublicationResults(array $publicationResults): string
    {
        $groupedPublicationResults = $this->groupPublicationResultsByType($publicationResults);

        $html = [];

        $informationMessage =
            $this->getTranslator()->trans('PublishInformationMessage', [], 'Chamilo\Core\Repository\Publication');
        $html[] = '<div class="alert alert-info">' . $informationMessage . '</div>';

        foreach ($this->getStatusOrder() as $status)
        {
            if (array_key_exists($status, $groupedPublicationResults))
            {
                $html[] = $this->renderStatusPublicationResults($status, $groupedPublicationResults[$status]);
            }
        }

        return implode(PHP_EOL, $html);
    }

    public function renderStatusPublicationResult(PublicationResult $statusPublicationResult): string
    {
        $html = [];

        $html[] = '<li class="list-group-item">';
        $html[] = $this->renderStatusPublicationResultUrl($statusPublicationResult);
        $html[] = $statusPublicationResult->getMessage();
        $html[] = '</li >';

        return implode('', $html);
    }

    public function renderStatusPublicationResultUrl(PublicationResult $statusPublicationResult): string
    {
        $status = $statusPublicationResult->getStatus();
        $class = 'text-' . $this->getStatusType($status);

        if ($statusPublicationResult->getUrl())
        {
            return '<a class="' . $class . '" href="' . $statusPublicationResult->getUrl() .
                '" style="margin-right:15px;">' . $this->getStatusGlyph($status) . '</a>';
        }
        else
        {
            return '<a class="' . $class . '" style="margin-right:15px;">' . $this->getStatusGlyph($status) . '</a>';
        }
    }

    /**
     * @param \Chamilo\Core\Repository\Publication\Domain\PublicationResult[] $statusPublicationResults
     */
    public function renderStatusPublicationResults(int $status, array $statusPublicationResults): string
    {
        $html = [];

        $html[] = '<div class="panel panel-' . $this->getStatusType($status) . '">';
        $html[] = '<div class="panel-heading">' . $this->getStatusHeading($status) . '</div>';

        $html[] = '<ul class="list-group">';

        foreach ($statusPublicationResults as $statusPublicationResult)
        {
            $html[] = $this->renderStatusPublicationResult($statusPublicationResult);
        }

        $html[] = '</ul>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }
}