<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — Kenya Election Tally</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f4f7f5] min-h-screen flex items-center justify-center antialiased">

    <div class="w-full max-w-md px-4" x-data="{ loading: false }">
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-700 rounded-2xl shadow-lg mb-4">
                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Kenya Election Tally</h1>
            <p class="text-gray-700 text-sm mt-1">Secure access to the Kakamega tallying center</p>
        </div>

        {{-- Login Card --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6 sm:p-8">
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-6">
                {{ $errors->first() }}
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-6">
                {{ session('error') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" @submit="loading = true">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-800 mb-1.5">Email or username</label>
                        <input type="text" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="admin1, admin2, or admin1@polling.go.ke"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-gray-50 focus:bg-white">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-bold text-gray-800 mb-1.5">Password</label>
                        <input type="password" id="password" name="password" required placeholder="admin123 or password"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all bg-gray-50 focus:bg-white">
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <span class="text-sm text-gray-600">Remember me</span>
                        </label>
                    </div>

                    <button type="submit"
                            class="w-full bg-green-700 hover:bg-green-800 text-white font-semibold py-3 px-4 rounded-xl transition-all shadow-lg shadow-green-700/25 hover:shadow-green-700/40 active:scale-[0.98]"
                            :disabled="loading">
                        <span x-show="!loading">Sign In</span>
                        <span x-show="loading" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            Signing in...
                        </span>
                    </button>
                </div>
            </form>

            {{-- Quick Role Access Selector --}}
            <div class="mt-8 pt-6 border-t border-gray-100">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider text-center mb-3">Quick Role Demo Credentials</p>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" @click="document.getElementById('email').value='admin1@polling.go.ke'; document.getElementById('password').value='admin123';" class="p-2 text-xs font-medium text-gray-700 bg-gray-50 hover:bg-green-50 hover:text-green-700 rounded-lg border border-gray-200 transition-colors text-left flex items-center justify-between">
                        <span>Admin 1</span>
                        <span class="text-[10px] text-gray-400 font-mono">admin123</span>
                    </button>
                    <button type="button" @click="document.getElementById('email').value='admin2@polling.go.ke'; document.getElementById('password').value='admin123';" class="p-2 text-xs font-medium text-gray-700 bg-gray-50 hover:bg-green-50 hover:text-green-700 rounded-lg border border-gray-200 transition-colors text-left flex items-center justify-between">
                        <span>Admin 2</span>
                        <span class="text-[10px] text-gray-400 font-mono">admin123</span>
                    </button>
                    <button type="button" @click="document.getElementById('email').value='admin3@polling.go.ke'; document.getElementById('password').value='admin123';" class="p-2 text-xs font-medium text-gray-700 bg-gray-50 hover:bg-green-50 hover:text-green-700 rounded-lg border border-gray-200 transition-colors text-left flex items-center justify-between">
                        <span>Admin 3</span>
                        <span class="text-[10px] text-gray-400 font-mono">admin123</span>
                    </button>
                    <button type="button" @click="document.getElementById('email').value='admin4@polling.go.ke'; document.getElementById('password').value='admin123';" class="p-2 text-xs font-medium text-gray-700 bg-gray-50 hover:bg-green-50 hover:text-green-700 rounded-lg border border-gray-200 transition-colors text-left flex items-center justify-between">
                        <span>Admin 4</span>
                        <span class="text-[10px] text-gray-400 font-mono">admin123</span>
                    </button>
                    <button type="button" @click="document.getElementById('email').value='county@polling.go.ke'; document.getElementById('password').value='password';" class="p-2 text-xs font-medium text-gray-700 bg-gray-50 hover:bg-green-50 hover:text-green-700 rounded-lg border border-gray-200 transition-colors text-left flex items-center justify-between">
                        <span>County Admin</span>
                        <span class="text-[10px] text-gray-400 font-mono">password</span>
                    </button>
                    <button type="button" @click="document.getElementById('email').value='alice@agent.go.ke'; document.getElementById('password').value='password';" class="p-2 text-xs font-medium text-gray-700 bg-gray-50 hover:bg-green-50 hover:text-green-700 rounded-lg border border-gray-200 transition-colors text-left flex items-center justify-between">
                        <span>Field Agent</span>
                        <span class="text-[10px] text-gray-400 font-mono">password</span>
                    </button>
                </div>
            </div>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            Kakamega County Election Tallying System — Form 34A & 34B Management
        </p>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
