<?php

namespace App\Entity;

use App\Repository\MpesaReceiptNumberRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MpesaReceiptNumberRepository::class)
 */
class MpesaReceiptNumber
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    public function getId(): ?int
    {
        return $this->id;
    }
}
