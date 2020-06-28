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
    private $coverphoto;

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


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $online;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tsc;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $placeofwork;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bgphoto;
    private $photopath;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $tawkTo;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $signature;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $appendsignature;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $visibility;

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

    public function getCoverphoto(): ?string
    {
        return $this->coverphoto;
    }

    public function setCoverphoto(?string $coverphoto): self
    {
        $this->coverphoto = $coverphoto;

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

    public function getOnline(): ?string
    {
        return $this->online;
    }

    public function setOnline(?string $online): self
    {
        $this->online = $online;

        return $this;
    }

    public function getTsc(): ?string
    {
        return $this->tsc;
    }

    public function setTsc(?string $tsc): self
    {
        $this->tsc = $tsc;

        return $this;
    }

    public function getPlaceofwork(): ?string
    {
        return $this->placeofwork;
    }

    public function setPlaceofwork(?string $placeofwork): self
    {
        $this->placeofwork = $placeofwork;

        return $this;
    }

    public function getBgphoto(): ?string
    {
        return $this->bgphoto;
    }

    public function setBgphoto(?string $bgphoto): self
    {
        $this->bgphoto = $bgphoto;

        return $this;
    }

    public function getPhotoPath(): ?string
    {
        $url = "https://muske.co.ke/uploads/gallery/$this->bgphoto";

        $this->photopath = $url;
        return $this->photopath;

    }

    public function getTawkTo(): ?string
    {
        return $this->tawkTo;
    }

    public function setTawkTo(?string $tawkTo): self
    {
        $this->tawkTo = $tawkTo;

        return $this;
    }

    public function getSignature(): ?string
    {
        return $this->signature;
    }

    public function setSignature(?string $signature): self
    {
        $this->signature = $signature;

        return $this;
    }

    public function getAppendsignature(): ?string
    {
        return $this->appendsignature;
    }

    public function setAppendsignature(?string $appendsignature): self
    {
        $this->appendsignature = $appendsignature;

        return $this;
    }

    public function getVisibility(): ?string
    {
        return $this->visibility;
    }

    public function setVisibility(?string $visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }

}
