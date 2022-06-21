<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 */
class Book
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $BookName;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $BookCover;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $BookFile;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $BookAuthor;

    /**
     * @ORM\Column(type="datetime")
     */
    private $BookDateRead;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBookName(): ?string
    {
        return $this->BookName;
    }

    public function setBookName(string $BookName): self
    {
        $this->BookName = $BookName;

        return $this;
    }

    public function getBookCover(): ?string
    {
        return $this->BookCover;
    }

    public function setBookCover(string $BookCover): self
    {
        $this->BookCover = $BookCover;

        return $this;
    }

    public function getBookFile(): ?string
    {
        return $this->BookFile;
    }

    public function setBookFile(string $BookFile): self
    {
        $this->BookFile = $BookFile;

        return $this;
    }

    public function getBookAuthor(): ?string
    {
        return $this->BookAuthor;
    }

    public function setBookAuthor(string $BookAuthor): self
    {
        $this->BookAuthor = $BookAuthor;

        return $this;
    }

    public function getBookDateRead(): ?\DateTimeInterface
    {
        return $this->BookDateRead;
    }

    public function setBookDateRead(\DateTimeInterface $BookDateRead): self
    {
        $this->BookDateRead = $BookDateRead;

        return $this;
    }

    public function updateBookDateRead()
    {
        $this->BookDateRead = new \DateTime("now");

        return $this;
    }
}
