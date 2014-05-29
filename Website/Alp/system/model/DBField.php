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
				$framework->ShowErrorMessage('No valid data type for ' . $this->Field);
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

class CheckField extends EditField {

	function CheckField ($label, $field, $help='')
	{
		parent::EditField ($label, $field, 'I', $help);
	}
}

class SelectField extends EditField {

	public $Required;

	function SelectField ($label, $field, $required=0, $type='I', $help='')
	{
		parent::EditField ($label, $field, $type, $help);
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

class TextAreaField extends EditField {

	public $Rows, $Cols;

	function TextAreaField ($label, $field, $rows=0, $cols=0, $help='')
	{
		parent::EditField ($label, $field, 'S', $help);
		$this->Rows = $rows;
		$this->Cols = $cols;
	}
}

class NumberField extends EditField {

	public $Min, $Max;

	function NumberField ($label, $field, $max=0, $min=0, $help='', $type)
	{
		parent::EditField ($label, $field, 'I', $help);
		$this->Min = $min;
		$this->Max = $max;
	}
}

class IntField extends NumberField {

	public $Min, $Max;

	function IntField ($label, $field, $max=0, $min=0, $help='')
	{
		parent::NumberField ($label, $field, $max, $min, $help, 'I');
	}
}

class FloatField extends NumberField {

	function IntField ($label, $field, $max=0, $min=0, $help='')
	{
		parent::NumberField ($label, $field, $max, $min, $help, 'F');
	}
}

class DateField extends EditField {

	public $Required;

	function DateField ($label, $field, $req=false, $help='')
	{
		parent::EditField ($label, $field, 'D', $help);
		$this->Required = $req;
	}
}

?>