<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Format\Structure\ActionBar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
trait ActionButtonTrait
{

    /**
     *
     * @var string
     */
    private $action;

    /**
     *
     * @var boolean
     */
    private $confirmation;

    /**
     *
     * @var string
     */
    private $target;

    /**
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     *
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     *
     * @return boolean|string
     */
    public function getConfirmation()
    {
        return $this->confirmation;
    }

    /**
     *
     * @param boolean|string $confirmation
     */
    public function setConfirmation($confirmation)
    {
        if ($confirmation === true)
        {
            $this->confirmation = Translation::get('ConfirmChosenAction', null, Utilities::COMMON_LIBRARIES);
        }
        else
        {
            $this->confirmation = $confirmation;
        }
    }

    /**
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     *
     * @param string $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * Initialize method as replacement for constructor due to PHP issue
     * https://bugs.php.net/bug.php?id=65576
     * TODO: fix this once everyone moves to PHP 5.6
     *
     * @param string $action
     * @param boolean|string $confirmation
     * @param string $target
     */
    public function initialize($action = null, $confirmation = null, $target = null)
    {
        $this->setAction($action);
        $this->setConfirmation($confirmation);
        $this->setTarget($target);
    }

    /**
     *
     * @return boolean
     */
    public function needsConfirmation()
    {
        return $this->getConfirmation() !== false;
    }
}
