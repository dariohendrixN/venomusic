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

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
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
        </div>
    </div>

    <br>

    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header">
                Modifica profilo
            </div>
    
            <div class="card-body">
    
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')
    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome</label>
                        <input
                            type="text"
                            class="form-control"
                            id="name"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                        >
                        @error('name')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
    
                    <div class="mb-3">
                        <label for="surname" class="form-label">Cognome</label>
                        <input
                            type="text"
                            class="form-control"
                            id="surname"
                            name="surname"
                            value="{{ old('surname', $user->surname) }}"
                        >
                        @error('surname')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                        >
                        @error('email')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
    
                    <hr class="my-4">
    
                    <div class="mb-3">
                        <label for="display_name" class="form-label">Nome visualizzato</label>
                        <input
                            type="text"
                            class="form-control"
                            id="display_name"
                            name="display_name"
                            value="{{ old('display_name', $profile->display_name) }}"
                        >
                        @error('display_name')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
    
                    <div class="mb-3">
                        <label for="city" class="form-label">Città</label>
                        <input
                            type="text"
                            class="form-control"
                            id="city"
                            name="city"
                            value="{{ old('city', $profile->city) }}"
                        >
                        @error('city')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
    
                    <div class="mb-3">
                        <label for="province" class="form-label">Provincia</label>
                        <input
                            type="text"
                            class="form-control"
                            id="province"
                            name="province"
                            value="{{ old('province', $profile->province) }}"
                        >
                        @error('province')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
    
                    <div class="mb-3">
                        <label for="region" class="form-label">Regione</label>
                        <input
                            type="text"
                            class="form-control"
                            id="region"
                            name="region"
                            value="{{ old('region', $profile->region) }}"
                        >
                        @error('region')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
    
                    <button type="submit" class="btn btn-primary">
                        Salva modifiche
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
