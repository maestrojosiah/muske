<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EducationRepository")
 */
class Education
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
    private $institution;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fromyear;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $toyear;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $coursename;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $degree;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $location;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Musician", inversedBy="education")
     * @ORM\JoinColumn(name="musician_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $musician;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Specialty", mappedBy="education")
     */
    private $specialties;

    public function __construct()
    {
        $this->specialties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInstitution(): ?string
    {
        return $this->institution;
    }

    public function setInstitution(?string $institution): self
    {
        $this->institution = $institution;

        return $this;
    }

    public function getFromyear(): ?\DateTimeInterface
    {
        return $this->fromyear;
    }

    public function setFromyear(?\DateTimeInterface $fromyear): self
    {
        $this->fromyear = $fromyear;

        return $this;
    }

    public function getToyear(): ?\DateTimeInterface
    {
        return $this->toyear;
    }

    public function setToyear(?\DateTimeInterface $toyear): self
    {
        $this->toyear = $toyear;

        return $this;
    }

    public function getCoursename(): ?string
    {
        return $this->coursename;
    }

    public function setCoursename(?string $coursename): self
    {
        $this->coursename = $coursename;

        return $this;
    }

    public function getDegree(): ?string
    {
        return $this->degree;
    }

    public function setDegree(?string $degree): self
    {
        $this->degree = $degree;

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

    public function getMusician(): ?Musician
    {
        return $this->musician;
    }

    public function setMusician(?Musician $musician): self
    {
        $this->musician = $musician;

        return $this;
    }

    /**
     * @return Collection|Specialty[]
     */
    public function getSpecialties(): Collection
    {
        return $this->specialties;
    }

    public function addSpecialty(Specialty $specialty): self
    {
        if (!$this->specialties->contains($specialty)) {
            $this->specialties[] = $specialty;
            $specialty->setEducation($this);
        }

        return $this;
    }

    public function removeSpecialty(Specialty $specialty): self
    {
        if ($this->specialties->contains($specialty)) {
            $this->specialties->removeElement($specialty);
            // set the owning side to null (unless already changed)
            if ($specialty->getEducation() === $this) {
                $specialty->setEducation(null);
            }
        }

        return $this;
    }
}
