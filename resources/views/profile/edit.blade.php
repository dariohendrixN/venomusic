<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Profilo
        </h2>
    </x-slot>

    @if (session('status') === 'profile-updated')
        <div class="alert alert-success">
            Profilo aggiornato correttamente.
        </div>
    @endif

    @if (session('status') === 'gallery-updated')
        <div class="alert alert-success">
            Gallery aggiornata correttamente.
        </div>
    @endif

    @if (session('status') === 'gallery-image-deleted')
        <div class="alert alert-success">
            Immagine rimossa correttamente.
        </div>
    @endif

    @if (session('status') === 'track-uploaded')
        <div class="alert alert-success">
            Brano caricato correttamente.
        </div>
    @endif

    @if (session('status') === 'track-deleted')
        <div class="alert alert-success">
            Brano eliminato correttamente.
        </div>
    @endif

    @unless (auth()->user()->canUploadMedia())
        <div class="alert alert-info mt-4">
            Le funzionalità foto profilo sono disponibili solo per profili professionali approvati.
        </div>
    @endunless

    <div class="col-md-4 d-flex borde-none">
        @if ($profile->profile_image)
            <img src="{{ asset('storage/' . $profile->profile_image) }}" alt="Immagine profilo"
                class="img-thumbnail border-none" style="max-width: 300px;">
        @else
            <div class="card-img-overlay">
                <p class="text-muted ms-3">Nessuna immagine caricata</p>
            </div>
        @endif

        <h3 class="card-header my-4 ms-7">
            <label class="form-label">{{ Auth::user()->profile->display_name }}</label><br>
        </h3>
    </div>


    @unless (auth()->user()->canUploadMedia())
        <div class="alert alert-info mt-4">
            Le funzionalità media gallery sono disponibili solo per profili professionali approvati.
        </div>
    @endunless

    @if ($profile->images->isNotEmpty())
        <div id="profileGalleryCarousel" class="carousel slide mt-4 mx-16" data-bs-ride="false">
            <div class="carousel-inner">
                @foreach ($profile->images as $image)
                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                        <img src="{{ asset('storage/' . $image->path) }}" class="d-block w-100 rounded"
                            alt="Gallery image {{ $loop->iteration }}" style="height: 420px; object-fit: cover;">
                    </div>
                @endforeach
            </div>

            @if ($profile->images->count() > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#profileGalleryCarousel"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Precedente</span>
                </button>

                <button class="carousel-control-next" type="button" data-bs-target="#profileGalleryCarousel"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Successiva</span>
                </button>
            @endif
        </div>
    @else
        <p class="text-muted mt-4">Nessuna immagine nella gallery.</p>
    @endif

    <div class="card mt-4 shadow-sm mx-8">
        <h5 class="card-header">Mi trovi su..</h5>
        <div class="card-body">
            @if (
                $profile->qobuz_url ||
                    $profile->bandcamp_url ||
                    $profile->deezer_url ||
                    $profile->soundcloud_url ||
                    $profile->amazon_music_url ||
                    $profile->youtube_music_url ||
                    $profile->apple_music_url ||
                    $profile->spotify_url)

                @if ($profile->qobuz_url)
                    <a href="{{ $profile->qobuz_url }}" target="_blank"
                        class="btn btn-primary btn-sm mb-2">Qobuz</a><br>
                @endif

                @if ($profile->bandcamp_url)
                    <a href="{{ $profile->bandcamp_url }}" target="_blank"
                        class="btn btn-primary btn-sm mb-2">Bandcamp</a><br>
                @endif

                @if ($profile->deezer_url)
                    <a href="{{ $profile->deezer_url }}" target="_blank"
                        class="btn btn-primary btn-sm mb-2">Deezer</a><br>
                @endif

                @if ($profile->soundcloud_url)
                    <a href="{{ $profile->soundcloud_url }}" target="_blank"
                        class="btn btn-primary btn-sm mb-2">Soundcloud</a><br>
                @endif

                @if ($profile->amazon_music_url)
                    <a href="{{ $profile->amazon_music_url }}" target="_blank"
                        class="btn btn-primary btn-sm mb-2">Amazon Music</a><br>
                @endif

                @if ($profile->youtube_music_url)
                    <a href="{{ $profile->youtube_music_url }}" target="_blank"
                        class="btn btn-primary btn-sm mb-2">Youtube Music</a><br>
                @endif

                @if ($profile->apple_music_url)
                    <a href="{{ $profile->apple_music_url }}" target="_blank" class="btn btn-primary btn-sm mb-2">Apple
                        Music</a><br>
                @endif

                @if ($profile->spotify_url)
                    <a href="{{ $profile->spotify_url }}" target="_blank"
                        class="btn btn-primary btn-sm mb-2">Spotify</a>
                @endif
            @endif
        </div>

        <div class="card-footer mt-3">
            <div class="card-title">
                Collaborazioni attive / storiche
            </div>
        
            <div class="card-body">
                @forelse($profile->collaborations as $collaboration)
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">
                                {{ $collaboration->project_title ?: 'Collaborazione senza titolo' }}
                            </h5>
        
                            <p class="mb-2">
                                <strong>Tipo:</strong> {{ $collaboration->collaboration_type }}
                            </p>
        
                            <p class="mb-2">
                                <strong>Collaboratore:</strong>
                                {{ $collaboration->collaborator->display_name ?? 'Profilo non disponibile' }}
                            </p>
        
                            @if($collaboration->notes)
                                <p class="mb-2">
                                    <strong>Note:</strong> {{ $collaboration->notes }}
                                </p>
                            @endif
        
                            <p class="mb-2">
                                <strong>Periodo:</strong>
                                {{ $collaboration->started_at ?? 'N/D' }}
                                -
                                {{ $collaboration->ended_at ?? 'In corso' }}
                            </p>
        
                            <form method="POST" action="{{ route('profile.collaborations.destroy', $collaboration) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    Elimina collaborazione
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Nessuna collaborazione registrata.</p>
                @endforelse
            </div>
        </div>
    </div>

    @unless (auth()->user()->canUploadTracks())
        <div class="alert alert-info mt-4">
            Il caricamento brani è disponibile solo per profili professionali approvati.
        </div>
    @endunless

    <div class="card mt-4 shadow-sm mx-8">
        <div class="card-header">
            Brani
        </div>

        <div class="card-body">
            @forelse($profile->tracks as $track)
                <div class="card my-4 mx-10 border-none">
                    <div class="card-body bg-dark text-white rounded">
                        <h5 class="card-title">{{ $track->title }}</h5>

                        <p class="mb-2">
                            <strong>Genere:</strong>
                            {{ $track->genre?->name ?? 'Nessuno' }}
                        </p>

                        <audio controls class="w-100 mb-3">
                            <source src="{{ asset('storage/' . $track->audio_path) }}">
                            Il tuo browser non supporta l'audio.
                        </audio>

                        <form method="POST" action="{{ route('profile.tracks.destroy', $track) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                Elimina brano
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-muted mb-0">Nessun brano caricato.</p>
            @endforelse
        </div>
    </div>

    <div class="card mt-4 shadow-sm">

        <div class="card-header">
            Gestione del profilo
        </div>

        <div class="card-body">

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="container">
                    <div class="row d-flex justify-content-center">
                        @if (auth()->user()->canUploadMedia())
                            <div class="col-8">
                                <div class="card mb-3 border-none border-bottom">
                                    <div class="col-md-4 d-flex borde-none">
                                        @if ($profile->profile_image)
                                            <img src="{{ asset('storage/' . $profile->profile_image) }}"
                                                alt="Immagine profilo" class="img-thumbnail border-none"
                                                style="max-width: 300px;">
                                        @else
                                            <div class="card-img-overlay">
                                                <p class="text-muted ms-3">Nessuna immagine caricata</p>
                                            </div>
                                        @endif

                                        <h3 class="card-title my-4 ms-7">
                                            <label class="form-label"> Immagine profilo attuale</label><br>
                                        </h3>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card-body">

                                            <label for="profile_image" class="form-label text-center">Carica nuova
                                                immagine
                                                profilo</label>

                                            <input type="file" class="form-control" id="profile_image"
                                                name="profile_image" accept=".jpg,.jpeg,.png,.webp">
                                            <p class="card-text">
                                                @error('profile_image')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif


                        <div class="card shadow-sm px-0 w-50">
                            <details>
                                <summary class="card-header" style="cursor: pointer;">
                                    Modifica profilo
                                </summary>


                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nome</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ old('name', $user->profile->name ?? '') }}">
                                        @error('name')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="surname" class="form-label">Cognome</label>
                                        <input type="text" class="form-control" id="surname" name="surname"
                                            value="{{ old('surname', $user->profile->surname) }}">
                                        @error('surname')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ old('email', $user->email) }}">
                                        @error('email')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="display_name" class="form-label">Alias</label>
                                        <input type="text" class="form-control" id="display_name"
                                            name="display_name"
                                            value="{{ old('display_name', $profile->display_name) }}">
                                        @error('display_name')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <hr class="my-4">

                                    <div class="mb-3">
                                        <label for="address" class="form-label">Indirizzo</label>
                                        <input type="text" class="form-control" id="address" name="address"
                                            value="{{ old('address', $profile->address) }}">
                                        @error('address')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="city" class="form-label">Città</label>
                                        <input type="text" class="form-control" id="city" name="city"
                                            value="{{ old('city', $profile->city) }}">
                                        @error('city')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="province" class="form-label">Provincia</label>
                                        <input type="text" class="form-control" id="province" name="province"
                                            value="{{ old('province', $profile->province) }}">
                                        @error('province')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="region" class="form-label">Regione</label>
                                        <input type="text" class="form-control" id="region" name="region"
                                            value="{{ old('region', $profile->region) }}">
                                        @error('region')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Numero di telefono</label>
                                        <input type="text" class="form-control" id="phone" name="phone"
                                            value="{{ old('phone', $profile->phone) }}">
                                        @error('phone')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                            </details>
                        </div>
                        <div class ="d-flex justify-content-center mt-4">
                            <button type="submit" class="btn btn-success">
                                Salva modifiche
                            </button>
                        </div>
                    </div>
                </div>
            </form>



            <div class="container">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-auto">
                        <div class="card mt-4 shadow-sm mb-3">
                            <div class="card-body">
                                <h4 class="card-title">Generi musicali</h4>
                                <input type="text" id="genre-search" class="form-control w-50"
                                    placeholder="Cerca genere...">
                                <div id="genre-suggestions" class="list-group mt-2"></div>
                            </div>

                            <div class="card-footer text-body-secondary">

                                <strong>I tuoi generi preferiti:</strong>

                                @foreach (auth()->user()->profile->genres as $genre)
                                    <form method="POST" action="{{ route('profile.genres.destroy', $genre) }}"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <span
                                            class="badge bg-primary-subtle text-dark d-inline-flex align-items-center m-1">
                                            {{ $genre->name }}
                                            <button class="btn btn-sm bg-none">x</button>
                                        </span>
                                    </form>
                                @endforeach

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    @if (auth()->user()->canUploadTracks())
        <div class="container mt-4">
            <div class="row d-flex justify-content-center">
                <div class="col-12">
                    <div class="card mt-4 shadow-sm">
                        <div class="card-header">
                            Condividi i links delle tue piattaforme
                        </div>

                        <div class="card-body">
                            <form method="POST" action="{{ route('profile.links.update') }}">
                                @csrf
                                @method('PATCH')

                                <div class="container mt-4">
                                    <div class="row d-flex align-items-center">
                                        <div class="col-md-auto">
                                            <div class="card-group">

                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5 class="card-title">
                                                            <label class="form-label">
                                                                <a
                                                                    href="https://www.qobuz.com/it-it/music/streaming/offers">Qobuz</a>
                                                            </label>
                                                        </h5>
                                                        <input type="url" name="qobuz_url"
                                                            class="form-control mt-2"
                                                            value="{{ old('qobuz_url', $profile->qobuz_url) }}">
                                                    </div>
                                                </div>

                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5 class="card-title">
                                                            <label class="form-label">
                                                                <a href="https://bandcamp.com/discover">Bandcamp</a>
                                                            </label>
                                                        </h5>
                                                        <input type="url" name="bandcamp_url"
                                                            class="form-control"
                                                            value="{{ old('bandcamp_url', $profile->bandcamp_url) }}">
                                                    </div>
                                                </div>

                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5 class="card-title">
                                                            <label class="form-label">
                                                                <a
                                                                    href="https://www.deezer.com/it/channels/explore">Deezer</a>
                                                            </label>
                                                        </h5>
                                                        <input type="url" name="deezer_url" class="form-control"
                                                            value="{{ old('deezer_url', $profile->deezer_url) }}">
                                                    </div>
                                                </div>

                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5 class="card-title">
                                                            <label class="form-label">
                                                                <a href="https://soundcloud.com">SoundCloud</a>
                                                            </label>
                                                        </h5>
                                                        <input type="url" name="soundcloud_url"
                                                            class="form-control"
                                                            value="{{ old('soundcloud_url', $profile->soundcloud_url) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row d-flex align-items-center mt-3">
                                            <div class="col-md-auto">
                                                <div class="card-group">

                                                    <div class="card">
                                                        <div class="card-body">
                                                            <h5 class="card-title">
                                                                <label class="form-label">
                                                                    <a href="https://music.amazon.it/">Amazon Music</a>
                                                                </label>
                                                            </h5>
                                                            <input type="url" name="amazon_music_url"
                                                                class="form-control my-2"
                                                                value="{{ old('amazon_music_url', $profile->amazon_music_url) }}">
                                                        </div>
                                                    </div>

                                                    <div class="card">
                                                        <div class="card-body">
                                                            <h5 class="card-title">
                                                                <label class="form-label">
                                                                    <a href="https://music.youtube.com/">YouTube
                                                                        Music</a>
                                                                </label>
                                                            </h5>
                                                            <input type="url" name="youtube_music_url"
                                                                class="form-control"
                                                                value="{{ old('youtube_music_url', $profile->youtube_music_url) }}">
                                                        </div>
                                                    </div>

                                                    <div class="card">
                                                        <div class="card-body">
                                                            <h5 class="card-title">
                                                                <label class="form-label">
                                                                    <a href="https://music.apple.com/it/new">Apple
                                                                        Music</a>
                                                                </label>
                                                            </h5>
                                                            <input type="url" name="apple_music_url"
                                                                class="form-control"
                                                                value="{{ old('apple_music_url', $profile->apple_music_url) }}">
                                                        </div>
                                                    </div>

                                                    <div class="card">
                                                        <div class="card-body">
                                                            <h5 class="card-title">
                                                                <label class="form-label">
                                                                    <a
                                                                        href="https://open.spotify.com/intl-it">Spotify</a>
                                                                </label>
                                                            </h5>
                                                            <input type="url" name="spotify_url"
                                                                class="form-control"
                                                                value="{{ old('spotify_url', $profile->spotify_url) }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 d-flex justify-content-center">
                                    <button type="submit" class="btn btn-success">
                                        Salva link piattaforme
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-8">
                    <div class="card mt-4 shadow-sm">
                        <div class="card-header">
                            Condividi i tuoi brani
                        </div>

                        <div class="card-body">

                            <form method="POST" action="{{ route('profile.tracks.store') }}"
                                enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label for="title" class="form-label">Titolo brano</label>
                                    <input type="text" class="form-control" id="title" name="title">
                                    @error('title')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="genre_id" class="form-label">Genere principale</label>
                                    <select name="genre_id" id="genre_id" class="form-control">
                                        <option value="">-- nessuno --</option>
                                        @foreach ($profile->genres as $genre)
                                            <option value="{{ $genre->id }}">{{ $genre->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('genre_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="audio_file" class="form-label">File audio</label>
                                    <input type="file" class="form-control" id="audio_file" name="audio_file"
                                        accept=".mp3,.wav,.ogg,.m4a">
                                    @error('audio_file')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    Carica brano
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif



    @if (auth()->user()->canUploadMedia())

        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-8">

                    <div class="card mt-4 shadow-sm">
                        <div class="card-header">
                            Carica nuove immagini nella gallery
                        </div>

                        <div class="card-body">


                            <form method="POST" action="{{ route('profile.images.store') }}"
                                enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label for="images" class="form-label">Carica immagini</label>
                                    <input type="file" class="form-control" id="images" name="images[]"
                                        accept=".jpg,.jpeg,.png,.webp" multiple>
                                    @error('images')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                    @error('images.*')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    Carica immagini
                                </button>
                            </form>


                            <details class="mt-4">
                                <summary class="card-header rounded" style="cursor: pointer;">
                                    Gestisci immagini gallery
                                </summary>

                                <div class="card card-body border-top-0">
                                    <div class="row">
                                        @foreach ($profile->images as $image)
                                            <div class="col-md-4 col-lg-3 mb-4">
                                                <div class="card shadow-sm h-100">
                                                    <img src="{{ asset('storage/' . $image->path) }}"
                                                        class="card-img-top img-fluid" alt="Gallery image"
                                                        style="height: 180px; object-fit: cover;">

                                                    <div class="card-body text-center">
                                                        <form method="POST"
                                                            action="{{ route('profile.images.destroy', $image) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                Elimina
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </details>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            Collaborazioni
        </div>
    
        <div class="card-body">
            @if (session('status') === 'collaboration-added')
                <div class="alert alert-success">
                    Collaborazione aggiunta correttamente.
                </div>
            @endif
    
            @if (session('status') === 'collaboration-deleted')
                <div class="alert alert-success">
                    Collaborazione eliminata correttamente.
                </div>
            @endif
    
            <form method="POST" action="{{ route('profile.collaborations.store') }}">
                @csrf
    
                <div class="mb-3">
                    <label for="collaborator-search" class="form-label">Cerca collaboratore</label>
                    
                    <input type="text" class="form-control" id="collaborator-search" placeholder="Alias, nome, cognome o località" autocomplete="off">
                
                    <input type="hidden" id="collaborator_profile_id" name="collaborator_profile_id">
                
                    <div id="collaborator-suggestions" class="list-group mt-2"></div>
                
                    @error('collaborator_profile_id')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
    
                <div class="mb-3">
                    <label for="collaboration_type" class="form-label">Tipo collaborazione</label>
                    <select name="collaboration_type" id="collaboration_type" class="form-control">
                        <option value="featuring">Featuring</option>
                        <option value="production">Produzione</option>
                        <option value="co-production">Co-produzione</option>
                        <option value="label-support">Supporto etichetta</option>
                        <option value="songwriting">Songwriting</option>
                        <option value="remix">Remix</option>
                    </select>
                </div>
    
                <div class="mb-3">
                    <label for="project_title" class="form-label">Titolo progetto</label>
                    <input type="text" class="form-control" id="project_title" name="project_title">
                </div>
    
                <div class="mb-3">
                    <label for="notes" class="form-label">Note</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>
    
                <div class="mb-3">
                    <label for="started_at" class="form-label">Inizio collaborazione</label>
                    <input type="date" class="form-control" id="started_at" name="started_at">
                </div>
    
                <div class="mb-3">
                    <label for="ended_at" class="form-label">Fine collaborazione</label>
                    <input type="date" class="form-control" id="ended_at" name="ended_at">
                </div>
    
                <button type="submit" class="btn btn-primary">
                    Aggiungi collaborazione
                </button>
            </form>
        </div>
    </div>

    {{-- scripts --}}
    <script>
        const input = document.getElementById('genre-search');
        const box = document.getElementById('genre-suggestions');

        input.addEventListener('keyup', async () => {

            let q = input.value;

            if (q.length < 2) {
                box.innerHTML = '';
                return;
            }

            let res = await fetch(`/genres/search?q=${encodeURIComponent(q)}`);
            let data = await res.json();

            box.innerHTML = '';

            data.forEach(g => {

                let item = document.createElement('a');
                item.classList.add('list-group-item', 'list-group-item-action');
                item.innerText = g.name;

                item.onclick = () => {

                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/profile/genres';

                    form.innerHTML =
                        `<input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="genre_id" value="${g.id}">`;

                    document.body.appendChild(form);
                    form.submit();
                };

                box.appendChild(item);
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const carouselEl = document.getElementById('profileGalleryCarousel');

            if (carouselEl && window.bootstrap) {
                new window.bootstrap.Carousel(carouselEl, {
                    interval: false,
                    ride: false,
                    touch: false,
                    wrap: true
                });
            }
        });
    </script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const collaboratorInput = document.getElementById('collaborator-search');
        const collaboratorHidden = document.getElementById('collaborator_profile_id');
        const collaboratorBox = document.getElementById('collaborator-suggestions');

        if (!collaboratorInput || !collaboratorHidden || !collaboratorBox) return;

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
                    });

                    collaboratorBox.appendChild(item);
                });
            } catch (error) {
                console.error('Collaborator search error:', error);
            }
        }

        loadCollaborators('');

        collaboratorInput.addEventListener('focus', () => {
            if (collaboratorInput.value.trim() === '') {
                loadCollaborators('');
            }
        });

        collaboratorInput.addEventListener('keyup', () => {
            collaboratorHidden.value = '';
            loadCollaborators(collaboratorInput.value.trim());
        });
    });
</script>

    {{-- form breeze --}}
    {{-- <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div> --}}
</x-app-layout>
