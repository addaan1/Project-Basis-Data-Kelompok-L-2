<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OfferCreated extends Notification
{
    public $negosiasi;

    /**
     * Create a new notification instance.
     */
    public function __construct($negosiasi)
    {
        $this->negosiasi = $negosiasi;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'id' => $this->negosiasi->id,
            'message' => 'Tawaran baru: ' . $this->negosiasi->jumlah_kg . 'Kg untuk ' . $this->negosiasi->produk->nama_produk,
            'link' => route('negosiasi.show', $this->negosiasi->id),
            'type' => 'offer',
            'created_at' => now(),
        ];
    }
}
