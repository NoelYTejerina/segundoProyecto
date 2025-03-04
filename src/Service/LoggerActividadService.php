<?php
namespace App\Service;

use Psr\Log\LoggerInterface;

class LoggerActividadService
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $actividadUsuarioLogger)
    {
        $this->logger = $actividadUsuarioLogger;
    }

    public function log(string $usuario, string $accion): void
    {
        $this->logger->info("{$usuario} - {$accion}");
    }
}
