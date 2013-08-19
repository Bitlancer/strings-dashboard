<?php
    echo $this->DataTables->render(
        function($view,$outputRow,$rawRow) {
            $recordId = $rawRow['DeviceDns']['id'];
            $outputRow[count($outputRow)-1] .= "<a class=\"action remove\" data-id=\"$recordId\">Remove</a>";
            return $outputRow;
    });
