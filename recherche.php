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
       $username = "id15333860_romain";
       $password = ")OR-?b7u{\=}RZMy";

        try {
            $conn = new PDO("mysql:host=$servername;dbname=id15333860_abcsalles", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $truc = $conn->prepare('SELECT label, type, genre, origin FROM ref_prenom where label like\''.$search.'%\' order by label');
            $truc->execute();
            $result=$truc->fetchAll();
            $size=count($result);
            for ($i = 0; $i < $size; $i++) {
                if($result[$i][1] = 1){
                    $tmpT = 'type masculin';
                }
                else if($result[$i][1] = 2){
                    $tmpT = 'type feminin';
                }
                else{
                    $tmpT='type ambigu';
                }
                if($result[$i][2] = 1){
                    $tmpG = 'Genre masculin';
                }
                else{
                    $tmpG = 'Genre feminin';
                }
                echo '<p>'.$result[$i][0].', '.$tmpT.', '.$tmpG.', pays : '.$result[$i][3].'</p>';
            }
            echo '<p>nombre de résultats totaux : '.$size.'</p>';
            /*for($i=0; $i<count($result); $i++){
                echo '<p>'.$result[0].' '$result[1].'</p>'
            }*/
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }   
        isset($_POST['search']); 
    }
?>