<div>
    <label class="switch" wire:click="update({{ !$isActive  }})">
        <input wire:model="isActive" type="checkbox" {{ $isActive ? 'checked' : '' }} >
        <span class="slider round"></span>
    </label>
</div>
