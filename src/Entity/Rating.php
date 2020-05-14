<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RatingRepository")
 */
class Rating
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Musician", inversedBy="ratings")
     */
    private $musician;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $ratingval;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rater;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rateremail;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRatingval(): ?string
    {
        return $this->ratingval;
    }

    public function setRatingval(?string $ratingval): self
    {
        $this->ratingval = $ratingval;

        return $this;
    }

    public function getRater(): ?string
    {
        return $this->rater;
    }

    public function setRater(?string $rater): self
    {
        $this->rater = $rater;

        return $this;
    }

    public function getRateremail(): ?string
    {
        return $this->rateremail;
    }

    public function setRateremail(?string $rateremail): self
    {
        $this->rateremail = $rateremail;

        return $this;
    }
}
