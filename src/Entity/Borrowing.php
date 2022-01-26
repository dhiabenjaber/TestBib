<?php

namespace App\Entity;

use App\Repository\BorrowingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BorrowingRepository::class)
 */
class Borrowing
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Book::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $book;


    /**
     * @ORM\Column(type="string", length=50)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $borrowingDateTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $returnDateTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $requestDateTime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getBorrowingDateTime(): ?\DateTimeInterface
    {
        return $this->borrowingDateTime;
    }

    public function setBorrowingDateTime(\DateTimeInterface $borrowingDateTime): self
    {
        $this->borrowingDateTime = $borrowingDateTime;

        return $this;
    }

    public function getReturnDateTime(): ?\DateTimeInterface
    {
        return $this->returnDateTime;
    }

    public function setReturnDateTime(?\DateTimeInterface $returnDateTime): self
    {
        $this->returnDateTime = $returnDateTime;

        return $this;
    }

    public function getRequestDateTime(): ?\DateTimeInterface
    {
        return $this->requestDateTime;
    }

    public function setRequestDateTime(?\DateTimeInterface $requestDateTime): self
    {
        $this->requestDateTime = $requestDateTime;

        return $this;
    }
}
