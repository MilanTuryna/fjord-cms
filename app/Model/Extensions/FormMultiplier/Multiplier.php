<?php declare(strict_types = 1);

namespace App\Model\Extensions\FormMultiplier;

use App\Model\Extensions\FormMultiplier\Buttons\CreateButton;
use App\Model\Extensions\FormMultiplier\Buttons\RemoveButton;
use Iterator;
use Nette\ComponentModel\IComponent;
use Nette\Forms\Container;
use Nette\Forms\Control;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\SubmitButton;
use Nette\Forms\Form;
use Nette\Forms\Helpers;
use Nette\Forms\Rules;
use Nette\Utils\ArrayHash;
use Nette\Utils\Arrays;
use Nette\Utils\Html;
use Traversable;

class Multiplier extends Container implements Control
{

    public const SUBMIT_CREATE_NAME = 'multiplier_creator';

    public const SUBMIT_REMOVE_NAME = 'multiplier_remover';

    /** @var Form|null */
    private $form;

    /** @var bool */
    private $attachedCalled = false;

    /** @var callable */
    protected $factory;

    /** @var int */
    protected $copyNumber;

    /** @var int */
    protected $number = 0;

    /** @var bool */
    protected $created = false;

    /** @var mixed[] */
    protected $values = [];

    /** @var bool */
    protected $erase = false;

    /** @var CreateButton[] */
    protected $createButtons = [];

    /** @var RemoveButton|null */
    protected $removeButton;

    /** @var mixed[] */
    protected $httpData = [];

    /** @var int|null */
    protected $maxCopies = null;

    /** @var int */
    protected $totalCopies = 0;

    /** @var int */
    protected $minCopies = 1;

    /** @var bool */
    protected $resetKeys = true;

    /** @var callable[] */
    public $onCreate = [];

    /** @var callable[] */
    public $onRemove = [];

    /** @var callable[] */
    public $onCreateComponents = [];

    /** @var Container[] */
    protected $noValidate = [];

    public string $caption = "";

    public array $options = [];

    public function __construct(callable $factory, int $copyNumber = 1, ?int $maxCopies = null)
    {
        $this->factory = $factory;
        $this->minCopies = $this->copyNumber = $copyNumber;
        $this->maxCopies = $maxCopies;

        $this->monitor(Form::class, function (Form $form): void {
            $this->form = $form;

            if ($this->getCurrentGroup() === null) {
                $this->setCurrentGroup($form->getCurrentGroup());
            }

            if ($form instanceof \Nette\Application\UI\Form) {
                if ($form->isAnchored()) {
                    $this->whenAttached();
                } else {
                    $form->onAnchor[] = function (): void {
                        $this->whenAttached();
                    };
                }
            }

            $form->onRender[] = function (): void {
                $this->whenAttached();
            };
        });
        $this->monitor(self::class, [$this, 'whenAttached']);
        $this->control = Html::el('input', ['type' => null, 'name' => null]);
        $this->label = Html::el('label');
        $this->caption = "";
        $this->rules = new Rules($this);
        $this->setValue(null);
        $this->monitor(Form::class, function (Form $form): void {
            if (!$this->isDisabled() && $form->isAnchored() && $form->isSubmitted()) {
                $this->loadHttpData();
            }
        });
    }

    public function getTotalCopies(): int {
        return $this->totalCopies;
    }

    public function getForm(bool $throw = true): ?Form
    {
        if ($this->form) {
            return $this->form;
        }

        return parent::getForm($throw);
    }

    protected function whenAttached(): void
    {
        if ($this->attachedCalled) {
            return;
        }

        $this->loadHttpData();
        $this->createCopies();

        $this->attachedCalled = true;
    }

    public function setResetKeys(bool $reset = true): self
    {
        $this->resetKeys = $reset;

        return $this;
    }

    public function setMinCopies(int $minCopies): self
    {
        $this->minCopies = $minCopies;

        return $this;
    }

    public function setFactory(callable $factory): self
    {
        $this->factory = $factory;

        return $this;
    }

    public function getMaxCopies(): ?int
    {
        return $this->maxCopies;
    }

    public function getMinCopies(): ?int
    {
        return $this->minCopies;
    }

    public function getCopyNumber(): int
    {
        return $this->copyNumber;
    }

    public function addRemoveButton(?string $caption = null): RemoveButton
    {
        return $this->removeButton = new RemoveButton($caption);
    }

    public function addCreateButton(?string $caption = null, int $copyCount = 1): CreateButton
    {
        return $this->createButtons[$copyCount] = new CreateButton($caption, $copyCount);
    }

    protected function onCreateEvent(): void
    {
        foreach ($this->onCreate as $callback) {
            foreach ($this->getContainers() as $container) {
                $callback($container);
            }
        }
    }

    protected function onRemoveEvent(): void
    {
        foreach ($this->onRemove as $callback) {
            $callback($this);
        }
    }

    protected function isValidMaxCopies(): bool
    {
        return $this->maxCopies === null || $this->totalCopies < $this->maxCopies;
    }

    /**
     * @param Control[]|null $controls
     */
    public function validate(?array $controls = null): void
    {
        /** @var Control[] $controls */
        $controls = $controls ?? iterator_to_array($this->getComponents());

        foreach ($controls as $index => $control) {
            foreach ($this->noValidate as $item) {
                if ($control === $item) {
                    unset($controls[$index]);
                }
            }
        }

        parent::validate($controls);
    }

    /**
     * @param mixed[]|object $defaults
     */
    public function addCopy(?int $number = null, $defaults = []): Container
    {
        if (!is_numeric($number)) {
            $number = $this->createNumber();
        } else {
            /** @var Container|null $component */
            $component = $this->getComponent((string)$number, false);
            if ($component !== null) {
                return $component;
            }
        }

        $container = $this->createContainer();
        if ($defaults) {
            $container->setDefaults($defaults, $this->erase);
        }

        $this->attachContainer($container, (string)$number);
        $this->attachRemoveButton($container);

        $this->totalCopies++;

        return $container;
    }

    private function createComponents(ComponentResolver $resolver): void
    {
        $containers = [];

        // Components from httpData
        if ($this->isFormSubmitted()) {
            foreach ($resolver->getValues() as $number => $_) {
                $containers[] = $container = $this->addCopy($number);

                /** @var BaseControl $control */
                foreach ($container->getControls() as $control) {
                    $control->loadHttpData();
                }
            }
        } else { // Components from default values
            foreach ($resolver->getDefaults() as $number => $values) {
                $containers[] = $this->addCopy($number, $values);
            }
        }

        // Default number of copies
        if (!$this->isFormSubmitted() && !$this->values) {
            $copyNumber = $this->copyNumber;
            while ($copyNumber > 0 && $this->isValidMaxCopies()) {
                $containers[] = $this->addCopy();
                $copyNumber--;
            }
        }

        // Dynamic
        foreach ($this->onCreateComponents as $callback) {
            $callback($this);
        }

        // New containers, if create button hitted
        if ($this->form !== null && $resolver->isCreateAction() && $this->form->isValid()) {
            $count = $resolver->getCreateNum();
            while ($count > 0 && $this->isValidMaxCopies()) {
                $this->noValidate[] = $containers[] = $container = $this->addCopy();
                $container->setValues($this->createContainer()->getValues('array'));
                $count--;
            }
        }

        if ($this->removeButton && $this->totalCopies <= $this->minCopies) {
            foreach ($containers as $container) {
                $this->detachRemoveButton($container);
            }
        }
    }

    public function createCopies(): void
    {
        if ($this->created === true) {
            return;
        }

        $this->created = true;

        $resolver = new ComponentResolver($this->httpData, $this->values, $this->maxCopies, $this->minCopies);

        $this->attachCreateButtons();
        $this->createComponents($resolver);
        $this->detachCreateButtons();

        if ($this->maxCopies === null || $this->totalCopies < $this->maxCopies) {
            $this->attachCreateButtons();
        }

        if ($this->form !== null && $resolver->isRemoveAction() && $this->totalCopies >= $this->minCopies && !$resolver->reachedMinLimit()) {
            /** @var RemoveButton $removeButton */
            $removeButton = $this->removeButton;
            $this->form->setSubmittedBy($removeButton->create($this));

            $this->resetFormEvents();

            $this->onRemoveEvent();
        }

        // onCreateEvent
        $this->onCreateEvent();
    }

    private function detachCreateButtons(): void
    {
        foreach ($this->createButtons as $button) {
            $this->removeComponentProperly($this->getComponent($button->getComponentName()));
        }
    }

    private function attachCreateButtons(): void
    {
        foreach ($this->createButtons as $button) {
            $this->addComponent($button->create($this), $button->getComponentName());
        }
    }

    private function detachRemoveButton(Container $container): void
    {
        $button = $container->getComponent(self::SUBMIT_REMOVE_NAME);
        if ($this->getCurrentGroup() !== null) {
            $this->getCurrentGroup()->remove($button);
        }

        $container->removeComponent($button);
    }

    private function attachRemoveButton(Container $container): void
    {
        if (!$this->removeButton) {
            return;
        }

        $container->addComponent($this->removeButton->create($this), self::SUBMIT_REMOVE_NAME);
    }

    protected function isFormSubmitted(): bool
    {
        return $this->getForm() !== null && $this->getForm()->isAnchored() && $this->getForm()->isSubmitted();
    }

    public function loadHttpData(): void
    {
        if ($this->form !== null && $this->isFormSubmitted()) {
            $this->httpData = (array)Arrays::get($this->form->getHttpData(), $this->getHtmlName(), []);
        }
    }


    protected function createNumber(): int
    {
        $count = iterator_count($this->getComponents(false, Form::class));
        while ($this->getComponent((string)$count, false)) {
            $count++;
        }

        return $count;
    }

    protected function fillContainer(Container $container): void
    {
        call_user_func($this->factory, $container, $this->getForm());
    }

    /**
     * @return string[]
     */
    protected function getHtmlName(): array
    {
        return explode('-', $this->lookupPath(Form::class) ?? '');
    }

    protected function createContainer(): Container
    {
        $control = new Container();
        $control->currentGroup = $this->currentGroup;
        $this->fillContainer($control);

        return $control;
    }

    public function getRemoveButton($multiplier): string {
        return $this->removeButton->create($multiplier)->getName();
    }

    /**
     * @return Submitter[]
     */
    public function getCreateButtons(): array
    {
        if ($this->maxCopies !== null && $this->totalCopies >= $this->maxCopies) {
            return [];
        }

        $buttons = [];
        foreach ($this->createButtons as $button) {
            $buttons[$button->getCopyCount()] = $this->getComponent($button->getComponentName());
        }

        return $buttons;
    }

    /**
     * Return name of first submit button
     */
    protected function getFirstSubmit(): ?string
    {
        $submits = iterator_to_array($this->getComponents(false, SubmitButton::class));
        if ($submits) {
            return reset($submits)->getName();
        }

        return null;
    }

    protected function attachContainer(Container $container, ?string $name): void
    {
        $this->addComponent($container, $name, $this->getFirstSubmit());
    }

    protected function removeComponentProperly(IComponent $component): void
    {
        if ($this->getCurrentGroup() !== null && $component instanceof Control) {
            $this->getCurrentGroup()->remove($component);
        }

        $this->removeComponent($component);
    }

    /**
     * @internal
     */
    public function resetFormEvents(): void
    {
        if ($this->form === null) {
            return;
        }

        $this->form->onSuccess = $this->form->onError = $this->form->onSubmit = [];
    }

    /**
     * @param string|object|null $returnType
     * @param Control[]|null $controls
     * @return object|mixed[]
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     */
    public function getValues($returnType = null, ?array
    $controls = null)
    {
        if (!$this->resetKeys) {
            return parent::getValues($returnType, $controls);
        }

        /** @var mixed[] $values */
        $values = parent::getValues('array', $controls);
        $values = array_values($values);

        $returnType = $returnType === true ? 'array' : $returnType; // @phpstan-ignore-line nette backwards compatibility
        return $returnType === 'array' ? $values : ArrayHash::from($values);
    }

    /**
     * @return Iterator|Control[]
     */
    public function getControls(): Iterator
    {
        $this->createCopies();

        return $this->getComponents(true, Control::class);
    }

    /**
     * @return Iterator<int|string,Container>
     */
    public function getContainers(): Iterator
    {
        $this->createCopies();

        return $this->getComponents(false, Container::class);
    }

    /**
     * @param mixed[]|object $values
     */
    public function setValues($values, bool $erase = false): self
    {
        if ($values instanceof Traversable) {
            $values = iterator_to_array($values);
        } else {
            $values = (array)$values;
        }

        $this->values = $values;
        $this->erase = $erase;

        if ($this->created) {
            foreach ($this->getContainers() as $container) {
                $this->removeComponent($container);
            }

            $this->created = false;
            $this->detachCreateButtons();
            $this->createCopies();
        }

        return $this;
    }

    public static function register(string $name = 'addMultiplier'): void
    {
        Container::extensionMethod($name, function (Container $form, $name, $factory, $copyNumber = 1, $maxCopies = null) {
            $multiplier = new Multiplier($factory, $copyNumber, $maxCopies);
            $multiplier->setCurrentGroup($form->getCurrentGroup());

            return $form[$name] = $multiplier;
        });
    }

    /**
     * BaseControl
     *
     */

    public static string $idMask = 'frm-%s';

    /** current control value */
    protected mixed $value = null;

    /** control element template */
    protected Html $control;

    /** label element template */
    protected Html $label;

    /** @var bool|bool[] */
    protected $disabled = false;

    private array $errors = [];

    private ?bool $omitted = null;

    private Rules $rules;



    /**
     * Sets textual caption or label.
     */
    public function setCaption(string $caption): static
    {
        $this->caption = $caption;
        return $this;
    }


    public function getCaption(): string|null
    {
        return $this->caption;
    }

    /**
     * Loads HTTP data.
     */
    protected function getHttpData($type, ?string $htmlTail = null): mixed
    {
        return $this->getForm()->getHttpData($type, $this->getHtmlName() . $htmlTail);
    }


    /********************* interface Control ****************d*g**/


    /**
     * Sets control's value.
     * @internal
     */
    public function setValue($value): static
    {
        $this->value = $value;
        return $this;
    }


    /**
     * Returns control's value.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }


    /**
     * Is control filled?
     */
    public function isFilled(): bool
    {
        $value = $this->getValue();
        return $value !== null && $value !== [] && $value !== '';
    }


    /**
     * Sets control's default value.
     */
    public function setDefaultValue($value): static
    {
        $form = $this->getForm(false);
        if ($this->isDisabled() || !$form || !$form->isAnchored() || !$form->isSubmitted()) {
            $this->setValue($value);
        }

        return $this;
    }


    /**
     * Disables or enables control.
     */
    public function setDisabled(bool $value = true): static
    {
        if ($this->disabled = (bool) $value) {
            $this->setValue(null);
        } elseif (($form = $this->getForm(false)) && $form->isAnchored() && $form->isSubmitted()) {
            $this->loadHttpData();
        }

        return $this;
    }


    /**
     * Is control disabled?
     */
    public function isDisabled(): bool
    {
        return $this->disabled === true;
    }


    /**
     * Sets whether control value is excluded from $form->getValues() result.
     */
    public function setOmitted(bool $value = true): static
    {
        $this->omitted = $value;
        return $this;
    }


    /**
     * Is control value excluded from $form->getValues() result?
     */
    public function isOmitted(): bool
    {
        return $this->omitted || ($this->isDisabled() && $this->omitted === null);
    }


    /********************* rendering ****************d*g**/


    /**
     * Generates control's HTML element.
     */
    public function getControl(): Html|string
    {
        $this->setOption('rendered', true);
        $el = clone $this->control;
        return $el->addAttributes([
            'name' => $this->getHtmlName(),
            'id' => $this->getHtmlId(),
            'required' => $this->isRequired(),
            'disabled' => $this->isDisabled(),
            'data-nette-rules' => Helpers::exportRules($this->rules) ?: null,
            'data-nette-error' => $this->hasErrors(),
        ]);
    }


    /**
     * Generates label's HTML element.
     */
    public function getLabel(string $caption = null): Html|string|null
    {
        $label = clone $this->label;
        $label->for = $this->getHtmlId();
        $caption ??= $this->caption;
        $translator = $this->getForm()->getTranslator();
        $label->setText($translator ? $translator->translate($caption) : $caption);
        return $label;
    }


    public function getControlPart(): ?Html
    {
        return $this->getControl();
    }


    public function getLabelPart(): ?Html
    {
        return $this->getLabel();
    }


    /**
     * Returns control's HTML element template.
     */
    public function getControlPrototype(): Html
    {
        return $this->control;
    }


    /**
     * Returns label's HTML element template.
     */
    public function getLabelPrototype(): Html
    {
        return $this->label;
    }


    /**
     * Changes control's HTML id.
     */
    public function setHtmlId(string|bool|null $id): static
    {
        $this->control->id = $id;
        return $this;
    }


    /**
     * Returns control's HTML id.
     */
    public function getHtmlId(): string|bool|null
    {
        if (!isset($this->control->id)) {
            $form = $this->getForm();
            $prefix = $form instanceof \Nette\Application\UI\Form || $form->getName() === null
                ? ''
                : $form->getName() . '-';
            $this->control->id = sprintf(self::$idMask, $prefix . $this->lookupPath());
        }

        return $this->control->id;
    }


    /**
     * Changes control's HTML attribute.
     */
    public function setHtmlAttribute(string $name, mixed $value = true): static
    {
        $this->control->$name = $value;
        if (
            $name === 'name'
            && ($form = $this->getForm(false))
            && !$this->isDisabled()
            && $form->isAnchored()
            && $form->isSubmitted()
        ) {
            $this->loadHttpData();
        }

        return $this;
    }


    /**
     * @deprecated  use setHtmlAttribute()
     */
    public function setAttribute(string $name, mixed $value = true): static
    {
        return $this->setHtmlAttribute($name, $value);
    }


    /********************* rules ****************d*g**/


    /**
     * Adds a validation rule.
     */
    public function addRule(
        callable|string $validator,
        string|null $errorMessage = null,
        mixed $arg = null,
    ): static
    {
        $this->rules->addRule($validator, $errorMessage, $arg);
        return $this;
    }


    /**
     * Adds a validation condition a returns new branch.
     */
    public function addCondition($validator, $value = null): Rules
    {
        return $this->rules->addCondition($validator, $value);
    }


    /**
     * Adds a validation condition based on another control a returns new branch.
     */
    public function addConditionOn(Control $control, $validator, $value = null): Rules
    {
        return $this->rules->addConditionOn($control, $validator, $value);
    }


    /**
     * Adds a input filter callback.
     */
    public function addFilter(callable $filter): static
    {
        $this->getRules()->addFilter($filter);
        return $this;
    }


    public function getRules(): Rules
    {
        return $this->rules;
    }


    /**
     * Makes control mandatory.
     */
    public function setRequired(string|bool $value = true): static
    {
        $this->rules->setRequired($value);
        return $this;
    }


    /**
     * Is control mandatory?
     */
    public function isRequired(): bool
    {
        return $this->rules->isRequired();
    }


    /**
     * Adds error message to the list.
     */
    public function addError(string $message): void
    {
        $this->errors[] = $message;
    }


    /**
     * Returns errors corresponding to control.
     */
    public function getError(): ?string
    {
        return $this->errors ? implode(' ', array_unique($this->errors)) : null;
    }


    /**
     * Returns errors corresponding to control.
     */
    public function getErrors(): array
    {
        return array_unique($this->errors);
    }


    public function hasErrors(): bool
    {
        return (bool) $this->errors;
    }


    public function cleanErrors(): void
    {
        $this->errors = [];
    }


    /********************* user data ****************d*g**/


    /**
     * Sets user-specific option.
     */
    public function setOption($key, mixed $value): static
    {
        if ($value === null) {
            unset($this->options[$key]);
        } else {
            $this->options[$key] = $value;
        }

        return $this;
    }


    /**
     * Returns user-specific option.
     */
    public function getOption($key): mixed
    {
        if (func_num_args() > 1) {
            trigger_error(__METHOD__ . '() parameter $default is deprecated, use operator ??', E_USER_DEPRECATED);
            $default = func_get_arg(1);
        }
        return $this->options[$key] ?? $default ?? null;
    }


    /**
     * Returns user-specific options.
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
