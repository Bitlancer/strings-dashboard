<?php
    echo $this->DataTables->render(
        function($view,$outputRow,$rawRow) {

        $sudoRoleId = $rawRow['SudoRole']['id'];

        $outputRow[count($outputRow)-1] .= "<a class=\"action remove\" data-id=\"$sudoRoleId\">Remove</a>";
        return $outputRow;
    });
