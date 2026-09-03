<div class="mx-auto max-w-md px-4 py-20">
    <h1 class="text-3xl font-semibold">Sign in</h1>
    <p class="mt-2 text-sm text-muted">We’ll email you a one-time login link. No password.</p>

    @if (session('error'))
        <p class="mt-4 text-sm text-red-700">{{ session('error') }}</p>
    @endif

    @if ($sent)
        <p class="mt-8 rounded-xl bg-hempe-light px-4 py-6 text-hempe-dark">
            If that email can sign in, the link is on its way. Check your inbox.
        </p>
    @else
        <form wire:submit="submit" class="mt-8 space-y-4">
            <label class="block text-sm font-medium">
                Email
                <input type="email" wire:model="email" class="mt-1 w-full rounded-full border border-black/15 bg-white px-4 py-3" required>
            </label>
            @error('email')
                <p class="text-sm text-red-700">{{ $message }}</p>
            @enderror
            <button type="submit" class="w-full rounded-full bg-hempe py-3 font-semibold text-white">Email me a link</button>
        </form>
    @endif
</div>
