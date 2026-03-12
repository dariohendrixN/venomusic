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

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <table class="table table-striped table-bordered shadow-sm">

            <thead class="table-dark">
                <tr>
                    <th>Utente</th>
                    <th>Alias</th>
                    <th>Email</th>
                    <th>Ruolo/i attivo/i</th>
                    <th>Ruolo richiesto</th>
                    <th>Status</th>
                    <th>Azione</th>
                </tr>
            </thead>

            <tbody>

                @forelse($users as $user)

                    @foreach($user->roles->where('pivot.status','pending') as $role)
                        <tr>
                            <td>{{ $user->fullName() }} </td>
                            <td>{{ $user->profile->display_name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                
                                @foreach($user->roles->whereIn('pivot.status',['manually_approved', 'auto_approved'])->unique('id') as $activeRole)
                                
                                <span class="badge bg-success">
                                    {{ $activeRole->name }}
                                </span>
                                
                                @endforeach
                                
                            </td>
                            <td>
                                <span class="badge bg-primary">
                                    {{ $role->name }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-warning text-dark">
                                    {{ $role->pivot->status }}
                                </span>
                            </td>
                            <td class="d-flex gap-2">
                                <form method="POST"
                                    action="{{ route('admin.role-requests.approve', [$user->id, $role->name]) }}">
                                    @csrf
                                    <button class="btn btn-success btn-sm">
                                        Approva
                                    </button>
                                </form>
                                <form method="POST"
                                    action="{{ route('admin.role-requests.reject', [$user->id, $role->name]) }}">
                                    @csrf
                                    <button class="btn btn-danger btn-sm">
                                        Rifiuta
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            Nessuna richiesta pendente
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
