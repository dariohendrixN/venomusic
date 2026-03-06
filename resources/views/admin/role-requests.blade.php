<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Richieste Ruolo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="container mt-5">

    <h1 class="mb-4">Richieste ruolo in lista d'attesa</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @forelse($users as $user)
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">{{ $user->name }} ({{ $user->email }})</h5>

                <ul class="list-group list-group-flush mt-3">
                    @foreach($user->roles as $role)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $role->name }}</strong>
                                <span class="badge bg-warning text-dark ms-2">
                                    {{ $role->pivot->status }}
                                </span>
                            </div>

                            <div class="d-flex gap-2">
                                <form method="POST" action="{{ route('admin.role-requests.approve', [$user->id, $role->name]) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        Approva
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.role-requests.reject', [$user->id, $role->name]) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        Rifiuta
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            Nessuna richiesta pendente.
        </div>
    @endforelse

</div>
</body>
</html>