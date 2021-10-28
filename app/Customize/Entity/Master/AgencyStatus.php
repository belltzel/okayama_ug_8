<?php

namespace Customize\Entity\Master;

use Doctrine\ORM\Mapping as ORM;

/**
 * AgencyStatus
 *
 * @ORM\Table(name="customize_mtb_agency_status")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Customize\Repository\Master\AgencyStatusRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class AgencyStatus extends \Eccube\Entity\Master\AbstractMasterEntity
{
    /**
     * 非稼働
     */
    const NON_ACTIVE = 1;

    /**
     * 稼働
     */
    const ACTIVE = 2;
}
