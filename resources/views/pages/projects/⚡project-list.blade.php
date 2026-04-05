<?php

use Livewire\Component;

new class extends Component {
   public int $count = 0;

   public function increment() {
      $this->count++;
   }
};
?>

<div>
    Count: {{ $count }}
    
    <button wire:click="increment">Click to count!</button>
    
</div>