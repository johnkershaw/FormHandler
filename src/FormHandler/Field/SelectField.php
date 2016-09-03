<?php
namespace FormHandler\Field;

use FormHandler;

/**
 * Create a select form.
 *
 * It's recommended to add the validator "IsOptionValidator", to
 * check if the submitted value is also one of the options which is added.
 */
class SelectField extends AbstractFormField
{
    /**
     * Set if we can select multiple values in this selectfield, or just one
     * @var boolean
     */
    protected $multiple = false;

    /**
     * The size (number of options) which are displayed. By default this is 1.
     *
     * In most of the cases this is only used when $multple is set to true.
     * @var int
     */
    protected $size;

    /**
     * This is used to remember the current value.
     * This can be a string or an array, depending on the $multple setting.
     * @var mixed
     */
    protected $value;

    /**
     * The options of this selectfield
     *
     * @var array
     */
    protected $options = [];

    /**
     * Create a new SelectField
     *
     * @param FormHandler\Form $form
     * @param string $name
     */
    public function __construct(FormHandler\Form &$form, $name = '')
    {
        $this->form = $form;
        $this->form->addField($this);

        if (!empty($name)) {
            $this->setName($name);
        }

        $this->options = [];
    }

    /**
     * Set the name
     *
     * @param string $name
     * @return SelectField
     */
    public function setName($name)
    {
        $this->name = $name;
        // after we know the name, try to get the value for this field.
        $this->setValue($this->form->getFieldValue($this->name));
        return $this;
    }

    /**
     * Set the options of this selectfield
     *
     * @param array $options
     * @return SelectField
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        // this will auto select the options based on this fields value
        $this->selectOptionsFromValue();

        return $this;
    }

    /**
     * Set the options with an assoc array
     *
     * @param array $options
     * @param boolean $useArrayKeyAsValue
     * @return SelectField
     */
    public function setOptionsAsArray(array $options, $useArrayKeyAsValue = true)
    {
        $this->options = [];

        foreach ($options as $value => $label) {
            $option = new Option();
            $option->setLabel($label);
            $option->setValue($useArrayKeyAsValue ? $value : $label);

            $this->options[] = $option;
        }

        // this will auto select the options based on this fields value
        $this->selectOptionsFromValue();

        return $this;
    }

    /**
     * Add options with an assoc array
     *
     * @param array $options
     * @param boolean $useArrayKeyAsValue
     * @return SelectField
     */
    public function addOptionsAsArray(array $options, $useArrayKeyAsValue = true)
    {
        foreach ($options as $value => $label) {
            $option = new Option();
            $option->setLabel($label);
            $option->setValue($useArrayKeyAsValue ? $value : $label);

            $this->options[] = $option;
        }

        // this will auto select the options based on this fields value
        $this->selectOptionsFromValue();

        return $this;
    }

    /**
     * Add a option to this selectfield
     *
     * @param Option $option
     * @return SelectField
     */
    public function addOption(Option $option)
    {
        $this->options[] = $option;

        // this will auto select the options based on this fields value
        $this->selectOptionsFromValue();

        return $this;
    }

    /**
     * Add an optgroup to the options
     *
     * @param Optgroup $optgroup
     * @return SelectField
     */
    public function addOptgroup(Optgroup $optgroup)
    {
        $this->options[] = $optgroup;

        // this will auto select the options based on this fields value
        $this->selectOptionsFromValue();

        return $this;
    }

    /**
     * Add more options to this selectfield
     *
     * @param array $options
     * @return SelectField
     */
    public function addOptions(array $options)
    {
        foreach ($options as $option) {
            $this->options[] = $option;
        }
        // this will auto select the options based on this fields value
        $this->selectOptionsFromValue();

        return $this;
    }

    /**
     * Return the options which are located in this selectfield.
     * This can either be an Option or an Optgroup object.
     *
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Specifies that multiple options can be selected
     *
     * @param bool $multiple
     * @return SelectField
     */
    public function setMultiple($multiple)
    {
        $this->multiple = (bool)$multiple;
        $this->clearCache();
        return $this;
    }

    /**
     * Return if multiple options can be selected or not
     *
     * @return bool
     */
    public function isMultiple()
    {
        return $this->multiple;
    }

    /**
     * Specifies the number of visible options in a drop-down list
     *
     * @param int $size
     * @return SelectField
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * Get the number of visible options in a drop-down list
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set the value for this field and return the SelectField reference.
     * We do not yet set the option as selected, because it's likeley that the
     * options are not loaded into this field yet.
     *
     * @param mixed $value
     * @return SelectField
     */
    public function setValue($value)
    {
        parent::setValue($value);

        // this will auto select the options based on this fields value
        $this->selectOptionsFromValue();

        return $this;
    }

    /**
     * Return the value for this field.
     * This will return an array if the field
     * is setup as "multiple". Otherwise it will return a string with the selected value.
     *
     * @codeCoverageIgnore - Ignore because "$option instanceof Optgroup" is not covered while it is.
     * @return array|string
     */
    public function getValue()
    {
        $selected = [];

        // walk all options
        foreach ($this->options as $option) {
            if ($option instanceof Option) {
                if ($option->isSelected()) {
                    $selected[] = $option->getValue();
                }
            } elseif ($option instanceof Optgroup) {
                $options = $option->getOptions();
                foreach ($options as $option2) {
                    if ($option2->isSelected()) {
                        $selected[] = $option2->getValue();
                    }
                }
            }
        }

        $count = sizeof($selected);
        if ($count > 0) {
            if ($this->isMultiple()) {
                return $selected;
            } else {
                // return last selected item as value
                return $selected[$count - 1];
            }
        }

        return $this->isMultiple() ? [] : "";
    }

    /**
     * Return the option which has the given value.
     * If there are multiple options with the same value, the first will be returned.
     *
     * @codeCoverageIgnore - Ignore because "$option instanceof Optgroup" is not covered while it is.
     * @param $value
     * @return Option
     */
    public function getOptionByValue($value)
    {
        // walk all options
        foreach ($this->options as $option) {
            if ($option instanceof Option) {
                if ($option->getValue() == $value) {
                    return $option;
                }
            } elseif ($option instanceof Optgroup) {
                $options = $option->getOptions();
                foreach ($options as $option2) {
                    if ($option2->getValue() == $value) {
                        return $option2;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Remove option by value.
     *
     * In case that the option was nested in an Optgroup,
     * and it was the only option in that optgroup, then the
     * optgroup is also removed.
     *
     * @codeCoverageIgnore - Ignore because "$option instanceof Optgroup" is not covered while it is.
     * @param $value
     * @return SelectField
     */
    public function removeOptionByValue($value)
    {
        // walk all options
        $options = $this->options;

        // Empty options;
        $this->options = [];

        foreach ($options as $option) {
            if ($option instanceof Option) {
                if ($option->getValue() != $value) {
                    $this->options[] = $option;
                }
            } elseif ($option instanceof Optgroup) {
                $subOptions = [];
                $options2 = $option->getOptions();
                foreach ($options2 as $option2) {
                    if ($option2->getValue() != $value) {
                        $subOptions[] = $option2;
                    }
                }

                $option->setOptions($subOptions);

                if (sizeof($subOptions) > 0) {
                    $this->options[] = $option;
                }
            }
        }

        return $this;
    }

    /**
     * Return string representation of this field
     *
     * @codeCoverageIgnore - Ignore because "$option instanceof Optgroup" is not covered while it is.
     * @return string
     */
    public function render()
    {
        $str = '<select';

        if (!empty($this->name)) {
            $suffix = ($this->isMultiple() && substr($this->name, -1) != ']' ? "[]" : "");
            $str .= ' name="' . $this->name . $suffix . '"';
        }

        if ($this->isMultiple()) {
            $str .= ' multiple="multiple"';
        }

        if (!empty($this->size)) {
            $str .= ' size="' . $this->size . '"';
        }

        if ($this->disabled !== null && $this->disabled) {
            $str .= ' disabled="disabled"';
        }

        $str .= parent::render();
        $str .= '>';

        $value = is_array($this->value) ? $this->value : array(
            (string)$this->value
        );

        // walk all options
        foreach ($this->options as $option) {
            // set selected if the value matches
            if ($option instanceof Option) {
                $option->setSelected(in_array((string)$option->getValue(), $value));
            } elseif ($option instanceof Optgroup) {
                $options = $option->getOptions();
                foreach ($options as $option2) {
                    $option2->setSelected(in_array((string)$option2->getValue(), $value));
                }
            }

            $str .= $option->render();
        }

        $str .= '</select>';
        return $str;
    }

    /**
     * This function will use the "value" for this field to
     * select the correct options
     * @codeCoverageIgnore - Ignore because "$option instanceof Optgroup" is not covered while it is.
     */
    protected function selectOptionsFromValue()
    {
        if (!$this->options) {
            return;
        }

        // there is no current value known. So, just stop.
        if ($this->value === null) {
            return;
        }

        // walk all options
        foreach ($this->options as $option) {
            if ($option instanceof Option) {
                if (is_array($this->value)) {
                    $option->setSelected(in_array($option->getValue(), $this->value));
                } else {
                    $option->setSelected($option->getValue() == $this->value);
                }
            } elseif ($option instanceof Optgroup) {
                $options = $option->getOptions();
                if ($options) {
                    foreach ($options as $option2) {
                        if (is_array($this->value)) {
                            $option2->setSelected(in_array($option2->getValue(), $this->value));
                        } else {
                            $option2->setSelected($option2->getValue() == $this->value);
                        }
                    }
                }
            }
        }
    }
}
