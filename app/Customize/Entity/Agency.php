<?php

namespace Customize\Entity;

use Customize\Entity\Master\AgencyStatus;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\Member;

/**
 * Class Agency
 * @package Customize\Entity
 *
 * @ORM\Table(name="customize_dtb_agency",uniqueConstraints={@ORM\UniqueConstraint(columns={"code"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Customize\Repository\AgencyRepository")
 */
class Agency
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="margin_rate", type="decimal", precision=10, scale=1, options={"unsigned":true, "default":0})
     */
    private $margin_rate = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="remarks", type="string", length=4000, nullable=true)
     */
    private $remarks;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetimetz")
     */
    private $create_date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetimetz")
     */
    private $update_date;

    /**
     * @var \Customize\Entity\Master\AgencyStatus
     *
     * @ORM\ManyToOne(targetEntity="Customize\Entity\Master\AgencyStatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="agency_status_id", referencedColumnName="id")
     * })
     */
    private $Status;

    /**
     * @var Member
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Member")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
     * })
     */
    private $Creator;

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->getName();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return $this
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string|null $code
     * @return $this
     */
    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return int
     */
    public function getMarginRate(): float
    {
        return $this->margin_rate;
    }

    /**
     * @param float|null $margin_rate
     * @return $this
     */
    public function setMarginRate(?float $margin_rate): self
    {
        $this->margin_rate = $margin_rate;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRemarks(): ?string
    {
        return $this->remarks;
    }

    /**
     * @param string|null $remarks
     * @return $this
     */
    public function setRemarks(?string $remarks): self
    {
        $this->remarks = $remarks;

        return $this;
    }

    /**
     * Get createDate.
     *
     * @return \DateTime
     */
    public function getCreateDate(): \DateTime
    {
        return $this->create_date;
    }

    /**
     * Set createDate.
     *
     * @param \DateTime $createDate
     * @return $this
     */
    public function setCreateDate(?\DateTime $createDate): self
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get updateDate.
     *
     * @return \DateTime
     */
    public function getUpdateDate(): \DateTime
    {
        return $this->update_date;
    }

    /**
     * Set updateDate.
     *
     * @param \DateTime $updateDate
     * @return $this
     */
    public function setUpdateDate(?\DateTime $updateDate): self
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get status.
     *
     * @return AgencyStatus|null
     */
    public function getStatus(): ?AgencyStatus
    {
        return $this->Status;
    }

    /**
     * Set status.
     *
     * @param AgencyStatus|null $status
     *
     * @return $this
     */
    public function setStatus(?AgencyStatus $status = null): self
    {
        $this->Status = $status;

        return $this;
    }

    /**
     * Get creator.
     *
     * @return Member|null
     */
    public function getCreator(): ?Member
    {
        return $this->Creator;
    }

    /**
     * Set creator.
     *
     * @param Member|null $creator
     * @return $this
     */
    public function setCreator(?Member $creator): self
    {
        $this->Creator = $creator;

        return $this;
    }
}
