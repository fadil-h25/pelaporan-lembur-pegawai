<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Lembur Bawaslu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f4f7f6;
            font-family: 'Segoe UI', sans-serif;
        }

        .navbar {
            background: #007bff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .btn-primary {
            background: #007bff;
            border: none;
            padding: 10px 20px;
        }
    </style>
</head>

<body>
    @auth
        <nav class="navbar navbar-expand-lg navbar-dark mb-4">
            <div class="container">
                <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}"><i class="bi bi-clock-history"></i> BAWASLU
                    LEMBUR</a>
                <div class="ms-auto d-flex align-items-center">
                    <span class="text-white me-3 d-none d-md-block">Halo, <strong>{{ Auth::user()->name }}</strong></span>
                    <a href="{{ route('logout') }}" class="btn btn-light btn-sm fw-bold text-danger">Logout</a>
                </div>
            </div>
        </nav>
    @endauth

    <div class="container pb-5">
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
