#!/usr/php/bin/php
<?php
require_once('config.php');

function __autoload($class_name) {
	$filename = PATH_CLASS.$class_name.'.class.php';
	if(file_exists($filename))
	require_once $filename;
}

$args = parseArgs($argv);
$indexCounter = 0;

if(isset($args['h'])) {
	echoHelp($argv);
	exit(0);
}

if(isset($args['updateusage'])) {
	updateDiskUsage();
} 

if(isset($args['updatefeeds'])) {
	updateFeeds();
} 

if(isset($args['addfeed'])) {
	if(isset($args[$indexCounter]))
		addFeed($args[$indexCounter++]);
	else
		echoError($argv);
}

if(isset($args['addtorrent'])) {
	if(isset($args[$indexCounter]) && isset($args[$indexCounter+1]))
		addTorrent($args[$indexCounter++], $args[$indexCounter++]);
	else
		echoError($argv);
}

exit(0);

/**
 * parseArgs Command Line Interface (CLI) utility function.
 * @usage               $args = parseArgs($_SERVER['argv']);
 * @author              Patrick Fisher <patrick@pwfisher.com>
 * @source              https://github.com/pwfisher/CommandLine.php
 */
function parseArgs($argv) {
    array_shift($argv); $o = array();
    foreach ($argv as $a){
        if (substr($a,0,2) == '--'){ $eq = strpos($a,'=');
            if ($eq !== false){ $o[substr($a,2,$eq-2)] = substr($a,$eq+1); }
            else { $k = substr($a,2); if (!isset($o[$k])){ $o[$k] = true; } } }
        else if (substr($a,0,1) == '-'){
            if (substr($a,2,1) == '='){ $o[substr($a,1,1)] = substr($a,3); }
            else { foreach (str_split(substr($a,1)) as $k){ if (!isset($o[$k])){ $o[$k] = true; } } } }
        else { $o[] = $a; } }
    return $o;
}

function updateDiskUsage() {
	$fss = explode(',', MONITORED_FILESYSTEMS);
	
	foreach ($fss as $fs) {
		$fsmysql = Kernel::mysqli()->escape_string($fs);
		$total = disk_total_space($fs);
		$free = disk_free_space($fs);
		$used = $total-$free;
		
		Kernel::mysqli()->query("INSERT INTO fsdata (filesystem, date, free, total, used) VALUES('$fsmysql', NOW(), $free, $total, $used)");
	}
}

function updateFeeds() {
	$feedsResult = Kernel::mysqli()->query('SELECT * FROM feed');
	if(is_object($feedsResult)) {
		while($feedObj = $feedsResult->fetch_object()) {
			$feedData = file_get_contents($feedObj->url); 
			if ($feedData === false) {
				Logger::info("Could not download feed {$feedObj->id} : {$feedObj->url}");
				continue;
			}
			
			$feedData = Kernel::mysqli()->escape_string($feedData);
			
			Kernel::mysqli()->query("UPDATE feed SET data='$feedData' WHERE id={$feedObj->id}");
		}
		$feedsResult->close();
	}
}

function addFeed($url) {
	$url = Kernel::mysqli()->escape_string($url);
	
	Kernel::mysqli()->query("INSERT INTO feed (url) VALUES('$url')");
}

function addTorrent($storage, $torrent) {
	$storage = Kernel::mysqli()->escape_string($storage);
	$torrent = Kernel::mysqli()->escape_string($torrent);
	
	Kernel::mysqli()->query("INSERT INTO unhandled (file, torrent) VALUES('$storage','$torrent')");
}

function echoHelp($argv) {
	echo "Usage: {$argv[0]} --updatefeeds\n";
	echo "       {$argv[0]} --addfeed <rss url>\n";
	echo "       {$argv[0]} --addtorrent <storage> <torrent>\n";
	echo "\n";
	echo "  -h                               This help.\n";
	echo "  --updateusage                    Update disk space usage.\n";
	echo "  --updatefeeds                    Update rss feeds.\n";
	echo "  --addfeed <feed url>             Add feed.\n";
	echo "  --addtorrent <storage> <torrent> Add torrent.\n";
	echo "                                   <storage> is path to filesystem where files are located.\n";
	echo "                                   <torrent> is torrent file.\n";
}

function echoError($argv) {
	echo "Invalid option.\nTry '{$argv[0]} -h' for more information.\n";
	exit(1);	
}

?>
