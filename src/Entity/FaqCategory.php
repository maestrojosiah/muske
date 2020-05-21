<?php

namespace App\Entity;

use App\Repository\FaqCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FaqCategoryRepository::class)
 */
class FaqCategory
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity=FAQ::class, mappedBy="faqCategory")
     */
    private $faqs;

    public function __toString() {
        return $this->title;
    }

    public function __construct()
    {
        $this->faqs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection|FAQ[]
     */
    public function getFaqs(): Collection
    {
        return $this->faqs;
    }

    public function addFaq(FAQ $faqs): self
    {
        if (!$this->faqs->contains($faqs)) {
            $this->faqs[] = $faqs;
            $faqs->setFaqCategory($this);
        }

        return $this;
    }

    public function removeFaq(FAQ $faqs): self
    {
        if ($this->faqs->contains($faqs)) {
            $this->faqs->removeElement($faqs);
            // set the owning side to null (unless already changed)
            if ($faqs->getFaqCategory() === $this) {
                $faqs->setFaqCategory(null);
            }
        }

        return $this;
    }
}
