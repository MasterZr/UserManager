<x-filament::page>
    <form wire:submit.prevent="submit" class="space-y-6">
        {{ $this->form }}

        <div class="flex flex-wrap items-center gap-4 justify-start">
            <x-filament::button type="submit">
                保存
            </x-filament::button>

            <x-filament::button onclick="window.history.back()">
                取消
            </x-filament::button>
        </div>
    </form>
</x-filament::page>
