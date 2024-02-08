<?php
// File upload : MIME type
session_start();

$galerie_dir = 'galerie';
$www_dir     = './galerie/upload/' . session_id() . '/';
$upload_dir  = getcwd() . '/' .$www_dir;

$aff= '<html><head><style>body { background: black; color: white; }</style></head><body><h1>Photo gallery v 0.03</h1><span id=menu/>';

if (isset($_GET["galerie"]) &&
        preg_match("/^[a-z]+$/", $_GET["galerie"]) &&
        file_exists($galerie_dir."/".$_GET["galerie"]))
    $galerie = $_GET["galerie"];
else
    $galerie = "pirate";


if (file_exists($galerie_dir)) {
    $d = opendir($galerie_dir);
    while ($file = readdir($d)) {
	  if ($file[0] == ".") { }
	  else {
	      $aff .= "&nbsp;|&nbsp;<span><a href='?galerie=$file'>";
          if ($file == $galerie)
              $aff .= "<b>$file</b>";
          else
              $aff .= "$file";
	      $aff .= "</a></span>";
	  }
    }
    $aff .= "<br><hr>";
    closedir($d);
}


if (isset($_GET["action"]))
    $action = $_GET["action"];
else
    $action = "view";

if ($action == "view") {

    if (isset($galerie)) {
        if ($galerie == "upload") {
            $path = $www_dir;
        } else {
            $path = $galerie_dir . '/' . $galerie;
        }

        if (file_exists($path)) {
            $aff .= '<table id="content"><tr>';
            $d = opendir($path);
            $i = 0;
            while ($file = readdir($d)) {
              if ($file[0] == ".") { }
              else {
                  $aff .= "<td><a href='$path/$file'><img width=64px height=64px src='$path/$file?preview' alt='$file'></a></td>";
                  $i = $i+1;
              }

              if ($i%4 == 0)
                  $aff .= "</tr><tr>";
            }
            $aff .= '</tr></table>';
        }

        if ($galerie == "upload") {
            $aff .= '<br><p><a href="?action=upload">Upload</a> your photo !</p>';
        }
    }

} else if ($action == "upload") {

    if (isset($_FILES["file"])) {
        $allowedExts = array("jpg", "jpeg", "gif", "png");
        $allowedType = array("image/gif", "image/jpeg", "image/png");

        $extension = end(explode(".", $_FILES["file"]["name"]));

        if (($_FILES["file"]["size"] < 100000)) {

          if (preg_match($_FILES["file"]["name"],'^[^\s]+\.(jpe?g|png|gif)$')) {

            $aff .= "File information&nbsp;:<br><ul>";
            if ($_FILES["file"]["error"] > 0)  {
                $aff .= "<ul>";
                $aff .= "<li>Error: " . $_FILES["file"]["error"] . "</li>";
            } else {
                $aff .= "<li>Upload: " . $_FILES["file"]["name"] . "</li>";
                $aff .= "<li>Type: " . $_FILES["file"]["type"] . "</li>";
                $aff .= "<li>Size: " . ($_FILES["file"]["size"] / 1024) . " kB</li>";
                $aff .= "<li>Stored in: " . $WWW_DIR . "/". $_FILES["file"]["name"]."</li>";
            }
            $aff .= "</ul>";

            if (! file_exists($upload_dir)) {
                mkdir($upload_dir, 0750);
            }

            if (move_uploaded_file($_FILES["file"]["tmp_name"], $upload_dir."/".$_FILES["file"]["name"])) {
                $aff .= "<b>File uploaded</b>.";
            } else {
                $aff .= "<p style='color: red'>Error during upload</p>";
            }
          } else {
              $aff .= "<p style='color: red'>Wrong file type !</p>";
          }
        } else {
            $aff .= "<p style='color: red'>File size too big !</p>";
        }
    } else {
        $aff .= "<p><b>Upload your photo</b></p>";
        $aff .= "<form method=post enctype='multipart/form-data'>";
        $aff .= "<input type=file name=file />";
        $aff .= "<input type=submit value=upload />";
        $aff .= "</form>";
        $aff .= "<br><br><i>NB : only GIF, JPEG or PNG are accepted</i>";
    }
}

$aff .= "</body></html>";
echo $aff;

?>
