<div>
    <x-app-layout>
        <x-slot name="header">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Create Customer') }}
                </h2>
                <a href="{{ route('customers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Back to List
                </a>
            </div>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <form wire:submit="save">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                                    <input
                                        wire:model="name"
                                        type="text"
                                        id="name"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                                    <input
                                        wire:model="email"
                                        type="email"
                                        id="email"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                                    <input
                                        wire:model="phone"
                                        type="text"
                                        id="phone"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- VAT Number -->
                                <div>
                                    <label for="vat_number" class="block text-sm font-medium text-gray-700">VAT Number</label>
                                    <input
                                        wire:model="vat_number"
                                        type="text"
                                        id="vat_number"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    @error('vat_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Address -->
                                <div class="md:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                                    <input
                                        wire:model="address"
                                        type="text"
                                        id="address"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- City -->
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                                    <input
                                        wire:model="city"
                                        type="text"
                                        id="city"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    @error('city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- State -->
                                <div>
                                    <label for="state" class="block text-sm font-medium text-gray-700">State/Province</label>
                                    <input
                                        wire:model="state"
                                        type="text"
                                        id="state"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    @error('state') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- ZIP -->
                                <div>
                                    <label for="zip" class="block text-sm font-medium text-gray-700">ZIP/Postal Code</label>
                                    <input
                                        wire:model="zip"
                                        type="text"
                                        id="zip"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    @error('zip') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Country -->
                                <div>
                                    <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                                    <input
                                        wire:model="country"
                                        type="text"
                                        id="country"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    @error('country') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Create Customer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
</div>
