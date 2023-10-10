<x-app-layout>

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <h1 class="text-white text-lg font-bold">{{ $ticket->title }}</h1>
        <div class="w-full sm:max-w-xl mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            <div class="text-white flex justify-between">
                <p>{{ $ticket->description }}</p>
                <p>{{ $ticket->created_at->diffForHumans() }}</p>
                @if ($ticket->attachment)
                    <a href="{{ '/storage/attachment/' . $ticket->attachment }}" target="_blank">Attachment</a>
                @endif
            </div>
            <div class="flex justify-between">
                <div class="flex mt-2">
                    <a href="{{ route('ticket.edit', $ticket->id) }}">
                        <x-primary-button>Update</x-primary-button>
                    </a>
                    <form action="{{ route('ticket.destroy', $ticket->id) }}" method="post" class="ml-2">
                        @csrf
                        @method('DELETE')
                        <x-primary-button>Delete</x-primary-button>
                    </form>
                </div>
                @if (auth()->user()->isAdmin)
                    <div class="flex mt-2">
                        <form action="{{ route('ticket.update', $ticket->id) }}" method="post">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="resolved">
                            <x-primary-button>Resolve</x-primary-button>
                        </form>
                        <form action="{{ route('ticket.update', $ticket->id) }}" method="post">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="reject">
                            <x-primary-button class="ml-2">Reject</x-primary-button>
                        </form>
                    @else
                        <p class="text-white">Status: {{ $ticket->status }}</p>
                @endif
            </div>
        </div>
    </div>
    </div>
</x-app-layout>
