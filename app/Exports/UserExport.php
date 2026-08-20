<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\User;
use Saroven\Reportify\Contracts\Reportable;

class UserExport implements Reportable
{
    /**
     * Resolve data array or collection for export.
     *
     * @param array<string, mixed> $payload
     * @param string $exportType
     * @param int|string|null $userId
     * @return mixed
     */
    public function getExportData(array $payload, string $exportType, int|string|null $userId = null): mixed
    {
        $query = User::query();

        if (!empty($payload['search'])) {
            $search = $payload['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (!empty($payload['role'])) {
            $query->where('role', $payload['role']);
        }

        if (!empty($payload['status'])) {
            $query->where('status', $payload['status']);
        }

        if (!empty($payload['department'])) {
            $query->where('department', $payload['department']);
        }

        return $query->latest('id')->get();
    }
}