<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MailRepository")
 * @ORM\Table(name="`mail`")
 */
class Mail
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $mail_unique_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $mail_recipient_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sender_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mail_title;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private $mail_message;

    /**
     * @ORM\Column(type="integer")
     */
    private $unread;

    /**
     * @ORM\Column(type="integer")
     */
    private $attached_item_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $attached_kinah_count;

    /**
     * @ORM\Column(type="integer")
     */
    private $express;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $recieved_time;

    public function getId(): ?int
    {
        return $this->mail_unique_id;
    }

    public function setId(int $mail_unique_id): self
    {
        $this->mail_unique_id = $mail_unique_id;

        return $this;
    }

    public function getMailRecipientId(): ?int
    {
        return $this->mail_recipient_id;
    }

    public function setMailRecipientId(int $mail_recipient_id): self
    {
        $this->mail_recipient_id = $mail_recipient_id;

        return $this;
    }

    public function getSenderName(): ?string
    {
        return $this->sender_name;
    }

    public function setSenderName(string $sender_name): self
    {
        $this->sender_name = $sender_name;

        return $this;
    }

    public function getMailTitle(): ?string
    {
        return $this->mail_title;
    }

    public function setMailTitle(string $mail_title): self
    {
        $this->mail_title = $mail_title;

        return $this;
    }

    public function getMailMessage(): ?string
    {
        return $this->mail_message;
    }

    public function setMailMessage(string $mail_message): self
    {
        $this->mail_message = $mail_message;

        return $this;
    }

    public function getUnread(): ?int
    {
        return $this->unread;
    }

    public function setUnread(int $unread): self
    {
        $this->unread = $unread;

        return $this;
    }

    public function getAttachedItemId(): ?int
    {
        return $this->attached_item_id;
    }

    public function setAttachedItemId(int $attached_item_id): self
    {
        $this->attached_item_id = $attached_item_id;

        return $this;
    }

    public function getAttachedKinahCount(): ?int
    {
        return $this->attached_kinah_count;
    }

    public function setAttachedKinahCount(int $attached_kinah_count): self
    {
        $this->attached_kinah_count = $attached_kinah_count;

        return $this;
    }

    public function getExpress(): ?int
    {
        return $this->express;
    }

    public function setExpress(int $express): self
    {
        $this->express = $express;

        return $this;
    }

    public function getRecievedTime(): ?\DateTimeInterface
    {
        return $this->recieved_time;
    }

    public function setRecievedTime(\DateTimeInterface $recieved_time): self
    {
        $this->recieved_time = $recieved_time;

        return $this;
    }
}
