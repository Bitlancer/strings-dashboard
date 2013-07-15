<?php

	echo $this->DataTables->render(
		function($view,$outputRow,$rawRow) use($isAdmin){

        $keyId = $rawRow['UserKey']['id'];

		$modifiedOutputRow = $outputRow;

		//Append remove to last column
		$modifiedOutputRow[count($outputRow)-1] .= "<a class='action remove' data-id='$keyId'>Delete</a>";

		return $modifiedOutputRow;
	});
