<div class="mx-auto w-full max-w-md rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
    <h1 class="mb-2 text-2xl font-semibold text-slate-900">Register</h1>
    <p class="mb-6 text-sm text-slate-500">Create a new account and choose the role that describes your access level.</p>

    <form wire:submit.prevent="register" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-slate-700">
                Name
                <input wire:model.defer="name" type="text" required autofocus class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200" />
            </label>
            @error('name')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">
                Email address
                <input wire:model.defer="email" type="email" required class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200" />
            </label>
            @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">
                Password
                <input wire:model.defer="password" type="password" required class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200" />
            </label>
            @error('password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">
                Confirm Password
                <input wire:model.defer="password_confirmation" type="password" required class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200" />
            </label>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">
                Role
                <select wire:model.defer="role" required class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200">
                    <option value="">Select a role</option>
                    @foreach ($roles as $roleName)
                        <option value="{{ $roleName }}">{{ ucwords(str_replace('_', ' ', $roleName)) }}</option>
                    @endforeach
                </select>
            </label>
            @error('role')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        @if ($role === 'agency_owner')
            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Agency Name
                    <input wire:model.defer="agency_name" type="text" required class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200" />
                </label>
                @error('agency_name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <button type="submit" class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-700">Create account</button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        Already registered? <a href="{{ route('login') }}" class="font-semibold text-slate-900 hover:text-slate-700">Login</a>
    </p>
</div>
