<?php
namespace Chamilo\Core\Home\Ajax\Component;

use Chamilo\Core\Home\Storage\DataClass\Column;
use Chamilo\Core\Home\Storage\DataClass\Tab;
use Chamilo\Core\Home\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @author Hans De Bisschop
 */
class TabAddComponent extends \Chamilo\Core\Home\Ajax\Manager
{
    const PROPERTY_HTML = 'html';
    const PROPERTY_TITLE = 'title';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array();
    }

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    public function run()
    {
        $userId = DataManager::determine_user_id();
        
        if ($userId === false)
        {
            JsonAjaxResult::not_allowed();
        }
        
        $tab = new Tab();
        $tab->setTitle(Translation::get('NewTab'));
        $tab->setUserId($userId);
        
        if (! $tab->create())
        {
            JsonAjaxResult::general_error(Translation::get('TabNotAdded'));
        }
        
        $column = new Column();
        $column->setParentId($tab->get_id());
        $column->setTitle(Translation::get('NewColumn'));
        $column->setWidth(12);
        $column->setUserId($userId);
        
        if (! $column->create())
        {
            JsonAjaxResult::general_error(Translation::get('TabColumnNotAdded'));
        }
        
        $content = array();
        
        $content[] = '<div class="row portal-tab show" data-element-id="' . $tab->get_id() . '">';
        $content[] = '<div class="col-xs-12 col-md-' . $column->get_id() . ' portal-column" data-tab-id="' .
             $tab->get_id() . '" data-element-id="' . $column->get_id() . '">';
        $content[] = '<div class="panel panel-warning portal-column-empty show">';
        $content[] = '<div class="panel-heading">';
        $content[] = '<div class="pull-right">';
        $content[] = '<a href="#" class="portal-action portal-action-column-delete hidden" data-column-id="21" title="' .
             Translation::get('Delete') . '">';
        $content[] = '<span class="glyphicon glyphicon-remove"></span></a>';
        $content[] = '</div>';
        $content[] = '<h3 class="panel-title">' . Translation::get('EmptyColumnTitle') . '</h3>';
        $content[] = '</div>';
        $content[] = '<div class="panel-body">';
        $content[] = Translation::get('EmptyColumnBody');
        $content[] = '</div>';
        $content[] = '</div>';
        $content[] = '</div>';
        $content[] = '</div>';
        
        $title = array();
        
        $title[] = '<li class="portal-nav-tab active" data-tab-id="' . $tab->getId() . '">';
        $title[] = '<a class="portal-action-tab-title" href="#">';
        $title[] = '<span class="portal-nav-tab-title">' . $tab->getTitle() . '</span>';
        $title[] = '<span class="glyphicon glyphicon-remove portal-action-tab-delete"></span>';
        $title[] = '</a>';
        $title[] = '</li>';
        
        $result = new JsonAjaxResult(200);
        $result->set_property(self::PROPERTY_HTML, implode(PHP_EOL, $content));
        $result->set_property(self::PROPERTY_TITLE, implode(PHP_EOL, $title));
        $result->display();
    }
}
