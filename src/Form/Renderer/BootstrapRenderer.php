<?php declare(strict_types=1);

namespace Skyttes\Form\Renderer;

use DateInput;
use Nette;
use Nette\Forms\Control;
use Nette\Forms\Controls;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\Checkbox;
use Nette\Forms\Controls\CheckboxList;
use Nette\Forms\Controls\RadioList;
use Nette\Forms\Controls\UploadControl;
use Nette\Forms\Rendering\DefaultFormRenderer;
use Nette\Utils\Html;

class BootstrapRenderer extends DefaultFormRenderer
{
    protected bool $controlsInit = false;

    public function __construct()
    {
        $this->wrappers["controls"]["container"] = null;
        $this->wrappers["pair"]["container"] = 'div class="form-group row"';
        $this->wrappers["pair"][".error"] = "has-error";
        $this->wrappers["control"]["container"] = 'div class="col-sm-12 my-2"';
        $this->wrappers["label"]["container"] =
            'div class="col-sm-12 control-label"';
        $this->wrappers["control"]["description"] =
            'div class="form-text text-muted"';
        $this->wrappers["control"]["errorcontainer"] = "div class=note";
        $this->wrappers["error"]["container"] = "section";
        $this->wrappers["error"]["item"] =
            'p class="alert alert-danger alert-form"';
    }

    public function renderBegin(): string
    {
        $this->controlsInit();
        return parent::renderBegin();
    }

    public function renderEnd(): string
    {
        $this->controlsInit();
        return parent::renderEnd();
    }

    public function renderBody(): string
    {
        $this->controlsInit();
        return parent::renderBody();
    }

    public function renderControls($parent): string
    {
        $this->controlsInit();
        return parent::renderControls($parent);
    }

    public function renderPair(Control $control): string
    {
        $this->controlsInit();

        $pair = $this->getWrapper("pair container");

        if ($control instanceof RadioList || $control instanceof CheckboxList) {
            $pair->addHtml(
                Html::el("label", ["class" => "col-12"])->addText(
                    $control->getLabel()->getText()
                )
            );
            $pair->addHtml($this->generateControls($control));
        } elseif ($control instanceof Checkbox) {
            $pair->addHtml($this->generateControls($control));
        } elseif ($control instanceof UploadControl) {
            $pair->addHtml(
                Html::el("label", ["class" => "col-12"])->addText(
                    $control->getLabel()->getText()
                )
            );
            $pair->addHtml($this->generateControls($control));
        } else {
            $pair->addHtml($this->renderLabel($control));
            $pair->addHtml($this->renderControl($control));
        }

        $pair->setAttribute("id", $control->getName() . "-container");
        $pair->class(
            $this->getValue(
                $control->isRequired() ? "pair .required" : "pair .optional"
            ),
            true
        );
        $pair->class(
            $control->hasErrors() ? $this->getValue("pair .error") : null,
            true
        );
        $pair->class($control->getOption("class"), true);
        if (++$this->counter % 2) {
            $pair->class($this->getValue("pair .odd"), true);
        }

        return $pair->render(0);
    }

    protected function generateControls(BaseControl $control): Html
    {
        $wrapper = Html::el("div", ["class" => "col-12"]);

        if ($control instanceof RadioList || $control instanceof CheckboxList) {
            foreach ($control->getItems() as $key => $labelTitle) {
                if ($control instanceof RadioList) {
                    $container = Html::el("div", [
                        "class" => "custom-control custom-radio custom-control-inline",
                    ]);
                } else {
                    $container = Html::el("div", [
                        "class" => "custom-control custom-checkbox custom-control-inline",
                    ]);
                }

                $input = $control->getControlPart($key);
                $label = $control->getLabelPart($key);

                $label->setAttribute("class", "custom-control-label");
                $input->setAttribute("class", "custom-control-input");

                $container->addHtml($input);
                $container->addHtml($label);

                $wrapper->addHtml($container);
            }
        } elseif ($control instanceof Checkbox) {
            $container = Html::el("div", [
                "class" => "custom-control custom-checkbox custom-control-inline",
            ]);

            $input = $control->getControlPart();
            $input->setAttribute("class", "custom-control-input");

            $label = Html::el("label", [
                "class" => "custom-control-label",
                "for" => $control->getHtmlId(),
            ]);
            $label->addText($control->getCaption());

            $container->addHtml($input);
            $container->addHtml($label);

            $wrapper->addHtml($container);
        } elseif ($control instanceof UploadControl) {
            $container = Html::el("div", ["class" => "my-2"]);

            $input = $control->getControlPart();
            $input->setAttribute(
                "class",
                "form-control"
            );

            $label = Html::el("label", [
                "class" => "form-label d-none",
                "for" => $control->getHtmlId(),
            ]);

            if ($control->getControl()->multiple) {
                $label->addText("Vybrat soubory");
            } else {
                $label->addText("Vybrat soubor");
            }

            $container->addHtml($input);
            $container->addHtml($label);

            $wrapper->addHtml($container);
        }

        return $wrapper;
    }

    public function renderPairMulti(array $controls): string
    {
        $this->controlsInit();
        return parent::renderPairMulti($controls);
    }

    public function renderLabel(Control $control): Html
    {
        $this->controlsInit();
        return parent::renderLabel($control);
    }

    public function renderControl(Control $control): Html
    {
        $this->controlsInit();
        return parent::renderControl($control);
    }

    protected function renderControlElement(Nette\Forms\Control $control)
    {
        return parent::renderControlElement($control); // TODO: Change the autogenerated stub
    }

    protected function controlsInit(): void
    {
        if ($this->controlsInit) {
            return;
        }

        $this->controlsInit = true;
        $this->form
            ->getElementPrototype()
            ->appendAttribute("class", "form-horizontal");

        foreach ($this->form->getControls() as $control) {
            $type = $control->getOption("type");
            if (in_array($type, ["text", "textarea", "select"], true)) {
                $control->getControlPrototype()->addClass("form-control");
            } elseif (in_array($type, ["checkbox", "radio"], true)) {
                $control
                    ->getSeparatorPrototype()
                    ->setName("div")
                    ->addClass($type);
            } elseif ($control instanceof Controls\SubmitButton) {
                $class = empty($usedPrimary)
                    ? "btn btn-primary btn-block"
                    : "btn btn-default btn-block";
                $control
                    ->getControlPrototype()
            ->setAttribute("class", $class /*. " ajax"*/);
                $usedPrimary = true;
            } elseif ($control instanceof Controls\TextBase) {
                $control
                    ->getControlPrototype()
                    ->appendAttribute("class", "form-control");
            } elseif ($control instanceof DateInput) {
                $control
                    ->getControlPrototype()
                    ->appendAttribute("class", "form-control form-control-date");
            } elseif ($control instanceof Controls\Checkbox) {
                $control->getControlPrototype()->appendAttribute("class", "switch");
                $control->getControlPart()->appendAttribute("class", "default");
            }

            if (
                $control instanceof Controls\SelectBox ||
                $control instanceof Controls\MultiSelectBox
            ) {
                $control->getControlPrototype()->appendAttribute("class", "select2");
            }
        }

        foreach ($this->form->getControls() as $control) {
            $type = $control->getOption("type");

            if (in_array($type, ["text", "textarea", "select"], true)) {
                $control
                    ->getControlPrototype()
                    ->appendAttribute("class", "form-control");
            }
        }
    }
}
