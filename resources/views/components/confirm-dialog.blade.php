@props(['id', 'title' => '¿Confirmas esta acción?', 'description' => 'Esta acción no se puede deshacer.', 'confirmText' => 'Confirmar', 'cancelText' => 'Cancelar'])
<x-modal :id="$id" :title="$title" :description="$description" {{ $attributes }}>
    <div class="space-y-5"><div class="text-sm text-slate-600">{{ $slot }}</div><div class="flex justify-end gap-2"><x-button variant="secondary" data-modal-close="{{ $id }}">{{ $cancelText }}</x-button><x-button variant="danger" data-modal-close="{{ $id }}" data-confirm-action>{{ $confirmText }}</x-button></div></div>
</x-modal>
