<?php
namespace Chamilo\Libraries\Format\Structure\ActionBar;

use Chamilo\Libraries\Platform\Translation;
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
     * @var string
     */
    private $confirmation;

    /**
     *
     * @var string
     */
    private $target;

    /**
     *
     * @param string $label
     * @param string $imagePath
     * @param string $action
     * @param integer $display
     * @param string $confirmation
     * @param string $classes
     * @param string $target
     */
    public function __construct($label = null, $imagePath = null, $action = null, $display = self :: DISPLAY_ICON_AND_LABEL, $confirmation = false, $classes = null,
        $target = null)
    {
        parent :: __construct($label, $imagePath, $display, $classes);

        $this->setAction($action);
        $this->setConfirmation($confirmation);
        $this->setTarget($target);
    }

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
     * @return string
     */
    public function getConfirmation()
    {
        return $this->confirmation;
    }

    /**
     *
     * @param string $confirmation
     */
    public function setConfirmation($confirmation)
    {
        if ($confirmation === true)
        {
            $this->confirmation = Translation :: get('ConfirmChosenAction', null, Utilities :: COMMON_LIBRARIES);
        }
        else
        {
            $this->confirmation = $confirmation;
        }
    }

    /**
     *
     * @return boolean
     */
    public function needsConfirmation()
    {
        return $this->getConfirmation() !== false;
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
}
