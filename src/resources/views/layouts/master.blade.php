<!DOCTYPE html>
<html>
<head>
    <title>Task Manager</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

   <style>
        .btn-update-status .spinner-border { display: none; }
        .btn-update-status.loading .spinner-border { display: inline-block; }
        .btn-update-status.loading .btn-text { display: none; }
    </style>
<body>

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <span class="navbar-brand">Task Manager</span>
    </div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn btn-sm btn-light">Logout</button>
    </form>
</nav>

<div class="container">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @yield('content')
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@yield('scripts')

</body>
</html>