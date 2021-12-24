<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GroupRepository::class)
 * @ORM\Table(name="`group`")
 */
class Group
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
     * @ORM\OneToMany(targetEntity=Figure::class, mappedBy="groupe")
     */
    private $figuresList;

    public function __construct($name)
    {
        $this->name = $name;
        $this->figuresList = new ArrayCollection();
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

    /**
     * @return Collection|Figure[]
     */
    public function getFiguresList(): Collection
    {
        return $this->figuresList;
    }

    public function addFiguresList(Figure $figuresList): self
    {
        if (!$this->figuresList->contains($figuresList)) {
            $this->figuresList[] = $figuresList;
            $figuresList->setGroupe($this);
        }

        return $this;
    }

    public function removeFiguresList(Figure $figuresList): self
    {
        if ($this->figuresList->removeElement($figuresList)) {
            // set the owning side to null (unless already changed)
            if ($figuresList->getGroupe() === $this) {
                $figuresList->setGroupe(null);
            }
        }

        return $this;
    }
}
