<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Connector;

// use libraries\format\theme\Theme;
// use libraries\format\structure\ToolbarItem;
// use libraries\platform\translation\Translation;
// use core\repository\storage\data_class\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Application\Weblcms\Tool\Implementation\User\WeblcmsUserToolConnector;

/**
 * This connector allows to add links to the personal portfolios in the user table
 */
class PortfolioConnector implements WeblcmsUserToolConnector
{

    public function is_active()
    {
        return Application :: is_active('application\portfolio');
    }

    public function get_toolbar_items($user_id)
    {
        // $portfoliomanager = new \application\portfolio\Manager($user_id);
        // $has_portfolio = \application\portfolio\DataManager :: has_user_created_portfolio($user_id);
        // if ($has_portfolio)
        // {
        // $portfolio_url = $portfoliomanager->get_url(
        // array(
        // $portfoliomanager :: PARAM_CONTEXT => \application\portfolio\Manager :: context(),
        // $portfoliomanager :: PARAM_ACTION => $portfoliomanager :: ACTION_VIEW_PORTFOLIO,
        // $portfoliomanager :: PARAM_PORTFOLIO_OWNER_ID => $user_id));
        // $toolbar_item = new ToolbarItem(
        // Translation :: get('Portfolio'),
        // Theme :: getInstance()->getImagePath(ContentObject :: get_content_object_type_namespace('portfolio')) .
    // 'Logo/22.png',
        // $portfolio_url,
        // ToolbarItem :: DISPLAY_ICON);
        // }
        // else
        // {
        // $toolbar_item = new ToolbarItem(
        // Translation :: get('NoPortfolio'),
        // Theme :: getInstance()->getImagePath(ContentObject :: get_content_object_type_namespace('portfolio')) .
        // 'Logo/22_na.png',
        // null,
        // ToolbarItem :: DISPLAY_ICON);
        // }
        
        // return array($toolbar_item);
    }
}
?>