<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Framework\GraphQl\Query;

use GraphQL\Validator\DocumentValidator;
use GraphQL\Validator\Rules\DisableIntrospection;
use GraphQL\Validator\Rules\QueryDepth;
use GraphQL\Validator\Rules\QueryComplexity;

/**
 * Sets limits for query complexity. A single GraphQL query can potentially
 * generate thousands of database operations so, the very complex queries
 * should be filtered and rejected.
 *
 * https://github.com/webonyx/graphql-php/blob/master/docs/security.md#query-complexity-analysis
 */
class QueryComplexityLimiter
{
    /**
     * @var int
     */
    private $queryDepth;

    /**
     * @var int
     */
    private $queryComplexity;

    /**
     * @param int $queryDepth
     * @param int $queryComplexity
     */
    public function __construct(
        int $queryDepth = 10,
        int $queryComplexity = 50
    ) {
        $this->queryDepth = $queryDepth;
        $this->queryComplexity = $queryComplexity;
    }

    /**
     * @param bool $developerMode
     */
    public function execute(bool $developerMode = false): void
    {
        DocumentValidator::addRule(new QueryComplexity($this->queryComplexity));

        if (!$developerMode) {
            DocumentValidator::addRule(new DisableIntrospection());
            DocumentValidator::addRule(new QueryDepth($this->queryDepth));
        }
    }
}
