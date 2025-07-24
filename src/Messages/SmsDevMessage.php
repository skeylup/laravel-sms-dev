<?php

namespace Skeylup\LaravelSmsDev\Messages;

class SmsDevMessage
{
    public string $content;
    public ?string $from = null;

    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    /**
     * Créer une nouvelle instance de message
     */
    public static function create(string|self $content = ''): self
    {
        if ($content instanceof self) {
            return $content;
        }

        return new static($content);
    }

    /**
     * Définir le contenu du message
     */
    public function content(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Définir l'expéditeur du message
     */
    public function from(string $from): self
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Convertir le message en tableau
     */
    public function toArray(): array
    {
        return [
            'content' => $this->content,
            'from' => $this->from,
        ];
    }

    /**
     * Convertir le message en chaîne
     */
    public function __toString(): string
    {
        return $this->content;
    }
}
