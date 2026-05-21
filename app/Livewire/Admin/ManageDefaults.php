<?php

namespace App\Livewire\Admin;

use App\Models\ProjectNatureDefault;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ManageDefaults extends Component
{
    public bool  $isModalOpen  = false;
    public ?int  $editingId    = null;
    public int   $orderNumber  = 1;
    public string $name        = '';
    public float  $percentage  = 0;

    public function openCreate(): void
    {
        $this->resetForm();
        $this->orderNumber = (ProjectNatureDefault::max('order_number') ?? 0) + 1;
        $this->isModalOpen = true;
    }

    public function openEdit(int $id): void
    {
        $default = ProjectNatureDefault::findOrFail($id);
        $this->editingId    = $id;
        $this->orderNumber  = $default->order_number;
        $this->name         = $default->name;
        $this->percentage   = (float) $default->percentage;
        $this->isModalOpen  = true;
    }

    public function save(): void
    {
        $this->validate([
            'orderNumber' => 'required|integer|min:1',
            'name'        => 'required|string|min:3|max:255',
            'percentage'  => 'required|numeric|min:0.01|max:100',
        ]);

        // Vérifier que la somme ne dépasse pas 100 %
        $currentSum = ProjectNatureDefault::when($this->editingId, fn($q) => $q->where('id', '!=', $this->editingId))
            ->sum('percentage');

        if ($currentSum + $this->percentage > 100) {
            $available = 100 - $currentSum;
            $this->addError('percentage', "Dépassement de 100 %. Maximum disponible : {$available} %.");
            return;
        }

        if ($this->editingId) {
            ProjectNatureDefault::findOrFail($this->editingId)->update([
                'order_number' => $this->orderNumber,
                'name'         => $this->name,
                'percentage'   => $this->percentage,
            ]);
            session()->flash('success', 'Phase par défaut modifiée.');
        } else {
            ProjectNatureDefault::create([
                'order_number' => $this->orderNumber,
                'name'         => $this->name,
                'percentage'   => $this->percentage,
            ]);
            session()->flash('success', 'Phase par défaut ajoutée.');
        }

        $this->closeModal();
    }

    public function delete(int $id): void
    {
        ProjectNatureDefault::findOrFail($id)->delete();
        session()->flash('success', 'Phase supprimée.');
    }

    public function closeModal(): void
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId   = null;
        $this->orderNumber = 1;
        $this->name        = '';
        $this->percentage  = 0;
    }

    public function render()
    {
        $defaults = ProjectNatureDefault::orderBy('order_number')->get();
        $totalPct = $defaults->sum('percentage');

        return view('livewire.admin.manage-defaults', compact('defaults', 'totalPct'));
    }
}
