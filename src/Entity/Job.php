<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\JobRepository")
 */
class Job
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
    private $jobtitle;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $startingfrom;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $endedin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $currentornot;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Musician", inversedBy="jobs")
     */
    private $musician;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $location;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJobtitle(): ?string
    {
        return $this->jobtitle;
    }

    public function setJobtitle(?string $jobtitle): self
    {
        $this->jobtitle = $jobtitle;

        return $this;
    }

    public function getStartingfrom(): ?\DateTimeInterface
    {
        return $this->startingfrom;
    }

    public function setStartingfrom(?\DateTimeInterface $startingfrom): self
    {
        $this->startingfrom = $startingfrom;

        return $this;
    }

    public function getEndedin(): ?\DateTimeInterface
    {
        return $this->endedin;
    }

    public function setEndedin(?\DateTimeInterface $endedin): self
    {
        $this->endedin = $endedin;

        return $this;
    }

    public function getCurrentornot(): ?string
    {
        return $this->currentornot;
    }

    public function setCurrentornot(?string $currentornot): self
    {
        $this->currentornot = $currentornot;

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

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }
}
