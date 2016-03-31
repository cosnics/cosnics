<?php
namespace Chamilo\Application\Survey\Favourite\Component;

use Chamilo\Application\Survey\Favourite\Manager;
use Chamilo\Application\Survey\Favourite\Storage\DataClass\PublicationUserFavourite;
use Chamilo\Application\Survey\Favourite\Table\Favourite\FavouriteTable;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Application\Survey\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class BrowserComponent extends Manager implements TableSupport
{

    public function run()
    {
        $table = new FavouriteTable($this);

        $html = array();

        $html[] = $this->render_header();
        $html[] = $table->as_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function get_table_condition($table_class_name)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                PublicationUserFavourite :: class_name(),
                PublicationUserFavourite :: PROPERTY_USER_ID),
            new StaticConditionVariable($this->get_user_id()));
    }
}