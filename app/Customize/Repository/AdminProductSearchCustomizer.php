<?php

namespace Customize\Repository;

use Doctrine\ORM\QueryBuilder;
use Eccube\Doctrine\Query\QueryCustomizer;
use Eccube\Repository\QueryKey;

class AdminProductSearchCustomizer implements QueryCustomizer
{

    /**
     * {@inheritdoc}
     */
    public function customize(QueryBuilder $builder, $params, $queryKey)
    {
        if (isset($params['agency']) && null !== $params['agency']) {
            $builder
                ->andWhere('p.Agency = :Agency')
                ->setParameter('Agency', $params['agency']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryKey()
    {
        return QueryKey::PRODUCT_SEARCH_ADMIN;
    }

}
