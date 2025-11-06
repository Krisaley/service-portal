<div>
    <x-app-layout>
        <x-slot name="header">
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ticket #{{ $ticket->ticket_number }}</h2>
                <div class="space-x-2">
                    <a href="{{ route('tickets.edit', $ticket) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md">Edit</a>
                    <a href="{{ route('tickets.index') }}" class="px-4 py-2 bg-gray-300 rounded-md">Back</a>
                </div>
            </div>
        </x-slot>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
                @endif

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div><strong>Customer:</strong> {{ $ticket->customer?->name ?? 'N/A' }}</div>
                        <div><strong>Asset:</strong> {{ $ticket->asset?->name ?? 'N/A' }}</div>
                        <div><strong>Priority:</strong> <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $ticket->priority_color }}">{{ ucfirst($ticket->priority) }}</span></div>
                        <div><strong>Status:</strong> <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $ticket->status_color }}">{{ ucfirst($ticket->status) }}</span></div>
                        <div><strong>Assigned To:</strong> {{ $ticket->assignedTo?->name ?? 'Unassigned' }}</div>
                        <div><strong>Created By:</strong> {{ $ticket->createdBy->name }}</div>
                        <div><strong>Created:</strong> {{ $ticket->created_at->format('M d, Y H:i') }}</div>
                        <div><strong>Category:</strong> {{ $ticket->category ?? 'N/A' }}</div>
                    </div>
                    <div class="border-t pt-4">
                        <h3 class="font-semibold mb-2">{{ $ticket->title }}</h3>
                        <p class="text-gray-700 whitespace-pre-line">{{ $ticket->description }}</p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Comments</h3>
                    <div class="space-y-4 mb-6">
                        @forelse($ticket->comments as $comment)
                            <div class="border-l-4 {{ $comment->is_internal ? 'border-yellow-400 bg-yellow-50' : 'border-blue-400 bg-blue-50' }} p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <span class="font-semibold">{{ $comment->user->name }}</span>
                                        @if($comment->is_internal)
                                            <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Internal</span>
                                        @endif
                                    </div>
                                    <span class="text-sm text-gray-500">{{ $comment->created_at->format('M d, Y H:i') }}</span>
                                </div>
                                <p class="text-gray-700 whitespace-pre-line">{{ $comment->comment }}</p>
                            </div>
                        @empty
                            <p class="text-gray-500">No comments yet.</p>
                        @endforelse
                    </div>

                    <form wire:submit="addComment" class="border-t pt-4">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Add Comment</label>
                            <textarea wire:model="newComment" rows="3" class="w-full rounded-md border-gray-300" placeholder="Write your comment here..."></textarea>
                            @error('newComment')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input wire:model="isInternal" type="checkbox" class="rounded border-gray-300" />
                                <span class="ml-2 text-sm text-gray-700">Internal Comment (Staff Only)</span>
                            </label>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Add Comment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </x-app-layout>
</div>
