<?php

namespace Sfynx\DddBundle\Layer\Infrastructure\EventListener;

use Sfynx\DddBundle\Layer\Domain\Service\Generalisation\Manager\ManagerInterface;
use Sfynx\DddBundle\Layer\Infrastructure\Security\Connection\Multitenant;
use Sfynx\DddBundle\Layer\Infrastructure\Exception\InfrastructureException;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Doctrine\DBAL\Connection;

class HandlerDynamicOrmDatabase
{
    protected $sDatabaseType;
    protected $bUseDb;
    protected $oManager;
    protected $sTenantCacheDir;
    protected $sDefaultTenantFilePath;
    protected $oConnection;

    public function __construct(ManagerInterface $oManager, $sDatabaseType, $bUseDb, $sTenantCacheDir, $sDefaultTenantFilePath, Connection $oConnection)
    {
        $this->oManager = $oManager;
        $this->sDatabaseType = $sDatabaseType;
        $this->bUseDb = $bUseDb;
        $this->sTenantCacheDir = $sTenantCacheDir;
        $this->sDefaultTenantFilePath = $sDefaultTenantFilePath;
        $this->oConnection = $oConnection;
    }

    /**
     * @throws \Exception
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (('orm' !== $this->database_type)
            || (HttpKernel::MASTER_REQUEST != $event->getRequestType())
        ) {
            return;
        }

        // we get the dbname of the tenant
        $params = $this->connection->getParams();
        $params['dbname'] = Multitenant::getDbName($this->oManager, $this->bUseDb, $this->sTenantCacheDir, $this->sDefaultTenantFilePath);

        if (null === $params['dbname']) {
            return;
        }

        // we disconnect
        if ($this->connection->isConnected()) {
            $this->connection->close();
        }

        // Set up the parameters for the parent
        $this->connection->__construct(
            $params,
            $this->connection->getDriver(),
            $this->connection->getConfiguration(),
            $this->connection->getEventManager()
        );

        try {
            $this->connection->connect();
        } catch (\Exception $e) {
            throw InfrastructureException::NoTenantDatabaseConnection(Multitenant::getTenantId());
        }
    }
}
