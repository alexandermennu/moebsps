<div class="flex justify-end gap-2">
    <a href="{{ route('admin.users.edit', $u) }}" class="text-sm text-slate-600 hover:text-slate-800">Edit</a>
    <form method="POST" action="{{ route('admin.users.toggle-active', $u) }}">
        @csrf
        @method('PATCH')
        <button type="submit" class="text-sm {{ $u->is_active ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }}">
            {{ $u->is_active ? 'Deactivate' : 'Activate' }}
        </button>
    </form>
    @if($u->id !== auth()->id())
    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Are you sure you want to permanently delete {{ $u->name }}? This action cannot be undone.')">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Delete</button>
    </form>
    @endif
</div>
