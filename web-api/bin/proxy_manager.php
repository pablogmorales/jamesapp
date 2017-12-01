<?php
require dirname(__DIR__)  . '/config/bootstrap.php';

use Daytalytics\Database;
/********************************************************************
*                                                                   *
*                       WEBAPI PROXY IMPORT                         *
*   A script to update the proxy list in WebAPI by allowing you to  *
*   mass-disable a list of proxies and import a list of new ones    *
*                                                                   *
*            I want this script to be more robust...                *
*                                                                   *
*               Written by Jonathan Love, 2011                      *
*            Copyright (c) Doubledot Media Limited                  *
*                                                                   *
********************************************************************/

/*  Usage: php webapi-proxies.php [enable|disable|pb-disable|range-disable] [<filename>|<ip range>]
 
    If you specify enable/disable and a filename, the script will parse 
    the file and enable/disable them in the WebAPI accordingly. If you 
    specify no filename, the data will be read from STDIN. 
    
    If enable/disable/pb-disable is not specified, enable is assumed.

    If you specify pb-disable, the script will disable all proxies 
    in the DB with no path, no username, no password, and on port 
    60099; if you also specify a filename, the proxies will be read in
    and added to the API. Not that pb-disable will NOT read from
    STDIN.

    The layout of the input text should be as follows:
        <proxy IP>:<port>[:<username>:<password>]
        
*/

// Do not touch past this point!
$filestream = STDIN;
$filename   = '';
$range      = '';
$mode       = 'enable';

switch ($argc) {
    case 0:
        break;
    case 2:
        //Wahey, "tricky" case :)
        //But not really - let's check if argv[1] is what we need
        $opt = strtolower($argv[1]);
        if ($opt != "enable" && $opt != "disable" && $opt != "pb-disable" && $opt != "range-disable")
        {
            //Welp, it doesn't match any of the above! Must be a filename
            $filename = $argv[1];
        } else {
            $mode = $opt;
        }
        break;
    case 3:
        $mode    = strtolower($argv[1]);
	if ($mode=="range-disable")
	{
		$range = $argv[2];
	} else {
	        $filename= $argv[2];
	}
        break;
    default:
        exit("Usage: php webapi-proxies.php [enable|disable|pb-disable|range-disable] [<filename>|<ip range>]\n");
    break;
}
//Righto, do we have a file?
if ($filename != '')
{
    if (file_exists($filename))
    {
        $filestream=fopen($filename,'r');
        if (!$filestream)
        {
            exit("Error: Could not open ".$filename." for reading\n");
        }
    } else {
        exit("Error: File ".$filename." does not exist\n");
    }
}

$db = Database::get_instance();

if ($mode == 'pb-disable')
{
    // Right, let's disable some WebAPI Stuff!
    echo "Disabling WebAPI proxies\n";
    $result = $db->query('UPDATE `proxy_servers` SET enabled=0 WHERE path="" AND port=60099 AND username="" AND password=""', $db_h);
    echo $db->affected_rows()." ProxyBonanza proxies were disabled\n";
    $update_count = 0;
    $insert_count = 0;
    if ($filestream != STDIN)
    {
        while (($proxy_line = fgets($filestream, 1024)) != false)
        {
            $proxy_line=trim($proxy_line);
            $proxy = explode(":",$proxy_line);
            if (count($proxy) != 2)
            {
                echo "Error, ignoring malformed ProxyBonanza proxy line: \"". $proxy_line . "\"\n";
            } else {
                $ip = $proxy[0];
                $port = $proxy[1];                
                $db->query('UPDATE `proxy_servers` SET enabled=1 WHERE ip="'.$ip.'" AND port='.$port.' LIMIT 1;', $db_h);
                if ($db->affected_rows() == 0)
                {
                    $insert_count += 1;
                    $db->query('INSERT INTO `proxy_servers` SET path="", enabled=1, ip="'.$ip.'", port='.$port.', username="", password="";', $db_h);
                } else {
                    $update_count += 1;
                }
            }
        }
        echo $insert_count." proxies added, ".$update_count." proxies reenabled\n";
    }
}

if ($mode == 'enable')
{
    echo "Are you sure? If so, press Enter. If not, Ctrl+C this script now.";
    fgets(STDIN);
    $update_count = 0;
    $insert_count = 0;
    $already_enabled = 0;
    while (($proxy_line = fgets($filestream, 1024)) != false)
     {
         $proxy_line=trim($proxy_line);
         $proxy = explode(":",$proxy_line);
         if (count($proxy) < 2)
         {
             echo "Error, ignoring malformed proxy line: \"". $proxy_line . "\"\n";
         } else {
             $ip = $proxy[0];
             $port = $proxy[1];                
            if (isset($proxy[2]) && isset($proxy[3])) {
                $username = $proxy[2];
                $password = $proxy[3];
            } else {
                $username = '';
                $password = '';
            }
            
            $query = $db->query('SELECT * FROM `proxy_servers` WHERE enabled=1 AND port='.$port.' AND username="'.$username.'" AND password="'.$password.'" AND ip="'.$ip.'";', $db_h);
            if ($db->count($query) > 0)
            {
                $already_enabled += 1;
            } else {
                $db->query('UPDATE `proxy_servers` SET enabled=1, port='.$port.', username="'.$username.'", password="'.$password.'" WHERE ip="'.$ip.'" LIMIT 1;', $db_h);
                if ($db->affected_rows()==0)
                {
                    $db->query('INSERT INTO `proxy_servers` SET path="", enabled=1, ip="'.$ip.'", port='.$port.', username="'.$username.'", password="'.$password.'";', $db_h);
                    $insert_count +=1;
                }
                else
                {
                    $update_count += 1;
                }
            }
        }
    }
    echo $insert_count." proxies added, ".$update_count." proxies reenabled; ".$already_enabled." proxies were already enabled\n";
}

if ($mode == 'disable')
{
    echo "Are you sure? If so, press Enter. If not, Ctrl+C this script now.";
    fgets(STDIN);
    $update_count = 0;
    
    while (($proxy_line = fgets($filestream, 1024)) != false)
     {
         $proxy_line=trim($proxy_line);
         $proxy = explode(":",$proxy_line);
         if (count($proxy) != 2)
         {
             echo "Error, ignoring malformed proxy line: \"". $proxy_line . "\"\n";
         } else {
             $ip = $proxy[0];
             $port = $proxy[1];                
            if (isset($proxy[2]) && isset($proxy[3])) {
                $username = $proxy[2];
                $password = $proxy[3];
            } else {
                $username = '';
                $password = '';
            }
            
            $db->query('UPDATE `proxy_servers` SET enabled=0 WHERE ip="'.$ip.'" AND port='.$port.' AND username="'.$username.'" AND password="'.$password.'" LIMIT 1;', $db_h);
            $update_count +=1;
        }
    }
    echo $update_count." proxies disabled\n";
}
if ($mode == 'range-disable')
{
        echo "Are you sure? If so, press Enter. If not, Ctrl+C this script now.";
	fgets(STDIN);
	preg_match("/^(\d{1,3})\.(\d{1,3})(?:\.(\d{1,3}))?/", $range, $ip);
	switch(count($ip)){
		case 4:
		case 3:
			$ip = $ip[0];
			break;
		default:
			echo "Please enter at least two octects to specify an IP range.\n";
			echo "E.g. 172.0 will match 172.0.x.x\n";
			echo "172.0.10 will match 172.0.10.x\n";
			echo "172.0.10.5 will match 172.0.10.x\n";
			exit();
		}
	echo "Attempting to match all IPs beginning with $ip\n";
	$db->query('UPDATE `proxy_servers` SET enabled=0 WHERE ip LIKE "'.$ip.'.%";', $db_h);
	echo $db->affected_rows() ." proxies disabled\n";
}
if ($filestream != STDIN)
{
    fclose($filestream);
}
?>
