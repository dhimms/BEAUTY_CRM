<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditObserver
{
    public function created(Model $model): void
    {
        $this->log('created', $model, null, $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $this->log('updated', $model, $model->getOriginal(), $model->getChanges());
    }

    public function deleted(Model $model): void
    {
        $this->log('deleted', $model, $model->getOriginal(), null);
    }

    private function log(string $action, Model $model, ?array $old, ?array $new): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(),
            'old_values' => $old ? $this->filterFields($old) : null,
            'new_values' => $new ? $this->filterFields($new) : null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    private function filterFields(array $data): array
    {
        $hidden = ['password', 'remember_token', 'created_at', 'updated_at', 'deleted_at'];
        return collect($data)->except($hidden)->toArray();
    }
}