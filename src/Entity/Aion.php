<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AionRepository")
 * @ORM\Table(name="`account_data`")
 */
class Aion
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
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="integer")
     */
    private $membership;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $expire;

    /**
     * @ORM\Column(type="integer")
     */
    private $activated;

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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getMembership(): ?int
    {
        return $this->membership;
    }

    public function setMembership(int $membership): self
    {
        $this->membership = $membership;

        return $this;
    }

    public function getExpire(): ?\DateTimeInterface
    {
        return $this->expire;
    }

    public function setExpire(?\DateTimeInterface $expire): self
    {
        $this->expire = $expire;

        return $this;
    }

    public function getActivated(): ?int
    {
        return $this->activated;
    }

    public function setActivated(int $activated): self
    {
        $this->activated = $activated;

        return $this;
    }
}
