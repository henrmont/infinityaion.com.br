<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShopItemRepository")
 */
class ShopItem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $player;

    /**
     * @ORM\Column(type="integer")
     */
    private $item;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $history;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $modified_at;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $player_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     */
    private $unique_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?int
    {
        return $this->user;
    }

    public function setUser(int $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPlayer(): ?int
    {
        return $this->player;
    }

    public function setPlayer(int $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getItem(): ?int
    {
        return $this->item;
    }

    public function setItem(int $item): self
    {
        $this->item = $item;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getHistory(): ?int
    {
        return $this->history;
    }

    public function setHistory(?int $history): self
    {
        $this->history = $history;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeInterface
    {
        return $this->modified_at;
    }

    public function setModifiedAt(\DateTimeInterface $modified_at): self
    {
        $this->modified_at = $modified_at;

        return $this;
    }

    public function getPlayerName(): ?string
    {
        return $this->player_name;
    }

    public function setPlayerName(string $player_name): self
    {
        $this->player_name = $player_name;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getUnique(): ?int
    {
        return $this->unique_id;
    }

    public function setUnique(int $unique_id): self
    {
        $this->unique_id = $unique_id;

        return $this;
    }
}
