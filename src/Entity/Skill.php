<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SkillRepository")
 */
class Skill
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
    private $skillname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $levelofskill;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $experienceofskill;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Musician", inversedBy="skills")
     * @ORM\JoinColumn(name="musician_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $musician;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSkillname(): ?string
    {
        return $this->skillname;
    }

    public function setSkillname(?string $skillname): self
    {
        $this->skillname = $skillname;

        return $this;
    }

    public function getLevelofskill(): ?string
    {
        return $this->levelofskill;
    }

    public function setLevelofskill(?string $levelofskill): self
    {
        $this->levelofskill = $levelofskill;

        return $this;
    }

    public function getExperienceofskill(): ?string
    {
        return $this->experienceofskill;
    }

    public function setExperienceofskill(?string $experienceofskill): self
    {
        $this->experienceofskill = $experienceofskill;

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
