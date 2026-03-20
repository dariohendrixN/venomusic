<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
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
                                    <img src="{{ asset('storage/' . $profile->profile_image) }}" alt="Immagine profilo"
                                        class="img-thumbnail border-none" style="max-width: 300px;">
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

                                    <label for="profile_image" class="form-label text-center">Carica nuova immagine
                                        profilo</label>

                                    <input type="file" class="form-control" id="profile_image" name="profile_image"
                                        accept=".jpg,.jpeg,.png,.webp">
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
                                <input type="text" class="form-control" id="display_name" name="display_name"
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
                <div class ="card-footer d-flex justify-content-center mt-4">
                    <button type="submit" class="btn btn-success">
                        Salva modifiche
                    </button>
                </div>
            </div>
        </div>
    </form>

    @unless (auth()->user()->canUploadTracks())
        <div class="alert alert-info mt-4">
            Il caricamento brani è disponibile solo per profili professionali approvati.
        </div>
    @endunless

    @if (auth()->user()->canUploadTracks())

        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-5">
                    <div class="card mt-4 shadow-sm mb-3">
                        <div class="card-body">
                            <h4 class="card-title">Generi musicali</h4>
                            <input type="text" id="genre-search" class="form-control"
                                placeholder="Cerca genere...">
                            <div id="genre-suggestions" class="list-group mt-2"></div>
                        </div>

                        <div class="card-footer text-body-secondary">

                            <strong>I tuoi generi:</strong>

                            @foreach (auth()->user()->profile->genres as $genre)
                                <form method="POST" action="{{ route('profile.genres.destroy', $genre) }}"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <span class="badge bg-primary-subtle text-dark d-inline-flex align-items-center m-1">
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

        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-8">
                    <div class="card mt-4 shadow-sm">
                        <div class="card-header">
                            Brani audio
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
                                            <option value="{{ $genre->id }}">{{ $genre->name }}</option>
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

                    <div class="card mt-4 shadow-sm">
                        <div class="card-header">
                            I tuoi brani
                        </div>

                        <div class="card-body">
                            @forelse($profile->tracks as $track)
                                <div class="card my-4 mx-10">
                                    <div class="card-body">
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
                </div>
            </div>
        </div>
    @endif

    @unless (auth()->user()->canUploadMedia())
        <div class="alert alert-info mt-4">
            Le funzionalità media gallery sono disponibili solo per profili professionali approvati.
        </div>
    @endunless

    @if (auth()->user()->canUploadMedia())

        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-8">

                    <div class="card mt-4 shadow-sm">
                        <div class="card-header">
                            Gallery immagini
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

                            @if ($profile->images->isNotEmpty())
                                <div id="profileGalleryCarousel" class="carousel slide mt-4" data-bs-ride="false">
                                    <div class="carousel-inner">
                                        @foreach ($profile->images as $image)
                                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                                <img src="{{ asset('storage/' . $image->path) }}"
                                                    class="d-block w-100 rounded"
                                                    alt="Gallery image {{ $loop->iteration }}"
                                                    style="height: 420px; object-fit: cover;">
                                            </div>
                                        @endforeach
                                    </div>

                                    @if ($profile->images->count() > 1)
                                        <button class="carousel-control-prev" type="button"
                                            data-bs-target="#profileGalleryCarousel" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Precedente</span>
                                        </button>

                                        <button class="carousel-control-next" type="button"
                                            data-bs-target="#profileGalleryCarousel" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Successiva</span>
                                        </button>
                                    @endif
                                </div>
                            @else
                                <p class="text-muted mt-4">Nessuna immagine nella gallery.</p>
                            @endif

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
