<?php

namespace Skeylup\LaravelSmsDev\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class SmsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'to',
        'from',
        'message',
        'metadata',
        'status',
        'sent_at',
        'is_read',
    ];

    protected $casts = [
        'metadata' => 'array',
        'sent_at' => 'datetime',
        'is_read' => 'boolean',
    ];

    /**
     * Scope pour récupérer les SMS non lus
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope pour récupérer les SMS lus
     */
    public function scopeRead(Builder $query): Builder
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope pour récupérer les SMS par statut
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Marquer le SMS comme lu
     */
    public function markAsRead(): bool
    {
        return $this->update(['is_read' => true]);
    }

    /**
     * Marquer le SMS comme non lu
     */
    public function markAsUnread(): bool
    {
        return $this->update(['is_read' => false]);
    }

    /**
     * Obtenir le nom de la classe de notification depuis les métadonnées
     */
    public function getNotificationClassAttribute(): ?string
    {
        return $this->metadata['notification_class'] ?? null;
    }

    /**
     * Obtenir un résumé du message (premiers 50 caractères)
     */
    public function getMessagePreviewAttribute(): string
    {
        return strlen($this->message) > 50 
            ? substr($this->message, 0, 50) . '...' 
            : $this->message;
    }
}
