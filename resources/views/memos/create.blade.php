<x-app-layout>
    <x-slot name="header">Send Memo</x-slot>

    <div class="space-y-6" x-data="{ activeTab: 'upward' }">
        @if (session('success'))
            <div class="card card-body bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
        @endif

        {{-- Memo creation: Upward / Downward / Recent per mockup --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Left: Create memo form --}}
            <div class="card overflow-hidden">
                <div class="flex border-b border-slate-200 bg-slate-50">
                    <button @click="activeTab = 'upward'" :class="activeTab === 'upward' ? 'border-blue-600 text-blue-700 font-medium' : 'border-transparent text-slate-500'"
                            class="px-6 py-3 text-sm border-b-2 transition-colors">Send Memo Upward</button>
                    <button @click="activeTab = 'downward'" :class="activeTab === 'downward' ? 'border-blue-600 text-blue-700 font-medium' : 'border-transparent text-slate-500'"
                            class="px-6 py-3 text-sm border-b-2 transition-colors">Send Memo Downward</button>
                    <button @click="activeTab = 'personal'" :class="activeTab === 'personal' ? 'border-blue-600 text-blue-700 font-medium' : 'border-transparent text-slate-500'"
                            class="px-6 py-3 text-sm border-b-2 transition-colors">Personal Memo</button>
                    <a href="{{ route('memos.index') }}" class="px-6 py-3 text-sm border-transparent text-slate-500 hover:text-slate-700 border-b-2">Recent Memos</a>
                </div>
                <div class="p-6">
                    <form action="{{ route('memos.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="direction" :value="activeTab">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Title</label>
                                <input type="text" name="title" required class="input-field" placeholder="Memo title" value="{{ old('title') }}">
                                @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div x-show="activeTab !== 'personal'" x-cloak>
                                <label class="block text-sm font-medium text-slate-700 mb-1">To</label>
                                <select name="to_user_id" class="input-field" :required="activeTab !== 'personal'">
                                    <option value="">Select recipient</option>
                                    @foreach ($users as $u)
                                        <option value="{{ $u->id }}" @selected(old('to_user_id') == $u->id)>{{ $u->name }} ({{ $u->email }})</option>
                                    @endforeach
                                </select>
                                @error('to_user_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Subject / Body</label>
                                <textarea name="body" rows="4" class="input-field" placeholder="Memo content">{{ old('body') }}</textarea>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="requires_approval" id="requires_approval" value="1" {{ old('requires_approval') ? 'checked' : '' }}
                                       class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <label for="requires_approval" class="text-sm text-slate-700">Require approval workflow</label>
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit" class="btn-primary">Create Memo</button>
                            <a href="{{ route('memos.index') }}" class="btn-secondary ml-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Right: My Memos list --}}
            <div class="card">
                <div class="p-4 border-b border-slate-200 bg-slate-50 font-semibold text-slate-700">My Memos</div>
                <div class="p-4 max-h-96 overflow-y-auto">
                    @foreach (\App\Models\Memo::where('from_user_id', auth()->id())->latest()->limit(10)->get() as $m)
                        <a href="{{ route('memos.show', $m) }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50">
                            <svg class="w-8 h-8 text-slate-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-slate-800 truncate">{{ $m->title }}</div>
                                <div class="text-sm text-slate-500">{{ $m->direction }} · {{ $m->created_at->format('m.d.Y') }}</div>
                            </div>
                        </a>
                    @endforeach
                    @if (\App\Models\Memo::where('from_user_id', auth()->id())->count() === 0)
                        <p class="text-slate-500 text-sm">No memos yet. Create one above.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
