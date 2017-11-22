<?php
namespace Chamilo\Libraries\Platform\Session;

use Chamilo\Libraries\Storage\Cache\DataClassCache;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Libraries\Platform\Session
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class SessionHandler implements \SessionHandlerInterface
{

    /**
     *
     * @var string
     */
    private $savePath;

    /**
     *
     * @var string
     */
    private $name;

    /**
     *
     * @var integer
     */
    private $lifetime = 43200;

    public function __construct()
    {
        // see warning for php < 5.4: http://www.php.net/manual/en/function.session-set-save-handler.php
        register_shutdown_function('session_write_close');
    }

    /**
     *
     * @param string $save_path
     * @param string $name
     *
     * @return boolean
     */
    public function open($save_path, $name)
    {
        $this->savePath = $save_path;
        $this->name = $name;

        return true;
    }

    /**
     *
     * @return boolean
     */
    public function close()
    {
        return true;
    }

    /**
     *
     * @param string $session_id
     * @return boolean|string
     */
    public function read($session_id)
    {
        DataClassCache::truncate(\Chamilo\Core\User\Storage\DataClass\Session::class_name());
        $session = \Chamilo\Core\User\Storage\DataManager::retrieve(
            \Chamilo\Core\User\Storage\DataClass\Session::class_name(),
            new DataClassRetrieveParameters($this->getCondition($session_id)));

        if ($session instanceof \Chamilo\Core\User\Storage\DataClass\Session)
        {
            if ($session->is_valid())
            {
                return base64_decode($session->get_data());
            }
            else
            {
                $this->destroy($session_id);
            }
        }

        return '';
    }

    /**
     *
     * @param string $session_id
     * @param string $data
     * @return boolean
     */
    public function write($session_id, $data)
    {
        $data = base64_encode($data);

        DataClassCache::truncate(\Chamilo\Core\User\Storage\DataClass\Session::class_name());

        $session = \Chamilo\Core\User\Storage\DataManager::retrieve(
            \Chamilo\Core\User\Storage\DataClass\Session::class_name(),
            new DataClassRetrieveParameters($this->getCondition($session_id)));

        if ($session instanceof \Chamilo\Core\User\Storage\DataClass\Session)
        {
            $session->set_data($data);
            $session->set_modified(time());

            return $session->update();
        }
        else
        {
            $session = new \Chamilo\Core\User\Storage\DataClass\Session();
            $session->set_modified(time());
            $session->set_lifetime($this->lifetime);
            $session->set_data($data);
            $session->set_name($this->name);
            $session->set_save_path($this->savePath);
            $session->set_session_id($session_id);

            return $session->create();
        }
    }

    /**
     *
     * @param string $session_id
     * @return boolean
     */
    public function destroy($session_id)
    {
        return \Chamilo\Core\User\Storage\DataManager::deletes(
            \Chamilo\Core\User\Storage\DataClass\Session::class_name(),
            $this->getCondition($session_id));
    }

    /**
     *
     * @return boolean
     */
    public function garbage()
    {
        $border = time() - $this->lifetime;
        $condition = new ComparisonCondition(
            new PropertyConditionVariable(
                \Chamilo\Core\User\Storage\DataClass\Session::class_name(),
                \Chamilo\Core\User\Storage\DataClass\Session::PROPERTY_MODIFIED),
            ComparisonCondition::LESS_THAN,
            new StaticConditionVariable($border));

        return \Chamilo\Core\User\Storage\DataManager::deletes(
            \Chamilo\Core\User\Storage\DataClass\Session::class_name(),
            $condition);
    }

    /**
     *
     * @param integer $sessionId
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    public function getCondition($sessionId)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Core\User\Storage\DataClass\Session::class_name(),
                \Chamilo\Core\User\Storage\DataClass\Session::PROPERTY_SESSION_ID),
            new StaticConditionVariable($sessionId));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Core\User\Storage\DataClass\Session::class_name(),
                \Chamilo\Core\User\Storage\DataClass\Session::PROPERTY_NAME),
            new StaticConditionVariable($this->name));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                \Chamilo\Core\User\Storage\DataClass\Session::class_name(),
                \Chamilo\Core\User\Storage\DataClass\Session::PROPERTY_SAVE_PATH),
            new StaticConditionVariable($this->savePath));

        return new AndCondition($conditions);
    }

    /**
     *
     * @see SessionHandlerInterface::gc()
     */
    public function gc($maxlifetime)
    {
        $this->garbage();
    }
}
