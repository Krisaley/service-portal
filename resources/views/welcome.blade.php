<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'FSM Platform') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-50">
        <div class="min-h-screen flex flex-col justify-center items-center">
            <div class="max-w-4xl w-full mx-auto px-6 py-12">
                <!-- Header -->
                <div class="text-center mb-12">
                    <h1 class="text-5xl font-bold text-gray-900 mb-4">
                        Field Service Management Platform
                    </h1>
                    <p class="text-xl text-gray-600">
                        Multi-tenant SaaS platform for field service operations
                    </p>
                </div>

                <!-- Feature Cards -->
                <div class="grid md:grid-cols-3 gap-6 mb-12">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Multi-Tenant Teams</h3>
                        <p class="text-gray-600 text-sm">Jetstream Teams for complete data isolation between organizations</p>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Modular System</h3>
                        <p class="text-gray-600 text-sm">Install only the modules you need with automatic dependency management</p>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Role-Based Access</h3>
                        <p class="text-gray-600 text-sm">Comprehensive permission system with Super Admin, Admin, Staff, Customer roles</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                @auth
                    <div class="text-center">
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                            Go to Dashboard
                            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    </div>
                @else
                    <div class="text-center space-x-4">
                        <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                            Log In
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 bg-white hover:bg-gray-50 text-gray-900 font-semibold rounded-lg border border-gray-300 transition">
                            Register
                        </a>
                    </div>
                @endauth

                <!-- Info Box -->
                <div class="mt-12 bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-blue-900 mb-2">Test Credentials</h4>
                    <div class="grid md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="font-medium text-blue-900">Super Admin:</p>
                            <p class="text-blue-700">admin@test.com</p>
                            <p class="text-blue-700">password</p>
                        </div>
                        <div>
                            <p class="font-medium text-blue-900">Staff User:</p>
                            <p class="text-blue-700">staff@test.com</p>
                            <p class="text-blue-700">password</p>
                        </div>
                        <div>
                            <p class="font-medium text-blue-900">Customer:</p>
                            <p class="text-blue-700">customer@test.com</p>
                            <p class="text-blue-700">password</p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-12 text-center text-gray-500 text-sm">
                    <p>Laravel {{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})</p>
                    <p class="mt-2">Phase 1: Foundation Complete • Ready for Module Development</p>
                </div>
            </div>
        </div>
    </body>
</html>
