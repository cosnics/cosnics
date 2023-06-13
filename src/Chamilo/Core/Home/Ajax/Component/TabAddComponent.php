<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Ajax\Manager;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Throwable;

/**
 * @package Chamilo\Core\Home\Ajax\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TabAddComponent extends Manager
{
    public const PROPERTY_HTML = 'html';
    public const PROPERTY_TITLE = 'title';

    public function run()
    {
        try
        {
            $translator = $this->getTranslator();
            $homepageUserId = $this->getHomeService()->determineUserId(
                $this->getUser(), $this->getSession()->get(\Chamilo\Core\Home\Manager::SESSION_GENERAL_MODE, false)
            );

            $tab = new Element();
            $tab->setType(Element::TYPE_TAB);
            $tab->setTitle($translator->trans('NewTab', [], Manager::CONTEXT));
            $tab->setUserId($homepageUserId);

            if (!$this->getHomeService()->createElement($tab))
            {
                JsonAjaxResult::general_error($translator->trans('TabNotAdded', [], Manager::CONTEXT));
            }

            $column = new Element();
            $tab->setType(Element::TYPE_COLUMN);
            $column->setParentId($tab->getId());
            $column->setTitle($translator->trans('NewColumn', [], Manager::CONTEXT));
            $column->setWidth(12);
            $column->setUserId($homepageUserId);

            if (!$this->getHomeService()->createElement($column))
            {
                JsonAjaxResult::general_error($translator->trans('TabColumnNotAdded', [], Manager::CONTEXT));
            }

            $content = [];

            $content[] = '<div class="row portal-tab show" data-element-id="' . $tab->getId() . '">';
            $content[] =
                '<div class="col-xs-12 col-md-' . $column->getId() . ' portal-column" data-tab-id="' . $tab->getId() .
                '" data-element-id="' . $column->getId() . '">';
            $content[] = '<div class="panel panel-warning portal-column-empty show">';
            $content[] = '<div class="panel-heading">';
            $content[] = '<div class="pull-right">';
            $content[] =
                '<a href="#" class="portal-action portal-action-column-delete hidden" data-column-id="21" title="' .
                $translator->trans('Delete', [], Manager::CONTEXT) . '">';

            $glyph = new FontAwesomeGlyph('times', [], null, 'fas');

            $content[] = $glyph->render() . '</a>';
            $content[] = '</div>';
            $content[] =
                '<h3 class="panel-title">' . $translator->trans('EmptyColumnTitle', [], Manager::CONTEXT) . '</h3>';
            $content[] = '</div>';
            $content[] = '<div class="panel-body">';
            $content[] = $translator->trans('EmptyColumnBody', [], Manager::CONTEXT);
            $content[] = '</div>';
            $content[] = '</div>';
            $content[] = '</div>';
            $content[] = '</div>';

            $title = [];

            $title[] = '<li class="portal-nav-tab active" data-tab-id="' . $tab->getId() . '">';
            $title[] = '<a class="portal-action-tab-title" href="#">';
            $title[] = '<span class="portal-nav-tab-title">' . $tab->getTitle() . '</span>';

            $glyph = new FontAwesomeGlyph(
                'times', ['portal-action-tab-delete'], null, 'fas'
            );

            $title[] = $glyph->render();
            $title[] = '</a>';
            $title[] = '</li>';

            $result = new JsonAjaxResult(200);
            $result->set_property(self::PROPERTY_HTML, implode(PHP_EOL, $content));
            $result->set_property(self::PROPERTY_TITLE, implode(PHP_EOL, $title));
            $result->display();
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

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */

    public function getRequiredPostParameters(array $postParameters = []): array
    {
        return [];
    }
}
