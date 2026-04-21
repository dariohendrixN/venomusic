<x-app-layout>

    <body>
        <div class="container card my-5 border-none">
            <h1 class="my-3 card-title">Richieste ruolo in lista d'attesa</h1>

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

                    @forelse($pendingUsers as $user)

                        @foreach ($user->roles->where('pivot.status', 'pending') as $role)
                            <tr>
                                <td>{{ $user->fullName() }} </td>
                                <td>{{ $user->profile->display_name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>


                                    @foreach ($user->roles->whereIn('pivot.status', ['manually_approved', 'auto_approved'])->unique('id') as $activeRole)
                                        <span class="badge bg-success">
                                            {{__('roles.' . $activeRole->name) }}
                                        </span>
                                    @endforeach

                                    {{-- @forelse($user->visibleActiveRoleNames() as $role)
                                        <span class="badge bg-primary me-1">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="badge bg-secondary">observer</span>
                                    @endforelse --}}
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        {{__('roles.' . $role->name) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark">
                                        {{ $role->pivot->status }}
                                    </span>
                                </td>
                                <td>
                                    <span class="d-flex justify-content-around">
                                        <form method="POST"
                                            action="{{ route('admin.role-requests.approve', [$user->id, $role->name]) }}">
                                            @csrf
                                            <button class="btn btn-success btn-sm mx-1">
                                                Approva
                                            </button>
                                        </form>
                                        <form method="POST"
                                            action="{{ route('admin.role-requests.reject', [$user->id, $role->name]) }}">
                                            @csrf
                                            <button class="btn btn-danger btn-sm mx-1">
                                                Rifiuta
                                            </button>
                                        </form>
                                    </span>
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

            <div class="container card my-5 border-none">
                <h1 class="my-3 card-title">Utenti registrati</h1>
                <div class="card-body table-responsive ps-0">

                    <table class="table table-striped table-bordered shadow-sm align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Full name</th>
                                <th>Display name</th>
                                <th>Email</th>
                                <th>Ruoli attivi</th>
                                <th>Collaborazioni</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($registeredUsers as $user)
                                <tr>
                                    <td>
                                        {{ trim(($user->profile->name ?? '') . ' ' . ($user->profile->surname ?? '')) ?: '-' }}
                                    </td>

                                    <td>
                                        {{ $user->profile->display_name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $user->email }}
                                    </td>

                                    <td>
                                        @php
                                            $activeRoles = $user->roles
                                                ->whereIn('pivot.status', ['auto_approved', 'manually_approved'])
                                                ->unique('id');
                                        @endphp

                                        @forelse($user->visibleActiveRoleNames() as $role)
                                            <span class="badge bg-primary me-1">
                                                {{__('roles.' . $role->name) }}
                                            </span>
                                        @empty
                                            <span class="badge bg-secondary">observer</span>
                                        @endforelse

                                        {{-- @forelse($activeRoles as $role)
                                            <span class="badge bg-success me-1">
                                                {{ $role->name }}
                                            </span>
                                        @empty
                                            <span class="badge bg-secondary">
                                                observer
                                            </span>
                                        @endforelse --}}

                                    </td>

                                    <td>
                                        {{ $user->profile?->collaborations?->count() ?? 0 }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">
                                        Nessun utente registrato
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </body>
    </x-app-layout>
