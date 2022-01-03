<?php

namespace CloudCMS\Test;

use CloudCMS\Node;
use CloudCMS\Directionality;

final class GraphqlTest extends AbstractWithRepositoryTest
{
    public function testGraphql()
    {
        $schema = $this->branch->graphqlSchema();
        $this->assertNotNull($schema);

        $query = "query {
                    n_nodes {
                        title
                    }
                }";
        
        $queryResult = $this->branch->graphqlQuery($query);
        $this->assertNotNull($queryResult);
    }
}