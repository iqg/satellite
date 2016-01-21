<?php

/*
 * This file is part of the FOSRestBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DWD\ApiBundle\EventListener;
 
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This listener handles ensures that for specific formats AccessDeniedExceptions
 * will return a 403 regardless of how the firewall is configured
 *
 * @author Lukas Kahwe Smith <smith@pooteeweet.org>
 */
class RequestListener
{
    
    protected $container;

    public function __construct(ContainerInterface $container ) // this is @service_container
    {
        $this->container = $container;
    }

    /**
     * @param GetResponseEvent $reqEvent
     */
    public function logRequest(GetResponseEvent $reqEvent)
    {   
        $this->container->get('DWD_Logger')->addRequestLog($reqEvent->getRequest());
    }

}