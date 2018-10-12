<?php

namespace Seo\Error\Middleware;

use Cake\Http\Exception\NotFoundException;
use Cake\Routing\Exception\MissingRouteException;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Redirector\Lib\Redirector;
use Redirector\Lib\Urls\FailedUrl;
use Redirector\Lib\Urls\RedirectUrl;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\Response\TextResponse;

/**
 * @author David Yell <neon1024@gmail.com>
 */
class ErrorHandlerMiddleware extends \Cake\Error\Middleware\ErrorHandlerMiddleware
{
    /**
     * Redirects mapping
     *
     * Array of redirects in the form of
     * [
     *     '/url/to/redirect' => [
     *         'target' => '/target/url',
     *         'code' => 302
     *     ]
     * ]
     *
     * @see SeoRedirector Tests for more examples
     * @var array
     */
    private $redirects = [];

    /**
     * Handle an exception and generate an error response
     *
     * @param Exception $exception The exception to handle.
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Psr\Http\Message\ResponseInterface $response The response.
     * @return \Psr\Http\Message\ResponseInterface A response
     * @throws Exception
     */
    public function handleException($exception, $request, $response): ResponseInterface
    {
        if ($exception instanceof NotFoundException || $exception instanceof MissingRouteException) {
            $redirector = new Redirector($this->redirects);
            $redirect = $redirector->find($request->getUri()->getPath());

            if ($redirect instanceof FailedUrl) {
                return new TextResponse($redirect->getText(), $redirect->getCode());
            }

            if ($redirect instanceof RedirectUrl) {
                return new RedirectResponse($redirect->getUrl(), $redirect->getCode());
            }
        }

        return parent::handleException($exception, $request, $response);
    }
}
