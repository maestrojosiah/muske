<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DocumentRepository")
 */
class Document
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
    private $doc;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Education", inversedBy="document", cascade={"persist", "remove"})
     */
    private $education;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Musician", inversedBy="documents")
     */
    private $musician;

    private $docpath;

    public function __toString() {
        return $this->doc;
    }


    public function getDocpath(){
        $url = "http://localhost:8000/uploads/documents/$this->doc";
        $this->docpath = $url;
        return $this->docpath;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDoc(): ?string
    {
        return $this->doc;
    }

    public function setDoc(?string $doc): self
    {
        $this->doc = $doc;

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
