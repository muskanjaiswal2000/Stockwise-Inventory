<div class="min-h-screen bg-[#f6f8fc]">
    <div class="inventory-page-heading">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <a href="{{ route('inventory.categories.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 transition hover:text-indigo-800">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.56l3.22 3.22a.75.75 0 1 1-1.06 1.06l-4.5-4.5a.75.75 0 0 1 0-1.06l4.5-4.5a.75.75 0 1 1 1.06 1.06l-3.22 3.22h10.69A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
                </svg>
                Back to categories
            </a>
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-900">Edit category</h1>
            <p class="mt-2 text-sm text-slate-500">Update the details for <span class="font-semibold text-slate-700">{{ $category->name }}</span>.</p>
        </div>
    </div>

    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <form wire:submit="update" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-violet-50 px-6 py-5">
                <h2 class="font-semibold text-slate-900">Category details</h2>
                <p class="mt-1 text-sm text-slate-600">Make changes to the category name or description.</p>
            </div>

            <div class="space-y-6 p-6">
                <div>
                    <label for="category-name" class="mb-2 block text-sm font-semibold text-slate-700">Name <span class="text-rose-500">*</span></label>
                    <input id="category-name" type="text" wire:model="name" autofocus
                           class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('name') <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="category-description" class="mb-2 block text-sm font-semibold text-slate-700">Description <span class="font-normal text-slate-400">(optional)</span></label>
                    <textarea id="category-description" wire:model="description" rows="4"
                              class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    @error('description') <p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4 sm:flex-row sm:justify-end">
                <a href="{{ route('inventory.categories.index') }}" class="inline-flex justify-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                    Cancel
                </a>
                <button type="submit" wire:loading.attr="disabled" wire:target="update"
                        class="inline-flex justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-wait disabled:opacity-60">
                    <span wire:loading.remove wire:target="update">Save changes</span>
                    <span wire:loading wire:target="update">Saving...</span>
                </button>
            </div>
        </form>
    </div>
</div>
