<?php
class TestController {

public function __construct($testcase)
{
	$this->TestCnt = 0;

	$testcase->BeforeAll();

	$rootmethods = get_class_methods('TestCase');
	$casemethods = get_class_methods($testcase);
	foreach ($casemethods as $test) {
		if (array_search($test, $rootmethods, true) === FALSE) {
			$this->TestCnt++;

			$testcase->BeforeEach();
			$testcase->SetTestName($test);
			$testcase->$test($this);
			$testcase->AfterEach();
		}
	}

	$testcase->AfterAll();
	$testcase->Summarize($this->TestCnt);
}

}

abstract class TestCase extends AlpFramework 
{

	private $ErrorCnt=0;
	private $ClassName;
	private $MethodName;

  public function GetClassName()
	{
      return get_class($this);
  }

	public function SetTestName ($method)
	{
		$this->MethodName = $method;
	}

	public function Validate ($condition, $msg)
	{
		if (!$condition) {
			$this->ErrorCnt++;
			echo "<br><b>" . $this->GetClassName() . "::$this->MethodName:</b> $msg";
		}
	}

	public function Summarize($testcnt)
	{
		echo "<br><br>" . $this->GetClassName() . ": Tests executed: $testcnt. ";
		if ($this->ErrorCnt)
			echo '<b>';
		echo "Tests failed: $this->ErrorCnt";
		if ($this->ErrorCnt)
			echo '</b>';
	}

	function BeforeAll() 
	{}

	function BeforeEach() 
	{}

	function AfterAll()
	{}

	function AfterEach()
	{}

}
?>
