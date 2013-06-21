<?php
/*
Generate the datatables output for editing a teams sudo roles

@param $dataTable object The datatable object
*/
?>
<?php
    echo $this->DataTables->output($dataTable,
        function($view,$outputRow,$rawRow) {

        $sudoRoleId = $rawRow['SudoRole']['id'];

        $outputRow[count($outputRow)-1] .= "<a class=\"action remove\" data-id=\"$sudoRoleId\">Remove</a>";
        return $outputRow;
    });
