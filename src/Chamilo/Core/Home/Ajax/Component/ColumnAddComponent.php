<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Ajax\Manager;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Throwable;

/**
 * @package Chamilo\Core\Home\Ajax\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ColumnAddComponent extends Manager
{
    public const PARAM_TAB = 'tab';
    public const PROPERTY_HTML = 'html';
    public const PROPERTY_WIDTH = 'width';

    public function run()
    {
        try
        {
            $translator = $this->getTranslator();

            $isGeneralMode = $this->getSession()->get(\Chamilo\Core\Home\Manager::SESSION_GENERAL_MODE, false);
            $homepageUser = $this->getHomeService()->determineUser(
                $this->getUser(), $isGeneralMode
            );

            $homepageUserId = $this->getHomeService()->determineUserId($this->getUser(), $isGeneralMode);

            $tabId = $this->getPostDataValue(self::PARAM_TAB);

            if (isset($tabId))
            {
                $columns = $this->getColumns($tabId, $homepageUser);

                if ($columns->count() >= 12)
                {
                    JsonAjaxResult::general_error($translator->trans('TooManyColumns', [], Manager::CONTEXT));
                }

                try
                {
                    $newColumnWidth = $this->determineNewColumnWidth($columns);
                }
                catch (Exception)
                {
                    $newColumnWidth = 1;
                    $newWidths = $this->recalculateColumnWidths($columns);
                }

                // Create the new column + a dummy block for it
                $newColumn = new Element();
                $newColumn->setType(Element::TYPE_COLUMN);
                $newColumn->setParentId($tabId);
                $newColumn->setTitle($translator->trans('NewColumn', [], Manager::CONTEXT));
                $newColumn->setWidth($newColumnWidth);
                $newColumn->setUserId($homepageUserId);

                if (!$this->getHomeService()->createElement($newColumn))
                {
                    JsonAjaxResult::general_error($translator->trans('ColumnNotAdded', [], Manager::CONTEXT));
                }

                // Render the actual html to be displayed
                $html[] = '<div class="col-xs-12 col-md-' . $newColumn->getWidth() . ' portal-column" data-tab-id="' .
                    $tabId . '" data-element-id="' . $newColumn->getId() . '">';

                $html[] = '<div class="panel panel-warning portal-column-empty show">';
                $html[] = '<div class="panel-heading">';
                $html[] = '<div class="pull-right">';

                $glyph = new FontAwesomeGlyph('times');

                $html[] = '<a href="#" class="portal-action portal-action-column-delete show" data-column-id="' .
                    $newColumn->getId() . ' title="' . $translator->trans('Delete', [], StringUtilities::LIBRARIES) .
                    '">';
                $html[] = $glyph->render() . '</a>';

                $html[] = '</div>';
                $html[] = '<h3 class="panel-title">' .
                    $translator->trans('EmptyColumnTitle', [], \Chamilo\Core\Home\Manager::CONTEXT) . '</h3>';
                $html[] = '</div>';
                $html[] = '<div class="panel-body">';
                $html[] = $translator->trans('EmptyColumnBody', [], \Chamilo\Core\Home\Manager::CONTEXT);
                $html[] = '</div>';
                $html[] = '</div>';

                $html[] = '</div>';

                $result = new JsonAjaxResult(200);
                $result->set_property(self::PROPERTY_HTML, implode(PHP_EOL, $html));

                if (isset($newWidths))
                {
                    $result->set_property(self::PROPERTY_WIDTH, $newWidths);
                }

                $result->display();
            }
            else
            {
                JsonAjaxResult::bad_request();
            }
        }
        catch (NotAllowedException $exception)
        {
            JsonAjaxResult::not_allowed($exception->getMessage());
        }
        catch (Throwable $throwable)
        {
            JsonAjaxResult::error(500, $throwable->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function determineNewColumnWidth(ArrayCollection $columns): int
    {
        $widthTotal = $this->getCurrentTotalWidth($columns);

        if ($widthTotal < 12)
        {
            return 12 - $widthTotal;
        }
        else
        {
            throw new Exception($this->getTranslator()->trans('ColumnsTooWide', [], Manager::CONTEXT));
        }
    }

    /**
     * @param string $tabId
     * @param ?\Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Home\Storage\DataClass\Element>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getColumns(string $tabId, ?User $user = null): ArrayCollection
    {
        return $this->getHomeService()->findElementsByTypeUserAndParentIdentifier(
            Element::TYPE_COLUMN, $user, $tabId
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getCurrentTotalWidth(ArrayCollection $columns): int
    {
        $widthTotal = 0;

        foreach ($columns as $column)
        {
            $widthTotal += $column->getWidth();
        }

        return $widthTotal;
    }

    public function getRequiredPostParameters(array $postParameters = []): array
    {
        $postParameters[] = self::PARAM_TAB;

        return parent::getRequiredPostParameters($postParameters);
    }

    public function orderColumnsByWidth($widthLeft, $widthRight): int
    {
        if ($widthLeft < $widthRight)
        {
            return - 1;
        }
        elseif ($widthLeft > $widthRight)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    /**
     * @return int[]
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function recalculateColumnWidths(ArrayCollection $columns): array
    {
        $currentTotal = $this->getCurrentTotalWidth($columns);
        $newWidths = [];

        foreach ($columns as $column)
        {
            $newWidths[$column->getId()] = $column->getWidth();
        }

        while ($currentTotal > 11)
        {
            arsort($newWidths);

            foreach ($newWidths as $columnId => $newWidth)
            {
                $newWidths[$columnId] = $newWidth - 1;
                $currentTotal --;

                break;
            }
        }

        foreach ($columns as $column)
        {
            $column->setWidth($newWidths[$column->getId()]);
            $column->update();
        }

        return $newWidths;
    }
}
