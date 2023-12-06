<?php
/* Copyright (C) 2020-2023       ksar    		<ksar.ksar@gmail.com>
 * Copyright (C) 2021       	Gaëtan MAISON	<gm@ilad.org>
 * 
 * 		From an original idea of elarifr / accedinfo.com
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 	\file       doli_install.php
 *	\brief      File that help to install or upgrade dolibarr
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
@set_time_limit (120);
if (function_exists("apache_setenv")){
	@apache_setenv('no-gzip', 1);
}
@ini_set('zlib.output_compression', 0);
@ini_set('implicit_flush', 1);
for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
ob_implicit_flush(1);
 
/***********************************************************************
*																		*
*								Parameters								*
*																		*
************************************************************************/
define('SCRIPT_VERSION','1.1.0 Version');
$github_url = 'https://github.com/Dolibarr/dolibarr/archive/%s.zip';
$github_dev = 'https://github.com/Dolibarr/dolibarr/archive/develop.zip';
$github_api = 'https://api.github.com/repos/Dolibarr/dolibarr/branches';
$sourceforge_rss_url = 'https://sourceforge.net/projects/dolibarr/rss?path=/Dolibarr%20ERP-CRM';
$sourceforge_url = 'https://sourceforge.net/projects/dolibarr/files/Dolibarr%%20ERP-CRM/%s/dolibarr-%s.zip/download';
$conffile = "./conf/conf.php";
$download_file = "mydoli.zip";
$log_file = "doli_install.log";

/***********************************************************************
*																		*
*								Functions								*
*																		*
************************************************************************/

/**
 * Write logs
 *
 * @param	string		$message		Message
 * @param	array		$log_array		Array of log
 * @return	void
 */
function write_log($message, $log_array = ''){
	global $log_file ;
	
	$message = date("Y-m-d H:i:s")." ".$message."\n";
	
	@file_put_contents($log_file, $message , FILE_APPEND);
	
	if($log_array != ''){
		@file_put_contents($log_file, print_r($log_array, true) , FILE_APPEND);
	}
}


/**
 * Load the lang
 *
 * @param	string		$lang			Lang
 * @return	lang table
 */
function language($lang){
	//French Language
	if ($lang == 'fr'){
		$lang_array = array(
			"DolibarrSetup" 				=> "Installation ou Mise à jour de Dolibarr",
			"NextStep"						=> "Étape Suivante",
			"SelectLanguage"				=> "Sélection de la langue",
			"DefaultLanguage"				=> "Langue par défaut",
			"Check"							=> "Vérifications",
			"MiscellaneousChecks"			=> "Vérification des prérequis",
			"PHPVersion"					=> "Version PHP",
			"ErrorPHPVersionTooLow" 		=> "Version de PHP trop ancienne. PHP 5.5 Minimum requise. Votre Version PHP est",
			"ErrorPHPDoesNotSupportCurl"	=> "Votre version de PHP ne supporte pas l'extension Curl.",
			"ErrorPHPDoesNotSupportZip"		=> "Votre version de PHP ne supporte pas l'extension Zip.",
			"PHPSupportCurl"				=> "PHP supporte l'extension Curl.",
			"PHPSupportZip"					=> "PHP supporte l'extension Zip.",
			"PHPMemoryOK"					=> "Votre mémoire maximum de session PHP est définie à",
			"PHPMemoryTooLow"				=> "Votre mémoire maximum de session PHP est trop faible. Il est recommandé de modifier le paramètre <b>memory_limit</b> de votre fichier <b>php.ini</b> à au moins 64M octets. Elle est pour le moment définie à",
			"ErrorDocRootNotWrit"			=> "Le répertoire d'installation ne peut être écrit",
			"DocRootWrit"					=> "Le répertoire d'installation peut être écrit",
			"UpgradeDetected"				=> "Un fichier conf.php a été trouvé : <b> Mise à jour de Dolibarr </b>. Version détecté",
			"IntallNewDetected"				=> "Aucun fichier conf.php n'a été trouvé : <b> Installation Neuve de Dolibarr </b>.",
			"ChooseVersion"					=> "Choisir la version à installer",
			"Download"						=> "Téléchargement",
			"ErrorNoVersionSelectec"		=> "Vous n'avez pas sélectionné de version.",
			"UrlDownload"					=> "Adresse de téléchargement",
			"ErrorDuringDownload"			=> "Erreur pendant le téléchargement",
			"DownloadPackage"				=> "Téléchargement du package Dolibarr Version",
			"DownloadProgress"				=> "Progression du téléchargement",
			"DownloadCompleted"				=> "Téléchargement terminé. Taille du package",
			"ErrorDownloadFile"				=> "Le fichier téléchargé n'est pas une archive !",
			"Install"						=> "Installer la version téléchargée",
			"FileIsAZip"					=> "Le fichier téléchargé est bien un zip",
			"ErrorNotAZip"					=> "Le fichier téléchargé n'est pas un zip",
			"NumbersOfDirectories"			=> "Nombre de dossiers crées",
			"NumbersOfFiles"				=> "Nombre de fichiers crées",
			"NoErrors"						=> "Aucune erreur lors de l'extraction",
			"SomeErrors"					=> "Quelques erreurs lors de l'extraction. Nombre d'erreurs",
			"RedirectToInstall"				=> "L'installation ou mise à jour est maintenant terminée. En cliquant sur \"Suivant\" vous allez être redirigé vers l'installation de Dolibarr",
			"InstallLockDeleted"			=> "Le fichier install.lock a été trouvé et supprimé",
			"InstallLockNotFounded"			=> "Aucun fichier install.lock n'a été trouvé. Si il existe vous devez le supprimer manuellement",
			"InstallLockFoundNoDeleted"		=> "Le fichier install.lock existe mais n'a pas pu être supprimé",
			"DeleteScript"					=> "Supprimer ce script php du serveur (Recommandé)",
			"DeleteLog"						=> "Supprimer le fichier log de ce script (Recommandé)",
			"AFewAdditionalOptions"			=> "Quelques options supplémentaires",
			"From"							=> "depuis"
		);
	}else{
		$lang_array = array(
			"DolibarrSetup" 				=> "Dolibarr Install or Upgrade",
			"NextStep"						=> "Next Step",
			"SelectLanguage"				=> "Language selection",
			"DefaultLanguage"				=> "Default language",
			"Check"							=> "Initial Checks",
			"MiscellaneousChecks"			=> "Prerequisites check",
			"PHPVersion"					=> "PHP Version",
			"ErrorPHPVersionTooLow" 		=> "PHP version too old. PHP 5.5 Minimum is required. Your PHP Version is",
			"ErrorPHPDoesNotSupportCurl"	=> "Your PHP installation does not support Curl.",
			"PHPSupportCurl"				=> "This PHP supports Curl.",
			"ErrorPHPDoesNotSupportZip"		=> "Your PHP installation does not support Zip.",
			"PHPSupportZip"					=> "This PHP supports Zip.",
			"PHPMemoryOK"					=> "Your PHP max session memory is set to",
			"PHPMemoryTooLow"				=> "Your maximum PHP session memory is too low. It is recommended to change the <b>memory_limit</b> parameter of your <b>php.ini</b> file to at least 64M bytes. It is currently set to",
			"ErrorDocRootNotWrit"			=> "The installation directory is not writtable",
			"DocRootWrit"					=> "The installation directory is writtable",
			"UpgradeDetected"				=> "A conf.php file has been found: <b> Dolibar update </b>. Version detected",
			"IntallNewDetected"				=> "No conf.php file was found : <b> New installation of Dolibar </b>.",
			"ChooseVersion"					=> "Choose the version to be installed",
			"Download"						=> "Download",
			"ErrorNoVersionSelectec"		=> "No versions has been selected.",
			"UrlDownload"					=> "Download URL",
			"ErrorDuringDownload"			=> "Error during Download",
			"DownloadPackage"				=> "Download Dolibarr package Version",
			"DownloadProgress"				=> "Download Progress",
			"DownloadCompleted"				=> "Download completed. Package size",
			"ErrorDownloadFile"				=> "The downloaded package is not a zip file !",
			"Install"						=> "Install the downloaded version",
			"FileIsAZip"					=> "The downloaded file is a zip",
			"ErrorNotAZip"					=> "The downloaded file is not a zip",
			"NumbersOfDirectories"			=> "Numbers of directories created",
			"NumbersOfFiles"				=> "Numbers of files created",
			"NoErrors"						=> "No error during extraction",
			"SomeErrors"					=> "Some errors during extractions. Errors numbers",
			"RedirectToInstall"				=> "The Dolibarr new install or update is now over. When you will click on \"Next\" you will be redirected to dolibarr installation script",
			"InstallLockDeleted"			=> "The install.lock files has been found and removed",
			"InstallLockNotFounded"			=> "No install.lock file has been found. If it exist, you have to remove it manualy",
			"InstallLockFoundNoDeleted"		=> "The install.lock file exist but not possible to delete it",
			"DeleteScript"					=> "Delete this php script from the server (Recommended)",
			"DeleteLog"						=> "Delete the log file of this script (Recommended)",
			"AFewAdditionalOptions"			=> "A few additional options",
			"From"							=> "from"
		);
	}
	return $lang_array;
}

/**
 * Show HTML header of install pages
 *
 * @param	string		$subtitle			Title
 * @param 	string		$action    			Action code
 * @param 	array		$langs				Language
 * @return	void
 */
function pHeader($subtitle, $action = '', $langs = ''){

    // We force the content charset
    header("Content-type: text/html; charset=utf-8");
    header("X-Content-Type-Options: nosniff");

    print '<!DOCTYPE HTML>'."\n";
    print '<html>'."\n";
    print '<head>'."\n";
    print '<meta charset="utf-8">'."\n";
    print '<meta name="viewport" content="width=device-width, initial-scale=1.0">'."\n";
    print '<meta name="generator" content="Dolibarr installer">'."\n";
    print '<link rel="stylesheet" type="text/css" href="'.$_SERVER["PHP_SELF"].'?action=css">'."\n";

    print '<title>'.$langs["DolibarrSetup"].'</title>'."\n";
    print '</head>'."\n";

    print '<body>'."\n";

    print '<div class="divlogoinstall" style="text-align:center">';
    print '<img class="imglogoinstall" src="'.$_SERVER["PHP_SELF"].'?action=img&file=logo" alt="Dolibarr logo" width="300px"><br>';
    print SCRIPT_VERSION;
	print '</div><br>';

    print '<span class="titre">'.$langs["DolibarrSetup"];
    if ($subtitle) {
        print ' - '.$subtitle;
    }
    print '</span>'."\n";

    print '<form name="forminstall" style="width: 100%" action="'.$_SERVER["PHP_SELF"].($action?'?action='.$action:'').'" method="POST">'."\n";
    print '<input type="hidden" name="action" value="'.$action.'">'."\n";

    print '<table class="main" width="100%"><tr><td>'."\n";

    print '<table class="main-inside" width="100%"><tr><td>'."\n";
}

/**
 * Print HTML footer of install pages
 *
 * @param 	integer	$nonext				1=No button "Next step"
 * @param	array	$langs				Language array
 * @param	string	$selectlang			Language code
 * @return	void
 */
function pFooter($nonext = 0, $langs = '', $selectlang = ''){
   
    print '</td></tr></table>'."\n";
    print '</td></tr></table>'."\n";

    if (! $nonext){
        print '<div class="nextbutton" id="nextbutton">';
        print '<input type="submit" value="'.$langs["NextStep"].' ->"></div>';
    }
    if ($selectlang)
    {
        print '<input type="hidden" name="selectlang" value="'.$selectlang.'">';
    }

    print '</form>'."\n";
    print '</body>'."\n";
    print '</html>'."\n";
}

/**
 * Return image relative URL
 *
 * @param	string	$image			Image tag
 * @return	string 					Relative URL of the image
 */
function url_img($image){
	return $_SERVER["PHP_SELF"].'?action=img&file='.$image ;
}

/**
 * Get le list of versions from Sourcforge
 *
 * @param	string	$url			URL of the RSS field
 * @return	array 					List of versions
 */
function get_sourceforge_files($url){

	$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
	$xml = file_get_contents($url, false, $context);
	$xml = simplexml_load_string($xml);
	
	$sourceforge_versions = array();
	foreach($xml->channel->item as $file){
		if (strstr($file->link,".zip")){
			preg_match ('/[0-9]+\.[0-9]+\.[0-9]+/', $file->link , $matches);
			$sourceforge_versions[] = $matches[0];
		}
	}
	rsort($sourceforge_versions, SORT_NUMERIC );
	return $sourceforge_versions;
}

/**
 * Get le list of branches from GitHub
 *
 * @param	string	$url			URL of the github branches API
 * @return	array 					List of versions
 */
function get_github_banches($url){
	$context  = stream_context_create(array('http' => array('header' => 'User-Agent: request')));
	$jsonResponse = file_get_contents($url, false, $context);
	
	// Convertir la réponse JSON en tableau associatif
	$data = json_decode($jsonResponse, true);
	
	$github_versions = array();
	// Vérifier si la conversion a réussi
	if ($data === null) {
		// La conversion a échoué
		write_log('Github Branches answer was not a JSON '.$url.' '.$jsonResponse);
	} else {
		// La conversion a réussi

		// Parcourir chaque tableau dans la liste
		foreach ($data as $version) {
			// Accéder aux données de chaque version
			$found_version = $version['name'];
			if (preg_match('/^\d+\.\d+$/', $found_version)){
				$github_versions[] = $found_version;
			}

		}
	}
	rsort($github_versions, SORT_NUMERIC );
	return $github_versions;
}


/**
 * CURL follow redirections event if open_basedir or safe_mode are ON
 *
 * @param	resource	$ch				Curl Resource
 * @param	Int			$maxredirect	Max redirections
 * @return	curl_exec					Execution of CURL
 * Code from : https://www.php.net/manual/fr/function.curl-setopt.php#102121
 */
function curl_exec_follow($ch, &$maxredirect = null) {
    $mr = $maxredirect === null ? 5 : intval($maxredirect);
    if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $mr > 0);
        curl_setopt($ch, CURLOPT_MAXREDIRS, $mr);
    } else {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        if ($mr > 0) {
            $newurl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

            $rch = curl_copy_handle($ch);
            curl_setopt($rch, CURLOPT_HEADER, true);
            curl_setopt($rch, CURLOPT_NOBODY, true);
            curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
            curl_setopt($rch, CURLOPT_RETURNTRANSFER, true);
            do {
                curl_setopt($rch, CURLOPT_URL, $newurl);
                $header = curl_exec($rch);
                if (curl_errno($rch)) {
                    $code = 0;
                } else {
                    $code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
                    if ($code == 301 || $code == 302) {
                        preg_match('/Location:(.*?)\n/i', $header, $matches);
                        $newurl = trim(array_pop($matches));
						write_log('New URL found '.$newurl);
                    } else {
                        $code = 0;
                    }
                }
            } while ($code && --$mr);
            curl_close($rch);
            if (!$mr) {
                if ($maxredirect === null) {
					write_log('Too many redirects');
                    trigger_error('Too many redirects. When following redirects, libcurl hit the maximum amount.', E_USER_WARNING);
                } else {
                    $maxredirect = 0;
                }
                return false;
            }
            curl_setopt($ch, CURLOPT_URL, $newurl);
        }
    }
    return curl_exec($ch);
}

/**
 * CURL Progress function
 *
 * @param	resource	$ch				Curl Resource
 * @param	Int			$download_size	Downloaded size
 * @param	Int			$downloaded		Downloaded 
 * @param	Int			$upload_size	Uploaded size
 * @param	Int			$uploaded		Uploaded 
 * @return	Nothing
 * Code from : https://stackoverflow.com/questions/13958303/curl-download-progress-in-php
 */
function progress($resource,$download_size, $downloaded, $upload_size, $uploaded)
{
	static $previousProgress = 0;
    
    if ( $download_size == 0 ) {
		//Github doesn't send the size, so estimated size
        $progress = round($downloaded / 650000);
    } else {
        $progress = round($downloaded * 100 / $download_size);
	}
    
    if ( $progress > $previousProgress)
    {
        $previousProgress = $progress;
		//update JavaScript progress bar to show download progress
		echo '<script>
		//size : '.$download_size.' downloaded : '.$downloaded.'
		document.getElementById(\'prog\').value = '.$progress.';</script>'."\n";
	}
}

/**
 * Output Human file size
 *
 * @param	Int		$bytes		Size in bytes
 * @param	Int		$decimals	Numbers of decimals
 * @return	String
 * Code from : https://www.php.net/manual/fr/function.filesize.php#120250
 */
function human_filesize($bytes, $decimals = 2) {
    $factor = floor((strlen($bytes) - 1) / 3);
    if ($factor > 0) $sz = 'KMGT';
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor - 1] . 'B';
}

/**
 * Extend class ZipArchive for directory extraction
 *
 * Code from : https://www.php.net/manual/fr/ziparchive.extractto.php#116353
 */
if (class_exists("ZipArchive")){
	class my_ZipArchive extends ZipArchive{
		public function extractSubdirTo($destination, $subdir){
			$errors = array();
			$nb_directories = 0;
			$nb_files = 0;

			// Prepare dirs
			$destination = str_replace(array("/", "\\"), DIRECTORY_SEPARATOR, $destination);
			$subdir = str_replace(array("/", "\\"), "/", $subdir);

			if (substr($destination, mb_strlen(DIRECTORY_SEPARATOR, "UTF-8") * -1) != DIRECTORY_SEPARATOR)
			$destination .= DIRECTORY_SEPARATOR;

			if (substr($subdir, -1) != "/")
			$subdir .= "/";

			// Extract files
			for ($i = 0; $i < $this->numFiles; $i++){
				$filename = $this->getNameIndex($i);

				if (substr($filename, 0, mb_strlen($subdir, "UTF-8")) == $subdir){
					$relativePath = substr($filename, mb_strlen($subdir, "UTF-8"));
					$relativePath = str_replace(array("/", "\\"), DIRECTORY_SEPARATOR, $relativePath);

					if (mb_strlen($relativePath, "UTF-8") > 0){
						// Directory
						if (substr($filename, -1) == "/"){
							// New dir
							if (!is_dir($destination . $relativePath)){
								if (!@mkdir($destination . $relativePath, 0755, true)){
									$errors[$i] = $filename;
								}else{
									$nb_directories++;
								}
							}
						}else{
							if (dirname($relativePath) != "."){
								if (!is_dir($destination . dirname($relativePath))){
									// New dir (for file)
									@mkdir($destination . dirname($relativePath), 0755, true);
									$nb_directories++;
								}
							}
							
							// New file
							if (@file_put_contents($destination . $relativePath, $this->getFromIndex($i)) === false){
								$errors[$i] = $filename;
							}else{
								$nb_files++;
							}
						}
					}
				}
			}
			return array( 
				"nb_directories"	=> $nb_directories,
				"nb_files"			=> $nb_files,
				"errors"			=> $errors );
		}
	}
}

/***********************************************************************
*																		*
*								Main									*
*																		*
************************************************************************/

// initialize action variable
$action = (!empty($_GET['action'])) ? $_GET['action'] : '';
write_log('Action = '.$action);

// initialize lang table
if (!empty($_POST['selectlang'])){
	$selectlang = $_POST['selectlang'];
	$langs = language($selectlang);
}else{
	$templang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2); 
	$selectlang = '';
	$langs = language($templang);
}

//If conf.php exist, load the conf
if (file_exists($conffile)){
    include ($conffile);
	// Just to define version DOL_VERSION
	if (! defined('DOL_INC_FOR_VERSION_ERROR')) define('DOL_INC_FOR_VERSION_ERROR', '1');
	require_once './filefunc.inc.php';
	write_log('Conf file exits. Dolibarr version installed : '.DOL_VERSION);
}else{
    //we use the current folder to extract dolibarr
    $dolibarr_main_document_root=__DIR__;
    $dolibarr_main_data_root="../documents";
	write_log('Conf file doesn\'t exist');
}

//Index page
if ($action == ''){
	write_log('--- Enter in Index Page ---');
	pHeader($langs["SelectLanguage"], 'check', $langs);
	echo '<br /><br /><div class="center">'."\n";
	echo '<table><tr><td>'."\n";
	echo $langs["DefaultLanguage"].': </td>'."\n";
	echo '<td><select class="flat" id="selectlang" name="selectlang">'."\n";
	echo '<option value="fr">Français</option>'."\n";
	echo '<option value="en">English</option>'."\n";
	echo '</select></td></tr></table></div><br><br>'."\n";
	pFooter( 0, $langs, $selectlang);
}

//Check page
if ($action == 'check'){
	write_log('--- Enter in Check Page ---');
	$checkfail = 0;
	
	pHeader($langs["Check"], 'download', $langs);
	
	print '<h3><img class="valigntextbottom" src="'.url_img('gear').'" width="20" alt="Database"> '.$langs["MiscellaneousChecks"]." :</h3>\n";
	
	//Check PHP version
	if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
		print '<img src="'.url_img('tick').'" alt="Ok"> '.$langs["PHPVersion"]." ".PHP_VERSION."<br>\n";
	}else{
		print '<img src="'.url_img('error').'" alt="Error"> '.$langs["ErrorPHPVersionTooLow"]." ".PHP_VERSION."<br>\n";
		$checkfail=1;
	}
	write_log('PHP Version : '.PHP_VERSION);
	
	// Check if Curl supported
	if (! function_exists("curl_init")){
		print '<img src="'.url_img('error').'" alt="Error"> '.$langs["ErrorPHPDoesNotSupportCurl"]."<br>\n";
		$checkfail=1;
		write_log('Curl is not supported');
	}else{
		print '<img src="'.url_img('tick').'" alt="Ok"> '.$langs["PHPSupportCurl"]."<br>\n";
		write_log('Curl is supported');
	}
	
	// Check if Zip supported
	if (! class_exists("ZipArchive")){
		print '<img src="'.url_img('error').'" alt="Error"> '.$langs["ErrorPHPDoesNotSupportZip"]."<br>\n";
		$checkfail=1;	
		write_log('Zip is not supported');		
	}else{
		print '<img src="'.url_img('tick').'" alt="Ok"> '.$langs["PHPSupportZip"]."<br>\n";
		write_log('Zip is supported');
	}
	
	// Check memory
	$memrequired=64*1024*1024;
	$memmaxorig=@ini_get("memory_limit");
	$memmax=@ini_get("memory_limit");
	if ($memmaxorig != ''){
		preg_match('/([0-9]+)([a-zA-Z]*)/i', $memmax, $reg);
		if ($reg[2]){
			if (strtoupper($reg[2]) == 'G') $memmax=$reg[1]*1024*1024*1024;
			if (strtoupper($reg[2]) == 'M') $memmax=$reg[1]*1024*1024;
			if (strtoupper($reg[2]) == 'K') $memmax=$reg[1]*1024;
		}
		if ($memmax >= $memrequired || $memmax == -1){
			print '<img src="'.url_img('tick').'" alt="Ok"> '.$langs["PHPMemoryOK"]." ".$memmaxorig."<br>\n";
		}else{
			print '<img src="'.url_img('warning').'" alt="Warning"> '.$langs["PHPMemoryTooLow"]." ".$memmaxorig."<br>\n";
		}
		write_log('PHP Memory limit : '.$memmaxorig);
	}
	
	// Check if main dir is writable
	if (! is_writable($dolibarr_main_document_root)){
		print '<img src="'.url_img('error').'" alt="Error"> '.$langs["ErrorDocRootNotWrit"]."<br>\n";
		$checkfail=1;
		write_log('Directory is not writtable');		
	}else{
		print '<img src="'.url_img('tick').'" alt="Ok"> '.$langs["DocRootWrit"]."<br>\n";
		write_log('Directory is writtable');
	}
	
	print "<br>\n";
	//Display if it is new install or upgrade
	if (defined('DOL_VERSION')) {
		print $langs["UpgradeDetected"].': <b><span class="ok">'.DOL_VERSION.'</span></b>'."<br>\n";
	}else{
		print $langs["IntallNewDetected"]."<br>\n";
	}
	
	echo '<br /><br /><div class="center">'."\n";
	echo '<table><tr><td>'."\n";
	$sourceforge_versions = get_sourceforge_files($sourceforge_rss_url);
	$github_versions = get_github_banches($github_api);
	write_log('SourceForge versions found ',$sourceforge_versions);
	write_log('GitHub versions found ',$github_versions);
	echo $langs["ChooseVersion"].': </td>'."\n";
	echo '<td><select class="flat" id="selectversion" name="selectversion">'."\n";
	echo '<option value="develop">Develop Branch '.$langs["From"].' GITHUB</option>'."\n";
	foreach ($github_versions as $version){
		echo '<option value="'.$version.'">'.$version.' Branch '.$langs["From"].' GitHub</option>'."\n";
	}
	foreach ($sourceforge_versions as $version){
		echo '<option value="'.$version.'">'.$version.' '.$langs["From"].' Sourceforge</option>'."\n";
	}
	echo '</select></td></tr></table></div><br><br>'."\n";
	pFooter( $checkfail, $langs, $selectlang);
}

//Download page
if ($action == 'download'){
	
	write_log('--- Enter in Download Page ---');
	// initialize download version
	if (!empty($_POST['selectversion'])){
		if ($_POST['selectversion'] == 'develop'){
			$url_version = $github_dev ;
		}elseif (preg_match('/^\d+\.\d+$/', $_POST['selectversion'])){
			$url_version = sprintf($github_url,$_POST['selectversion']);
		}else{
			$url_version = sprintf($sourceforge_url,$_POST['selectversion'],$_POST['selectversion']);
		}	
	}else{
		$url_version = '';
	}
	write_log('URL to download version : '.$url_version);
	
	$error = 0;
	
	pHeader($langs["Download"], 'install', $langs);
	
	print '<input type="hidden" name="selectversion" value="'.$_POST['selectversion'].'">'."\n";
	
	print '<h3><img class="valigntextbottom" src="'.url_img('gear').'" width="20" alt="Database"> '.$langs["DownloadPackage"]." ".$_POST['selectversion']." :</h3>\n";
	
	//Check if we have an URL
	if ($url_version != ''){
			print '<img src="'.url_img('tick').'" alt="Ok"> '.$langs["UrlDownload"]." : ".$url_version."<br>\n";
	}else{
		print '<div class="error">'.$langs["ErrorNoVersionSelectec"].'</div>'."<br />\n";
		$error=1;
	}
	
	echo $langs["DownloadProgress"] . ' : <progress id="prog" value="0" max="100"></progress>'."<br>\n";

	//Delete the file if exist
	if (file_exists ($download_file)){
		if (@unlink ($download_file)){
			write_log('Archive already exist. Delete successful');
		}else{
			write_log('Archive already exist. Delete impossible');
		}
	}
	
	//Open the archive
	$file = fopen($download_file, 'wb');  // (w)rite mode (b)inary
	
	$new = 1;
	while ($new){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url_version);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'progress');
		curl_setopt($ch, CURLOPT_NOPROGRESS, false); // needed to make progress function work
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		$contents = curl_exec_follow($ch);
		//For Sourceforge need to extract the mirror
		if (preg_match('/<meta http-equiv="refresh" content="5; url=([^"]+)"/', $contents, $matches)){
			$url_version = $matches[1];
			write_log('New url found : '.$url_version);
		}else{
			$new = 0;
		}
	}
	if (curl_error($ch)!="") {
		//Error during download
		print '<div class="error">'.$langs["ErrorDuringDownload"]." : ".curl_error($ch)."<br />\n";
		$error=1;
		$info = curl_getinfo($ch);
		write_log('Curl Error : '.$curl_error($ch),$info);
		curl_close ($ch);
		exit();
	} else {
		//Download is completed
		fwrite($file, $contents);
		fclose($file);
		curl_close ($ch);
		write_log('Download successful');
	}
	
	//Check if it is a ZIP
	$zip = new ZipArchive;
	$res = $zip->open($download_file);
	if ($res === TRUE) {
		$zip->close();
		print '<img src="'.url_img('tick').'" alt="Ok"> '.$langs["DownloadCompleted"]." : ".human_filesize(filesize($download_file))."<br>\n";
		write_log('Downloaded archive is a zip. Size : '.human_filesize(filesize($download_file)));
	} else {
		print '<div class="error">'.$langs["ErrorDownloadFile"]." : ".$res."<br />\n";
		$error=1;
		write_log('Downloaded archive is not a zip. Error : '.$res);
	}
	
	pFooter( $error, $langs, $selectlang);
}

//Install page
if ($action == 'install'){
	
	write_log('--- Enter in Install Page ---');
	// initialize download version
	if (!empty($_POST['selectversion'])){
		$selected_version = $_POST['selectversion'] ;
		if ($selected_version == 'dev'){
			$zip_directory = 'dolibarr-develop/htdocs/';
		}elseif ($selected_version == 'stable'){
			$zip_directory = 'dolibarr-'.STABLE.'/htdocs/';
		}elseif ($selected_version == 'old_stable'){
			$zip_directory = 'dolibarr-'.OLD_STABLE.'/htdocs/';
		}else{
			$zip_directory = 'dolibarr-'.$selected_version.'/htdocs/';
		}
	}
	
	write_log('Zip directory : '.$zip_directory);
	
	$error = 0;
	
	pHeader($langs["Install"], 'redirect', $langs);
	
	print '<h3><img class="valigntextbottom" src="'.url_img('gear').'" width="20" alt="Database"> '.$langs["Install"]." ".$selected_version." :</h3>\n";
	
	//extract the zip
	$zip = new my_ZipArchive();
	if ($zip->open($download_file) === TRUE){
		
		print '<img src="'.url_img('tick').'" alt="Ok"> '.$langs["FileIsAZip"]."<br>\n";
		write_log('File is a zip');
		
		$output = $zip->extractSubdirTo("./", $zip_directory);
		$zip->close();

		print '<img src="'.url_img('tick').'" alt="Ok"> '.$langs["NumbersOfDirectories"]." : ".$output["nb_directories"]."<br>\n";
		print '<img src="'.url_img('tick').'" alt="Ok"> '.$langs["NumbersOfFiles"]." : ".$output["nb_files"]."<br>\n";
		if (count ($output["errors"]) == 0){
			print '<img src="'.url_img('tick').'" alt="Ok"> '.$langs["NoErrors"]."<br>\n";
			write_log('Unzip successful. Number of directories : '.$output["nb_directories"].' Numbers of files :'.$output["nb_files"]);
		}else{
			print '<img src="'.url_img('warning').'" alt="Warning"> '.$langs["SomeErrors"]." : ".count($output["errors"])."<br>\n";
			write_log('Unzip successful with errors. Number of directories : '.$output["nb_directories"].' Numbers of files :'.$output["nb_files"],$output["errors"]);
		}
	}else{
		print '<div class="error">'.$langs["ErrorNotAZip"].'</div>'."<br />\n";
		$error=1;
		write_log('Archive is not a zip');
	}
	
	//Delete the archive
	if (@unlink ($download_file)){
		write_log('Delete archive done');
	}else{
		write_log('Impossible to delete archive');
	}
	
	//Try to remove install.lock
	$install_lock_found = false ;
	$install_lock_deleted = false ;
	if(file_exists($dolibarr_main_document_root.'/install.lock')){
		$install_lock_found = true ;
		write_log('Install.lock found on document dir');
		if (@unlink($dolibarr_main_document_root.'/install.lock')){
			$install_lock_deleted = true ;
			write_log('Install.lock deleted');
		}
	}
    if(file_exists($dolibarr_main_data_root.'/install.lock')){
		$install_lock_found = true ;
		write_log('Install.lock found on htdocs dir');
		if (@unlink($dolibarr_main_data_root.'/install.lock')){
			$install_lock_deleted = true ;
			write_log('Install.lock deleted');
		}
	}
    if(file_exists('install.lock')){
		$install_lock_found = true ;
		write_log('Install.lock found on current dir');
		if (@unlink('install.lock')){
			$install_lock_deleted = true ;
			write_log('Install.lock deleted');
		}
	}
	
	if ($install_lock_found && $install_lock_deleted){
		print '<img src="'.url_img('tick').'" alt="Ok"> '.$langs["InstallLockDeleted"]."<br>\n";
	}
	if (!$install_lock_found){
		print '<img src="'.url_img('warning').'" alt="Warning"> '.$langs["InstallLockNotFounded"]."<br>\n";
	}
	if ($install_lock_found && !$install_lock_deleted){
		print '<img src="'.url_img('error').'" alt="Error"> '.$langs["InstallLockFoundNoDeleted"]."<br>\n";
	}
	
	if (!$error ){
		print "<br />\n";
		print "<br />\n";
		print $langs["RedirectToInstall"]."<br />\n";
		print "<br />\n";
		print $langs["AFewAdditionalOptions"]."<br />\n";
		print '<div class="label">'."\n";
		print '	<input type="checkbox" id="removescript" name="removescript" checked> '.$langs["DeleteScript"]."<br />\n";
		print '	<input type="checkbox" id="removelog" name="removelog" checked> '.$langs["DeleteLog"]."<br />\n";
	}
	
	pFooter( $error, $langs, $selectlang);
}

//Redirection page
if ($action == 'redirect'){
	
	write_log('--- Enter in Redirect Page ---');
	
	//try to remove ourselves
	if (isset($_POST['removescript'])){
		if (@unlink (__FILE__)){
			write_log('Remove of this script done');
		}else{
			write_log('Not possible to remove this script');
		}
	}
	
	//try to remove logs
	if (isset($_POST['removelog'])){
		@unlink ($log_file);
	}
		
	header("Location: install/index.php");
	exit;
}

/***********************************************************************
*																		*
*								Includes								*
*																		*
************************************************************************/

//Output the CSS
if ($action == 'css'){
	//set the content type header
	header("Content-type: text/css");
	echo "
.opacitymedium {
	opacity: 0.5;
}

body {
	font-size:14px;
	font-family: roboto,arial,tahoma,verdana,helvetica;
	/* background: #fcfcfc; */
	margin: 15px 30px 10px;
}

table.main-inside {
	padding-left: 10px;
	padding-right: 10px;
	padding-bottom: 10px;
	margin-bottom: 10px;
	margin-top: 10px;
	color: #000000;
	border-top: 1px solid #ccc;
	border-bottom: 1px solid #ccc;
	line-height: 22px;
}

table.main {
	padding-left: 6px;
	padding-right: 6px;
	padding-top: 12px;
	padding-bottom: 12px;
	background-color: #fff;
}

div.titre {
	padding: 5px 5px 5px 5px;
	margin: 0 0 0 0;
}

span.titre {
	/* font-weight: bold; */
	background: #FFFFFF;
	color: rgb(0,113,121);
	border: 1px solid #bbb;
	padding: 10px 10px 10px 10px;
	margin: 0 0 10px 10px;
}

div.soustitre {
	font-size: 15px;
	font-weight: bold;
	color: #4965B3;
	padding: 0 1.2em 0.5em 2em;
	margin: 1.2em 1.2em 1.2em 1.2em;
	border-bottom: 1px solid #999;
	border-right: 1px solid #999;
	text-align: right;
}

.minwidth100 { min-width: 100px; }
.minwidth200 { min-width: 200px; }
.minwidth300 { min-width: 300px; }
.minwidth400 { min-width: 400px; }
.minwidth500 { min-width: 500px; }
.minwidth50imp  { min-width: 50px !important; }
.minwidth100imp { min-width: 100px !important; }
.minwidth200imp { min-width: 200px !important; }
.minwidth300imp { min-width: 300px !important; }
.minwidth400imp { min-width: 400px !important; }
.minwidth500imp { min-width: 500px !important; }

tr.trlineforchoice {
    height: 4em;
}
a.button.runupgrade {
    padding: 10px;
}

/* Force values for small screen 570 */
@media only screen and (max-width: 570px)
{
	body {
		margin: 15px 4px 4px;
	}

	input, input[type=text], input[type=password], select, textarea     {
		min-width: 20px;
    	min-height: 1.4em;
    	line-height: 1.4em;
    	padding: .4em .1em;
    	border: 1px solid #BBB;
    	/* max-width: inherit; why this ? */
     }

    .hideonsmartphone { display: none; }
    .noenlargeonsmartphone { width : 50px !important; display: inline !important; }
    .maxwidthonsmartphone { max-width: 100px; }
    .maxwidth50onsmartphone { max-width: 40px; }
    .maxwidth75onsmartphone { max-width: 50px; }
    .maxwidth100onsmartphone { max-width: 70px; }
    .maxwidth150onsmartphone { max-width: 120px; }
    .maxwidth200onsmartphone { max-width: 200px; }
    .maxwidth300onsmartphone { max-width: 300px; }
    .maxwidth400onsmartphone { max-width: 400px; }
	.minwidth50imp  { min-width: 50px !important; }
    .minwidth100imp { min-width: 50px !important; }
    .minwidth200imp { min-width: 50px !important; }
    .minwidth300imp { min-width: 50px !important; }
    .minwidth400imp { min-width: 50px !important; }
    .minwidth500imp { min-width: 50px !important; }

	table.main {
    	padding-left: 0;
    	padding-right: 0;
	}

	table.main-inside {
		padding-left: 1px;
		padding-right: 1px;
		line-height: 20px;
	}

	span.titre {
	    font-size: 90%;
	    font-weight: normal;
	    background: #FFFFFF;
	    color: #444;
	    border: 1px solid #999;
	    padding: 5px 5px 5px 5px;
	    margin: 0 0 0 4px;
	}
}


input:disabled
{
	background: #FDFDFD;
	border: 1px solid #ACBCBB;
	padding: 0 0 0 0;
	margin: 0 0 0 0;
	color: #AAA !important;
	cursor: not-allowed !important;
}

input[type=submit] {
	border-color: #c5c5c5;
	border-color: rgba(0, 0, 0, 0.15) rgba(0, 0, 0, 0.15) rgba(0, 0, 0, 0.25);
	display: inline-block;
	padding: 4px 14px;
	margin-bottom: 0;
	text-align: center;
	cursor: pointer;
	color: #333333;
	text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);
	background-color: #f5f5f5;
	background-image: -moz-linear-gradient(top, #ffffff, #e6e6e6);
	background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), to(#e6e6e6));
	background-image: -webkit-linear-gradient(top, #ffffff, #e6e6e6);
	background-image: -o-linear-gradient(top, #ffffff, #e6e6e6);
	background-image: linear-gradient(to bottom, #ffffff, #e6e6e6);
	background-repeat: repeat-x;
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffffff', endColorstr='#ffe6e6e6', GradientType=0);
	border-color: #e6e6e6 #e6e6e6 #bfbfbf;
	border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
	filter: progid:DXImageTransform.Microsoft.gradient(enabled = false);
	border: 1px solid #bbbbbb;
	border-bottom-color: #a2a2a2;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	border-radius: 4px;
	-webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
	-moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
	box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
}
input:focus, textarea:focus, button:focus, select:focus {
    box-shadow: 0 0 4px #8091BF;
}
input[type=text], input[type=password] {
    border: 1px solid #ACBCBB;
    padding: 4px;
}
select {
    padding: 4px;
    background-color: #fff;
}
input[type=text]:focus, input[type=password]:focus, textarea:focus, select:focus {
    border: 1px solid #ACBCBB;
	box-shadow: 0 0 5px #C091AF;
}
input:-webkit-autofill {
    background: #FBFFEA none !important;
    -webkit-box-shadow: 0 0 0 50px #FBFFEA inset;
}

table.listofchoices, table.listofchoices tr, table.listofchoices td {
	border-collapse: collapse;
    padding: 8px;
    color: #000000;
	border: 1px solid #ccc !important;
	line-height: 18px;
}

.listofchoicesdesc {
	color: #999 !important;
}
.blinkwait {
	font-weight: bold;
	text-decoration:blink !important;
}

.installchoices table tr td  {
	margin-left: 2px;
	margin-right: 2px;
	border-bottom: 1px solid #999;
	border-right: 1px solid #999;
	color: #000000;
}

/* OK */
div.ok {
	color: #114466;
}
span.ok {
	color: #114466;
}

/* Warning */
div.warning {
	color: #777711;
}
span.warning {
	color: #777711;
}

/* Error */
div.error {
	color: #550000;
	font-weight: bold;
	padding: 0.2em 0.2em 0.2em 0;
	margin: 0.5em 0 0.5em 0;
}
span.error {
	color: #550000;
	font-weight: bold;
}

/* Next button */
div.nextbutton {
	text-align: center;
	margin-top: 10px;
	padding-top: 5px;
	padding-bottom: 5px;
	padding-right: 10px;
}


div.header {
	background-color: #dcdff4;
	border-bottom: solid black 1px;
	padding-left: 5px;
	text-align: center;
}

a:link,a:visited,a:active {
	text-decoration:none;
	color: #114466;
}
a:hover {
	text-decoration:underline;
}

a.titre {
	text-decoration:none;
}


div.comment {
	text-decoration:none;
	color:black;
}

h3 {
	margin-top: 20px;
	font-size:16px;
	font-weight: normal;
	color: rgb(100,60,20);
	/* text-shadow: 1px 1px 1px #c0c0c0; */
}

tr.bg1 {
	background-color: #E5E5E5;
}

tr.bg2 {
	background-color: #B5C5C5;
}

/* Class for parameters key and value */
td.label {
	color: #5945A3;
	padding: 5px 5px 5px 5px;
	margin: 0 0 0 0;
	border-bottom: 1px solid #CCCCDB;
}

/* Class for parameters example */
td.comment {
	color: black;
	padding: 5px 5px 5px 5px;
	margin: 0 0 0 0;
	text-decoration:none;
	font-size: 12px;
	border-bottom: 1px solid #CCCCDB;
}

.install
{
	border: 1px solid #999;
	padding: 4px 4px 4px 4px;
}

div.visible {
    display: block;
}

div.hidden {
    display: none;
}

ul {
	margin: 0;
	padding-top: 0;
	padding-bottom: 0;
}

tr.choiceselected td.listofchoicesdesc {
    color: #000 !important;
}

tr.choiceselected td .button {
    background: rgb(0,113,121);
    color: #fff;
}

a.button:link,a.button:visited,a.button:active {
    color: #888;
}
.button {
    background: #ddd;
    color: #fff;
    /* border: 1px solid #e0e0e0; */
    padding: 0.5em 0.7em;
    margin: 0 0.5em;
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
    -moz-box-shadow: 2px 2px 3px #ddd;
    -webkit-box-shadow: 2px 2px 3px #ddd;
    box-shadow: 2px 2px 3px #ddd;
}
a.button:hover {
    text-decoration:none;
}

.suggestedchoice {
	color: rgba(70, 3, 62, 0.6) !important;
    /* background-color: rgba(70, 3, 62, 0.3); */
    padding: 2px 4px;
    border-radius: 4px;
    white-space: nowrap;
}
.choiceselected {
	background-color: #f4f6f4;
	background-repeat: repeat-x;
	background-position: top left;
}

.center {
    text-align: center;
}

.valignmiddle {
	vertical-align: middle;
}

.valigntextbottom {
	vertical-align: text-bottom;
}";
	exit();
}

//Output the images
if ($action == 'img'){
	// initialize file variable
	$file = (!empty($_GET['file'])) ? $_GET['file'] : '';
	
	if ($file == 'logo'){
		header("Content-type: image/svg+xml");
		echo base64_decode("PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjwhLS0gQ3JlYXRlZCB3aXRoIElua3NjYXBlIChodHRwOi8vd3d3Lmlua3NjYXBlLm9yZy8pIC0tPgoKPHN2ZwogICB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iCiAgIHhtbG5zOmNjPSJodHRwOi8vY3JlYXRpdmVjb21tb25zLm9yZy9ucyMiCiAgIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyIKICAgeG1sbnM6c3ZnPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIKICAgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIgogICB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIKICAgeG1sbnM6c29kaXBvZGk9Imh0dHA6Ly9zb2RpcG9kaS5zb3VyY2Vmb3JnZS5uZXQvRFREL3NvZGlwb2RpLTAuZHRkIgogICB4bWxuczppbmtzY2FwZT0iaHR0cDovL3d3dy5pbmtzY2FwZS5vcmcvbmFtZXNwYWNlcy9pbmtzY2FwZSIKICAgd2lkdGg9IjcyMCIKICAgaGVpZ2h0PSIyMDAiCiAgIGlkPSJzdmczNDUwIgogICB2ZXJzaW9uPSIxLjEiCiAgIGlua3NjYXBlOnZlcnNpb249IjAuOTIuMyAoMjQwNTU0NiwgMjAxOC0wMy0xMSkiCiAgIHNvZGlwb2RpOmRvY25hbWU9ImRvbGliYXJyX2xvZ28uc3ZnIgogICBpbmtzY2FwZTpleHBvcnQtZmlsZW5hbWU9Ii9ob21lL2xkZXN0YWlsbGV1ci9naXQvZG9saWJhcnItZm91bmRhdGlvbi9sb2dvLWNsaXBhcnRzL2RvbGliYXJyX2xvZ28ucG5nIgogICBpbmtzY2FwZTpleHBvcnQteGRwaT0iNzcuMzYyODMxIgogICBpbmtzY2FwZTpleHBvcnQteWRwaT0iNzcuMzYyODMxIj4KICA8dGl0bGUKICAgICBpZD0idGl0bGUzMDcyIj5Mb2dvIERvbGliYXJyIEVSUC1DUk08L3RpdGxlPgogIDxkZWZzCiAgICAgaWQ9ImRlZnMzNDUyIj4KICAgIDxsaW5lYXJHcmFkaWVudAogICAgICAgaWQ9ImxpbmVhckdyYWRpZW50MzczNC0zLTYiPgogICAgICA8c3RvcAogICAgICAgICBzdHlsZT0ic3RvcC1jb2xvcjojNDk0OTZmO3N0b3Atb3BhY2l0eToxIgogICAgICAgICBvZmZzZXQ9IjAiCiAgICAgICAgIGlkPSJzdG9wMzczNiIgLz4KICAgICAgPHN0b3AKICAgICAgICAgc3R5bGU9InN0b3AtY29sb3I6IzQ1NDU1YTtzdG9wLW9wYWNpdHk6MSIKICAgICAgICAgb2Zmc2V0PSIxIgogICAgICAgICBpZD0ic3RvcDM3MzgiIC8+CiAgICA8L2xpbmVhckdyYWRpZW50PgogICAgPGxpbmVhckdyYWRpZW50CiAgICAgICBpbmtzY2FwZTpjb2xsZWN0PSJhbHdheXMiCiAgICAgICB4bGluazpocmVmPSIjbGluZWFyR3JhZGllbnQzNzM0LTMtNiIKICAgICAgIGlkPSJsaW5lYXJHcmFkaWVudDQ2MzYiCiAgICAgICBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIKICAgICAgIHgxPSI3NS42OTc0NDEiCiAgICAgICB5MT0iMzEwLjUzMzkxIgogICAgICAgeDI9Ijc0Mi45ODAwNCIKICAgICAgIHkyPSIzMTAuNTMzOTEiIC8+CiAgICA8bGluZWFyR3JhZGllbnQKICAgICAgIGlua3NjYXBlOmNvbGxlY3Q9ImFsd2F5cyIKICAgICAgIHhsaW5rOmhyZWY9IiNsaW5lYXJHcmFkaWVudDM3MzQtMy02IgogICAgICAgaWQ9ImxpbmVhckdyYWRpZW50ODk5IgogICAgICAgZ3JhZGllbnRVbml0cz0idXNlclNwYWNlT25Vc2UiCiAgICAgICB4MT0iNzUuNjk3NDQxIgogICAgICAgeTE9IjMxMC41MzM5MSIKICAgICAgIHgyPSI3NDIuOTgwMDQiCiAgICAgICB5Mj0iMzEwLjUzMzkxIiAvPgogIDwvZGVmcz4KICA8c29kaXBvZGk6bmFtZWR2aWV3CiAgICAgaWQ9ImJhc2UiCiAgICAgcGFnZWNvbG9yPSIjZmZmZmZmIgogICAgIGJvcmRlcmNvbG9yPSIjNjY2NjY2IgogICAgIGJvcmRlcm9wYWNpdHk9IjEuMCIKICAgICBpbmtzY2FwZTpwYWdlb3BhY2l0eT0iMCIKICAgICBpbmtzY2FwZTpwYWdlc2hhZG93PSIyIgogICAgIGlua3NjYXBlOnpvb209IjEuNDE0MjEzNiIKICAgICBpbmtzY2FwZTpjeD0iMzkxLjY3ODIiCiAgICAgaW5rc2NhcGU6Y3k9Ii01MC4zNjYwMTUiCiAgICAgaW5rc2NhcGU6Y3VycmVudC1sYXllcj0iZzQ2NDgiCiAgICAgaW5rc2NhcGU6ZG9jdW1lbnQtdW5pdHM9InB4IgogICAgIHNob3dncmlkPSJmYWxzZSIKICAgICBpbmtzY2FwZTp3aW5kb3ctd2lkdGg9IjE5MjAiCiAgICAgaW5rc2NhcGU6d2luZG93LWhlaWdodD0iMTAyMyIKICAgICBpbmtzY2FwZTp3aW5kb3cteD0iMCIKICAgICBpbmtzY2FwZTp3aW5kb3cteT0iMCIKICAgICBpbmtzY2FwZTp3aW5kb3ctbWF4aW1pemVkPSIxIgogICAgIGlua3NjYXBlOnNob3dwYWdlc2hhZG93PSJmYWxzZSIKICAgICBzaG93Ym9yZGVyPSJ0cnVlIgogICAgIGJvcmRlcmxheWVyPSJmYWxzZSIKICAgICBmaXQtbWFyZ2luLXRvcD0iMjQiCiAgICAgZml0LW1hcmdpbi1sZWZ0PSIyNCIKICAgICBmaXQtbWFyZ2luLXJpZ2h0PSIwIgogICAgIGZpdC1tYXJnaW4tYm90dG9tPSIwIgogICAgIHNob3dndWlkZXM9InRydWUiCiAgICAgaW5rc2NhcGU6Z3VpZGUtYmJveD0idHJ1ZSIKICAgICBpbmtzY2FwZTptZWFzdXJlLXN0YXJ0PSIwLDAiCiAgICAgaW5rc2NhcGU6bWVhc3VyZS1lbmQ9IjAsMCIKICAgICBpbmtzY2FwZTpwYWdlY2hlY2tlcmJvYXJkPSJ0cnVlIj4KICAgIDxpbmtzY2FwZTpncmlkCiAgICAgICB0eXBlPSJ4eWdyaWQiCiAgICAgICBpZD0iZ3JpZDE0NTgiIC8+CiAgPC9zb2RpcG9kaTpuYW1lZHZpZXc+CiAgPG1ldGFkYXRhCiAgICAgaWQ9Im1ldGFkYXRhMzQ1NSI+CiAgICA8cmRmOlJERj4KICAgICAgPGNjOldvcmsKICAgICAgICAgcmRmOmFib3V0PSIiPgogICAgICAgIDxkYzpmb3JtYXQ+aW1hZ2Uvc3ZnK3htbDwvZGM6Zm9ybWF0PgogICAgICAgIDxkYzp0eXBlCiAgICAgICAgICAgcmRmOnJlc291cmNlPSJodHRwOi8vcHVybC5vcmcvZGMvZGNtaXR5cGUvU3RpbGxJbWFnZSIgLz4KICAgICAgICA8ZGM6dGl0bGU+TG9nbyBEb2xpYmFyciBFUlAtQ1JNPC9kYzp0aXRsZT4KICAgICAgICA8Y2M6bGljZW5zZQogICAgICAgICAgIHJkZjpyZXNvdXJjZT0iaHR0cDovL2NyZWF0aXZlY29tbW9ucy5vcmcvbGljZW5zZXMvYnktbmQvNC4wLyIgLz4KICAgICAgICA8ZGM6Y3JlYXRvcj4KICAgICAgICAgIDxjYzpBZ2VudD4KICAgICAgICAgICAgPGRjOnRpdGxlPkxhdXJlbnQgRGVzdGFpbGxldXI8L2RjOnRpdGxlPgogICAgICAgICAgPC9jYzpBZ2VudD4KICAgICAgICA8L2RjOmNyZWF0b3I+CiAgICAgICAgPGRjOnJpZ2h0cz4KICAgICAgICAgIDxjYzpBZ2VudD4KICAgICAgICAgICAgPGRjOnRpdGxlPkxhdXJlbnQgRGVzdGFpbGxldXI8L2RjOnRpdGxlPgogICAgICAgICAgPC9jYzpBZ2VudD4KICAgICAgICA8L2RjOnJpZ2h0cz4KICAgICAgPC9jYzpXb3JrPgogICAgICA8Y2M6TGljZW5zZQogICAgICAgICByZGY6YWJvdXQ9Imh0dHA6Ly9jcmVhdGl2ZWNvbW1vbnMub3JnL2xpY2Vuc2VzL2J5LW5kLzQuMC8iPgogICAgICAgIDxjYzpwZXJtaXRzCiAgICAgICAgICAgcmRmOnJlc291cmNlPSJodHRwOi8vY3JlYXRpdmVjb21tb25zLm9yZy9ucyNSZXByb2R1Y3Rpb24iIC8+CiAgICAgICAgPGNjOnBlcm1pdHMKICAgICAgICAgICByZGY6cmVzb3VyY2U9Imh0dHA6Ly9jcmVhdGl2ZWNvbW1vbnMub3JnL25zI0Rpc3RyaWJ1dGlvbiIgLz4KICAgICAgICA8Y2M6cmVxdWlyZXMKICAgICAgICAgICByZGY6cmVzb3VyY2U9Imh0dHA6Ly9jcmVhdGl2ZWNvbW1vbnMub3JnL25zI05vdGljZSIgLz4KICAgICAgICA8Y2M6cmVxdWlyZXMKICAgICAgICAgICByZGY6cmVzb3VyY2U9Imh0dHA6Ly9jcmVhdGl2ZWNvbW1vbnMub3JnL25zI0F0dHJpYnV0aW9uIiAvPgogICAgICA8L2NjOkxpY2Vuc2U+CiAgICA8L3JkZjpSREY+CiAgPC9tZXRhZGF0YT4KICA8ZwogICAgIGlkPSJsYXllcjEiCiAgICAgaW5rc2NhcGU6bGFiZWw9IkxheWVyIDEiCiAgICAgaW5rc2NhcGU6Z3JvdXBtb2RlPSJsYXllciIKICAgICBzdHlsZT0iZGlzcGxheTppbmxpbmUiCiAgICAgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTUxLjQxMzY4MSwtMTk5LjYwOTU3KSI+CiAgICA8ZwogICAgICAgaWQ9Imc0NTkyIj4KICAgICAgPGcKICAgICAgICAgc3R5bGU9ImZpbGw6dXJsKCNsaW5lYXJHcmFkaWVudDQ2MzYpO2ZpbGwtb3BhY2l0eToxIgogICAgICAgICBpZD0iZzQ2MjYiPgogICAgICAgIDxnCiAgICAgICAgICAgc3R5bGU9ImZpbGw6dXJsKCNsaW5lYXJHcmFkaWVudDg5OSkiCiAgICAgICAgICAgaWQ9Imc0NjQ4Ij4KICAgICAgICAgIDxwYXRoCiAgICAgICAgICAgICBzb2RpcG9kaTpub2RldHlwZXM9ImNjY2Njc2Nzc3NjY3Nzc2MiCiAgICAgICAgICAgICBkPSJtIDExMi4zMjkxMywyODAuMjk0NzYgdiA5OC4wMjU4OSBsIC0zNi42MzE2ODgsLTAuNDAxMjMgdiAwLjQwMTIzIC0xMzcuMzU2NzIgaCA0Ny43Mjk0NjggYyAyMC45MzIzOSwwIDM3LjMyMzQxLDUuMjg0MjQgNDkuMTczMDcsMTUuODUyNzIgMTQuMDc1MjMsMTIuNjQyMDQgMjEuMTEyODUsMjkuNzk5MSAyMS4xMTI4NSw1MS40NzExNyAwLDIwLjczNTYzIC02LjAxNTA2LDM3LjY1ODU3IC0xOC4wNDUxNyw1MC43Njg4NCAtMTIuMDMwMTEsMTMuMTEwMjcgLTI3LjIwNjM3LDE5LjM1MjkxIC00Ni4xNTM4LDE5LjM1MjkxIC0yLjIyNTU3LDAgLTYuMjM5NjgsLTAuMDcxMyAtMTEuMjI5ODIsLTAuMDg4OCB2IC00MC4yMzM3OSBsIDEwLjA4OTU0LC0wLjA1NDIgYyAyMS4zNTMxNCwtMC4xMTQ2OSAyNy44MDUzLC05Ljg3ODg1IDI3LjgwNTMsLTI5Ljc0NDkyIDAsLTE4LjY2MjA2IC0xMC40OTYyNywtMjcuOTkzMDkgLTMxLjQ4ODgxLC0yNy45OTMwOSB6IgogICAgICAgICAgICAgc3R5bGU9ImZvbnQtc3R5bGU6bm9ybWFsO2ZvbnQtdmFyaWFudDpub3JtYWw7Zm9udC13ZWlnaHQ6Ym9sZDtmb250LXN0cmV0Y2g6bm9ybWFsO2ZvbnQtc2l6ZToxOTQuODU4MjMwNTlweDtsaW5lLWhlaWdodDoxMjUlO2ZvbnQtZmFtaWx5OidCYXVoYXVzIDkzJzstaW5rc2NhcGUtZm9udC1zcGVjaWZpY2F0aW9uOidCYXVoYXVzIDkzJzt0ZXh0LWFsaWduOnN0YXJ0O3dyaXRpbmctbW9kZTpsci10Yjt0ZXh0LWFuY2hvcjpzdGFydDtmaWxsOiMyNjNjNWM7ZmlsbC1vcGFjaXR5OjE7ZmlsbC1ydWxlOm5vbnplcm87c3Ryb2tlOm5vbmU7c3Ryb2tlLXdpZHRoOjEuNzA3Mjk0NztzdHJva2UtbGluZWNhcDpyb3VuZDtzdHJva2UtbWl0ZXJsaW1pdDo0O3N0cm9rZS1kYXNoYXJyYXk6bm9uZTtzdHJva2Utb3BhY2l0eTowLjE4NDMxMzczIgogICAgICAgICAgICAgaWQ9InBhdGg4NDYzIgogICAgICAgICAgICAgaW5rc2NhcGU6Y29ubmVjdG9yLWN1cnZhdHVyZT0iMCIgLz4KICAgICAgICAgIDxwYXRoCiAgICAgICAgICAgICBkPSJtIDI1MS4xODY2OSwyNzQuMjYzMjIgcSAxOS4xMjc4NywwIDMyLjc1MTk3LDE1LjM1MTA2IDEzLjcxNDMyLDE1LjI1MDcxIDEzLjcxNDMyLDM2LjYyMTc5IDAsMjEuNjcyMDYgLTEzLjg5NDc4LDM2LjkyMjc5IC0xMy44MDQ1NSwxNS4yNTA3MSAtMzMuMzgzNTYsMTUuMjUwNzEgLTE5LjU3ODk5LDAgLTMzLjQ3Mzc3LC0xNS4yNTA3MSAtMTMuODk0NzksLTE1LjM1MTA1IC0xMy44OTQ3OSwtMzYuOTIyNzkgMCwtMjEuOTczMDggMTMuODk0NzksLTM2LjkyMjggMTMuODk0NzgsLTE1LjA1MDA1IDM0LjI4NTgyLC0xNS4wNTAwNSB6IG0gLTAuOTAyMjcsMzcuMDIzMTMgcSAtNS40MTM1NSwwIC05LjIwMzAzLDQuNDE0NjggLTMuNzg5NDksNC4zMTQzNSAtMy43ODk0OSwxMC42MzUzNyAwLDYuMjIwNjggMy43ODk0OSwxMC42MzUzNyAzLjg3OTcxLDQuNDE0NjggOS4yMDMwMyw0LjQxNDY4IDUuNDEzNTYsMCA5LjIwMzA0LC00LjQxNDY4IDMuODc5NywtNC40MTQ2OSAzLjg3OTcsLTEwLjYzNTM3IDAsLTYuMzIxMDIgLTMuNzg5NDcsLTEwLjYzNTM3IC0zLjc4OTQ5LC00LjQxNDY4IC05LjI5MzI3LC00LjQxNDY4IHoiCiAgICAgICAgICAgICBzdHlsZT0iZm9udC1zdHlsZTpub3JtYWw7Zm9udC12YXJpYW50Om5vcm1hbDtmb250LXdlaWdodDpib2xkO2ZvbnQtc3RyZXRjaDpub3JtYWw7Zm9udC1zaXplOjE5NC44NTgyMzA1OXB4O2xpbmUtaGVpZ2h0OjEyNSU7Zm9udC1mYW1pbHk6J0JhdWhhdXMgOTMnOy1pbmtzY2FwZS1mb250LXNwZWNpZmljYXRpb246J0JhdWhhdXMgOTMnO3RleHQtYWxpZ246c3RhcnQ7d3JpdGluZy1tb2RlOmxyLXRiO3RleHQtYW5jaG9yOnN0YXJ0O2ZpbGw6IzI2M2M1YztmaWxsLW9wYWNpdHk6MTtmaWxsLXJ1bGU6bm9uemVybztzdHJva2U6bm9uZTtzdHJva2Utd2lkdGg6MS43MDcyOTQ3O3N0cm9rZS1saW5lY2FwOnJvdW5kO3N0cm9rZS1taXRlcmxpbWl0OjQ7c3Ryb2tlLWRhc2hhcnJheTpub25lO3N0cm9rZS1vcGFjaXR5OjAuMTg0MzEzNzMiCiAgICAgICAgICAgICBpZD0icGF0aDg0NjUiCiAgICAgICAgICAgICBpbmtzY2FwZTpjb25uZWN0b3ItY3VydmF0dXJlPSIwIiAvPgogICAgICAgICAgPHBhdGgKICAgICAgICAgICAgIHNvZGlwb2RpOm5vZGV0eXBlcz0iY2NjY2MiCiAgICAgICAgICAgICBkPSJNIDM0NS40NzI2OCwyNDEuMDUyODcgViAzNzguNDA5NTYgSCAzMTEuNzI4MjIgViAyNDEuMDUyODcgWiIKICAgICAgICAgICAgIHN0eWxlPSJmb250LXN0eWxlOm5vcm1hbDtmb250LXZhcmlhbnQ6bm9ybWFsO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1zdHJldGNoOm5vcm1hbDtmb250LXNpemU6MTk0Ljg1ODIzMDU5cHg7bGluZS1oZWlnaHQ6MTI1JTtmb250LWZhbWlseTonQmF1aGF1cyA5Myc7LWlua3NjYXBlLWZvbnQtc3BlY2lmaWNhdGlvbjonQmF1aGF1cyA5Myc7dGV4dC1hbGlnbjpzdGFydDt3cml0aW5nLW1vZGU6bHItdGI7dGV4dC1hbmNob3I6c3RhcnQ7ZmlsbDojMjYzYzVjO2ZpbGwtb3BhY2l0eToxO2ZpbGwtcnVsZTpub256ZXJvO3N0cm9rZTpub25lO3N0cm9rZS13aWR0aDoxLjcwNzI5NDc7c3Ryb2tlLWxpbmVjYXA6cm91bmQ7c3Ryb2tlLW1pdGVybGltaXQ6NDtzdHJva2UtZGFzaGFycmF5Om5vbmU7c3Ryb2tlLW9wYWNpdHk6MC4xODQzMTM3MyIKICAgICAgICAgICAgIGlkPSJwYXRoODQ2NyIKICAgICAgICAgICAgIGlua3NjYXBlOmNvbm5lY3Rvci1jdXJ2YXR1cmU9IjAiIC8+CiAgICAgICAgICA8cGF0aAogICAgICAgICAgICAgc29kaXBvZGk6bm9kZXR5cGVzPSJjc3Njc2NzY2NzY3Nzc2Njc2NjIgogICAgICAgICAgICAgZD0ibSA0NTAuNjc2LDIzOS45ODQ1OCB2IDgxLjk3MjYxIGMgMCwxMy4zMTA5MyA0LjMwMDc2LDE5Ljk2NjQgMTIuOTAyMjksMTkuOTY2NCAzLjY2OTE4LDAgNi43NjY5MywtMS4zNzEyMyA5LjI5MzI2LC00LjExMzY4IDIuNTg2NDcsLTIuODA5MzUgMy44Nzk3MSwtNi4xODcyNSAzLjg3OTcxLC0xMC4xMzM3IDAsLTQuMDgwMjMgLTEuMjAzMDEsLTcuNDkxNTggLTMuNjA5MDMsLTEwLjIzNDAzIC0yLjQwNjAzLC0yLjgwOTM1IC01LjM1MzQsLTQuMjE0MDIgLTguODQyMTMsLTQuMjE0MDIgLTIuNDY2MTcsMCAtOC4yMzEyMywtMC4wMTMgLTguMjMxMjMsLTAuMDEzIGwgLTAuMjUsLTM4LjA4NzQ3IGMgMCwwIDYuOTc3NDcsLTAuMjI3MDEgOS4wMjI1OCwtMC4yMjcwMSAxMi42OTE3NywwIDIzLjU0ODk1LDUuMDgzNTggMzIuNTcxNTMsMTUuMjUwNzMgOS4wODI3MywxMC4xNjcxNCAxMy42MjQxLDIyLjQwNzg1IDEzLjYyNDEsMzYuNzIyMTIgMCwxNC42NDg3MiAtNS4xNDU2MiwyNi41MTY2IC0xMy44MDQ1NSwzNy4wMjMxMyAtOC41NzgwMywxMC40MDgzNiAtMjIuOTg2MjEsMTQuNTk5OTMgLTMzLjkyNDkxLDE0LjUxMTU0IC02LjczNjY0LC0wLjA1NDQgLTEzLjMyMzM1LC0xLjEwMDI0IC0xOS43NTk0NywtNC4zNzc4NCAtNi4zNzU5NSwtMy4yNzc1NyAtMTEuNjM5MTIsLTcuNjkyMjUgLTE1Ljc4OTUxLC0xMy4yNDQwNCAtNy4yMTgwNywtOS42MzIwMyAtMTAuODI3MSwtMjIuMTA2ODYgLTEwLjgyNzEsLTM3LjQyNDQ3IHYgLTgzLjM3NzI4IHoiCiAgICAgICAgICAgICBzdHlsZT0iZm9udC1zdHlsZTpub3JtYWw7Zm9udC12YXJpYW50Om5vcm1hbDtmb250LXdlaWdodDpib2xkO2ZvbnQtc3RyZXRjaDpub3JtYWw7Zm9udC1zaXplOjE5NC44NTgyMzA1OXB4O2xpbmUtaGVpZ2h0OjEyNSU7Zm9udC1mYW1pbHk6J0JhdWhhdXMgOTMnOy1pbmtzY2FwZS1mb250LXNwZWNpZmljYXRpb246J0JhdWhhdXMgOTMnO3RleHQtYWxpZ246c3RhcnQ7d3JpdGluZy1tb2RlOmxyLXRiO3RleHQtYW5jaG9yOnN0YXJ0O2ZpbGw6IzI2M2M1YztmaWxsLW9wYWNpdHk6MTtmaWxsLXJ1bGU6bm9uemVybztzdHJva2U6bm9uZTtzdHJva2Utd2lkdGg6MS43MDcyOTQ3O3N0cm9rZS1saW5lY2FwOnJvdW5kO3N0cm9rZS1taXRlcmxpbWl0OjQ7c3Ryb2tlLWRhc2hhcnJheTpub25lO3N0cm9rZS1vcGFjaXR5OjAuMTg0MzEzNzMiCiAgICAgICAgICAgICBpZD0icGF0aDg0NzEiCiAgICAgICAgICAgICBpbmtzY2FwZTpjb25uZWN0b3ItY3VydmF0dXJlPSIwIiAvPgogICAgICAgICAgPHBhdGgKICAgICAgICAgICAgIHNvZGlwb2RpOm5vZGV0eXBlcz0iY2Nzc3Njc2NzY2NzY3Njc2NzY2MiCiAgICAgICAgICAgICBkPSJtIDU3NS42NjY0OSwzNDIuOTgzNTQgLTAuMTU2MjUsMzUuNDI2MDIgYyAwLDAgLTUuMDc0NSwwIC03LjM2MDIyLDAgLTEzLjUzMzg3LDAgLTI0LjgxMjEsLTMuMjQ0MDMgLTMzLjgzNDY4LC0xMy4xNDM2MiAtOS4wMjI1OSwtOS44OTk1OSAtMTMuNTMzODgsLTIyLjMwNzUzIC0xMy41MzM4OCwtMzcuMjIzOCAwLC0xNC41MTQ5NCA0LjU0MTM3LC0yNi44MjI1NCAxMy42MjQxLC0zNi45MjI3OSA5LjE0Mjg5LC0xMC4xMDAyNiAyMC4yNDA2NiwtMTUuMTUwMzkgMzMuMjkzMzMsLTE1LjE1MDM5IDE0LjU1NjQzLDAgMjYuMDE1MTEsNC43NDkxMyAzNC4zNzYwNCwxNC4yNDczOSA4LjQyMTA4LDkuNDMxMzYgMTIuNjMxNjIsMjIuMzQwOTYgMTIuNjMxNjIsMzguNzI4NzkgbCAtMC4wNjI1LDQ5LjQ2NDQyIGggLTMzLjgwNjk2IGwgMC4xMjUsLTQ2Ljc1NTQxIGMgMC4wMTQxLC01LjI4NDIxIC0xLjE3MjkzLC05LjQ5ODI0IC0zLjUxODgsLTEyLjY0MjAzIC0yLjI4NTczLC0zLjE0Mzc5IC01LjM4MzQ4LC00LjcxNTY5IC05LjI5MzI3LC00LjcxNTY5IC0zLjU0ODg5LDAgLTYuNjE2NTcsMS40MzgxMSAtOS4yMDMwNCw0LjMxNDM0IC0yLjU4NjQ3LDIuODc2MjMgLTMuODc5NzEsNi4yODc1OCAtMy44Nzk3MSwxMC4yMzQwNSAwLDQuMTQ3MTIgMS4xNzI5NCw3LjU1ODQ2IDMuNTE4ODEsMTAuMjM0MDMgMi4zNDU4NywyLjY3NTU2IDUuMzIzMzMsNC4wMTMzNCA4LjkzMjM2LDQuMDEzMzQgMi45NDczNywwIDguMTQ4MDUsLTAuMTA4NjcgOC4xNDgwNSwtMC4xMDg2NyB6IgogICAgICAgICAgICAgc3R5bGU9ImZvbnQtc3R5bGU6bm9ybWFsO2ZvbnQtdmFyaWFudDpub3JtYWw7Zm9udC13ZWlnaHQ6Ym9sZDtmb250LXN0cmV0Y2g6bm9ybWFsO2ZvbnQtc2l6ZToxOTQuODU4MjMwNTlweDtsaW5lLWhlaWdodDoxMjUlO2ZvbnQtZmFtaWx5OidCYXVoYXVzIDkzJzstaW5rc2NhcGUtZm9udC1zcGVjaWZpY2F0aW9uOidCYXVoYXVzIDkzJzt0ZXh0LWFsaWduOnN0YXJ0O3dyaXRpbmctbW9kZTpsci10Yjt0ZXh0LWFuY2hvcjpzdGFydDtmaWxsOiMyNjNjNWM7ZmlsbC1vcGFjaXR5OjE7ZmlsbC1ydWxlOm5vbnplcm87c3Ryb2tlOm5vbmU7c3Ryb2tlLXdpZHRoOjEuNzA3Mjk0NztzdHJva2UtbGluZWNhcDpyb3VuZDtzdHJva2UtbWl0ZXJsaW1pdDo0O3N0cm9rZS1kYXNoYXJyYXk6bm9uZTtzdHJva2Utb3BhY2l0eTowLjE4NDMxMzczIgogICAgICAgICAgICAgaWQ9InBhdGg4NDczIgogICAgICAgICAgICAgaW5rc2NhcGU6Y29ubmVjdG9yLWN1cnZhdHVyZT0iMCIgLz4KICAgICAgICAgIDxwYXRoCiAgICAgICAgICAgICBzb2RpcG9kaTpub2RldHlwZXM9ImNjc3NjY3Njc2MiCiAgICAgICAgICAgICBkPSJtIDY4My42NjMwNSwyNzUuODc2NjcgMC4wMDQsMzguOTE2NjIgYyAwLDAgLTQuNDE4NzIsMC4xMDUxNyAtNi4zNDM1MywwLjEwNTE3IC02LjEzNTM1LDAgLTkuMjAzMDMsNS4yMTczNSAtOS4yMDMwMywxNS42NTIwNCB2IDQ3Ljg1OTA2IGggLTMzLjc0NDQ3IHYgLTU1LjA4MzA4IGMgMCwtMTQuNDQ4MDUgMy41NDg4OCwtMjUuOTUyOTggMTAuNjQ2NjUsLTM0LjUxNDc5IDcuMDk3NzcsLTguNjI4NjkgMTYuNTcxNDgsLTEyLjk0MzA0IDI4LjQyMTE0LC0xMi45NDMwNCAyLjY0NjYyLDAgMTAuMjE5NDUsMC4wMDggMTAuMjE5NDUsMC4wMDggeiIKICAgICAgICAgICAgIHN0eWxlPSJmb250LXN0eWxlOm5vcm1hbDtmb250LXZhcmlhbnQ6bm9ybWFsO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1zdHJldGNoOm5vcm1hbDtmb250LXNpemU6MTk0Ljg1ODIzMDU5cHg7bGluZS1oZWlnaHQ6MTI1JTtmb250LWZhbWlseTonQmF1aGF1cyA5Myc7LWlua3NjYXBlLWZvbnQtc3BlY2lmaWNhdGlvbjonQmF1aGF1cyA5Myc7dGV4dC1hbGlnbjpzdGFydDt3cml0aW5nLW1vZGU6bHItdGI7dGV4dC1hbmNob3I6c3RhcnQ7ZmlsbDojMjYzYzVjO2ZpbGwtb3BhY2l0eToxO2ZpbGwtcnVsZTpub256ZXJvO3N0cm9rZTpub25lO3N0cm9rZS13aWR0aDoxLjcwNzI5NDc7c3Ryb2tlLWxpbmVjYXA6cm91bmQ7c3Ryb2tlLW1pdGVybGltaXQ6NDtzdHJva2UtZGFzaGFycmF5Om5vbmU7c3Ryb2tlLW9wYWNpdHk6MC4xODQzMTM3MyIKICAgICAgICAgICAgIGlkPSJwYXRoODQ3NSIKICAgICAgICAgICAgIGlua3NjYXBlOmNvbm5lY3Rvci1jdXJ2YXR1cmU9IjAiIC8+CiAgICAgICAgICA8cGF0aAogICAgICAgICAgICAgc29kaXBvZGk6bm9kZXR5cGVzPSJjY3NzY2NzY3NjYyIKICAgICAgICAgICAgIGQ9Im0gNzQyLjk3OTk5LDI3NS44Nzg1NyAtMC4wMzEyLDM4Ljk0NTk3IGMgMCwwIC00LjUxMjQ3LDAuMDczOSAtNi40MzcyOCwwLjA3MzkgLTYuMTM1MzcsMCAtOS4yMDMwNSw1LjIxNzM1IC05LjIwMzA1LDE1LjY1MjA0IHYgNDcuODU5MDYgaCAtMzMuNzQ0NDUgdiAtNTUuMDgzMDggYyAwLC0xNC40NDgwNSAzLjU0ODg4LC0yNS45NTI5OCAxMC42NDY2NCwtMzQuNTE0NzkgNy4wOTc3NywtOC42Mjg2OSAxNi41NzE0OSwtMTIuOTQzMDQgMjguNDIxMTQsLTEyLjk0MzA0IDIuNjQ2NjMsMCAxMC4zNDgyNSwwLjAxIDEwLjM0ODI1LDAuMDEgeiIKICAgICAgICAgICAgIHN0eWxlPSJmb250LXN0eWxlOm5vcm1hbDtmb250LXZhcmlhbnQ6bm9ybWFsO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1zdHJldGNoOm5vcm1hbDtmb250LXNpemU6MTk0Ljg1ODIzMDU5cHg7bGluZS1oZWlnaHQ6MTI1JTtmb250LWZhbWlseTonQmF1aGF1cyA5Myc7LWlua3NjYXBlLWZvbnQtc3BlY2lmaWNhdGlvbjonQmF1aGF1cyA5Myc7dGV4dC1hbGlnbjpzdGFydDt3cml0aW5nLW1vZGU6bHItdGI7dGV4dC1hbmNob3I6c3RhcnQ7ZmlsbDojMjYzYzVjO2ZpbGwtb3BhY2l0eToxO2ZpbGwtcnVsZTpub256ZXJvO3N0cm9rZTpub25lO3N0cm9rZS13aWR0aDoxLjcwNzI5NDc7c3Ryb2tlLWxpbmVjYXA6cm91bmQ7c3Ryb2tlLW1pdGVybGltaXQ6NDtzdHJva2UtZGFzaGFycmF5Om5vbmU7c3Ryb2tlLW9wYWNpdHk6MC4xODQzMTM3MyIKICAgICAgICAgICAgIGlkPSJwYXRoODQ3NyIKICAgICAgICAgICAgIGlua3NjYXBlOmNvbm5lY3Rvci1jdXJ2YXR1cmU9IjAiIC8+CiAgICAgICAgICA8cGF0aAogICAgICAgICAgICAgc29kaXBvZGk6bm9kZXR5cGVzPSJjY2NjYyIKICAgICAgICAgICAgIGQ9Im0gMzk4LjkxMzc4LDI3Ni40MDk1NyB2IDEwMiBoIC0zMy43NDQ0NiB2IC0xMDIgeiIKICAgICAgICAgICAgIHN0eWxlPSJmb250LXN0eWxlOm5vcm1hbDtmb250LXZhcmlhbnQ6bm9ybWFsO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1zdHJldGNoOm5vcm1hbDtmb250LXNpemU6MTk0Ljg1ODIzMDU5cHg7bGluZS1oZWlnaHQ6MTI1JTtmb250LWZhbWlseTonQmF1aGF1cyA5Myc7LWlua3NjYXBlLWZvbnQtc3BlY2lmaWNhdGlvbjonQmF1aGF1cyA5Myc7dGV4dC1hbGlnbjpzdGFydDt3cml0aW5nLW1vZGU6bHItdGI7dGV4dC1hbmNob3I6c3RhcnQ7ZGlzcGxheTppbmxpbmU7ZmlsbDojMjYzYzVjO2ZpbGwtb3BhY2l0eToxO2ZpbGwtcnVsZTpub256ZXJvO3N0cm9rZTpub25lO3N0cm9rZS13aWR0aDoxLjQ3MTI0MDUyO3N0cm9rZS1saW5lY2FwOnJvdW5kO3N0cm9rZS1taXRlcmxpbWl0OjQ7c3Ryb2tlLWRhc2hhcnJheTpub25lO3N0cm9rZS1vcGFjaXR5OjAuMTg0MzEzNzMiCiAgICAgICAgICAgICBpZD0icGF0aDg0NjctNSIKICAgICAgICAgICAgIGlua3NjYXBlOmNvbm5lY3Rvci1jdXJ2YXR1cmU9IjAiIC8+CiAgICAgICAgPC9nPgogICAgICA8L2c+CiAgICA8L2c+CiAgICA8dGV4dAogICAgICAgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIKICAgICAgIHN0eWxlPSJmb250LXN0eWxlOm5vcm1hbDtmb250LXZhcmlhbnQ6bm9ybWFsO2ZvbnQtd2VpZ2h0Om5vcm1hbDtmb250LXN0cmV0Y2g6bm9ybWFsO2xpbmUtaGVpZ2h0OjAlO2ZvbnQtZmFtaWx5OkFyaWFsOy1pbmtzY2FwZS1mb250LXNwZWNpZmljYXRpb246QXJpYWw7dGV4dC1hbGlnbjpzdGFydDt3cml0aW5nLW1vZGU6bHItdGI7dGV4dC1hbmNob3I6c3RhcnQ7ZmlsbDojMDA3YjhjO2ZpbGwtb3BhY2l0eToxO2ZpbGwtcnVsZTpub256ZXJvO3N0cm9rZTpub25lOyIKICAgICAgIHg9IjY0My4zMTE0NiIKICAgICAgIHk9IjIyOS43NzIxMSIKICAgICAgIGlkPSJ0ZXh0NTQ4NCIKICAgICAgIHRyYW5zZm9ybT0ic2NhbGUoMC44ODUzMzIxMywxLjEyOTUxOTYpIj48dHNwYW4KICAgICAgICAgc3R5bGU9ImZvbnQtc3R5bGU6bm9ybWFsO2ZvbnQtdmFyaWFudDpub3JtYWw7Zm9udC13ZWlnaHQ6bm9ybWFsO2ZvbnQtc3RyZXRjaDpub3JtYWw7Zm9udC1zaXplOjQyLjk1MjE5ODAzcHg7bGluZS1oZWlnaHQ6MTAwJTtmb250LWZhbWlseTpBcmlhbDstaW5rc2NhcGUtZm9udC1zcGVjaWZpY2F0aW9uOkFyaWFsO3RleHQtYWxpZ246c3RhcnQ7d3JpdGluZy1tb2RlOmxyLXRiO3RleHQtYW5jaG9yOnN0YXJ0O2ZpbGw6IzAwN2I4YztmaWxsLW9wYWNpdHk6MTtmaWxsLXJ1bGU6bm9uemVybztzdHJva2U6bm9uZTsiCiAgICAgICAgIHNvZGlwb2RpOnJvbGU9ImxpbmUiCiAgICAgICAgIGlkPSJ0c3BhbjU0ODYiCiAgICAgICAgIHg9IjY0My4zMTE0NiIKICAgICAgICAgeT0iMjI5Ljc3MjExIj5FUlAvQ1JNPC90c3Bhbj48L3RleHQ+CiAgICA8ZWxsaXBzZQogICAgICAgc3R5bGU9ImZpbGw6IzAwN2I4YztmaWxsLW9wYWNpdHk6MTtzdHJva2U6bm9uZTtzdHJva2Utd2lkdGg6MS4wMjk5OTEyNztzdHJva2Utb3BhY2l0eToxIgogICAgICAgaWQ9InBhdGg5NTciCiAgICAgICBjeD0iMzgxLjk0MTkzIgogICAgICAgY3k9IjI0Ny41ODE2MiIKICAgICAgIHJ4PSIxNy40NTY2OTkiCiAgICAgICByeT0iMTguMTE5NjEyIiAvPgogIDwvZz4KPC9zdmc+Cg==");
		exit();
	}
	
	if ($file == 'gear'){
		header("Content-type: image/svg+xml");
		echo base64_decode("PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNCIgaGVpZ2h0PSIxNiIgdmlld0JveD0iMCAwIDE0IDE2Ij48cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xNCA4Ljc3di0xLjZsLTEuOTQtLjY0LS40NS0xLjA5Ljg4LTEuODQtMS4xMy0xLjEzLTEuODEuOTEtMS4wOS0uNDUtLjY5LTEuOTJoLTEuNmwtLjYzIDEuOTQtMS4xMS40NS0xLjg0LS44OC0xLjEzIDEuMTMuOTEgMS44MS0uNDUgMS4wOUwwIDcuMjN2MS41OWwxLjk0LjY0LjQ1IDEuMDktLjg4IDEuODQgMS4xMyAxLjEzIDEuODEtLjkxIDEuMDkuNDUuNjkgMS45MmgxLjU5bC42My0xLjk0IDEuMTEtLjQ1IDEuODQuODggMS4xMy0xLjEzLS45Mi0xLjgxLjQ3LTEuMDlMMTQgOC43NXYuMDJ6TTcgMTFjLTEuNjYgMC0zLTEuMzQtMy0zczEuMzQtMyAzLTMgMyAxLjM0IDMgMy0xLjM0IDMtMyAzeiIvPjwvc3ZnPg==");
		exit();
	}
	
	if ($file == 'tick'){
		header("Content-type: image/png");
		echo base64_decode("iVBORw0KGgoAAAANSUhEUgAAAA4AAAAOCAMAAAAolt3jAAAASFBMVEX////d898xuUBUxWHD68cgszETryWD1Yyc3aQYsSmY3KDY8ttPw11ky3AltTXt+e960oTy+/OP2Zil4aw1u0VcyGnH7MzL7c+AO76zAAAAAXRSTlMAQObYZgAAAElJREFUCNd9ztEKgCAMheFR+c9SFCrx/d+0G2ETwt197HA4IovrkxSvHc8M0dTwTx26CzzyjmQCKNWSGfA18QQuqzkCOu3Z/kd/sbUB0OenPPYAAAAASUVORK5CYII=");
		exit();
	}
	
	if ($file == 'warning'){
		header("Content-type: image/png");
		echo base64_decode("iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAABmJLR0QA3wCRAAAON4EwAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3wwEEhoU0HsntAAAARVJREFUOMvNk71KQ0EQhb9rkspGlEGD+gRp/KlSiKRR0EaxmDponUTtBDuNpTapY6OQpxjwCULAF9BGCT5DbCZwve41WQtxYNiZs7OHmbO78FdmKmVTKU+qK07B1QFGwNFPRTMTutkCdoBdj+OJTKUEnAGz7qeORXdUBQ5S+aFj0USPAewhishUboBlT+/cAVZMpR06kwRIKsATMO/Qqq+vvn4A27Xe8Dm3I1MpAM0UScgWgIbX5r6jClDPYOcBsrq/r0Ee0X0GGwGtVDyWogR0gc1vo5nKCbAe0HDOPavnhqkcfxHbVJaAPrAYGGN8Y63A3huwVusN3xNTSYAr4OKX//oauCz6LewBL65DjCXAPnDLv7NPrj00Vfm5cGcAAAAASUVORK5CYII=");
		exit();
	}
		
	if ($file == 'error'){
		header("Content-type: image/png");
		echo base64_decode("iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH4AwMCisRROG71gAAAQ5JREFUOMvNk71KA0EUhb85amUjKsSgPkEa41YWwcYHUHyCoLW/nWAXTZvGR9CXsLO/QQSfwEYLrVLP2tzAZp1EN4J44TLMmbPf3js/8FdhUt2k+ne+2R+wboAc2P9NNS2TBp6tSV5NgMwBZ8C856lr1UDAFrBbmO+5Vhl0l9BuK4FM6gKrPu15AqyZdJ36JiQgDeABWHRp3ccXH9+B7SzG57EVmTQDHBcgqVgCjtw79h41gHZJO0/A2n6/npKtmdQHmgUpL3jykr+fxZh9ac2kwxJk+KMFz/J+bpp0MFKRSSvAI1BLtDE8sZPE2iuwkcX4FkwKQAe4mPIlXQGXwaRl4N5PKq8ICcAHsMO/i0/KdzaZfIaieAAAAABJRU5ErkJggg==");
		exit();
	}
}
