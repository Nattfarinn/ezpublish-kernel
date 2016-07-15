<?php

/**
 * File containing the RequestListenerTest class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace eZ\Bundle\EzPublishRestBundle\Tests\EventListener;

use eZ\Publish\Core\REST\Server\View\AcceptHeaderVisitorDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use eZ\Bundle\EzPublishRestBundle\EventListener\RequestListener;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestListenerTest extends EventListenerTest
{
    const REST_ROUTE = '/api/ezp/v2/rest-route';
    const NON_REST_ROUTE = '/non-rest-route';

    public function provideExpectedSubscribedEventTypes()
    {
        return [
            [
                [KernelEvents::REQUEST],
            ],
        ];
    }

    public function testOnKernelRequestNotMasterRequest()
    {
        $request = $this->performFakeRequest(self::REST_ROUTE, HttpKernelInterface::SUB_REQUEST);

        self::assertTrue($request->attributes->get('is_rest_request'));
    }

    public function testOnKernelRequestNotRestRequest()
    {
        $request = $this->performFakeRequest(self::NON_REST_ROUTE);

        self::assertFalse($request->attributes->get('is_rest_request'));
    }

    public function testOnKernelRequestRestRequest()
    {
        $request = $this->performFakeRequest(self::REST_ROUTE);

        self::assertTrue($request->attributes->get('is_rest_request'));
    }

    public function testRestRequestVariations()
    {
        self::assertTrue(
            $this->performFakeRequest('/api/ezp/v2/true')->attributes->get('is_rest_request')
        );

        self::assertTrue(
            $this->performFakeRequest('/api/bundle-name/v2/true')->attributes->get('is_rest_request')
        );

        self::assertTrue(
            $this->performFakeRequest('/api/MyBundle12/v2/true')->attributes->get('is_rest_request')
        );

        self::assertTrue(
            $this->performFakeRequest('/api/ThisIs_Bundle123/v2/true')->attributes->get('is_rest_request')
        );

        self::assertTrue(
            $this->performFakeRequest('/api/my-bundle/v1/true')->attributes->get('is_rest_request')
        );

        self::assertTrue(
            $this->performFakeRequest('/api/my-bundle/v2/true')->attributes->get('is_rest_request')
        );

        self::assertTrue(
            $this->performFakeRequest('/api/my-bundle/v2.7/true')->attributes->get('is_rest_request')
        );

        self::assertTrue(
            $this->performFakeRequest('/api/my-bundle/v122.73/true')->attributes->get('is_rest_request')
        );
    }

    public function testNonRestRequestVariations()
    {
        self::assertFalse(
            $this->performFakeRequest('/ap/ezp/v2/false')->attributes->get('is_rest_request')
        );

        self::assertFalse(
            $this->performFakeRequest('/api/bundle name/v2/false')->attributes->get('is_rest_request')
        );

        self::assertFalse(
            $this->performFakeRequest('/api/My/Bundle/v2/false')->attributes->get('is_rest_request')
        );

        self::assertFalse(
            $this->performFakeRequest('/api//v2/false')->attributes->get('is_rest_request')
        );

        self::assertFalse(
            $this->performFakeRequest('/api/my-bundle/v/false')->attributes->get('is_rest_request')
        );

        self::assertFalse(
            $this->performFakeRequest('/api/my-bundle/v2-2/false')->attributes->get('is_rest_request')
        );

        self::assertFalse(
            $this->performFakeRequest('/api/my-bundle/v2 7/false')->attributes->get('is_rest_request')
        );

        self::assertFalse(
            $this->performFakeRequest('/api/my-bundle/v/7/false')->attributes->get('is_rest_request')
        );
    }

    /**
     * @return RequestListener
     */
    protected function getEventListener()
    {
        return new RequestListener(
            $this->getVisitorDispatcherMock()
        );
    }

    /**
     * @return AcceptHeaderVisitorDispatcher|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getVisitorDispatcherMock()
    {
        return $this->getMock(
            'eZ\Publish\Core\REST\Server\View\AcceptHeaderVisitorDispatcher'
        );
    }

    /**
     * @return Request
     */
    protected function performFakeRequest($uri, $type = HttpKernelInterface::MASTER_REQUEST)
    {
        $event = new GetResponseEvent(
            $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface'),
            Request::create($uri),
            $type
        );

        $this->getEventListener()->onKernelRequest($event);

        return $event->getRequest();
    }
}
