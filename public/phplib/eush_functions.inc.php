<?php

//Get the actual user (the user logged in)
function getUser($userId) {
	//initiallize variables
        $response="{}";

        //fetch user
	$user = checkUserIDExists($userId);

        $response = json_encode($user, JSON_PRETTY_PRINT);

        return $response;
}

// TODO: For next meeting demo we will show only 1 specific project. Marcel Koek recommendation.
function getEuroBioImagingProjects(){
	$response="{}";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://xnat.bmia.nl/data/archive/projects?format=json&accessible=true");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        $response = json_encode($response);
        curl_close($curl);
	return $response;
}

function getEuroBioImagingSubjects($var){
        $response="{}";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://xnat.bmia.nl/data/archive/projects/'.$var.'/subjects?format=json');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        $response = json_encode($response);
        curl_close($curl);
        return $response;
}

function getEuroBioImagingExperiments($var, $var2){
        $response="{}";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://xnat.bmia.nl/data/archive/projects/'.$var.'/subjects/'.$var2.'/experiments?format=json');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        $response = json_encode($response);
        curl_close($curl);
        return $response;
}

function getEuroBioImagingExperimentsFormat($var, $var2, $var3){
        $response="{}";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://xnat.bmia.nl/data/archive/projects/'.$var.'/subjects/'.$var2.'/experiments/'.$var3.'?format=json');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        $response = json_encode($response);
        curl_close($curl);
        return $response;
}

function getEuroBioImagingExperimentsFiles($var, $var2, $var3){
        $response="{}";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://xnat.bmia.nl/data/archive/projects/'.$var.'/subjects/'.$var2.'/experiments/'.$var3.'/scans/ALL/files?format=zip');
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        $response = curl_exec($curl);
        curl_close($curl);
        file_put_contents("'$var'_'$var2'_'$var3'.zip", $response);
        //$response = json_encode($response);
        
        //return $response;
        return (filesize("'$var'_'$var2'_'$var3'.zip") > 0)? true : false;
}