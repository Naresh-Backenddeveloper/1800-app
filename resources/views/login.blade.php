<!-- resources/views/auth/login.blade.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>1800 Admin - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-primary-green {
            background: linear-gradient(135deg, #6BA145 0%, #5a8f38 100%);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 p-4">

    <div class="w-full max-w-md bg-white rounded-xl shadow-2xl border-0">
        <!-- Header -->
        <div class="p-8 text-center space-y-4 pb-8">
            <div class="mx-auto w-16 h-16 rounded-xl flex items-center justify-center shadow-lg bg-primary-green">
                <span class="text-white text-2xl font-bold">PW</span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Admin Panel Login</h1>
                <!-- <p class="text-gray-500 mt-2">Pengwin Solutions Private Limited</p> -->
            </div>
        </div>

        <!-- Login Form -->
        <form method="POST" action="{{ url('_admin') }}" class="px-8 pb-8">
            @csrf

            @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
                {{ session('error') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email / Username
                    </label>
                    <input type="text"
                        id="username"
                        name="username"
                        value="{{ old('username') }}"
                        placeholder="Enter your email or username"
                        required
                        autofocus
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500/20 focus:border-green-500 outline-none @error('email') border-red-500 @enderror">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <input type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500/20 focus:border-green-500 outline-none @error('password') border-red-500 @enderror">
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox"
                            id="remember"
                            name="remember"
                            {{ old('remember') ? 'checked' : '' }}
                            class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        <label for="remember" class="ml-2 text-sm text-gray-600 cursor-pointer">
                            Remember Me
                        </label>
                    </div>

                    @if (Route::has('password.request'))
                    <a href="{{ url('_admin/request') }}"
                        class="text-sm hover:underline"
                        style="color: #6BA145;">
                        Forgot Password?
                    </a>
                    @endif
                </div>

                <button type="submit"
                    class="w-full text-white py-2.5 rounded-lg hover:opacity-90 transition-opacity shadow-md bg-primary-green font-medium">
                    Login
                </button>
            </div>
        </form>

        <div class="pb-6 text-center text-xs text-gray-500">
            © {{ date('Y') }} Pengwin Solutions Private Limited. All rights reserved.
        </div>
    </div>

    <!-- 
        Remove this in production!
        <div class="fixed bottom-4 right-4 bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm shadow-lg">
            <div class="font-semibold text-blue-900 mb-1">Demo Credentials:</div>
            <div class="text-blue-700">Email: admin@pengwin.com</div>
            <div class="text-blue-700">Password: admin123</div>
        </div>
    -->

</body>

</html>