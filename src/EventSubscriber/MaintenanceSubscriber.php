<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class MaintenanceSubscriber implements EventSubscriberInterface
{

    public function __construct(
        private bool $maintenanceActive,
    ) {}

    public function onKernelResponse(ResponseEvent $event): void
    {

        if($this->maintenanceActive)
        {
            $content = $event->getResponse()->getContent();
    
            $newContent = str_replace("<body>", "<body><div class=\"alert alert-danger\">Maintenance prévue mardi 10 janvier à 17h00</div>", $content);
    
            $event->getResponse()->setContent($newContent);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}
