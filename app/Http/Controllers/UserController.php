<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Saroven\Reportify\Contracts\Reportable;
use Saroven\Reportify\Traits\HasReportify;

class UserController extends Controller implements Reportable
{
    use HasReportify;

    public function index(Request $request)
    {
        if ($request->has('export')) {
            $view = in_array($request->get('export'), ['pdfStream', 'pdf', 'pdfChunk']) ? 'reports.users-pdf' : null;

            $additionalData = [];
            if ($request->filled('header_margin')) {
                $additionalData['headerMargin'] = (int) $request->input('header_margin');
            }
            if ($request->filled('additional_header_margin')) {
                $additionalData['additionalHeaderMargin'] = (int) $request->input('additional_header_margin');
            }

            return $this->exportReport(
                $request,
                'User Directory Report',
                view: $view,
                additionalData: $additionalData,
                dataProvider: \App\Exports\UserExport::class
            );
        }

        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('department')) {
            $query->where('department', $request->input('department'));
        }

        $users = $query->latest('id')->paginate(10)->withQueryString();

        $stats = [
            'total' => User::count(),
            'active' => User::where('status', 'Active')->count(),
            'admins' => User::where('role', 'Admin')->count(),
            'departments' => User::distinct('department')->count('department'),
        ];

        $roles = ['Admin', 'Manager', 'Developer', 'Designer', 'Analyst', 'User'];
        $statuses = ['Active', 'Inactive', 'Pending'];
        $departments = ['Engineering', 'Sales', 'Marketing', 'Human Resources', 'Finance', 'Product', 'Support'];

        return view('users.index', compact('users', 'stats', 'roles', 'statuses', 'departments'));
    }

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
