<?php
/*
dbview2 v1.0
2023-06-10
License: AGPLv3
@aaviator42
*/

//---CLASS DEFINITIONS GO HERE

/*
class Example {
    public $publicVar = 'public';
    protected $protectedVar = 'protected';
    private $privateVar = 'private';
}
*/

//--END CLASS DEFINITIONS


require 'lib/StorX.php';

// the folder containing db files
const DBDIR = "db";

// file extension of db files
const DBEXT = "db";

if(isset($_GET["dbfile"])){
	$dbfile = trim($_GET["dbfile"]);
	
	printHeader($dbfile);
	printData($dbfile);
	printFooter();

} else {
	$files = glob(DBDIR . "/*.".DBEXT);

	printHeader("[FILE LISTING]");
	
	printLinks($files);
	
	printFooter();
}

function print_r_plus($variable, $indent = "") {
    if (is_array($variable)) {
        echo "Array\n";
        echo $indent . "(\n";
        foreach ($variable as $key => $value) {
            echo $indent . "    [$key] => ";
            print_r_plus($value, $indent . "    ");
        }
        echo $indent . ")\n";
    } elseif (is_object($variable)) {
        $reflection = new ReflectionObject($variable);
        $properties = $reflection->getProperties(
            ReflectionProperty::IS_PUBLIC | 
            ReflectionProperty::IS_PROTECTED | 
            ReflectionProperty::IS_PRIVATE
        );
        $class_name = $reflection->getName();
        echo "{$class_name} Object\n";
        echo $indent . "(\n";
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $property_name = $property->getName();
			if($class_name === "__PHP_Incomplete_Class"){
				$property_value = "[INCOMPLETE OBJECT]";
			} else {
				$property_value = $property->getValue($variable);
			}
            $visibility = '';
            if ($property->isPrivate()) {
                $visibility = 'private';
            } elseif ($property->isProtected()) {
                $visibility = 'protected';
            } else {
                $visibility = 'public';
            }
            echo $indent . "    [{$property_name}:{$class_name}:{$visibility}] => ";
            print_r_plus($property_value, $indent . "    ");
        }
        echo $indent . ")\n";
    } elseif (is_null($variable)) {
        echo "NULL\n";
    } elseif (is_bool($variable)) {
        echo ($variable ? "TRUE" : "FALSE") . "\n";
    } elseif (is_string($variable)) {
        echo "\"{$variable}\"\n";
    } else {
        echo $variable . "\n";
    }
}




function printHeader($dbname){
	echo <<<ENDEND
	
<!DOCTYPE html>
<!-- dbview2.php v1.0 by @aaviator42 -->
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>dbview.php</title>
	<style>
	body {
		font-family: Verdana, sans-serif;
		padding: 2rem;
		margin: auto;
		font-size: 1rem !important;
	}
	code, pre {
		font-family: monospace;
		background-color: #E6E6E6;
		white-space: pre-wrap;
	}
	table {
		width: 95%;
		border: 0.01rem solid;
		margin-left: 1rem;
		margin-right: 1rem;
		border-collapse: collapse;
	}
	td {
		border: 0.01rem solid;
		vertical-align: text-top;
	}
	th {
		border: 0.01rem solid;
	}
	</style>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
	<meta name="robots" content="noindex, nofollow, noarchive">

</head>
<body>
	<h2><a href="?">dbview2</a></h2>
	DB: <code>$dbname</code>
	<hr>

ENDEND;
}

function printFooter(){
	echo<<<ENDEND
	</pre>
</body>
</html>

ENDEND;
}

function printData($dbname){
	$sx = new \StorX\Sx;
	
	if($sx->checkFile($dbname) !== 1){
		echo "ERROR: Not a vaild StorX DB file!";
		return;
	}
	
	$sx->openFile($dbname);
	$sx->readAllKeys($keyArray);
	echo "
		<table>
			<tr>
				<th></th>
				<th>keyName</th>
				<th>keyValue</th>
			</tr>
		";
	$count = 1;
	foreach($keyArray as $key => $value){
		echo "<tr>" . PHP_EOL;
		echo "<td><pre>[" . $count . "]</pre></td>" . PHP_EOL;
		echo "<td><b><pre>" . $key . "</pre></b></td>" . PHP_EOL;
		if(is_array($value)){
			echo "<td><pre>" . PHP_EOL; print_r_plus($value); echo "</pre></td>" . PHP_EOL;
		} else {
			// echo "<td><pre>" . var_export($value, 1) . "</pre></td>" . PHP_EOL;
			echo "<td><pre>" . PHP_EOL; print_r_plus($value); echo  "</pre></td>" . PHP_EOL;
		}
		echo "</tr>" . PHP_EOL;
		$count++;
	}

	echo "
		</table>
	";
}

function printLinks($files){
	echo "
		<ul>";
	
	foreach($files as $file){
		echo "
			<li>
				<a href='?dbfile=$file'><code>$file</code></a>
			</li>";
	}
	
	echo "
		</ul>";
}