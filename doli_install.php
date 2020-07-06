<?php
/* Copyright (C) 2020       ksar    <ksar.ksar@gmail.com>
 * From an original idea of elarifr accedinfo.com
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
define('SCRIPT_VERSION','Alpha 0.0.2 Version');
$github_url = 'https://github.com/Dolibarr/dolibarr/archive/develop.zip';
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
		file_put_contents($log_file, print_r($log_array, true) , FILE_APPEND);
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
			"NextStep"						=> "Etape Suivante",
			"SelectLanguage"				=> "Sélection de la langue",
			"DefaultLanguage"				=> "Langue par défaut",
			"Check"							=> "Verifications",
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
			"UpgradeDetected"				=> "Un fichier conf.php a été trouvé : <b> Mise à jour de Dolibar </b>. Version détecté",
			"IntallNewDetected"				=> "Aucun fichier conf.php n'a été trouvé : <b> Installation Neuve de Dolibar </b>.",
			"ChooseVersion"					=> "Choisiez la version a intaller",
			"Download"						=> "Téléchargement",
			"ErrorNoVersionSelectec"		=> "Vous n'avez pas sélectionner de version.",
			"UrlDownload"					=> "Adresse de téléchargement",
			"ErrorDuringDownload"			=> "Erreur pendant le téléchargement",
			"DownloadPackage"				=> "Téléchargement du package Dolibarr Version",
			"DownloadProgress"				=> "Progression du téléchargement",
			"DownloadCompleted"				=> "Téléchargement terminé. Taille du package",
			"ErrorDownloadFile"				=> "Le fichier télécharger n'est pas une archive !",
			"Install"						=> "Installer la version téléchargée",
			"FileIsAZip"					=> "Le fichier téléchargé est bien un zip",
			"ErrorNotAZip"					=> "Le fichier téléchargé n'est pas un zip",
			"NumbersOfDirectories"			=> "Nombre de dossiers crées",
			"NumbersOfFiles"				=> "Nombre de fichiers crées",
			"NoErrors"						=> "Aucune erreure lors de l'extraction",
			"SomeErrors"					=> "Quelques erreures lors de l'extraction. Nombre d'erreurs",
			"RedirectToInstall"				=> "L'installation ou mise à jour est maintenant terminée. En cliquant sur \"Suivant\" vous allez être rediriger vers l'installation de Dolibarr",
			"InstallLockDeleted"			=> "Le fichier install.lock a été trouvé et supprimé",
			"InstallLockNotFounded"			=> "Aucun fichier install.lock n'a été trouvé. Si il existe vous devez le supprimer manuelement",
			"InstallLockFoundNoDeleted"		=> "Le fichier install.lock existe mais n'a pas pu être supprimé",
			"DeleteScript"					=> "Supprimer ce script php du serveur (Recommandé)",
			"DeleteLog"						=> "Supprimer le fichier log de ce script(Recommandé)",
			"AFewAdditionalOptions"			=> "Quelques options supplémentaires"
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
			"DeleteLog"						=> "Delete the log file of this script(Recommended)",
			"AFewAdditionalOptions"			=> "A few additional options"
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
    print '<img class="imglogoinstall" src="'.$_SERVER["PHP_SELF"].'?action=img&file=logo" alt="Dolibarr logo"><br>';
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
 * CURL follow redircetions event if open_basedir or safe_mode are ON
 *
 * @param	resource	$ch				Curl Ressource
 * @param	Int			$maxredirect	Max redirections
 * @return	curl_exec					Excution of CURL
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
 * @param	resource	$ch				Curl Ressource
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
		//update javacsript progress bar to show download progress
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
	
	// Check if main dir is writtable
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
	write_log('SourceForge versions found ',$sourceforge_versions);
	echo $langs["ChooseVersion"].': </td>'."\n";
	echo '<td><select class="flat" id="selectversion" name="selectversion">'."\n";
	echo '<option value="dev">develop from GITHUB</option>'."\n";
	foreach ($sourceforge_versions as $version){
		echo '<option value="'.$version.'">'.$version.' from Sourceforge</option>'."\n";
	}
	echo '</select></td></tr></table></div><br><br>'."\n";
	pFooter( $checkfail, $langs, $selectlang);
}

//Download page
if ($action == 'download'){
	
	write_log('--- Enter in Download Page ---');
	// initialize download version
	if (!empty($_POST['selectversion'])){
		if ($_POST['selectversion'] == 'dev'){
			$url_version = $github_url ;
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
		//For sourceforge need to extract the mirror
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

//Output the css
if ($action == 'css'){
	//set the content type header
	header("Content-type: text/css");
	echo "
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
	font-size: 90%;
	font-weight: bold;
	background: #FFFFFF;
	color: #444;
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
    padding: 4px;
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


.button {
    background: #fcfcfc;
    border: 1px solid #e0e0e0;
    padding: 0.3em 0.7em;
    margin: 0 0.5em;
    -moz-border-radius: 5px 5px 5px 5px;
    -webkit-border-radius: 5px 5px 5px 5px;
    border-radius: 5px 5px 5px 5px;
    -moz-box-shadow: 2px 2px 3px #ddd;
    -webkit-box-shadow: 2px 2px 3px #ddd;
    box-shadow: 2px 2px 3px #ddd;
}
a.button:hover {
    text-decoration:none;
}

.choiceselected {
	background-color: #dfd;
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
		header("Content-type: image/png");
		echo base64_decode("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");
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
