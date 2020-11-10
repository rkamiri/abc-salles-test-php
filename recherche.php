<?php
echo'  <html>
    <body>
    
    <form action="recherche.php" method="post">
        Search <input type="text" name="search" placeholder="prénom" required><br>
    <input type ="submit">
    </form>
    
    </body>
    </html>';


    if(isset($_POST['search'])) {
        $search = $_POST['search'];
       $servername = "localhost";
       $username = "id15366796_rkamiri";
       $password = "?|Wv-V69oH#8mXiN";

        try {
            $conn = new PDO("mysql:host=$servername;dbname=id15366796_abcsalles", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $truc = $conn->prepare('SELECT label, type, genre, origin FROM ref_prenom where label like\''.$search.'%\' order by label');
            $truc->execute();
            $result=$truc->fetchAll();
            $size=count($result);
            $color="";
            for ($i = 0; $i < $size; $i++) {
                if($result[$i][1] == 1){
                    $tmpT = 'type masculin';
                }
                else if($result[$i][1] == 2){
                    $tmpT = 'type feminin';
                }
                else{
                    $tmpT='type ambigu';
                    $color="white";
                }
                if($result[$i][2] == 1){
                    $tmpG = 'Genre prioritaire masculin';
                    $color= "#789AFF";
                }
                else{
                    $tmpG = 'Genre prioritaire feminin';
                    $color="#FFA9E4";
                }
                echo '<p style="background-color:'.$color.'">'.$result[$i][0].', '.$tmpT.', '.$tmpG.', origine : '.$result[$i][3].'</p>';
            }
            echo '<p>nombre de résultats totaux : '.$size.'</p>';

        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }   
        isset($_POST['search']); 
    }
?>