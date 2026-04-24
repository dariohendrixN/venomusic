<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $profile->display_name ?? 'Profilo pubblico' }}
        </h2>
    </x-slot>

    <div class="container py-4">

        {{-- header profilo --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center gap-4">
                    @if ($profile->profile_image)
                        <img src="{{ asset('storage/' . $profile->profile_image) }}" alt="Immagine profilo"
                            class="img-thumbnail" style="max-width: 220px;">
                    @endif

                    <div>
                        <h3>{{ $profile->display_name }}</h3>

                        <p class="mb-1">
                            {{ trim(($profile->name ?? '') . ' ' . ($profile->surname ?? '')) }}
                        </p>

                        <p class="text-muted mb-2">
                            {{ collect([$profile->city, $profile->province, $profile->region])->filter()->implode(', ') }}
                        </p>

                        <div>
                            @php
                                $activeRoles = $profile->user->roles
                                    ->whereIn('pivot.status', ['auto_approved', 'manually_approved'])
                                    ->unique('id');
                            @endphp

                            {{-- @forelse($activeRoles as $role)
                                <span class="badge bg-success me-1">{{ $role->name }}</span>
                            @empty
                                <span class="badge bg-secondary">observer</span>
                            @endforelse --}}

                            @foreach ($profile->user->visibleActiveRoleNames() as $role)
                                <span class="badge bg-primary me-1">{{ $role->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- generi --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header">Generi</div>
            <div class="card-body">
                @forelse($profile->genres as $genre)
                    <span class="badge bg-primary me-1 mb-1">{{ $genre->name }}</span>
                @empty
                    <p class="text-muted mb-0">Nessun genere associato.</p>
                @endforelse
            </div>
        </div>

        {{-- collaborazioni --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header">Collaborazioni</div>
            <div class="card-body d-flex flex-wrap">
                @forelse($acceptedCollaborations as $collaboration)
                    @php
                        $otherProfile =
                            $collaboration->profile_id === $profile->id
                                ? $collaboration->collaborator
                                : $collaboration->profile;
                    @endphp
                    <div class="border-end p-3 mb-1 w-25">
                        <h5 class="card-title bg-primary-subtle p-2 text-center rounded">{{ $collaboration->project_title ?: 'Collaborazione senza titolo' }}</h5>


                        <div class="small text-center text-muted mb-3">
                            Tipo: {{ $collaboration->collaboration_type }}
                        </div>

                        <div class="mt-2">
                            Con: 
                            <strong class="text-primary-emphasis text-decoration-underline text-center">
                                <a href="{{ route('profiles.show', $otherProfile) }}">
                                {{ $otherProfile->display_name ?? 'Profilo non disponibile' }}
                            </a>
                            </strong>
                        </div>

                        @if ($collaboration->notes)
                            <div class="mt-2">
                                <strong>
                                Note: {{ $collaboration->notes }}
                            </strong>
                            </div>
                        @endif

                        <div class="card-footer bg-body-tertiary mt-2 small text-muted">
                           Periodo:
                           <strong>
                            {{ $collaboration->started_at ?? 'N/D' }} 
                                
                            </strong>
                            <br>
                            Termine:
                            <strong>
                                {{ $collaboration->ended_at ?? '(in corso)' }}
                            </strong>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Nessuna collaborazione.</p>
                @endforelse
            </div>
        </div>

        {{-- link piattaforme --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header">Piattaforme</div>
            <div class="card-body">
                @php
                    $links = [
                        'Qobuz' => $profile->qobuz_url,
                        'Bandcamp' => $profile->bandcamp_url,
                        'Deezer' => $profile->deezer_url,
                        'SoundCloud' => $profile->soundcloud_url,
                        'Amazon Music' => $profile->amazon_music_url,
                        'YouTube Music' => $profile->youtube_music_url,
                        'Apple Music' => $profile->apple_music_url,
                        'Spotify' => $profile->spotify_url,
                    ];
                @endphp

                @php $hasLinks = collect($links)->filter()->isNotEmpty(); @endphp

                @if ($hasLinks)
                    @foreach ($links as $label => $url)
                        @if ($url)
                            <a href="{{ $url }}" target="_blank"
                                class="btn btn-outline-primary btn-sm me-2 mb-2">
                                {{ $label }}
                            </a>
                        @endif
                    @endforeach
                @else
                    <p class="text-muted mb-0">Nessun link disponibile.</p>
                @endif
            </div>
        </div>

        {{-- brani --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header">Brani</div>
            <div class="card-body">
                @forelse($profile->tracks as $track)
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">{{ $track->title }}</h5>

                            <p class="mb-2">
                                <strong>Genere:</strong> {{ $track->genre?->name ?? 'Nessuno' }}
                            </p>

                            <audio controls class="w-100">
                                <source src="{{ asset('storage/' . $track->audio_path) }}">
                                Il tuo browser non supporta l'audio.
                            </audio>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Nessun brano disponibile.</p>
                @endforelse
            </div>
        </div>

        {{-- gallery --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header">Gallery</div>
            <div class="card-body">
                @if ($profile->images->isNotEmpty())
                    <div id="publicProfileGalleryCarousel" class="carousel slide" data-bs-ride="false">
                        <div class="carousel-inner">
                            @foreach ($profile->images as $image)
                                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                    <img src="{{ asset('storage/' . $image->path) }}" class="d-block w-100 rounded"
                                        alt="Gallery image {{ $loop->iteration }}"
                                        style="height: 420px; object-fit: cover;">
                                </div>
                            @endforeach
                        </div>

                        @if ($profile->images->count() > 1)
                            <button class="carousel-control-prev" type="button"
                                data-bs-target="#publicProfileGalleryCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Precedente</span>
                            </button>

                            <button class="carousel-control-next" type="button"
                                data-bs-target="#publicProfileGalleryCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Successiva</span>
                            </button>
                        @endif
                    </div>
                @else
                    <p class="text-muted mb-0">Nessuna immagine in gallery.</p>
                @endif
            </div>
        </div>

        {{-- form richiesta --}}
        @auth
            @if (auth()->user()->profile && auth()->user()->profile->id !== $profile->id)
                @if (!empty($allowedRequestTypes))
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">Invia richiesta</div>

                        <div class="card-body">
                            @if (session('status') === 'request-sent')
                                <div class="alert alert-success">
                                    Richiesta inviata correttamente.
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('profile.requests.store') }}">
                                @csrf

                                <input type="hidden" name="receiver_profile_id" value="{{ $profile->id }}">

                                <div class="mb-3">
                                    <label for="request_type" class="form-label">Tipo richiesta</label>
                                    <select name="request_type" id="request_type" class="form-control">
                                        @foreach ($allowedRequestTypes as $type)
                                            <option value="{{ $type }}">
                                                @switch($type)
                                                    @case('booking')
                                                        Prenotazione
                                                    @break

                                                    @case('collaboration')
                                                        Collaborazione
                                                    @break

                                                    @case('live-candidacy')
                                                        Candidatura live
                                                    @break

                                                    @case('roster-proposal')
                                                        Proposta roster
                                                    @break

                                                    @default
                                                        {{ $type }}
                                                @endswitch
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('request_type')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="subject" class="form-label">Oggetto</label>
                                    <input type="text" name="subject" id="subject" class="form-control">
                                    @error('subject')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="message" class="form-label">Messaggio</label>
                                    <textarea name="message" id="message" class="form-control" rows="4"></textarea>
                                    @error('message')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="requested_date" class="form-label">Data proposta</label>
                                    <input type="datetime-local" name="requested_date" id="requested_date"
                                        class="form-control">
                                    @error('requested_date')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    Invia richiesta
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <p class="text-muted mb-0">
                                Non puoi inviare richieste a questo profilo con i ruoli attualmente attivi.
                            </p>
                        </div>
                    </div>
                @endif
            @endif
        @endauth
    </div>
</x-app-layout>
