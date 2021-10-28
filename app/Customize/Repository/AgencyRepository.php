<?php

namespace Customize\Repository;

use Customize\Entity\Agency;
use Eccube\Repository\AbstractRepository;
use Eccube\Util\StringUtil;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class AgencyRepository
 * @package Customize\Repository
 */
class AgencyRepository extends AbstractRepository
{
    /**
     * AgencyRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Agency::class);
    }

    public function newAgency()
    {
        return new \Customize\Entity\Agency();
    }

    /**
     * 代理店を登録します.
     *
     * @param $Agency
     */
    public function save($Agency)
    {
        $em = $this->getEntityManager();
        $em->persist($Agency);
        $em->flush($Agency);
    }

    /**
     * 代理店を削除します.
     *
     * @param Agency $Agency
     *
     * @throws ForeignKeyConstraintViolationException 外部キー制約違反の場合
     * @throws DriverException SQLiteの場合, 外部キー制約違反が発生すると, DriverExceptionをthrowします.
     */
    public function delete($Agency)
    {
        $em = $this->getEntityManager();
        $em->remove($Agency);
        $em->flush($Agency);
    }

    public function getQueryBuilderBySearchData($searchData)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a');

        if (isset($searchData['multi']) && StringUtil::isNotBlank($searchData['multi'])) {
            //スペース除去
            $clean_key_multi = preg_replace('/\s+|[　]+/u', '', $searchData['multi']);
            $id = preg_match('/^\d{0,10}$/', $clean_key_multi) ? $clean_key_multi : null;
            if ($id && $id > '2147483647' && $this->isPostgreSQL()) {
                $id = null;
            }
            $qb
                ->andWhere('a.id = :agency_id OR a.name LIKE :name OR a.code LIKE :code')
                ->setParameter('agency_id', $id)
                ->setParameter('name', '%'.$clean_key_multi.'%')
                ->setParameter('code', '%'.$clean_key_multi.'%');
        }

        // create_date
        if (!empty($searchData['create_datetime_start']) && $searchData['create_datetime_start']) {
            $date = $searchData['create_datetime_start'];
            $qb
                ->andWhere('a.create_date >= :create_date_start')
                ->setParameter('create_date_start', $date);
        } elseif (!empty($searchData['create_date_start']) && $searchData['create_date_start']) {
            $qb
                ->andWhere('a.create_date >= :create_date_start')
                ->setParameter('create_date_start', $searchData['create_date_start']);
        }

        if (!empty($searchData['create_datetime_end']) && $searchData['create_datetime_end']) {
            $date = $searchData['create_datetime_end'];
            $qb
                ->andWhere('a.create_date < :create_date_end')
                ->setParameter('create_date_end', $date);
        } elseif (!empty($searchData['create_date_end']) && $searchData['create_date_end']) {
            $date = clone $searchData['create_date_end'];
            $date->modify('+1 days');
            $qb
                ->andWhere('a.create_date < :create_date_end')
                ->setParameter('create_date_end', $date);
        }

        // update_date
        if (!empty($searchData['update_datetime_start']) && $searchData['update_datetime_start']) {
            $date = $searchData['update_datetime_start'];
            $qb
                ->andWhere('a.update_date >= :update_date_start')
                ->setParameter('update_date_start', $date);
        } elseif (!empty($searchData['update_date_start']) && $searchData['update_date_start']) {
            $qb
                ->andWhere('a.update_date >= :update_date_start')
                ->setParameter('update_date_start', $searchData['update_date_start']);
        }

        if (!empty($searchData['update_datetime_end']) && $searchData['update_datetime_end']) {
            $date = $searchData['update_datetime_end'];
            $qb
                ->andWhere('a.update_date < :update_date_end')
                ->setParameter('update_date_end', $date);
        } elseif (!empty($searchData['update_date_end']) && $searchData['update_date_end']) {
            $date = clone $searchData['update_date_end'];
            $date->modify('+1 days');
            $qb
                ->andWhere('a.update_date < :update_date_end')
                ->setParameter('update_date_end', $date);
        }

        // status
        if (!empty($searchData['agency_status']) && count($searchData['agency_status']) > 0) {
            $qb
                ->andWhere($qb->expr()->in('a.Status', ':statuses'))
                ->setParameter('statuses', $searchData['agency_status']);
        }

        // Order By
        $qb->addOrderBy('a.update_date', 'DESC');

        return $qb;
    }
}
