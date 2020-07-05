<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PaymentRepository::class)
 */
class Payment
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
    private $merchantrequestid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $checkoutrequestid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $responsecode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $responsedescription;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $customermessage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resultcode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resultdesc;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMerchantrequestid(): ?string
    {
        return $this->merchantrequestid;
    }

    public function setMerchantrequestid(string $merchantrequestid): self
    {
        $this->merchantrequestid = $merchantrequestid;

        return $this;
    }

    public function getCheckoutrequestid(): ?string
    {
        return $this->checkoutrequestid;
    }

    public function setCheckoutrequestid(string $checkoutrequestid): self
    {
        $this->checkoutrequestid = $checkoutrequestid;

        return $this;
    }

    public function getResponsecode(): ?string
    {
        return $this->responsecode;
    }

    public function setResponsecode(string $responsecode): self
    {
        $this->responsecode = $responsecode;

        return $this;
    }

    public function getResponsedescription(): ?string
    {
        return $this->responsedescription;
    }

    public function setResponsedescription(string $responsedescription): self
    {
        $this->responsedescription = $responsedescription;

        return $this;
    }

    public function getCustomermessage(): ?string
    {
        return $this->customermessage;
    }

    public function setCustomermessage(string $customermessage): self
    {
        $this->customermessage = $customermessage;

        return $this;
    }

    public function getResultcode(): ?string
    {
        return $this->resultcode;
    }

    public function setResultcode(?string $resultcode): self
    {
        $this->resultcode = $resultcode;

        return $this;
    }

    public function getResultdesc(): ?string
    {
        return $this->resultdesc;
    }

    public function setResultdesc(?string $resultdesc): self
    {
        $this->resultdesc = $resultdesc;

        return $this;
    }


}
