<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=Purchased::class, mappedBy="user", orphanRemoval=true)
     */
    private $purchaseds;

    public function __construct()
    {
        $this->purchaseds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
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

    /**
     * @return Collection|Purchased[]
     */
    public function getPurchaseds(): Collection
    {
        return $this->purchaseds;
    }

    public function addPurchased(Purchased $purchased): self
    {
        if (!$this->purchaseds->contains($purchased)) {
            $this->purchaseds[] = $purchased;
            $purchased->setUser($this);
        }

        return $this;
    }

    public function removePurchased(Purchased $purchased): self
    {
        if ($this->purchaseds->removeElement($purchased)) {
            // set the owning side to null (unless already changed)
            if ($purchased->getUser() === $this) {
                $purchased->setUser(null);
            }
        }

        return $this;
    }
}
