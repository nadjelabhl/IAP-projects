<?php

namespace App\Livewire\Admin;

use App\Models\LegalStep;
use App\Models\ProjectNature;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ManageParametres extends Component
{
    use WithPagination;

    // ── Natures ───────────────────────────────────────────────────────────
    public $natureName      = '';
    public $natureId        = null;
    public bool $natureModalOpen  = false;
    public bool $deleteNatureOpen = false;
    public $natureToDelete  = null;

    // ── Phases juridiques par défaut ──────────────────────────────────────
    public string $defaultName  = '';
    public ?int   $defaultId    = null;
    public int    $orderNumber  = 1;
    public float  $percentage   = 0;
    public bool   $defaultModalOpen = false;

    // ─────────────────────────────────────────────────────────────────────
    // NATURES
    // ─────────────────────────────────────────────────────────────────────

    public function openNatureCreate(): void
    {
        $this->resetNature();
        $this->natureModalOpen = true;
    }

    public function openNatureEdit(int $id): void
    {
        $nature = ProjectNature::findOrFail($id);
        $this->natureId   = $nature->id;
        $this->natureName = $nature->name_nature;
        $this->natureModalOpen = true;
    }

    public function saveNature(): void
    {
        $rules = $this->natureId
            ? ['natureName' => 'required|string|min:3|max:100|unique:project_natures,name,' . $this->natureId]
            : ['natureName' => 'required|string|min:3|max:100|unique:project_natures,name'];

        $this->validate($rules, [
            'natureName.required' => 'Le nom de la nature est obligatoire.',
            'natureName.unique'   => 'Cette nature existe déjà.',
            'natureName.min'      => 'Le nom doit contenir au moins 3 caractères.',
        ]);

        if ($this->natureId) {
            ProjectNature::findOrFail($this->natureId)->update(['name_nature' => $this->natureName]);
            session()->flash('message', 'Nature mise à jour.');
        } else {
            ProjectNature::create(['name_nature' => $this->natureName, 'is_active' => true]);
            session()->flash('message', 'Nature créée.');
        }

        $this->closeNatureModal();
    }

    public function confirmDeleteNature(int $id): void
    {
        $this->natureToDelete  = ProjectNature::findOrFail($id);
        $this->deleteNatureOpen = true;
    }

    public function deleteNature(): void
    {
        if ($this->natureToDelete) {
            if ($this->natureToDelete->projects()->count() > 0) {
                $this->natureToDelete->update(['is_active' => false]);
                session()->flash('message', 'Nature désactivée (utilisée par des projets).');
            } else {
                $this->natureToDelete->delete();
                session()->flash('message', 'Nature supprimée.');
            }
        }
        $this->deleteNatureOpen = false;
        $this->natureToDelete   = null;
    }

    public function closeNatureModal(): void
    {
        $this->natureModalOpen = false;
        $this->resetNature();
    }

    private function resetNature(): void
    {
        $this->natureName = '';
        $this->natureId   = null;
    }

    // ─────────────────────────────────────────────────────────────────────
    // PHASES JURIDIQUES PAR DÉFAUT
    // ─────────────────────────────────────────────────────────────────────

    public function openDefaultCreate(): void
    {
        $this->resetDefault();
        $this->orderNumber = (LegalStep::max('order_number') ?? 0) + 1;
        $this->defaultModalOpen = true;
    }

    public function openDefaultEdit(int $id): void
    {
        $d = LegalStep::findOrFail($id);
        $this->defaultId    = $id;
        $this->orderNumber  = $d->order_number;
        $this->defaultName  = $d->name_phase;
        $this->percentage   = (float) $d->percentage;
        $this->defaultModalOpen = true;
    }

    public function saveDefault(): void
    {
        $this->validate([
            'orderNumber' => 'required|integer|min:1',
            'defaultName' => 'required|string|min:3|max:255',
            'percentage'  => 'required|integer|min:1|max:100',
        ], [
            'defaultName.required' => 'Le nom de la phase est obligatoire.',
            'defaultName.min'      => 'Au moins 3 caractères.',
            'percentage.required'  => 'Le pourcentage est obligatoire.',
        ]);

        $currentSum = LegalStep::when($this->defaultId, fn($q) => $q->where('id_phase', '!=', $this->defaultId))
            ->sum('percentage');

        if ($currentSum + $this->percentage > 100) {
            $available = 100 - $currentSum;
            $this->addError('percentage', "Dépassement de 100 %. Maximum disponible : {$available} %.");
            return;
        }

        if ($this->defaultId) {
            LegalStep::findOrFail($this->defaultId)->update([
                'order_number' => $this->orderNumber,
                'name_phase'   => $this->defaultName,
                'percentage'   => $this->percentage,
            ]);
            session()->flash('message', 'Phase modifiée.');
        } else {
            LegalStep::create([
                'order_number' => $this->orderNumber,
                'name_phase'   => $this->defaultName,
                'percentage'   => $this->percentage,
            ]);
            session()->flash('message', 'Phase ajoutée.');
        }

        $this->closeDefaultModal();
    }

    public function deleteDefault(int $id): void
    {
        LegalStep::findOrFail($id)->delete();
        session()->flash('message', 'Phase supprimée.');
    }

    public function closeDefaultModal(): void
    {
        $this->defaultModalOpen = false;
        $this->resetDefault();
    }

    private function resetDefault(): void
    {
        $this->defaultId   = null;
        $this->orderNumber = 1;
        $this->defaultName = '';
        $this->percentage  = 0;
    }

    // ─────────────────────────────────────────────────────────────────────

    public function render()
    {
        $defaults  = LegalStep::orderBy('order_number')->get();
        $totalPct  = $defaults->sum('percentage');

        return view('livewire.admin.manage-parametres', [
            'natures'  => ProjectNature::orderBy('name_nature')->paginate(10),
            'defaults' => $defaults,
            'totalPct' => $totalPct,
        ]);
    }
}
