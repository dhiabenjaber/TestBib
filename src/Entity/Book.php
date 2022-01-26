<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 */
class Book implements \JsonSerializable
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
    private $title;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbrPages;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $editionDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbrCopies;


    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="books")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="book", orphanRemoval=true,cascade={"persist","remove"})
     * @Vich\UploadableField(mapping="books")
     */
    private $images;


    /**
     * @ORM\OneToMany(targetEntity=Borrowing::class, mappedBy="book", orphanRemoval=true)
     */
    private $bookings;

    /**
     * @ORM\ManyToOne(targetEntity=Editor::class, inversedBy="books")
     * @ORM\JoinColumn(nullable=false)
     */
    private $editor;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\ManyToMany(targetEntity=Author::class, inversedBy="books")
     */
    private $authors;

    /**
     * @ORM\Column(type="bigint")
     */
    private $isbn;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->publications = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->authors = new ArrayCollection();
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

    public function getNbrPages(): ?int
    {
        return $this->nbrPages;
    }

    public function setNbrPages(?int $nbrPages): self
    {
        $this->nbrPages = $nbrPages;

        return $this;
    }
    public function decreaseNumberOfCopies()
    {
        $this->nbrCopies--;
    }
    public function increaseNumberOfCopies()
    {
        $this->nbrCopies++;
    }
    public function getEditionDate(): ?\DateTimeInterface
    {
        return $this->editionDate;
    }

    public function setEditionDate(?\DateTimeInterface $editionDate): self
    {
        $this->editionDate = $editionDate;

        return $this;
    }

    public function getNbrCopies(): ?int
    {
        return $this->nbrCopies;
    }

    public function setNbrCopies(int $nbrCopies): self
    {
        $this->nbrCopies = $nbrCopies;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setBook($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getBook() === $this) {
                $image->setBook(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Borrowing[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Borrowing $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setBook($this);
        }

        return $this;
    }

    public function removeBooking(Borrowing $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getBook() === $this) {
                $booking->setBook(null);
            }
        }

        return $this;
    }

    public function getEditor(): ?Editor
    {
        return $this->editor;
    }

    public function setEditor(?Editor $editor): self
    {
        $this->editor = $editor;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }
    public function __toString(): string
    {
        return $this->title;
    }

    /**
     * @return Collection|Author[]
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(Author $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors[] = $author;
        }

        return $this;
    }

    public function removeAuthor(Author $author): self
    {
        $this->authors->removeElement($author);

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }


    public function jsonSerialize()
    {
        return [
            "id"=> $this->getId(),
            "title" => $this->getTitle(),
            "nbrPages"=>$this->getNbrPages(),
            "category"=>$this->getCategory(),
            "images"=>$this->getImages(),
            "price"=>$this->getPrice(),
            "authors"=>$this->getAuthors()
        ];
    }

}
