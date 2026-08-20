<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Manager - Reportify Demo</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

    <div class="container py-4">
        <!-- Navigation Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Download Manager</h1>

            <div class="btn-group">
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-users me-1"></i>User List
                </a>
                <a href="{{ route('downloads.index') }}" class="btn btn-secondary active">
                    <i class="fas fa-download me-1"></i>Downloads
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Downloads Table Card -->
        <div class="card">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Report Title</th>
                            <th>Format</th>
                            <th>Status</th>
                            <th>File Path</th>
                            <th>Generated At</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($downloads as $index => $download)
                            <tr>
                                <td>{{ $downloads->firstItem() + $index }}</td>
                                <td><strong>{{ $download->title }}</strong></td>
                                <td>
                                    <span class="badge bg-secondary">{{ $download->format }}</span>
                                </td>
                                <td>
                                    @if($download->status === 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($download->status === 'failed')
                                        <span class="badge bg-danger" title="{{ $download->error }}">Failed</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Processing</span>
                                    @endif
                                </td>
                                <td class="text-muted small">
                                    <code>{{ $download->file_path ?? '-' }}</code>
                                </td>
                                <td class="text-muted small">
                                    {{ $download->created_at ? $download->created_at->format('Y-m-d H:i:s') : '-' }}
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        @if($download->status === 'completed' && $download->file_path)
                                            <a href="{{ route('downloads.download', $download) }}" class="btn btn-success">
                                                <i class="fas fa-download me-1"></i>Download
                                            </a>
                                        @endif
                                        <form method="POST" action="{{ route('downloads.destroy', $download) }}" onsubmit="return confirm('Delete this download record?')" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No download records yet. Export a report to see background export files here.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($downloads->hasPages())
                <div class="card-footer bg-white">
                    {{ $downloads->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
