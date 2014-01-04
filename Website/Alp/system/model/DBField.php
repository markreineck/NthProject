<?php
class FieldBase {
	
	public $Field, $DataType;

	function FieldBase ($field, $type)
	{
		$this->Field = $field;
		$this->DataType = $type;
	}

	function FormattedValue($db, $posted)
	{
		switch ($this->DataType) {
			case 'S':
				$val = $db->MakeStringValue($posted);
				break;
			case 'I':
			case 'F':
				$val = $db->MakeNumericValue($posted);
				break;
			case 'D':
				$val = $db->MakeDateValue($posted);
				break;
			default:
				$framework->ShowErrorMessage('No bound database field type for ' . $fldid);
		}
		return $val;
	}

}

class KeyField extends FieldBase {

	public $Value;

	function FieldBase ($field, $type)
	{
		parent::FieldBase ($field, $type);
	}

	function SetValue ($val)
	{
		$this->Value = $val;
	}

	function FormattedValue($db)
	{
		return parent::FormattedValue($db,$this->Value);
	}
}

class EditField extends FieldBase {

	public $Label, $Hint;

	function EditField ($label, $field, $datatype, $help='')
	{
		parent::FieldBase ($field, $datatype);
		$this->Label = $label;
		$this->Hint = $help;
	}
}

class SelectField extends EditField {

	public $Required;

	function SelectField ($label, $field, $required=0, $help='')
	{
		parent::EditField ($label, $field, 'I', $help);
		$this->Required = $required;
	}
}

class TextField extends EditField {

	public $Min, $Max, $Mask;

	function TextField ($label, $field, $maxlen=0, $minlen=0, $help='')
	{
		parent::EditField ($label, $field, 'S', $help);
		$this->Min = $minlen;
		$this->Max = $maxlen;
	}
}

class IntField extends TextField {

	function IntField ($label, $field, $max=0, $min=0, $help='')
	{
		parent::TextField ($label, $field, 'I', $max, $min, $help);
	}
}

class FloatField extends TextField {

	function IntField ($label, $field, $max=0, $min=0, $help='')
	{
		parent::TextField ($label, $field, 'F', $max, $min, $help);
	}
}

class DateField extends TextField {

	function IntField ($label, $field, $max='', $min='', $help='')
	{
		parent::TextField ($label, $field, 'D', $max, $min, $help);
	}
}

?>