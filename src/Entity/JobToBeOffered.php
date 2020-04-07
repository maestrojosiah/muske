<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\JobToBeOfferedRepository")
 */
class JobToBeOffered
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $medium;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Musician", inversedBy="jobstobeoffered")
     */
    private $musician;

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

    public function getMedium(): ?string
    {
        return $this->medium;
    }

    public function setMedium(?string $medium): self
    {
        $this->medium = $medium;

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
}
