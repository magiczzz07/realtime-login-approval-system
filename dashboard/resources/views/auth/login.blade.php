<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Login | {{ config('app.name') }}</title>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
</head>
<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block">
                                <img src="https://source.unsplash.com/K4mSJ7kc0As/464x577">
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5 d-none" id="loginApprovalQueue">
                                    <div class="w-50 my-5 mx-auto mobilePhone">
                                        <img src="{{ asset('img/approval-mobile.svg') }}" class="w-100">
                                    </div>
                                    <h5 class="text-center text-primary">We sent an approval request to your registered devices</h5>
                                    <small class="d-block text-secondary text-center">
                                        To log in, open the dashboard app on one of your registered devices and approve the login request.
                                    </small>
                                    <div class="text-center mt-3">
                                        <img src="{{ asset('/img/spinner.svg') }}" alt="">
                                    </div>
                                </div>

                                <div class="p-5" id="loginFormWrapper">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Log in</h1>
                                    </div>
                                    <form method="POST" action="{{ route('login') }}" class="user" id="authenticationForm">
                                        @csrf
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user" id="email" aria-describedby="emailHelp" placeholder="Email Address" name="email" value="{{ old('email') }}" required autofocus>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user" id="password" placeholder="Password" name="password" required>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="remember">Remember Me</label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Login
                                        </button>
                                        <hr>
                                        <a href="index.html" class="btn btn-google btn-user btn-block">
                                            <i class="fab fa-google fa-fw"></i> Login with Google
                                        </a>
                                        <a href="index.html" class="btn btn-facebook btn-user btn-block">
                                            <i class="fab fa-facebook-f fa-fw"></i> Login with Facebook
                                        </a>
                                    </form>
                                    <hr>
                                    @if (Route::has('password.request'))
                                    <div class="text-center">
                                        <a class="small" href="{{ route('password.request') }}">Forgot Password?</a>
                                    </div>
                                    @endif
                                    <div class="text-center">
                                        <a class="small" href="{{ route('register') }}">Create an Account!</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

    <script type="text/javascript">
    const showApprovalAndListenForApproval = callback => {
        $('#loginFormWrapper').addClass('d-none');
        $('#loginApprovalQueue').removeClass('d-none');

        Echo.channel('auth-request').listen('.approval-granted', e => callback(e.hash));
    };

    const getLoginCredentials = () => {
        return {
            email: $('#email').val(),
            password: $('#password').val(),
            remember: $('#remember').val()
        }
    };

    $(document).ready(() => {
        $('#authenticationForm').on('submit', e => {
            e.preventDefault();

            const { email, password, remember } = getLoginCredentials();

            axios.post('/login/confirm', {email, password, remember })
                .then(() => {
                    showApprovalAndListenForApproval(hash => {
                        axios.post('/login/authorize', { email, password, remember, hash })
                            .then(() => (window.location = '/home'))
                            .catch(() => alert('Invalid authorization. Please try again.'));
                    });
                })
                .catch(() => alert('Invalid login credentials!'));
        })
    })
    </script>
</body>
</html>
