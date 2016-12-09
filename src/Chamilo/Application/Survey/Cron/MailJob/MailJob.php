<?php

/**
 * This script will start a cronjob manager and launch it.
 */
use Chamilo\Application\Survey\Cron\MailJob\MailJobManager;

try
{
    
    require_once __DIR__ . '/../../../../../common/global.inc.php';
    
    echo '[MAIL JOB STARTED] ' . date('c', time()) . "\n";
    
    MailJobManager::launch_job();
    
    echo '  [MAIL JOB ENDED] ' . date('c', time()) . "\n";
}
catch (Exception $exception)
{
    echo '[MAIL JOB FAILED] ' . date('c', time()) . "\n";
}
?>