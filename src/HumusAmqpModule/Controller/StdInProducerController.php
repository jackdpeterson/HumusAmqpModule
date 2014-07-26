<?php

namespace HumusAmqpModule\Controller;

use Zend\Console\ColorInterface;
use Zend\Mvc\Controller\AbstractConsoleController;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;

class StdInProducerController extends AbstractConsoleController
{
    /**
     * {@inheritdoc}
     */
    public function dispatch(RequestInterface $request, ResponseInterface $response = null)
    {
        parent::dispatch($request, $response);
        /* @var $request \Zend\Console\Request */
        $producerName = $request->getParam('name');

        if (!$this->getServiceLocator()->has($producerName)) {
            return $this->getConsole()->writeLine(
                'ERROR: Producer "' . $producerName . '" not found',
                ColorInterface::RED
            );
        }

        $debug = $request->getParam('debug') || $request->getParam('d');

        if ($debug && !defined('AMQP_DEBUG')) {
            define('AMQP_DEBUG', true);
        }

        $producer = $this->getServiceLocator()->get($producerName);

        $route = $request->getParam('route', '');
        $msg = trim($request->getParam('msg'));

        $producer->publish($msg, $route);
    }
}
