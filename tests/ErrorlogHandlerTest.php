<?php
// @codingStandardsIgnoreFile

namespace Vperyod\ErrorlogHandler;

use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

use Psr\Log\LogLevel;

use Exception;

class ErrorlogHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected $log;

    protected $handler;

    protected $message;

    protected $exception;

    public function setup()
    {
        parent::setup();
        $this->log = $this->getMock('Psr\Log\LoggerInterface');
        $this->handler = new ErrorlogHandler($this->log);
        $this->message = 'error';
        $this->exception = new Exception($this->message);
    }

    public function testNoException()
    {
        $handler = $this->handler;
        $response = new Response();
        $result = $handler(
            ServerRequestFactory::fromGlobals(),
            $response,
            [$this, 'noError']
        );

        $this->assertSame($response, $result);
    }

    public function testExceptionThrow()
    {
        $this->setExpectedException(
            get_class($this->exception),
            $this->message
        );

        $req = ServerRequestFactory::fromGlobals();

        $this->log->expects($this->once())
            ->method('log')
            ->with(
                LogLevel::ALERT,
                $this->exception,
                ['request' => $req]
            );

        $handler = $this->handler;
        $response = new Response();

        $handler(
            $req,
            $response,
            [$this, 'error']
        );
    }

    public function testCustomLevel()
    {
        $custom = 'foo';

        $this->setExpectedException(
            get_class($this->exception),
            $this->message
        );

        $req = ServerRequestFactory::fromGlobals();

        $this->log->expects($this->once())
            ->method('log')
            ->with(
                $custom,
                $this->exception,
                ['request' => $req]
            );

        $handler = $this->handler;
        $response = new Response();

        $this->assertSame(
            $handler,
            $handler->setLogLevel($custom)
        );

        $handler(
            $req,
            $response,
            [$this, 'error']
        );
    }

    public function testNoThrow()
    {
        $req = ServerRequestFactory::fromGlobals();

        $this->log->expects($this->once())
            ->method('log')
            ->with(
                LogLevel::ALERT,
                $this->exception,
                ['request' => $req]
            );

        $handler = $this->handler;
        $handler->setReThrow(false);
        $response = $this->getMock(Response::class);
        $body = $this->getMock('Psr\Http\Message\StreamInterface');

        $response->expects($this->once())
            ->method('withStatus')
            ->with(500)
            ->will($this->returnValue($response));

        $response->expects($this->once())
            ->method('withHeader')
            ->with('Content-Type', 'text/plain; charset=utf-8')
            ->will($this->returnValue($response));

        $response->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue($body));

        $body->expects($this->once())
            ->method('write')
            ->with(
                get_class($this->exception)
                . ': '
                . $this->message
            );

        $handler(
            $req,
            $response,
            [$this, 'error']
        );

    }


    public function noError($request, $response)
    {
        $request;
        return $response;
    }

    public function error()
    {
        throw $this->exception;
    }
}
