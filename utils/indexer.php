<?php
require_once("../inc/inc.ClassSettings.php");

function usage() { /* {{{ */
	echo "Usage:\n";
	echo "  seeddms-indexer [-h] [-v] [--config <file>]\n";
	echo "\n";
	echo "Description:\n";
	echo "  This program recreates the full text index of SeedDMS.\n";
	echo "\n";
	echo "Options:\n";
	echo "  -h, --help: print usage information and exit.\n";
	echo "  -v, --version: print version and exit.\n";
	echo "  -c: recreate index.\n";
	echo "  --config: set alternative config file.\n";
} /* }}} */

$version = "0.0.1";
$shortoptions = "hvc";
$longoptions = array('help', 'version', 'config:');
if(false === ($options = getopt($shortoptions, $longoptions))) {
	usage();
	exit(0);
}

/* Print help and exit */
if(isset($options['h']) || isset($options['help'])) {
	usage();
	exit(0);
}

/* Print version and exit */
if(isset($options['v']) || isset($options['verѕion'])) {
	echo $version."\n";
	exit(0);
}

/* Set alternative config file */
if(isset($options['config'])) {
	$settings = new Settings($options['config']);
} else {
	$settings = new Settings();
}

/* recreate index */
$recreate = false;
if(isset($options['c'])) {
	$recreate = true;
}

if(isset($settings->_extraPath))
	ini_set('include_path', $settings->_extraPath. PATH_SEPARATOR .ini_get('include_path'));

require_once("SeedDMS/Core.php");
require_once("SeedDMS/Lucene.php");

function tree($dms, $index, $folder, $indent='') {
	global $settings;
	echo $indent."D ".$folder->getName()."\n";
	$subfolders = $folder->getSubFolders();
	foreach($subfolders as $subfolder) {
		tree($dms, $index, $subfolder, $indent.'  ');
	}
	$documents = $folder->getDocuments();
	foreach($documents as $document) {
		echo $indent."  ".$document->getId().":".$document->getName()." ";
		if(!($hits = $index->find('document_id:'.$document->getId()))) {
			$index->addDocument(new SeedDMS_Lucene_IndexedDocument($dms, $document, isset($settings->_converters['fulltext']) ? $settings->_converters['fulltext'] : null));
			echo " (Document added)\n";
		} else {
			$hit = $hits[0];
			try {
				$created = (int) $hit->getDocument()->getFieldValue('created');
			} catch (Zend_Search_Lucene_Exception $e) {
				$created = 0;
			}
			$content = $document->getLatestContent();
			if($created >= $content->getDate()) {
				echo " (Document unchanged)\n";
			} else {
				if($index->delete($hit->id)) {
					$index->addDocument(new SeedDMS_Lucene_IndexedDocument($dms, $document, $settings->_converters['fulltext'] ? $settings->_converters['fulltext'] : null));
					echo " (Document updated)\n";
				}
			}
		}
	}
}

$db = new SeedDMS_Core_DatabaseAccess($settings->_dbDriver, $settings->_dbHostname, $settings->_dbUser, $settings->_dbPass, $settings->_dbDatabase);
$db->connect() or die ("Could not connect to db-server \"" . $settings->_dbHostname . "\"");

$dms = new SeedDMS_Core_DMS($db, $settings->_contentDir.$settings->_contentOffsetDir);
if(!$dms->checkVersion()) {
	echo "Database update needed.";
	exit;
}

$dms->setRootFolderID($settings->_rootFolderID);

if($recreate)
	$index = Zend_Search_Lucene::create($settings->_luceneDir);
else
	$index = Zend_Search_Lucene::open($settings->_luceneDir);
SeedDMS_Lucene_Indexer::init($settings->_stopWordsFile);

$folder = $dms->getFolder($settings->_rootFolderID);
tree($dms, $index, $folder);

$index->commit();
$index->optimize();
?>
