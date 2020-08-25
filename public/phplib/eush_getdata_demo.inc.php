<?php
// import sampleData into into current WS user 

function getData_demo2020() { //sampleData

    $params['sampleData']=array("EUC0198100");
	
    foreach ($params['sampleData'] as $sampleName ){
        $_SESSION['errorData']['Info'][]="Importing EUC0198100 dataset";
        $dataDir = $_SESSION['User']['id'] ."/".$_SESSION['User']['activeProject'];
        $r = setUserWorkSpace_sampleData_demo2020($sampleName,$dataDir);
        if ($r=="0"){
                $_SESSION['errorData']['Warning'][] = "Cannot fully import  dataset into user workspace.";
                redirect($GLOBALS['URL']."/getdata/datasets.php");
        }else{
                $_SESSION['errorData']['Info'][] = "Dataset successfuly imported.";
                header("Location:".$GLOBALS['URL']."/workspace/");
        }
    }

}

function setUserWorkSpace_sampleData_demo2020($sampleData,$dataDir,$verbose=TRUE){

        $dataDirP   = $GLOBALS['dataDir']."/$dataDir";

        // path for dataset set
        $dt['sample_path'] = "EUC0198100";
        $dt['name'] = "EUC0198100";
        if (!$dt){
                $_SESSION['errorData']['Error'][]="No dataset named '$sampleData' found. Please, make sure the dataset is correctly registered";
                return 0;
        }
        $sampleData_rfn = $GLOBALS['sampleData']."/".$dt['sample_path'];

        // validate sample Data integrity
        $datafolders  = scanDir($GLOBALS['sampleData']."/".$dt['sample_path']);
        $meta_rfn = $GLOBALS['sampleData']."/".$dt['sample_path']."/.sample_metadata.json";
        if (!is_file($meta_rfn)){
                $_SESSION['errorData']['Warning'][]="Dataset '".$dt['name']."' has no metadata (.sample_metadata.json) to load -> $meta_rfn ";
                return 0;
        }
        // read sample Data metadata
        $meta = json_decode(file_get_contents($meta_rfn),true);
        if (count($meta) == 0 ){
                $_SESSION['errorData']['Warning'][]="Dataset '".$dt['name']."' has malformated json in '$meta_rfn'";
                return 0;
        }

        foreach ($meta as $meta_folder){
            if (!isset($meta_folder['file_path']) ){
                $_SESSION['errorData']['Warning'][]="Wrong dataset '".$dt['name']."' metadata contains elements without 'file_path' attribute. Ignoring them.";
                continue;
            }

            $r = save_fromSampleDataMetadata($meta_folder,$dataDir,$sampleData,"folder",$verbose);
            if ($r == "0")
                $_SESSION['errorData']['Warning'][]="Failed to inject dataset '".$meta_folder['file_path']."'";

            // looking for files in the folder

            $sample_rfn = $GLOBALS['sampleData']."/".$dt['sample_path']."/".$meta_folder['file_path'];
            $metaF_rfn  = "$sample_rfn/.sample_metadata.json";

            if (!is_file($metaF_rfn) ){
                $_SESSION['errorData']['Warning'][]="Dataset '".$dt['name']."' has no metadata in $sample_rfn to load. Empty directory.";
                continue;
            }
            $metaF = json_decode(file_get_contents($metaF_rfn),true);
            if (count($metaF) == 0 ){
                $_SESSION['errorData']['Warning'][]="Dataset '".$dt['name']."' has malformated json in folder '$sample_rfn'";
                continue;
            }

            foreach ($metaF as $meta_file){
                if (!isset($meta_file['file_path']) ){
                        $_SESSION['errorData']['Warning'][]="Dataset '".$dt['name']."' contains elements without 'file_path' attribute. Ignoring them.";
                        continue;
                }
                $r = save_fromSampleDataMetadata($meta_file,$dataDir,$sampleData,"file",$verbose);
                if ($r == "0")
                        $_SESSION['errorData']['Warning'][]="Failed to inject dataset '".$meta_file['file_path']."'";
            }
        }
        return 1;
}


