<?php

namespace App\Forms;

use Contributte\FormWizard\Wizard;
use Nette\Http\Session;

/**
 * see https://github.com/contributte/forms-wizard/tree/master/.docs 20.2.2024
 */
/* use createStep<NUM> function*/
class WizardForm extends Wizard
{

    /**
     * @param Session $session
     * @param array $stepNames [number (start from 1) => name]
     */
    public function __construct(Session $session, private array $stepNames)
    {
        parent::__construct($session);
    }

    /**
     * @param int $step
     * @return array
     */
    public function getStepData(int $step): array
    {
        return [
            'name' => $this->stepNames[$step]
        ];
    }

    protected function startup(): void
    {}

    protected function finish(): void
    {
        $values = $this->getValues();
    }
}