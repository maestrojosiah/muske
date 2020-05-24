<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 */
class Project
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
    private $projecttitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $projectdescription;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Musician", inversedBy="projects")
     * @ORM\JoinColumn(name="musician_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $musician;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $projectimage;

    public function __toString() {
        return $this->projecttitle;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProjecttitle(): ?string
    {
        return $this->projecttitle;
    }

    public function setProjecttitle(?string $projecttitle): self
    {
        $this->projecttitle = $projecttitle;

        return $this;
    }

    public function getProjectdescription(): ?string
    {
        return $this->projectdescription;
    }

    public function setProjectdescription(?string $projectdescription): self
    {
        $this->projectdescription = $projectdescription;

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

    public function getProjectimage(): ?string
    {
       return $this->projectimage;

    }

    public function imagelink(): ?string
    {
        $url = "https://muske.co.ke/uploads/projects/$this->projectimage";

        return $url;

    }

    public function setProjectimage(?string $projectimage): self
    {
        $this->projectimage = $projectimage;

        return $this;
    }
}
