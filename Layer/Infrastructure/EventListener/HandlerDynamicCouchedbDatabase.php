<?php

namespace Sfynx\DddBundle\Layer\Infrastructure\EventListener;

use Sfynx\DddBundle\Layer\Domain\Service\Generalisation\Manager\ManagerInterface;
use Sfynx\DddBundle\Layer\Infrastructure\Security\Connection\Multitenant;
use Sfynx\DddBundle\Layer\Infrastructure\Exception\InfrastructureException;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;

class HandlerDynamicCouchedbDatabase
{
    protected $sDatabaseType;
    protected $bUseDb;
    protected $oManager;
    protected $sTenantCacheDir;
    protected $sDefaultTenantFilePath;
    protected $oConnection;

    public function __construct(ManagerInterface $oManager, $sDatabaseType, $bUseDb, $sTenantCacheDir, $sDefaultTenantFilePath, $oConnection)
    {
        $this->oManager = $oManager;
        $this->sDatabaseType = $sDatabaseType;
        $this->bUseDb = $bUseDb;
        $this->sTenantCacheDir = $sTenantCacheDir;
        $this->sDefaultTenantFilePath = $sDefaultTenantFilePath;
        $this->oConnection = $oConnection;
    }

    /**
     *
     * @throws \Exception
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (('couchdb' !== $this->database_type)
            || (HttpKernel::MASTER_REQUEST != $event->getRequestType())
        ) {
            return;
        }

        // we get the dbname of the tenant
        $params['dbname'] = Multitenant::getDbName($this->database_multitenant_path_file);

        if (null === $params['dbname']) {
            return;
        }

        try{
            try {
                $this->connection->createDatabase($params['dbname']);
            } catch (\Exception $e){}
            $this->connection->__construct(
                $this->connection->getHttpClient(),
                $params['dbname']
            );
        } catch (\Exception $e){
            throw InfrastructureException::NoTenantDatabaseConnection(Multitenant::getTenantId());
        }
    }
}
