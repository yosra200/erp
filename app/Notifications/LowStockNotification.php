<?php
namespace App\Notifications;
use App\Models\SupplierStockAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;
class LowStockNotification extends Notification
{
    use Queueable;
    public function __construct(public SupplierStockAlert $alert) {}
    public function via(object $notifiable): array { return ['database']; }
    public function toDatabase(object $notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'title' => 'تنبيه انخفاض مخزون',
            'body' => "الصنف {$this->alert->product->name} وصل إلى {$this->alert->current_stock}، والحد المحدد {$this->alert->threshold_stock}.",
            'alert_id' => $this->alert->id,
            'product_id' => $this->alert->product_id,
        ]);
    }
}
