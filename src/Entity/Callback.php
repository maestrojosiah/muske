<?php

namespace App\Entity;

use App\Repository\CallbackRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CallbackRepository::class)
 */
class Callback
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
    private $CheckoutRequestID;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $ResultCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Amount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $MpesaReceiptNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $TransactionDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $PhoneNumber;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $Callbackmetadata = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCheckoutRequestID(): ?string
    {
        return $this->CheckoutRequestID;
    }

    public function setCheckoutRequestID(?string $CheckoutRequestID): self
    {
        $this->CheckoutRequestID = $CheckoutRequestID;

        return $this;
    }

    public function getResultCode(): ?string
    {
        return $this->ResultCode;
    }

    public function setResultCode(?string $ResultCode): self
    {
        $this->ResultCode = $ResultCode;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->Amount;
    }

    public function setAmount(?string $Amount): self
    {
        $this->Amount = $Amount;

        return $this;
    }

    public function getMpesaReceiptNumber(): ?string
    {
        return $this->MpesaReceiptNumber;
    }

    public function setMpesaReceiptNumber(?string $MpesaReceiptNumber): self
    {
        $this->MpesaReceiptNumber = $MpesaReceiptNumber;

        return $this;
    }

    public function getTransactionDate(): ?string
    {
        return $this->TransactionDate;
    }

    public function setTransactionDate(?string $TransactionDate): self
    {
        $this->TransactionDate = $TransactionDate;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->PhoneNumber;
    }

    public function setPhoneNumber(?string $PhoneNumber): self
    {
        $this->PhoneNumber = $PhoneNumber;

        return $this;
    }

    public function getCallbackmetadata(): ?array
    {
        return $this->Callbackmetadata;
    }

    public function setCallbackmetadata(?array $Callbackmetadata): self
    {
        $this->Callbackmetadata = $Callbackmetadata;

        return $this;
    }
}
