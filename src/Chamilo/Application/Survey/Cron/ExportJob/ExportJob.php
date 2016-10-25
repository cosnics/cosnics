<?php

/**
 * This script will start a cronjob manager and launch it.
 */
use Chamilo\Application\Survey\Cron\ExportJob\ExportJobManager;

try
{
    require_once __DIR__ . '/../../../../../common/global.inc.php';
    
    echo '[EXPORT JOB STARTED] ' . date('c', time()) . "\n";
    
    ExportJobManager :: launch_job();
    
    echo '  [EXPORT JOB ENDED] ' . date('c', time()) . "\n";
}
catch (Exception $exception)
{
    echo '[EXPORT JOB FAILED] ' . date('c', time()) . "\n";
}
?>