<?php

/*
 * ************************************
 *  VIEW RELATED METHODS
 *  Get Data -> From Repository -> EGA
 * ************************************
 */

function listEGADatasets(){
	$dir_list = json_decode(listEGAOutboxFiles());
	$dataset_ids  = array_map('basename',$dir_list);
	return json_encode($dataset_ids,JSON_PRETTY_PRINT);
}

function listEGAFilesFromDataset($dataset_id){
	$files_list = json_decode(listEGAOutboxFiles($dataset_id));
	$files_ids  = array_map('basename',$files_list);
	return json_encode($files_ids,JSON_PRETTY_PRINT);
}

function listEGAOutboxFiles($remote_path="."){
	$response="[]";

	# TODO: inject EGA credentials to KC. Now, hardcoded in users.setUser()
	
	$EGA_outbox_server = 'outbox.ega-archive.org';

	# Check the user has EGA credentials
	if ($_SESSION['User']['Type'] == 3 ){
		$_SESSION['errorData']['Error'][]="Not authorized to list EGA datasets. Please, login to validate your account\'s privileges";
		return $response;
	}
	if (!isset($_SESSION['User']['linked_accounts']['EGA'])){
		$_SESSION['errorData']['Error'][]="Not authorized to list datasets. No EGA credentials found in your central euCanSHare user.\nIf you have an EGA account, please, <a href=\"helpdesk/\">email us</a> to set up a crosslink with your euCanSHare account.";
		return $response;
	}
	# Write down SSH keys
	$dirTmp   = $GLOBALS['dataDir']."/".$_SESSION['User']['id']."/".$_SESSION['User']['activeProject']."/".$GLOBALS['tmpUser_dir'];
	$priv_key = "$dirTmp/EGA_keys/id_ed25519";
	$pub_key  = "$dirTmp/EGA_keys/id_ed25519.pub";
	if (! is_file($pub_key) || !is_file($priv_key)){
		mkdir(dirname($priv_key), 0755);
		$r = file_put_contents($priv_key,$_SESSION['User']['linked_accounts']['EGA']['priv_key']);
		if (!$r) { $_SESSION['errorData']['Error'][]= "Cannot materialize EGA private SSH key. Please, double check your user configuration .. $priv_key"; return $response;}
		$r = file_put_contents($pub_key, $_SESSION['User']['linked_accounts']['EGA']['pub_key']);
		if (!$r) { $_SESSION['errorData']['Error'][]= "Cannot materialize EGA pub SSH key. Please, double check your user configuration"; return $response;}
		chmod($priv_key, 0600);
		chmod($pub_key, 0644);
	}

	# List EGA remote content of the sFTP server EGA-outbox

	$list_datasets = list_SFTP_dir($remote_path,$EGA_outbox_server,$_SESSION['User']['linked_accounts']['EGA']['username'],$priv_key,$_SESSION['User']['linked_accounts']['EGA']['passphrase']);

	if (!$list_datasets){
		return $response; 
	}
	$response = json_encode($list_datasets,JSON_PRETTY_PRINT); 
	return $response; 

	# $target_dir = "$dirTmp/EGA_outbox"; 
	# mount_EGAoutbox_sshfs($priv_key,$target_dir);

}

function list_SFTP_dir($directory, $host, $username, $pri_key="", $phrase=0){

	# Check bash script is there - it requires 'expect' lang installed 
	$sftp_listDir_script = $GLOBALS['internalTools']."/sftp_ed25519/sftp_listDir.sh";
	if (! is_file($sftp_listDir_script) ){
		$_SESSION['errorData']['Error'][]="Cannot list sFTP server contents. 'sftp' tool not correctly installed.";
		return 0;
	}
	$cmd = "$sftp_listDir_script --host $host --user $username --key $pri_key --passphrase $phrase --dir $directory";
	logger("EGA:list_SFTP_dir(): $cmd");

	$r =  proc_open($cmd, array( 0=>array("pipe","r"), 1 => array("pipe", "w"),2 => array("pipe", "w")), $pipes);
	if (!is_resource($r)) {
		$_SESSION['errorData']['Error'][]="Cannot open child process on the server. Please, report the error.";
		return 0;
	}
	$stdout = stream_get_contents($pipes[1]);
	fclose($pipes[1]);

	$stderr = stream_get_contents($pipes[2]);
	fclose($pipes[2]);

	$return_var = proc_close($r);

	if ($return_var || $stderr){
		$_SESSION['errorData']['Error'][]="Cannot retrieve data from EGA outbox. Please,try it later";
		logger("User ".$_SESSION['User']['_id']." cannot retrieve data from EGA outbox.\nSTDERR: $stderr\n STDOUT: $stdout");
		logger ("CMD:$cmd");
		return 0;
	}
	if (!preg_match('/EGA/',$stdout)){
		$_SESSION['errorData']['Error'][]="Cannot find EGA files in your EGA outbox. Please,try it later";
		logger("User ".$_SESSION['User']['_id']." cannot find /EGA/ files in your EGA outbox.\n STDERR: $stderr\n STDOUT: $stdout");
		logger ("CMD:$cmd");
		return 0;
	}
	$rr = explode("\n", trim($stdout));
	return $rr;
}

#use phpseclib3\Crypt\Base;
use phpseclib3\Net\SFTP;
use phpseclib3\Crypt\EC;
use phpseclib3\Crypt\RSA;

function mount_EGAoutbox_phpsec_USELESS($username, $pub_key, $pri_key="", $phrase=0){

	$sftp = new SFTP('outbox.ega-archive.org');

	#$key = new EC();
	$key = new RSA();
	
	$dd = $key->getSupportedKeyFormats();

	echo "ff";
	print_r($dd);
	return true;
}

function mount_EGAoutbox_USELESS($username, $pub_key, $pri_key="", $phrase=0){
    $host = 'outbox.ega-archive.org';
    $port = 22;
    $conn = ssh2_connect($host, $port);
    if (! $conn)
            throw new Exception("Could not connect to $host on port $port.");
    print_r($conn);
    print_r("______________-");

    $username = 'laia.codo@bsc.es';
    $pub_key = '/home/user/.ssh/id_ed25519.pub';
    $pri_key = '/home/user/.ssh/id_ed25519';
  
    ssh2_auth_pubkey_file($conn, $username, $pub_key, $pri_key,"bsccns");
    $F = fopen("ssh2.sftp://$conn/EGAD50000000045/EGAF50000000082", 'r');

    $sftp = ssh2_sftp($connection);

    if (! $sftp)	
	    throw new Exception("Could not initialize SFTP subsystem.");
    print_r($sftp);
    print_r("______________-");

    $F = fopen("ssh2.sftp://$sftp/EGAD50000000045/EGAF50000000082", 'r');
    if (! $F)
          throw new Exception("Could not open remote file");
    print_r($F);
    print_r("______________-");

}

/*
 * ************************************
 *  GET DATA RELATED METHODS
 *  Used when importing the resource
 * ************************************
 */




