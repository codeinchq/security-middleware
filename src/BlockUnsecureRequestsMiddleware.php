<?php
//
// +---------------------------------------------------------------------+
// | CODE INC. SOURCE CODE                                               |
// +---------------------------------------------------------------------+
// | Copyright (c) 2017 - Code Inc. SAS - All Rights Reserved.           |
// | Visit https://www.codeinc.fr for more information about licensing.  |
// +---------------------------------------------------------------------+
// | NOTICE:  All information contained herein is, and remains the       |
// | property of Code Inc. SAS. The intellectual and technical concepts  |
// | contained herein are proprietary to Code Inc. SAS are protected by  |
// | trade secret or copyright law. Dissemination of this information or |
// | reproduction of this material  is strictly forbidden unless prior   |
// | written permission is obtained from Code Inc. SAS.                  |
// +---------------------------------------------------------------------+
//
// Author:   Joan Fabrégat <joan@codeinc.fr>
// Date:     27/04/2018
// Time:     17:54
// Project:  SecurityMiddleware
//
declare(strict_types=1);
namespace CodeInc\SecurityMiddleware;
use CodeInc\SecurityMiddleware\Assets\UnsecureResponse;
use CodeInc\SecurityMiddleware\Tests\BlockUnsecureRequestsMiddlewareTest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;


/**
 * Class BlockUnsecureRequestsMiddleware
 *
 * @see BlockUnsecureRequestsMiddlewareTest
 * @package CodeInc\SecurityMiddleware
 * @uses UnsecureResponse
 * @author Joan Fabrégat <joan@codeinc.fr>
 * @license MIT <https://github.com/CodeIncHQ/SecurityMiddleware/blob/master/LICENSE>
 * @link https://github.com/CodeIncHQ/SecurityMiddleware
 */
class BlockUnsecureRequestsMiddleware implements MiddlewareInterface
{
    /**
     * Object returned for unsecure requests.
     *
     * @var ResponseInterface
     */
    private $unsecureResponse;

    /**
     * BlockUnsecureRequestsMiddleware constructor.
     *
     * @param null|ResponseInterface $unsecureResponse
     */
    public function __construct(?ResponseInterface $unsecureResponse = null)
    {
        $this->unsecureResponse  = $unsecureResponse ?? new UnsecureResponse();
    }

    /**
     * Sets the object returned for unsecure requests.
     *
     * @param ResponseInterface $unsecureResponse
     */
    public function setUnsecureResponse(ResponseInterface $unsecureResponse):void
    {
        $this->unsecureResponse = $unsecureResponse;
    }

    /**
     * Returns the object returned for unsecure requests.
     *
     * @return ResponseInterface
     */
    public function getUnsecureResponse():ResponseInterface
    {
        return new $this->unsecureResponse;
    }

    /**
     * @inheritdoc
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler):ResponseInterface
    {
        // blocks HTTP requests
        if (!self::isRequestSecure($request)) {
            return $this->getUnsecureResponse();
        }

        return $handler->handle($request);
    }

    /**
     * Checks if a request is secure.
     *
     * @param ServerRequestInterface $request
     * @return bool
     */
    public static function isRequestSecure(ServerRequestInterface $request):bool
    {
        return $request->getUri()->getScheme() == 'https';
    }
}