<?php

namespace Customize\Entity;

use Customize\Entity\Agency;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation\EntityExtension;

/**
  * @EntityExtension("Eccube\Entity\Product")
 */
trait ProductTrait
{
    /**
     * @var \Customize\Entity\Master\AgencyStatus
     *
     * @ORM\ManyToOne(targetEntity="Customize\Entity\Agency")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="agency_id", referencedColumnName="id")
     * })
     */
    private $Agency;

    /**
     * @return Agency
     */
    public function getAgency(): ?Agency
    {
        return $this->Agency;
    }

    /**
     * @param Agency|null $Agency
     * @return $this
     */
    public function setAgency(?Agency $Agency): self
    {
        $this->Agency = $Agency;

        return $this;
    }
}
