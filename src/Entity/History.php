<?php

namespace App\Entity;

use App\Repository\HistoryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HistoryRepository::class)
 */
class History
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="user_history")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Books::class, inversedBy="book_history")
     */
    private $book;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $loan_date;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $due_date;

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

    public function getBook(): ?Books
    {
        return $this->book;
    }

    public function setBook(?Books $book): self
    {
        $this->book = $book;

        return $this;
    }

    public function getLoanDate(): ?\DateTimeInterface
    {
        return $this->loan_Date;
    }

    public function setLoanDate(?\DateTimeInterface $loan_date): self
    {
        $this->loan_date = $loan_date;

        return $this;
    }

    public function getDueDate(): ?\DateTimeInterface
    {
        return $this->due_date;
    }

    public function setDueDate(?\DateTimeInterface $due_date): self
    {
        $this->due_date = $due_date;

        return $this;
    }
}