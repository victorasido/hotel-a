<?php

namespace App\Livewire\Fnb;

use App\Models\FnbCategory;
use App\Models\FnbMenu;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Manajemen Menu F&B')]
class MenuManagement extends Component
{
    public bool $showModal = false;
    public ?int $editingId = null;

    public ?int $category_id = null;
    public string $name = '';
    public string $description = '';
    public string $price = '';
    public bool $is_available = true;

    public function openCreate(): void { $this->resetForm(); $this->showModal = true; }

    public function openEdit(int $id): void
    {
        $menu = FnbMenu::findOrFail($id);
        $this->editingId = $id;
        $this->category_id = $menu->category_id;
        $this->name = $menu->name;
        $this->description = $menu->description ?? '';
        $this->price = $menu->price;
        $this->is_available = $menu->is_available;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate(['category_id' => 'required|exists:fnb_categories,id', 'name' => 'required', 'price' => 'required|numeric|min:0']);

        $data = ['category_id' => $this->category_id, 'name' => $this->name, 'description' => $this->description, 'price' => $this->price, 'is_available' => $this->is_available];

        if ($this->editingId) { FnbMenu::findOrFail($this->editingId)->update($data); session()->flash('success', 'Menu diperbarui.'); }
        else { FnbMenu::create($data); session()->flash('success', 'Menu ditambahkan.'); }

        $this->showModal = false; $this->resetForm();
    }

    public function toggleAvailability(int $id): void
    {
        $menu = FnbMenu::findOrFail($id);
        $menu->update(['is_available' => !$menu->is_available]);
    }

    public function delete(int $id): void { FnbMenu::findOrFail($id)->delete(); session()->flash('success', 'Menu dihapus.'); }
    public function closeModal(): void { $this->showModal = false; $this->resetForm(); }
    private function resetForm(): void { $this->editingId = null; $this->category_id = null; $this->name = ''; $this->description = ''; $this->price = ''; $this->is_available = true; }

    public function render()
    {
        return view('livewire.fnb.menu-management', [
            'categories' => FnbCategory::with('menus')->get(),
        ]);
    }
}
