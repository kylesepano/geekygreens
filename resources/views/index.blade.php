<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Geeky Greens - Partner Performance Dashboard</title>
    <link rel="stylesheet" href="index.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    @livewireStyles
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>The Geeky Greens</h1>
            <p>Partner Performance Dashboard</p>
            <a href="{{ route('data') }}" class="btn-add-data">➕ Add Data</a>
        </div>

        {{-- ✅ Flash message from Laravel --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <livewire:dashboard />
    </div>
    @livewireScripts



</body>

</html>
