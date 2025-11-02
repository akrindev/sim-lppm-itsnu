<!-- Section: RAB (Rencana Anggaran Biaya) -->
<div class="mb-3 card">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center">
                <x-lucide-calculator class="me-3 icon" />
                <h3 class="mb-0 card-title">Rencana Anggaran Biaya (RAB)</h3>
            </div>
            <button type="button" wire:click="addBudgetItem" class="btn btn-primary btn-sm">
                <x-lucide-plus class="icon" />
                Tambah Item
            </button>
        </div>

        @if (empty($form->budget_items))
            <div class="alert alert-info">
                <x-lucide-info class="me-2 icon" />
                Belum ada item anggaran. Klik tombol "Tambah Item" untuk menambahkan.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="15%">Kelompok RAB</th>
                            <th width="15%">Komponen</th>
                            <th width="20%">Item</th>
                            <th width="10%">Satuan</th>
                            <th width="10%">Volume</th>
                            <th width="12%">Harga Satuan</th>
                            <th width="13%">Total</th>
                            <th width="5%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($form->budget_items as $index => $item)
                            @php
                                $selectedGroupValue =
                                    isset($item['budget_group_id']) && $item['budget_group_id'] !== ''
                                        ? $item['budget_group_id']
                                        : null;
                                $selectedComponentValue =
                                    isset($item['budget_component_id']) && $item['budget_component_id'] !== ''
                                        ? $item['budget_component_id']
                                        : null;
                            @endphp
                            <tr wire:key="budget-{{ $index }}" x-data="{
                                selectedGroup: @js($selectedGroupValue),
                                selectedComponent: @js($selectedComponentValue),
                                components: @js($this->budgetComponents->groupBy('budget_group_id')->map(fn($items) => $items->map(fn($i) => ['id' => $i->id, 'name' => $i->name])->values())->toArray()),
                                get filteredComponents() {
                                    if (!this.selectedGroup) return [];
                                    return this.components[this.selectedGroup] || [];
                                }
                            }">
                                <td>
                                    <select wire:model.live="form.budget_items.{{ $index }}.budget_group_id"
                                        x-model="selectedGroup" class="form-select-sm form-select">
                                        <option value="">-- Pilih --</option>
                                        @foreach ($this->budgetGroups as $group)
                                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select wire:model="form.budget_items.{{ $index }}.budget_component_id"
                                        x-model="selectedComponent" class="form-select-sm form-select"
                                        :disabled="!selectedGroup">
                                        <option value="">-- Pilih --</option>
                                        <template x-for="comp in filteredComponents" :key="comp.id">
                                            <option :value="comp.id" x-text="comp.name"
                                                :selected="comp.id == selectedComponent"></option>
                                        </template>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" wire:model="form.budget_items.{{ $index }}.item"
                                        class="form-control form-control-sm" placeholder="Item">
                                </td>
                                <td>
                                    <input type="text" wire:model="form.budget_items.{{ $index }}.unit"
                                        class="form-control form-control-sm" placeholder="Satuan">
                                </td>
                                <td>
                                    <input type="number" wire:model.live="form.budget_items.{{ $index }}.volume"
                                        wire:change="calculateTotal({{ $index }})"
                                        class="form-control form-control-sm" placeholder="0" min="0"
                                        step="0.01">
                                </td>
                                <td>
                                    <input type="number"
                                        wire:model.live="form.budget_items.{{ $index }}.unit_price"
                                        wire:change="calculateTotal({{ $index }})"
                                        class="form-control form-control-sm" placeholder="0" min="0"
                                        step="0.01">
                                </td>
                                <td>
                                    <input type="number" wire:model="form.budget_items.{{ $index }}.total"
                                        class="form-control form-control-sm" placeholder="0" readonly>
                                </td>
                                <td>
                                    <button type="button" wire:click="removeBudgetItem({{ $index }})"
                                        class="btn btn-sm btn-danger">
                                        <x-lucide-trash-2 class="icon" />
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" class="text-end"><strong>Total Anggaran:</strong></td>
                            <td colspan="2">
                                <strong>Rp
                                    {{ number_format(collect($form->budget_items)->sum(function ($item) {return (float) ($item['total'] ?? 0);}),2,',','.') }}</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>
