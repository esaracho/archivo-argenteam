<!-- This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, 
either version 3 of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with this program. If not, see <https://www.gnu.org/licenses/>.  -->

<!DOCTYPE html>
<html lang="es">
<head>
	<title>Archivo Argenteam</title>
  <link rel="icon" type="image/x-icon" href="favicon.ico">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <link rel="stylesheet" href="css/custom.css">
</head>

<body>
<!-- Titulo y descripcion-->
<header class="container-md">
<h1 class="text-center mt-4 titulo"><span class="ar">Ar</span>chivo <span class="ar">Ar</span>genteam</h1>
<p class="text-center">Archivo de los subtítulos realizados por la comunidad <a class="link-opacity-50-hover" href="https://argenteam.net/" target="_blank">Argenteam.net</a></p>
<p class="text-center text-secondary">Se puede descargar el archivo completo (1,99 GB) con todos los subtitulos por <a class="link-opacity-50-hover" href="magnet:?xt=urn:btih:DBYJKHEO6UGINGYCSA4SMI44V7MYMTRA&dn=subtitulos-argenteam&tr=udp%3A%2F%2Ftracker.opentrackr.org%3A1337%2Fannounce&tr=http%3A%2F%2Ftracker.opentrackr.org%3A1337%2Fannounce">torrent</a></p>
</header>

<!-- Formulario busqueda -->
<search class="container-md mt-4">
<form name="formlink" method="post" class="form-inline" action="index.php" role="form" id="busqueda">
	<div class="input-group">
		<input type="text" class="form-control" name="query" id="query" placeholder="Buscar subtítulo" required autofocus>
	  <input class="btn btn-bd-primary" type="submit" name="Submit" value="Buscar">
	</div>
</form>
</search>

<!-- Tabla de resultados -->
<div class="container-md mt-4">
<table class="table table-hover">
    <tbody>
        
<?php

function getQuery($q) : string {

  $input = preg_replace("/[^a-z0-9\'\&]+/i", " ", $q);
  $inputa = explode(' ', $input);

  if (strlen($input) > 3 ) {

    if (count($inputa) > 1) {

      $s1 = "";

      foreach ($inputa as $word) {

        
        $s1 .= "[[:print:]]*" . "\b" . $word . "\b";

      }
      $s = "/" . $s1 . "/i";
      
      return $s;

    } else {

      return "/\b". $inputa[0] . "\b/i";

    }
    
  } else {
  
    $s = "/^\b". $input . "\b/i";
    return $s;
  
  }
}

function getName($n) : string {
  
  $rmext = str_replace([".srt", ".sub", ".txt"], "", $n);
  $name = str_replace(".", " ", $rmext);
  return $name;

}

function getLink($p, $n) : string {

  $directory = str_replace(["db", ".json"], ["subs", "/"], $p);
  $file = rawurlencode($n);
  $link = $directory . $file . ".zip";
  return $link;

}

function searchZip($s, $n) : bool {
  
  $name = getName($n->name);
  
  if (preg_match($s, $name)) {

    return true;

  } else {
    
    return false;
  
  }
}

function searchContent($s, $na) : bool {

  foreach ($na as $n) {

    $namec = getName($n->name);

    if (preg_match($s, $namec)) {

      return true;
  
    }

  }

  return false;

}

$search = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
 
  $search = getQuery($_POST["query"]);
}

$indexf = file_get_contents("db/index.json");
$indexj = json_decode($indexf);
$filenames = [...$indexj[0]->contents];

//if search no está vacio

if ($search != "") {
foreach ($filenames as $file) {

$path = "db/" . $file->name;
$data = file_get_contents($path);
$datautf8 = mb_convert_encoding($data, "UTF-8", "auto");
$json = json_decode($datautf8);
$dir = [...$json[0]->contents];

        foreach ($dir as $zip) {
        
        $nameZip = preg_replace("/-aRGENTeaM-[[:digit:]]+/" , "" , getName($zip->name));//nombre del zip
        $contents = $zip->contents; //array de subtitulos

          if (searchContent($search, $contents) || searchZip($search, $zip)) {

          $file = getLink($path, $zip->name); 

            echo "<tr>";
            echo '<td class="text-center">', "<a class='link-opacity-50-hover' href=", $file ,">",$nameZip,"</a>", "</td>";
            echo "</tr>";

            foreach ($contents as $contentd) {
            
              if ($contentd->type == "file") {
                
                echo "<tr>";              
                echo '<td class="text-center">', $contentd->name , "</td>";
                echo "</tr>";

              }

            }
          
          }
        }
    }

 }
?>
</tbody>
</table>
</div>

<footer class="container-md mt-5">
  <p class="text-center text-secondary mb-0">¿Problemas?</p>
  <address>
  <p class="text-center"><a class="link-opacity-50-hover link-dark me-1" href="https://github.com/esaracho/archivo-argenteam/issues" target="_blank">GitHub</a> <a class="link-opacity-50-hover link-dark ms-1" href="mailto:archivo.argenteam@gmail.com">Mail</a></p>
  </address>
</footer>

</body>

</html>