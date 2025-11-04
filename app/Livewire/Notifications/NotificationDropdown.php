<?php

namespace App\Livewire\Notifications;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationDropdown extends Component
{
    public function render()
    {
        $user = Auth::user();

        $unreadNotifications = $user->unreadNotifications()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $unreadCount = $user->unreadNotifications()->count();

        return view('livewire.notifications.notification-dropdown', [
            'unreadNotifications' => $unreadNotifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function markAsRead(string $notificationId): void
    {
        $user = Auth::user();
        $user->notifications()->where('id', $notificationId)->update([
            'read_at' => now(),
        ]);

        $this->dispatch('notification-updated');
    }

    public function markAllAsRead(): void
    {
        $user = Auth::user();
        $user->unreadNotifications()->update([
            'read_at' => now(),
        ]);

        $this->dispatch('notification-updated');
    }

    public function getIconAttribute(string $type): string
    {
        return match ($type) {
            'proposal_submitted' => 'file-text',
            'dekan_approval_decision' => 'check-circle',
            'reviewer_assigned' => 'user-check',
            'review_completed', 'all_reviews_completed' => 'check-square',
            'final_decision_made' => 'award',
            default => 'bell',
        };
    }
}
