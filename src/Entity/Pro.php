<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProRepository")
 */
class Pro
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $started;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $ending;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Musician", inversedBy="pro", cascade={"persist", "remove"})
     */
    private $musician;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStarted(): ?\DateTimeInterface
    {
        return $this->started;
    }

    public function setStarted(?\DateTimeInterface $started): self
    {
        $this->started = $started;

        return $this;
    }

    public function getEnding(): ?\DateTimeInterface
    {
        return $this->ending;
    }

    public function setEnding(?\DateTimeInterface $ending): self
    {
        $this->ending = $ending;

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
