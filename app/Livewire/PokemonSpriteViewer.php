<?php

namespace App\Livewire;

use App\Models\Pokemon;
use Livewire\Component;

class PokemonSpriteViewer extends Component
{
    public ?Pokemon $record = null;
    public string $orientation = 'front';
    public string $variant = 'default';

    public function mount($record): void
    {
        $this->record = $record;
    }

    public function setOrientation(string $orientation): void
    {
        $this->orientation = $orientation;
    }

    public function setVariant(string $variant): void
    {
        $this->variant = $variant;
    }

    public function getSpriteUrlProperty(): ?string
    {
        if (!$this->record) {
            return null;
        }

        $spriteField = "sprite_{$this->orientation}_{$this->variant}";

        return $this->record->{$spriteField};
    }

    public function render()
    {
        return view('livewire.pokemon-sprite-viewer');
    }
}