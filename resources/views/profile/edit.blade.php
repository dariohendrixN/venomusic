<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">

        @if (session('status') === 'profile-updated')
            <div class="alert alert-success">
                Profilo aggiornato correttamente.
            </div>
        @endif

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
    </div>

    <br>

    <div class="container mt-5">
        <details class="card shadow-sm">

            <summary class="card-header" style="cursor: pointer;">
                Modifica profilo
            </summary>


            <div class="card-body">

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

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

                    <button type="submit" class="btn btn-success">
                        Salva modifiche
                    </button>
                </form>
            </div>
        </details>
        <h4 class="mt-4">Generi musicali</h4>

        <input type="text" id="genre-search" class="form-control" placeholder="Cerca genere...">

        <div id="genre-suggestions" class="list-group mt-2"></div>

        <div class="mt-3">
            <strong>I tuoi generi:</strong>

            @foreach (auth()->user()->profile->genres as $genre)
                <form method="POST" action="{{ route('profile.genres.destroy', $genre) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <span class="badge bg-primary d-inline-flex align-items-center">
                        {{ $genre->name }}
                        <button class="btn btn-sm btn-primary">x</button>
                    </span>
                </form>
            @endforeach
        </div>
    </div>
    
    {{-- scripts --}}
    <script>
        const input = document.getElementById('genre-search');
        const box = document.getElementById('genre-suggestions');
        
        input.addEventListener('keyup', async () => {
            
            let q = input.value;
            
            if(q.length < 2){
                box.innerHTML = '';
                return;
            }
            
            let res = await fetch(`/genres/search?q=${encodeURIComponent(q)}`);
            let data = await res.json();
            
            box.innerHTML = '';
            
            data.forEach(g => {
                
                let item = document.createElement('a');
                item.classList.add('list-group-item','list-group-item-action');
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
        </x-app-layout>
