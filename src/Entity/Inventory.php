<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InventoryRepository")
 * @ORM\Table(name="`inventory`")
 */
class Inventory
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $item_unique_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $item_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $item_count;

    /**
     * @ORM\Column(type="integer")
     */
    private $item_skin;

    /**
     * @ORM\Column(type="integer")
     */
    private $item_owner;

    public function getId(): ?int
    {
        return $this->item_unique_id;
    }

    public function setId(int $item_unique_id): self
    {
        $this->item_unique_id = $item_unique_id;

        return $this;
    }

    public function getItemId(): ?int
    {
        return $this->item_id;
    }

    public function setItemId(int $item_id): self
    {
        $this->item_id = $item_id;

        return $this;
    }

    public function getItemCount(): ?string
    {
        return $this->item_count;
    }

    public function setItemCount(string $item_count): self
    {
        $this->item_count = $item_count;

        return $this;
    }

    public function getItemSkin(): ?int
    {
        return $this->item_skin;
    }

    public function setItemSkin(int $item_skin): self
    {
        $this->item_skin = $item_skin;

        return $this;
    }

    public function getItemOwner(): ?int
    {
        return $this->item_owner;
    }

    public function setItemOwner(int $item_owner): self
    {
        $this->item_owner = $item_owner;

        return $this;
    }
}
