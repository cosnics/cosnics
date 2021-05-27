<?php
namespace Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Repository\Publication\Domain\PublicationResult;
use Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Publication\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationResultsRenderer
{

    /**
     *
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @param \Chamilo\Core\Repository\Publication\Domain\PublicationResult[] $publicationResults
     *
     * @return string
     */
    public function renderPublicationResults(array $publicationResults)
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

    /**
     * @param integer $status
     * @param \Chamilo\Core\Repository\Publication\Domain\PublicationResult[] $statusPublicationResults
     *
     * @return string
     */
    public function renderStatusPublicationResults(int $status, array $statusPublicationResults)
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

    /**
     * @param \Chamilo\Core\Repository\Publication\Domain\PublicationResult $statusPublicationResult
     *
     * @return string
     */
    public function renderStatusPublicationResult(PublicationResult $statusPublicationResult)
    {
        $html = [];

        $html[] = '<li class="list-group-item">';
        $html[] = $this->renderStatusPublicationResultUrl($statusPublicationResult);
        $html[] = $statusPublicationResult->getMessage();
        $html[] = '</li >';

        return implode('', $html);
    }

    /**
     * @param \Chamilo\Core\Repository\Publication\Domain\PublicationResult $statusPublicationResult W*
     *
     * @return string
     */
    public function renderStatusPublicationResultUrl(PublicationResult $statusPublicationResult)
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
     * @param int $status
     *
     * @return string
     */
    public function getStatusGlyph(int $status)
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

    /**
     * @param integer $status
     *
     * @return string
     */
    public function getStatusType(int $status)
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

    /**
     * @param integer $status
     *
     * @return string
     */
    public function getStatusHeading(int $status)
    {
        $translator = $this->getTranslator();

        switch ($status)
        {
            case PublicationResult::STATUS_FAILURE:
                return $translator->trans('PublicationFailures', [], 'Chamilo\Core\Repository\Publication');
            case PublicationResult::STATUS_SUCCESS:
                return $translator->trans('PublicationSuccesses', [], 'Chamilo\Core\Repository\Publication');
        }
    }

    /**
     * @param \Chamilo\Core\Repository\Publication\Domain\PublicationResult[] $publicationResults
     *
     * @return \Chamilo\Core\Repository\Publication\Domain\PublicationResult[][]
     */
    public function groupPublicationResultsByType(array $publicationResults)
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
     * @return integer[]
     */
    protected function getStatusOrder()
    {
        return [PublicationResult::STATUS_FAILURE, PublicationResult::STATUS_SUCCESS];
    }
}