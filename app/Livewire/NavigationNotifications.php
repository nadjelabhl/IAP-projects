<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Notification;
use App\Services\NotificationService;

class NavigationNotifications extends Component
{
    protected NotificationService $notificationService;

    public function boot(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function render()
    {
        return view('livewire.navigation-notifications', [
            // On récupère les notifications non lues de l'utilisateur connecté
            'notifications' => Notification::where('user_id', auth()->id())
                ->orderBy('is_read', 'asc')
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get(),
            'unreadCount' => Notification::where('user_id', auth()->id())
                ->where('is_read', false)
                ->count()
        ]);
    }

    public function markAsRead($id)
    {
        $this->notificationService->markAsRead($id, auth()->id());
    }

    public function markAllAsRead()
    {
        $this->notificationService->markAllAsRead(auth()->id());
    }
}