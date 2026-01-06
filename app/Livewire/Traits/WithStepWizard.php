<?php

namespace App\Livewire\Traits;

trait WithStepWizard
{
    public int $currentStep = 1;

    public function nextStep(): void
    {
        $this->validateCurrentStep();

        if ($this->currentStep < 5) {
            $this->currentStep++;
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    protected function validateCurrentStep(): void
    {
        $rules = $this->getStepValidationRules($this->currentStep);

        if (! empty($rules)) {
            $this->validate($rules);
        }
    }

    abstract protected function getStepValidationRules(int $step): array;
}
