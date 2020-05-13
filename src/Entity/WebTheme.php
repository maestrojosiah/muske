<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WebThemeRepository")
 */
class WebTheme
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
    private $template;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $screenshot;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;
    private $themeurl;

    public function __construct()
    {
        $this->musicians = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(?string $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function getScreenshot(): ?string
    {
        return $this->screenshot;
    }

    public function setScreenshot(?string $screenshot): self
    {
        $this->screenshot = $screenshot;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }
    
    public function themeurl(): ?string
    {
        $url = "http://localhost:8000/img/screenshots/$this->screenshot";
        $this->themeurl = $url;

        return $this->themeurl;
    }

}
