<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toolsme - Login</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600&family=JetBrains+Mono:wght@400;500;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <!-- External CSS -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

    <!-- Background -->
    <div class="bg-atmosphere">
        <div class="overlay"></div>
        <div class="gradient"></div>
    </div>

    <!-- Main -->
    <main>
        <div class="login-card">

            <!-- Frame -->
            <div class="frame"></div>

            <!-- Plate -->
            <div class="plate">

                <!-- Rivets -->
                <div class="rivet rivet-tl"></div>
                <div class="rivet rivet-tr"></div>
                <div class="rivet rivet-bl"></div>
                <div class="rivet rivet-br"></div>

                <!-- Gears -->
                <span class="material-symbols-outlined gear-deco top-right">settings</span>
                <span class="material-symbols-outlined gear-deco bottom-left">settings</span>

                <!-- Header -->
                <div class="login-header">
                    <h1>Toolsme</h1>
                    <p class="subtitle">Authentication Protocol</p>
                    <div class="divider"></div>
                </div>

                <!-- Alert Error -->
                @if(session('error'))
                    <div class="alert alert-error">
                        <span class="material-symbols-outlined" style="font-size:20px;">error</span>
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Alert Success -->
                @if(session('success'))
                    <div class="alert alert-success">
                        <span class="material-symbols-outlined" style="font-size:20px;">check_circle</span>
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Validation Errors -->
                @if($errors->any())
                    <div class="alert alert-error">
                        <span class="material-symbols-outlined" style="font-size:20px;">warning</span>
                        <ul style="list-style:disc; padding-left:20px; margin:0;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form Login -->
                <form class="login-form" action="{{ route('login') }}" method="POST">
                    @csrf

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <span class="material-symbols-outlined icon">mail</span>
                            <input type="email" name="email" id="email" placeholder="operative@toolsme.eng" value="{{ old('email') }}" required autofocus>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                            <label for="password">Passcode</label>
                            <a href="#" class="forgot-link">Forgot Passcode?</a>
                        </div>
                        <div class="input-wrapper">
                            <span class="material-symbols-outlined icon">lock</span>
                            <input type="password" name="password" id="password" placeholder="••••••••" required>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div style="padding-top:12px;">
                        <button type="submit" class="btn-auth">
                            <span class="highlight"></span>
                            <span class="content">
                                Authenticate
                                <span class="material-symbols-outlined icon">key</span>
                            </span>
                        </button>
                    </div>

                </form>

                <!-- Footer Links -->
                <div class="login-footer">
                    <p>
                        Require clearance?
                        <a href="#">Request Access</a>
                    </p>
                </div>

            </div>
        </div>
    </main>

    <!-- Page Footer -->
    <footer class="page-footer">
        <div class="container">
            <div class="brand">Toolsme</div>
            <div class="copyright">&copy; 1892 Toolsme Engineering Works. All Rights Reserved.</div>
        </div>
    </footer>

</body>
</html>