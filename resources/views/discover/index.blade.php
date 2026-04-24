{{-- <!doctype html>
<html lang="it">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Discover</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head> --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Scopri artisti e professionisti
        </h2>
    </x-slot>

    <body>
        <form method="GET" action="{{ route('discover.search') }}" class="row g-3 mb-4">


                <div class="card-body text-center">
                    <div class="container  d-flex flex-column align-items-center justify-content-center min-vh-50">

                        <ul class="list-group list-group-horizontal-xl">
                            <li class="list-group-item col-md-auto">
                                <div>
                                    <label for="profile_search" class="form-label">Profilo</label>
                                    <input type="text" name="profile_search" id="profile_search" class="form-control"
                                        placeholder="Nome, cognome o alias" value="{{ request('profile_search', '') }}">
                                </div>
                            </li>
                            <li class="list-group-item col-md-auto">
                                <div>
                                    <label for="location_search" class="form-label">Località</label>
                                    <input type="text" name="location_search" id="location_search"
                                        class="form-control" placeholder="Città, provincia o regione"
                                        value="{{ request('location_search', '') }}">
                                </div>
                            </li>

                        </ul>

                        <ul class="list-group list-group-horizontal-xl">
                            <li class="list-group-item col-md-auto">
                                <div>
                                    <label>Ruolo</label>
                                    <select name="role" class="form-control">
                                        <option value="">-- tutti --</option>
                                        <option value="artist" {{ request('role') === 'artist' ? 'selected' : '' }}>
                                            Artista
                                        </option>
                                        <option value="producer" {{ request('role') === 'producer' ? 'selected' : '' }}>
                                            Producer</option>
                                        <option value="studio" {{ request('role') === 'studio' ? 'selected' : '' }}>
                                            Studo di
                                            Regist.
                                        </option>
                                        <option value="venue" {{ request('role') === 'venue' ? 'selected' : '' }}>
                                            Locale
                                        </option>
                                        <option value="label" {{ request('role') === 'label' ? 'selected' : '' }}>
                                            Etichetta Disc.
                                        </option>
                                    </select>
                                </div>
                            </li>
                            <li class="list-group-item col-md-auto">
                                <div>
                                    <label for="genre-search" class="form-label">Genere</label>
                                    <input type="text" id="genre-search" class="form-control"
                                        placeholder="Cerca genere..." value="{{ request('genre_name', '') }}">

                                    <input type="hidden" name="genre_id" id="genre-id"
                                        value="{{ request('genre_id', '') }}">
                                    <input type="hidden" name="genre_name" id="genre-name"
                                        value="{{ request('genre_name', '') }}">

                                    <div id="genre-suggestions" class="list-group mt-2"></div>
                                </div>
                            </li>
                        </ul>

                    </div>

                    <div class="container  d-flex flex-column align-items-center justify-content-center min-vh-30">
                        <button class="btn btn-primary my-4 px-4">
                            Cerca
                        </button>
                    </div>




                    <div class="card-footer text-body-secondary">
                        @isset($profiles)
                            <h4 class="mb-3">Risultati</h4>

                            @forelse($profiles as $profile)
                                <div class="card mb-3 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <a href="{{ route('profiles.show', $profile) }}" class="btn btn-outline-secondary btn-sm mt-2">
                                                {{ $profile->display_name }}
                                            </a>
                                        </h5>

                                        <p class="mb-2">
                                            <strong>Nome:</strong>
                                            {{ trim(($profile->name ?? '') . ' ' . ($profile->surname ?? '')) }}
                                        </p>

                                        <p class="mb-2">
                                            <strong>Località:</strong>
                                            {{ $profile->city ?? '-' }}
                                            @if ($profile->province)
                                                , {{ $profile->province }}
                                            @endif
                                            @if ($profile->region)
                                                , {{ $profile->region }}
                                            @endif
                                        </p>

                                        <div class="mb-2">
                                            <strong>Professioni:</strong>
                                            @foreach ($profile->user->visibleActiveRoleNames() as $role)
                                                <span class="badge bg-success">
                                                    {{__('roles.' . $role->name) }}
                                                </span>
                                            @endforeach
                                        </div>

                                        <div>
                                            <strong>Generi:</strong>
                                            @foreach ($profile->genres as $genre)
                                                <span class="badge bg-primary">
                                                    {{ $genre->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p>Nessun profilo trovato.</p>
                            @endforelse
                        @endisset
                    </div>
                </div>

                {{-- scripts --}}

                {{-- search auto complete --}}
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const input = document.getElementById('genre-search');
                        const box = document.getElementById('genre-suggestions');
                        const hiddenId = document.getElementById('genre-id');
                        const hiddenName = document.getElementById('genre-name');

                        if (!input || !box || !hiddenId || !hiddenName) return;

                        input.addEventListener('keyup', async () => {
                            const q = input.value.trim();

                            hiddenId.value = '';
                            hiddenName.value = q;

                            if (q.length < 2) {
                                box.innerHTML = '';
                                return;
                            }

                            try {
                                const res = await fetch(`/genres/search?q=${encodeURIComponent(q)}`);
                                const data = await res.json();

                                box.innerHTML = '';

                                data.forEach(g => {
                                    const item = document.createElement('button');
                                    item.type = 'button';
                                    item.classList.add('list-group-item', 'list-group-item-action');
                                    item.innerText = g.name;

                                    item.addEventListener('click', () => {
                                        input.value = g.name;
                                        hiddenId.value = g.id;
                                        hiddenName.value = g.name;
                                        box.innerHTML = '';
                                    });

                                    box.appendChild(item);
                                });
                            } catch (error) {
                                console.error('Discover genre search error:', error);
                            }
                        });
                    });
                </script>

                {{-- filtro by activeRoles per roles type select --}}

                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const collaboratorInput = document.getElementById('collaborator-search');
                        const collaboratorHidden = document.getElementById('collaborator_profile_id');
                        const collaboratorBox = document.getElementById('collaborator-suggestions');
                        const collaborationTypeSelect = document.getElementById('collaboration_type');
                
                        if (!collaboratorInput || !collaboratorHidden || !collaboratorBox || !collaborationTypeSelect) return;
                
                        const currentUserRoles = @json(auth()->user()->visibleActiveRoles()->pluck('name')->values());
                
                        function normalizeRoles(roles) {
                            return Array.isArray(roles) ? roles : [];
                        }
                
                        function getAllowedCollaborationTypes(myRoles, collaboratorRoles) {
                            myRoles = normalizeRoles(myRoles);
                            collaboratorRoles = normalizeRoles(collaboratorRoles);
                
                            const hasMe = (role) => myRoles.includes(role);
                            const hasOther = (role) => collaboratorRoles.includes(role);
                
                            const allowed = new Set();
                
                            if (hasOther('label')) {
                                allowed.add('label-support');
                            }
                
                            if (hasMe('label') && (hasOther('artist') || hasOther('producer'))) {
                                allowed.add('label-support');
                            }
                
                            if (hasMe('producer') && hasOther('producer')) {
                                allowed.add('production');
                                allowed.add('co-production');
                                allowed.add('remix');
                            }
                
                            if (hasMe('artist') && hasOther('artist')) {
                                allowed.add('featuring');
                                allowed.add('songwriting');
                                allowed.add('remix');
                            }
                
                            const artistProducerPair =
                                (hasMe('artist') && hasOther('producer')) ||
                                (hasMe('producer') && hasOther('artist'));
                
                            if (artistProducerPair) {
                                allowed.add('featuring');
                                allowed.add('production');
                                allowed.add('songwriting');
                                allowed.add('remix');
                            }
                
                            return Array.from(allowed);
                        }
                
                        function collaborationTypeLabel(type) {
                            switch (type) {
                                case 'featuring': return 'Featuring';
                                case 'production': return 'Produzione';
                                case 'co-production': return 'Co-produzione';
                                case 'label-support': return 'Supporto etichetta';
                                case 'songwriting': return 'Songwriting';
                                case 'remix': return 'Remix';
                                default: return type;
                            }
                        }
                
                        function renderCollaborationTypeOptions(allowedTypes) {
                            collaborationTypeSelect.innerHTML = '';
                
                            if (!allowedTypes.length) {
                                const option = document.createElement('option');
                                option.value = '';
                                option.textContent = 'Nessun tipo disponibile per questa combinazione di ruoli';
                                collaborationTypeSelect.appendChild(option);
                                return;
                            }
                
                            const placeholder = document.createElement('option');
                            placeholder.value = '';
                            placeholder.textContent = 'Seleziona tipo collaborazione';
                            collaborationTypeSelect.appendChild(placeholder);
                
                            allowedTypes.forEach(type => {
                                const option = document.createElement('option');
                                option.value = type;
                                option.textContent = collaborationTypeLabel(type);
                                collaborationTypeSelect.appendChild(option);
                            });
                        }
                
                        async function loadCollaborators(query = '') {
                            try {
                                const res = await fetch(`/profile/collaborators/search?q=${encodeURIComponent(query)}`);
                                const data = await res.json();
                
                                collaboratorBox.innerHTML = '';
                
                                data.forEach(profile => {
                                    const item = document.createElement('button');
                                    item.type = 'button';
                                    item.classList.add('list-group-item', 'list-group-item-action');
                
                                    const location = [profile.city, profile.province, profile.region]
                                        .filter(Boolean)
                                        .join(', ');
                
                                    item.innerHTML = `
                                        <div><strong>${profile.display_name ?? 'Profilo'}</strong></div>
                                        <div class="small text-muted">
                                            ${profile.full_name || 'Nome non disponibile'}
                                            ${location ? ' · ' + location : ''}
                                        </div>
                                    `;
                
                                    item.addEventListener('click', () => {
                                        collaboratorInput.value = profile.display_name ?? profile.full_name ?? 'Profilo selezionato';
                                        collaboratorHidden.value = profile.id;
                                        collaboratorBox.innerHTML = '';
                
                                        const allowedTypes = getAllowedCollaborationTypes(currentUserRoles, profile.roles || []);
                                        renderCollaborationTypeOptions(allowedTypes);
                                    });
                
                                    collaboratorBox.appendChild(item);
                                });
                            } catch (error) {
                                console.error('Collaborator search error:', error);
                            }
                        }
                
                        renderCollaborationTypeOptions([]);
                
                        loadCollaborators('');
                
                        collaboratorInput.addEventListener('focus', () => {
                            if (collaboratorInput.value.trim() === '') {
                                loadCollaborators('');
                            }
                        });
                
                        collaboratorInput.addEventListener('keyup', () => {
                            collaboratorHidden.value = '';
                            renderCollaborationTypeOptions([]);
                            loadCollaborators(collaboratorInput.value.trim());
                        });
                    });
                </script>
    </body>
</x-app-layout>
