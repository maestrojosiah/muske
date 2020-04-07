<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SpecialtyRepository")
 */
class Specialty
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
    private $instrumentorskill;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInstrumentorskill(): ?string
    {
        return $this->instrumentorskill;
    }

    public function setInstrumentorskill(?string $instrumentorskill): self
    {
        $this->instrumentorskill = $instrumentorskill;

        return $this;
    }
}
