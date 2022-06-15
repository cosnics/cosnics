<?php
namespace Chamilo\Libraries\Format\Response;

/**
 *
 * @package Chamilo\Libraries\Format\Response
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Response extends \Symfony\Component\HttpFoundation\Response
{

    /**
     * @param string[] $headers
     */
    public function __construct(int $version, ?string $content = '', int $status = 200, array $headers = [])
    {
        $headers['Content-Type'] = 'text/html';
        $headers['X-Powered-By'] = 'Cosnics ' . $version;

        parent::__construct($content, $status, $headers);
    }
}