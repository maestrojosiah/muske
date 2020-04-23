<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SettingsRepository")
 */
class Settings
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $facebook;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $twitter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $linkedin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $instagram;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $youtube;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $job_order;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $job_order_by;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Musician", inversedBy="settings", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="musician_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $musician;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $edu_order;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $edu_order_by;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(?string $facebook): self
    {
        $this->facebook = $facebook;

        return $this;
    }

    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setTwitter(?string $twitter): self
    {
        $this->twitter = $twitter;

        return $this;
    }

    public function getLinkedin(): ?string
    {
        return $this->linkedin;
    }

    public function setLinkedin(?string $linkedin): self
    {
        $this->linkedin = $linkedin;

        return $this;
    }

    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    public function setInstagram(?string $instagram): self
    {
        $this->instagram = $instagram;

        return $this;
    }

    public function getYoutube(): ?string
    {
        return $this->youtube;
    }

    public function setYoutube(?string $youtube): self
    {
        $this->youtube = $youtube;

        return $this;
    }

    public function getJobOrder(): ?string
    {
        return $this->job_order;
    }

    public function setJobOrder(?string $job_order): self
    {
        $this->job_order = $job_order;

        return $this;
    }

    public function getJobOrderBy(): ?string
    {
        return $this->job_order_by;
    }

    public function setJobOrderBy(?string $job_order_by): self
    {
        $this->job_order_by = $job_order_by;

        return $this;
    }

    public function getMusician(): ?Musician
    {
        return $this->musician;
    }

    public function setMusician(?Musician $musician): self
    {
        $this->musician = $musician;

        return $this;
    }

    public function getEduOrder(): ?string
    {
        return $this->edu_order;
    }

    public function setEduOrder(?string $edu_order): self
    {
        $this->edu_order = $edu_order;

        return $this;
    }

    public function getEduOrderBy(): ?string
    {
        return $this->edu_order_by;
    }

    public function setEduOrderBy(?string $edu_order_by): self
    {
        $this->edu_order_by = $edu_order_by;

        return $this;
    }
}
