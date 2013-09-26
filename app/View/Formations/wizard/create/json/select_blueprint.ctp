<?php     

    $visibleDescriptionLen = '150';     //Number of characters of the description that will be visible

    echo $this->DataTables->render(
        function($view,$outputRow,$rawRow) use($visibleDescriptionLen){

        $blueprintName = $outputRow[0];
        $blueprintDescription = $rawRow['Blueprint']['short_description'];
        if(strlen($blueprintDescription) > $visibleDescriptionLen){
            $blueprintDescription = substr($blueprintDescription,0,$visibleDescriptionLen) . "..."; 
        }

        $dataId = $rawRow['Blueprint']['id'];
        $dataSrc = "/Blueprints/summary/" . $rawRow['Blueprint']['id'] . ".json";

        $src = "<div class=\"blueprint\">";
        $src .= "<h3>";
        $src .= "<a class=\"select\" data-src=\"$dataSrc\" data-id=\"$dataId\" >$blueprintName</a>";
        $src .= "</h3>";
        $src .= "<a class=\"action select\" data-src=\"$dataSrc\" data-id=\"$dataId\" >Select</a>";
        $src .= "</div>";

        $src .= "<div class=\"blueprint-description\">";
        $src .= "<span>$blueprintDescription</span>";
        $src .= "</div>";

        return array($src);
    });
