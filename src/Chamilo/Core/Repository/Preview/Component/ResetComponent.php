<?php
namespace Chamilo\Core\Repository\Preview\Component;

use Chamilo\Core\Repository\Preview\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 *
 * @package core\repository\preview
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ResetComponent extends Manager
{

    /**
     * Executes this controller
     */
    public function run()
    {
        if (! $this->get_content_object()->is_complex_content_object())
        {
            throw new NoObjectSelectedException(Translation::get('ContentObject'));
        }

        $context = ClassnameUtilities::getInstance()->getNamespaceParent($this->get_content_object()->context(), 2) .
             '\Display\Preview';
        $preview = $this->getApplicationFactory()->getApplication(
            $context,
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));

        if ($preview->supports_reset())
        {
            try
            {
                if ($preview->reset())
                {
                    $message = Translation::get('PreviewReset');
                    $is_error = false;
                }
                else
                {
                    $message = Translation::get('PreviewNotReset');
                    $is_error = true;
                }
            }
            catch (Exception $exception)
            {
                $message = $exception->getMessage();
                $is_error = true;
            }
        }
        else
        {
            $message = Translation::get('PreviewResetNotSupported');
            $is_error = true;
        }

        $parameters = [];
        $parameters[self::PARAM_ACTION] = self::ACTION_DISPLAY;

        $this->redirectWithMessage($message, $is_error, $parameters);
    }

    /**
     *
     * @return \core\repository\ContentObject
     */
    public function get_root_content_object()
    {
        return $this->get_content_object();
    }
}
