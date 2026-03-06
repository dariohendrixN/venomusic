<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">

        <div class="card shadow-sm">
            <div class="card-header">
                Richiedi un ruolo
            </div>

            <div class="card-body">

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('roles.request') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="role" class="form-label">Seleziona il ruolo</label>

                        <select name="role" id="role" class="form-select">
                            <option value="">-- scegli ruolo --</option>
                            <option value="artist">Artist</option>
                            <option value="producer">Producer</option>
                            <option value="studio">Studio</option>
                            <option value="venue">Venue</option>
                            <option value="label">Label</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Invia richiesta
                    </button>

                </form>

            </div>
        </div>

    </div>
</x-app-layout>
