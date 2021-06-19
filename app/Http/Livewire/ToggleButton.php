<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Database\Eloquent\Model;
class ToggleButton extends Component
{
    public $model;
    public $field;

    public $isActive;

    public function mount(){
        $this->isActive = $this->model->getAttribute($this->field);
    }
    public function render()
    {
        return view('livewire.toggle-button');
    }

    public function update(){
        $this->isActive = !$this->isActive;
        $this->model->setAttribute($this->field, $this->isActive )->save();
    }
}
