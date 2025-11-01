<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Module Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Installed Modules -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Installed Modules</h3>

                    @if ($installed->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($installed as $module)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900">{{ $module->name }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ $module->description }}</p>
                                            <div class="mt-2">
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    v{{ $module->version }}
                                                </span>
                                                @if ($module->is_core)
                                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800 ml-2">
                                                        Core
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @if (!$module->is_core)
                                        <form method="POST" action="{{ route('modules.uninstall', $module) }}" class="mt-4">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm font-medium transition"
                                                    onclick="return confirm('Are you sure you want to uninstall this module?')">
                                                Uninstall
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">No modules installed yet.</p>
                    @endif
                </div>
            </div>

            <!-- Available Modules -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Available Modules</h3>

                    @if ($available->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($available as $module)
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-indigo-500 transition">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900">{{ $module->name }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ $module->description }}</p>
                                            <div class="mt-2">
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                    v{{ $module->version }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <form method="POST" action="{{ route('modules.install', $module) }}" class="mt-4">
                                        @csrf
                                        <button type="submit" class="w-full bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded text-sm font-medium transition">
                                            Install Module
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">All available modules are already installed.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
