<?php
namespace FormHandler\Validator;

use FormHandler\Field\AbstractFormField;
use FormHandler\Field\PassField;

/**
 * This validator will validate if the passwords
 * match in two fields
 */
class SamePasswordValidator extends AbstractValidator
{
    /**
     * The field which should be checked. This can either be an instance of a PassField,
     * or the name of a PassField in the form.
     * @var string|PassField
     */
    protected $field2;

    /**
     * Create a new SamePasswordValidator validator
     *
     * @param string|PassField $field2
     * @param string $message
     * @param bool $required
     * @throws \Exception
     */
    public function __construct($field2, $message = null, $required = true)
    {
        if ($message === null) {
            $message = dgettext('formhandler', 'The given passwords are not the same.');
        }

        $this->setRequired($required);

        $this->setErrorMessage($message);

        // is good type?
        if (!($field2 instanceof PassField) && !is_string($field2)) {
            throw new \Exception(
                'The first parameter of the SamePasswordValidator has to ' .
                'be a PassField object or the name of an PassField field!'
            );
        }

        $this->field2 = $field2;
    }

    /**
     * Set the field which should be validated.
     *
     * @param AbstractFormField $field
     * @throws \Exception
     */
    public function setField(AbstractFormField $field)
    {
        if (!($field instanceof PassField)) {
            throw new \Exception('The validator "' . get_class($this) . '" only works on a PassField!');
        }

        $this->field = $field;
    }

    /**
     * Return the instance of the second passfield
     * @return PassField
     * @throws \Exception
     */
    protected function getField2()
    {
        if (!$this->field2 instanceof PassField) {
            $this->field2 = $this->field->getForm()->getFieldByName($this->field2);
            if (!$this->field2 instanceof PassField) {
                throw new \Exception('The given name of the second PassField seems to be invalid; Field not found!');
            }
        }

        return $this->field2;
    }

    /**
     * Check if the given field is valid or not.
     *
     * @return boolean
     */
    public function isValid()
    {
        // value should not be empty
        if ($this -> required && $this->field->getValue() == "") {
            return false;
        }

        // values not the same
        if ($this->field->getValue() != $this->getField2()->getValue()) {
            return false;
        }

        // if here, it's ok
        return true;
    }
}
