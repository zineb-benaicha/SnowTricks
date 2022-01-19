<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;



/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *  fields={"email"},
 *  message="Cet adresse e-mail est déjà enregistrée"
 * )
 */
class User implements PasswordAuthenticatedUserInterface,UserInterface 
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
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message="Le format de votre adresse e-mail est incorrect")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=500)
     * @Assert\Length(min="8", minMessage="Le mot de passe doit contenir au moins 8 caractères")
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Le mot de passe et sa confirmation doivent être identiques")
     */
    public $confirm_password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $avatar;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="user", orphanRemoval=true)
     */
    private $messagesList;

    public function __construct()
    {
        $this->messagesList = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;

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
            $messagesList->setUser($this);
        }

        return $this;
    }

    public function removeMessagesList(Message $messagesList): self
    {
        if ($this->messagesList->removeElement($messagesList)) {
            // set the owning side to null (unless already changed)
            if ($messagesList->getUser() === $this) {
                $messagesList->setUser(null);
            }
        }

        return $this;
    }

    public function getRoles()
    {
        return ['USER_ROLE'];
    }

    public function getSalt()
    {}

    public function eraseCredentials()
    {}
    
    public function getUserIdentifier()
    {
        return $this->username;
    }
}
