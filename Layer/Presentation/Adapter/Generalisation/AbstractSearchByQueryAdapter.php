<?php

namespace Sfynx\DddBundle\Layer\Presentation\Adapter\Generalisation;

use Sfynx\DddBundle\Layer\Presentation\Request\Generalisation\QueryRequestInterface;

abstract class AbstractSearchByQueryAdapter
{
    protected $queryNamespace;

    public function createQueryFromRequest(QueryRequestInterface $request)
    {
        $parameters = $request->getRequestParameters();
        $start = empty($parameters['start']) ? null : $parameters['start'];
        $count = empty($parameters['count']) ? null : $parameters['count'];
        $criterias = empty($parameters['criterias']) ? null : $parameters['criterias'];

        //check for 2 params or nothing ?

        return new $this->queryNamespace($parameters['criterias'], $start, $count);
    }
}
