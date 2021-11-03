<?php
$filename = "download";
$result = GetExport($db);
$file_ending = "xls";

//header info for browser
header("Content-Type: application/xls");    
header("Content-Disposition: attachment; filename=$filename.xls");  
header("Pragma: no-cache"); 
header("Expires: 0");

/*******Start of Formatting for Excel*******/   
//define separator (defines columns in excel & tabs in word)
$sep = "\t"; //tabbed character
//start of printing column names as names of MySQL fields
while($mysql_query_fields = mysqli_fetch_field($result)){
    if ($mysql_query_fields->name != "risposta")
        echo $mysql_query_fields->name . "\t";
    else {
        echo "Anno\tCampo D'Azione\tTipologia\tNumero priorità invenzione\tStatus brevetto\tLicenza\tTitolarità\tInventori Interni\tInventori Esterni\tModalità di partecipazione\tSoci interni\tPagina Web\tTitolo\tParole Chiave\tDescrizione\tCollegata Onu\tArea Onu\tValore di entrate\tIndicatori\tValore SocioEconomico\tPersonale di Riferimento\tPubblicazioni\tPartner\tBrevetto Conseguito\tData e documento\tSSD Azione\t";
    }
}

print("\n");    
//end of printing column names  
//start while loop to get data
    while($row = mysqli_fetch_array($result))
    {
        $schema_insert = "";
        for($j=0; $j<mysqli_num_fields($result);$j++)
        {
            if(!isset($row[$j]))
                $schema_insert .= "NULL".$sep;
            elseif ($row[$j] != "" and $j == 8) { //risposta
                $risp = json_decode($row[$j]);
                $schema_insert .= ((isset($risp->{'d-1'})) ? $risp->{'d-1'} : '') .$sep;
                $schema_insert .= ((isset($risp->{'d-3'})) ? GetCampoDAzione($db, 1, $risp->{'d-3'}) : '') .$sep;
                $schema_insert .= ((isset($risp->{'d-3-2'})) ? GetTipologia($db, 1, $risp->{'d-3'}, $risp->{'d-3-2'}) : '') .$sep;
                $schema_insert .= ((isset($risp->{'d-17'})) ? $risp->{'d-17'} : '') .$sep;
                $schema_insert .= ((isset($risp->{'d-18'})) ? $risp->{'d-18'} : '') .$sep;
                $schema_insert .= ((isset($risp->{'d-19'})) ? $risp->{'d-19'} : '') .$sep;
                $schema_insert .= ((isset($risp->{'d-20'})) ? $risp->{'d-20'} : '') .$sep;
                $schema_insert .= stampaRipetute($risp->{'d-21'}) .$sep;
                $schema_insert .= stampaRipetute($risp->{'d-22'}) .$sep;
                $schema_insert .= ((isset($risp->{'d-23'})) ? $risp->{'d-23'} : '') .$sep;
                $schema_insert .= stampaRipetute($risp->{'d-24'}) .$sep;
                $schema_insert .= ((isset($risp->{'d-26'})) ? $risp->{'d-26'} : '') .$sep;
                $schema_insert .= ((isset($risp->{'d-5'})) ? $risp->{'d-5'} : '') .$sep;
                $schema_insert .= ((isset($risp->{'d-6'})) ? $risp->{'d-6'} : '') .$sep;
                $schema_insert .= ((isset($risp->{'d-7'})) ? $risp->{'d-7'} : '') .$sep;
                $schema_insert .= ((isset($risp->{'d-8'})) ? $risp->{'d-8'} : '') .$sep;
                $schema_insert .= ((isset($risp->{'d-8-2'})) ? $risp->{'d-8-2'} : '') .$sep;
                $schema_insert .= ((isset($risp->{'d-9'})) ? $risp->{'d-9'} : '') .$sep;
                $schema_insert .= ((isset($risp->{'d-10'})) ? $risp->{'d-10'} : '') .$sep;
                $schema_insert .= ((isset($risp->{'d-11'})) ? $risp->{'d-11'} : '') .$sep;
                $schema_insert .= stampaRipetute($risp->{'d-12'}) .$sep;
                $schema_insert .= ((isset($risp->{'d-13'})) ? $risp->{'d-13'} : '') .$sep;
                $schema_insert .= ((isset($risp->{'d-14'})) ? $risp->{'d-14'} : '') .$sep;
                $schema_insert .= ((isset($risp->{'d-25'})) ? $risp->{'d-25'} : '') .$sep;
                $schema_insert .= ((isset($risp->{'d-15'})) ? $risp->{'d-15'} : '') .$sep;
		$schema_insert .= ((isset($risp->{'d-27'})) ? stampaRipetute($risp->{'d-27'}) : '') .$sep;
            }
            elseif ($row[$j] != "" and $j != 8)
                $schema_insert .= $row[$j].$sep;
            else
                $schema_insert .= "".$sep;
        }
        $schema_insert = str_replace($sep."$", "", $schema_insert);
        $schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
        $schema_insert .= "\t";
        print(trim($schema_insert));
        print "\n";
    }   

    function stampaRipetute($ris){
        $str='';
        if(isset($ris)){
            $aaa = (json_decode($ris));
            if (is_array($aaa) || is_object($aaa))
            foreach($aaa as $aa){
                $str .= $aa->value;
                if (next($aaa)==true) $str .= ', ';
            }
        }
        return $str;
    }
?>