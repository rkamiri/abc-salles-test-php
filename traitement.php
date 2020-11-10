<?php

if(isset($_FILES['uploaded_file'])){
    $path=upload();
    if($path!=='error'){
        initDb();
        readLineByLine($path);
        forceDownload('download/export.csv');
    }
    else{
        echo 'there was an error with the file ...';
    }
}

else{
    echo'
<!DOCTYPE html>
        <html>
        <body>

        <form action="traitement.php" method="post" enctype="multipart/form-data">
        Select File to Upload:
        <input required type="file" accept=".csv" name="uploaded_file" id="fileToUpload">
        <input type="submit" value="Upload File" name="submit">
        </form>

        </body>
        </html>
';
}

function initDb(){
    $servername = "localhost";
    $username = "id15366796_rkamiri";
    $password = "?|Wv-V69oH#8mXiN";
    try{
        $conn = new PDO("mysql:host=$servername;dbname=id15366796_abcsalles", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $_SESSION['conn']=$conn;
    }catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
}

function upload(){
    if(!empty($_FILES['uploaded_file'])){
        $path = "upload/";
        $path = $path . basename( $_FILES['uploaded_file']['name']);
        if(move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $path)) {
            $info = pathinfo($path);
            if ($info["extension"] == "csv")
                return $path;
            else
                return 'error';
        }
    }
}

function readLineByLine($path){
    $data="";

    if ($file = fopen($path, "r")) {

        while(!feof($file)) {
            $line = fgets($file);
            $nameArray=treat($line);
            $dataArray=nameTreatment($nameArray[1], $nameArray[2]);
            $name=formatData($dataArray[0], $dataArray[1], $dataArray[2]);
            $currLine=chunksTreatment($line, $dataArray[0], $dataArray[1], $name);
            $currLine= preg_replace( "/\r|\n/", "", $currLine );
            $currLine = $currLine."\n";
            $data = $data.$currLine;
        }
        fclose($file);
    }
    writeInFile($data);
}

function treat($chain){
    $pieces = "";
    if(strlen($chain)!==0){
        $pieces = explode(';', $chain);
    }
    return $pieces;
}

//fonction de vérification du nom
function nameTreatment($firstName, $lastname){

    $firstName=str_replace('"', "", $firstName);
    $lastname=str_replace('"', "", $lastname);
    $existFn=checkIfNameExists($firstName);
    $existLn=checkIfNameExists($lastname);
    $new_firstname="";
    $new_lastname="";
    $civilite="";


    if($existFn!=='noResult'){
        $new_firstname=$firstName;
        $new_lastname=$lastname;
        $civilite=$existFn;
    }
    else{
        if($existLn!=='noResult'){
            $genre=$existLn;
            $new_firstname=$lastname;
            $new_lastname=$firstName;
        }
        else{
            $new_firstname=$firstName;
            $new_lastname=$lastname;
        }
    }

    $tab=  array ($new_firstname,$new_lastname,$civilite);
    return $tab;
}

function formatData($firstname, $lastname, $civilite){

    if($civilite==1){
        $civilite = 'M. ';
    }
    else if($civilite==2){
        $civilite = 'Mme. ';
    }
    return $civilite.$firstname.' '.$lastname;
}

function chunksTreatment($line, $newFn, $newLn, $name){
    $line=$line.';';
    $newFn = '"'.$newFn.'";';
    $newLn = '"'.$newLn.'";';
    $name = '"'.$name.'"';
    return $line.$newFn.$newLn.$name;
}


function checkIfNameExists($name){
    $conn=$_SESSION['conn'];
    $result="";
    try {
        $truc = $conn->prepare("SELECT genre FROM ref_prenom where label like'".$name."' order by label");
        $truc->execute();
        $result=$truc->fetchAll();
    }catch(PDOException $e) {
    }
    if(strlen($result[0][0])===0){
        return 'noResult';
    }
    else{
        return $result[0][0];
    }
}

// fonction d'écriture et de téléchargement
function writeInFile($treat){
    unlink('download/export.csv');
    $file = 'download/export.csv';
    $handle = fopen($file, 'w') or die('Cannot open file:  '.$file);
    fwrite($handle, $treat);
    fclose($handle);
    return $file;
}

function forceDownload($filePath){
    header('Content-Type: application/octet-stream');
    header("Content-Transfer-Encoding: Binary");
    header("Content-disposition: attachment; filename=\"" . basename($filePath) . "\"");
    readfile($filePath);
}
?>