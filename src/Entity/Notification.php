<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use App\Repository\PostRepository;
use App\Repository\AdvertRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Post;
use App\Entity\Advert;


/**
 * @ORM\Entity(repositoryClass=NotificationRepository::class)
 */
class Notification
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Musician::class, inversedBy="notifications")
     */
    private $musician;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sender;

    /**
     * @ORM\Column(type="boolean")
     */
    private $unread;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="array")
     */
    private $parameters = [];
    // ['entity' = 'Post', 'field' => 'title']

    /**
     * @ORM\Column(type="integer")
     */
    private $reference;

    /**
     * @ORM\Column(type="date")
     */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity=Advert::class, inversedBy="notifications")
     */
    private $advert;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="notifications")
     */
    private $post;
    
    public function message() : ?string
    {
        return $this->messageForNotification($this);
    }

    public function messageForNotification(Notification $notification) : string
    {
        if($this->type == 'advert'){
            $tell = $this->getAdvert()->getTask();
        }
        if($this->type == 'postlike'){
            $tell = $this->getPost()->getTitle();
        }
        
        return "New advert for: " . substr($tell, 0, 20) . "...";
    }

    public function messageForNotifications(array $notifications) : string
    {
        return count($notifications)." New adverts posted";
    }

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

    public function getSender(): ?string
    {
        return $this->sender;
    }

    public function setSender(string $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getUnread(): ?bool
    {
        return $this->unread;
    }

    public function setUnread(bool $unread): self
    {
        $this->unread = $unread;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getParameters(): ?array
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters): self
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function getReference(): ?int
    {
        return $this->reference;
    }

    public function setReference(int $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getAdvert(): ?Advert
    {
        return $this->advert;
    }

    public function setAdvert(?Advert $advert): self
    {
        $this->advert = $advert;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }
}
