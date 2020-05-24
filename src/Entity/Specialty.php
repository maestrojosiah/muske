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

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Education", inversedBy="specialties")
     * @ORM\JoinColumn(name="education_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $education;

    public function __toString() {
        return $this->instrumentorskill;
    }
    
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

    public function getEducation(): ?Education
    {
        return $this->education;
    }

    public function setEducation(?Education $education): self
    {
        $this->education = $education;

        return $this;
    }
}
