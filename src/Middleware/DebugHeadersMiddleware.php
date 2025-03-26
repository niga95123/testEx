<?php

namespace App\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class DebugHeadersMiddleware
{
    // Вариант 1: Используем onKernelResponse
    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $response = $event->getResponse();
        $request = $event->getRequest();

        // Добавляем отладочные заголовки
        $response->headers->set('X-Debug-Time', microtime(true) - $request->server->get('REQUEST_TIME_FLOAT'));
        $response->headers->set('X-Debug-Memory', round(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB');
    }

    // ИЛИ Вариант 2: Используем __invoke
    /*
    public function __invoke(ResponseEvent $event): void
    {
        // Та же логика, что и выше
    }
    */
}