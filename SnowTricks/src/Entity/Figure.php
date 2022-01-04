<?php

namespace App\Entity;

use App\Repository\FigureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FigureRepository::class)
 */
class Figure
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=350)
     */
    private $description;
    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastUpdateDate;

    /**
     * @ORM\OneToMany(targetEntity=Media::class, mappedBy="figure", orphanRemoval=true, cascade={"persist"})
     */
    private $mediaList;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="Figure", orphanRemoval=true)
     */
    private $messagesList;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class, inversedBy="figuresList", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $groupe;

    public function __construct()
    {
        $this->mediaList = new ArrayCollection();
        $this->messagesList = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getLastUpdateDate(): ?\DateTimeInterface
    {
        return $this->lastUpdateDate;
    }

    public function setLastUpdateDate(\DateTimeInterface $lastUpdateDate): self
    {
        $this->lastUpdateDate = $lastUpdateDate;

        return $this;
    }

    /**
     * @return Collection|Media[]
     */
    public function getMediaList(): Collection
    {
        return $this->mediaList;
    }

    public function addMediaList(Media $mediaList): self
    {
        if (!$this->mediaList->contains($mediaList)) {
            $this->mediaList[] = $mediaList;
            $mediaList->setFigure($this);
        }

        return $this;
    }

    public function removeMediaList(Media $mediaList): self
    {
        if ($this->mediaList->removeElement($mediaList)) {
            // set the owning side to null (unless already changed)
            if ($mediaList->getFigure() === $this) {
                $mediaList->setFigure(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessagesList(): Collection
    {
        return $this->messagesList;
    }

    public function addMessagesList(Message $messagesList): self
    {
        if (!$this->messagesList->contains($messagesList)) {
            $this->messagesList[] = $messagesList;
            $messagesList->setFigure($this);
        }

        return $this;
    }

    public function removeMessagesList(Message $messagesList): self
    {
        if ($this->messagesList->removeElement($messagesList)) {
            // set the owning side to null (unless already changed)
            if ($messagesList->getFigure() === $this) {
                $messagesList->setFigure(null);
            }
        }

        return $this;
    }

    public function getGroupe(): ?Group
    {
        return $this->groupe;
    }

    public function setGroupe(?Group $groupe): self
    {
        $this->groupe = $groupe;

        return $this;
    }
}
