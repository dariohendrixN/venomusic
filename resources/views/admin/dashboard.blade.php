{{-- @extends('layouts.app')

@section('content')
    <h1>Benvenuto Admin!</h1>
    <p>Questa è la dashboard riservata agli amministratori.</p>
@endsection --}}

<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Benvenuto Admin!</h1>
    <p>Questa è la dashboard riservata agli amministratori.</p>

    <p>Utente: {{ auth()->user()->email }}</p>
</body>
</html>