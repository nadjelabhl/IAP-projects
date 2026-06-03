<?php

namespace App\Livewire\Notifications;

use App\Models\Notification;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class NotificationsList extends Component
{
    use WithPagination;

    public function markRead(int $id): void
    {
        Notification::where('id_notification', $id)
            ->where('user_id', auth()->id())
            ->update(['is_read' => true, 'read_at' => now()]);
    }

    public function markAllRead(): void
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }

    public function delete(int $id): void
    {
        Notification::where('id_notification', $id)
            ->where('user_id', auth()->id())
            ->delete();
    }

    public function render()
    {
        $uid = auth()->id();

        return view('livewire.notifications.list', [
            'notifications' => Notification::where('user_id', $uid)
                ->orderBy('created_at', 'desc')
                ->paginate(20),
            'unreadCount' => Notification::where('user_id', $uid)
                ->where('is_read', false)
                ->count(),
        ]);
    }
}
