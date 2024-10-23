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
  <meta name="description" content="Archivo de los subtítulos realizados por la comunidad de Argenteam.net">
  <meta name="keywords" content="Subtítulos, Argenteam.net, Archivo">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/custom-min.css">
  <link rel="stylesheet" href="css/custom-arar.css">
</head>

<body>
<!-- Titulo y descripcion-->
  <header class="container-md">
    <a href="https://www.arar.net.ar" class="text-reset text-decoration-none mt-5 home">
      <h1 class="titulo"><span class="text-info">Ar</span>chivo<br><span class="text-info">Ar</span>genteam</h1>
      <!-- <h1 class="mt-5 mb-0 titulo archivo"><span class="text-info">Ar</span>chivo</h1>
      <h1 class="mb-3 titulo"><span class="text-info">Ar</span>genteam</h1>--></a>
    <p class="text-center mt-4">Archivo de los subtítulos (+90000) realizados por la comunidad <a class="link-opacity-50-hover link-dark" href="https://argenteam.net/" target="_blank">Argenteam.net</a>
    . Se puede descargar el archivo completo (1,99 GB) por <a class="link-opacity-50-hover link-dark" href="magnet:?xt=urn:btih:DBYJKHEO6UGINGYCSA4SMI44V7MYMTRA&dn=subtitulos-argenteam&tr=udp%3A%2F%2Ftracker.opentrackr.org%3A1337%2Fannounce&tr=http%3A%2F%2Ftracker.opentrackr.org%3A1337%2Fannounce">torrent</a>.</p>
    <p class="text-center text-danger">Aviso: No hay traducciones de material estrenado después de 2023.</p>
  </header>

<!-- Formulario busqueda -->
  <search class="container-md mt-5">
    <form name="formlink" method="post" class="form-inline" action="index.php" id="busqueda">
      <div class="input-group">
        <input type="text" class="form-control" name="query" id="query" placeholder="Buscar subtítulo" required autofocus>
        <input class="btn btn-info" type="submit" name="Submit" value="Buscar">
      </div>
    </form>
  </search>

  <!-- Tabla de resultados -->
  <div class="container-md mt-4">
    <div class="table-responsive">
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

function searchQuery($search, $zip, $content) : bool {

  $nameZip = getName($zip->name);

  if (preg_match($search, $nameZip)) {

    return true;

  } else {
    
    foreach ($content as $c) {

      $nameContent = getName($c->name);
  
      if (preg_match($search, $nameContent)) {
  
        return true;
    
      }
  
    }
  
  }

  return false;

}

$search = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  
  $search = getQuery($_POST["query"]);
  
  //Se guarda lo ingresado en la búsqueda
  $queryString = date(DATE_RFC1123) . " " . $_POST["query"] . "\n";
  $logQueryFile = "/busquedas-log.txt";
  file_put_contents(__DIR__ . $logQueryFile, $queryString , FILE_APPEND);
}

$indexf = file_get_contents("db/index.json");
$indexj = json_decode($indexf);
$filenames = [...$indexj[0]->contents];
$noResults = true;

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

        if (searchQuery($search, $zip, $contents)) {

          $noResults = false;
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
  if ($noResults) {
    echo '<p class="text-center mt-5">No hay resultados  :(</p>';
  }
 }
?>
      </tbody>
    </table>
    </div>
  </div>

  <footer class="container-md mt-5">
    <p class="text-center mb-0">¿Problemas?</p>
    <address>
      <p class="text-center"><a class="link-opacity-50-hover link-dark me-1" href="https://github.com/esaracho/archivo-argenteam/issues" target="_blank">GitHub</a> <a class="link-opacity-50-hover link-dark ms-1" href="mailto:archivo.argenteam@gmail.com">eMail</a></p>
    </address>
  </footer>

</body>

</html>