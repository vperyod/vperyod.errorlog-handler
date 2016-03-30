<?php
/**
 * Errorlog Handler
 *
 * PHP version 5
 *
 * Copyright (C) 2016 Jake Johns
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 *
 * @category  Middleware
 * @package   Vperyod\ErrorlogHandler
 * @author    Jake Johns <jake@jakejohns.net>
 * @copyright 2016 Jake Johns
 * @license   http://jnj.mit-license.org/2016 MIT License
 * @link      https://github.com/vperyod/vperyod.errorlog-handler
 */

namespace Vperyod\ErrorlogHandler;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Psr\Log\LoggerInterface as Logger;
use Psr\Log\LogLevel as Level;

use Exception;

/**
 * ErrorlogHandler
 *
 * @category Middleware
 * @package  Vperyod\ErrorlogHandler
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  http://jnj.mit-license.org/2016 MIT License
 * @link     https://github.com/vperyod/vperyod.errorlog-handler
 */
class ErrorlogHandler
{

    /**
     * Log
     *
     * @var mixed
     *
     * @access protected
     */
    protected $log;

    /**
     * Rethrow exception?
     *
     * @var bool
     *
     * @access protected
     */
    protected $rethrow = true;

    /**
     * Level
     *
     * @var string
     *
     * @access protected
     */
    protected $level = Level::ALERT;

    /**
     * __construct
     *
     * @param Logger $logger DESCRIPTION
     *
     * @return mixed
     *
     * @access public
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Set Log Level
     *
     * @param mixed $level DESCRIPTION
     *
     * @return mixed
     *
     * @access public
     */
    public function setLogLevel($level)
    {
        $this->level = $level;
        return $this;
    }

    /**
     * Set Rethrow
     *
     * @param mixed $bool DESCRIPTION
     *
     * @return mixed
     * @access public
     */
    public function setRethrow($bool)
    {
        $this->rethrow = (bool) $bool;
        return $this;
    }

    /**
     * Log an exception if thrown
     *
     * @param Request  $request  PSR7 HTTP Request
     * @param Response $response PSR7 HTTP Response
     * @param callable $next     Next callable middleware
     *
     * @return Response
     *
     * @access public
     *
     * @throws Exception throws caught exception if rethrow is true
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        try {
            return $next($request, $response);
        } catch (Exception $error) {
            $this->log($error, $request);
            if ($this->rethrow) {
                throw $error;
            }

            return $this->formatResponse($error, $response);
        }
    }

    /**
     * Log an exception
     *
     * @param Exception $exception DESCRIPTION
     * @param Request   $request   DESCRIPTION
     *
     * @return void
     *
     * @access protected
     */
    protected function log(Exception $exception, Request $request)
    {
        $this->logger->log(
            $this->level,
            $exception,
            ['request' => $request]
        );
    }

    /**
     * FormatResponse
     *
     * @param Exception $exception DESCRIPTION
     * @param Response  $response  DESCRIPTION
     *
     * @return mixed
     *
     * @access protected
     */
    protected function formatResponse(Exception $exception, Response $response)
    {
        $response = $response->withStatus(500)
            ->withHeader('Content-Type', 'text/plain; charset=utf-8');
        $response->getBody()->write(
            get_class($exception)
            . ': '
            . $exception->getMessage()
        );
        return $response;
    }
}
