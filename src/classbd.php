<?php
namespace babirondo\classbd;
use PDO;
use MongoDB;

class db
{
    public $conectado = false;
    public $pdo;
    public $host;
    public $db;
    public $username;
    public $banco;
    public $sql;
    public $MongoDB;
    public $MongoTable;

    function conecta($bd="Postgres", $host, $db, $username, $password, $port=NULL)
    {

        $this->banco = $bd;
        switch ($bd){
            case("Postgres"):
                try {
                  $this->host = $host;
                  $this->db = $db;
                  $this->username = $username;

                    $port = (($port == NULL)?"5432":$port);

                    $conn = "pgsql:host=$host; port=$port;	dbname=$db  ";
                    $this->pdo = new PDO($conn, $username,    $password);

                    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true );


                }
                catch(PDOException $e) {
                    $this->erro =  '<font color=red>Error: ' . $e->getMessage().' \n <BR>
                                      host='.$host.'; port='.$port.';	dbname='.$db.'   , username:'.$username.'
                                    </font>';
                    echo   $this->erro;
                    $this->conectado = false;
                    return false;
                }
                $this->conectado = true;
                return $this;
            break;

            case("Mongo"):
                $port = (($port == NULL)?"27017":$port);

                try {
                  $this->Mongo = new MongoDB\Client("mongodb://$host:$port");
	 	 

                }
                catch(PDOException $e) {
                    $this->erro =  '<font color=red>Error: ' . $e->getMessage();
                    $this->conectado = false;
                    return false;
                }
                $this->conectado = true;

                return $this->Mongo;

              break;

              default:
                $this->erro =  '<BR>\n <font color=red>Error: Nao conectado a nenhum tipo de banco \n <BR>
                                  db='.$db.', host='.$host.'; port='.$port.';	dbname='.$db.'   , username:'.$username.'
                                </font>';
                echo   $this->erro;
                $this->conectado = false;
                return false;
              break;
        }



    }

    function displayError($error){
      echo "<BR><BR>
                 Error PDO:   <BR>
                  <font color=#ff0000>" . $error ." </font><BR>
                 SQL: <textarea rows=8 cols=90>".$this->sql."</textarea> <BR>
                 BANCO: ".$this->banco." <BR>
                 DATABASE:".$this->db." <BR>
                 USER:".$this->username." <BR>
                 HOST:".$this->host." <BR>
                 </font>" ;
    }

    function executa($sql, $prepared=0, $l=__LINE__, $debug=null)
    {
        $this->dados = null;
        $this->sql = $sql;
        if (substr(TRIM(STRTOUPPER($sql)),0,strpos(TRIM(STRTOUPPER($sql)), " " )  ) == "SELECT")
        {
            try {
                //select
                $select = 1;
                $this->res = $this->pdo->query($sql);
                if ($this->res)
                    $this->nrw = $this->res->rowCount();
                else
                    $this->nrw = null;
            }
            catch(\PDOException $e) {
                // if ($debug == 1)
                $this->displayError($e->getMessage());
            }
            catch(\Exception $e) {
                // if ($debug == 1)

                echo '<BR> <font color=#ff0000><pre>  Error EXCEPTION:  ' . $e->getMessage()." </font></pre>";
            }

            return $this->res;
        }
        else{
            //echo "\n ($l) $sql";
            //                echo "$sql \n";
            if ($prepared == 1)
            {
                $stmt = $this->pdo->prepare($sql);
                if ($stmt->execute()){
                    $this->res = true;
                    $this->dados = $stmt->fetch(PDO::FETCH_ASSOC);
                    return true;
                }
                else{
                    $this->res = false;
                    print_r($sql . "\n".$stmt->errorInfo);
                    return false;
                }
            }
            else{
                $this->res = $this->pdo->exec($sql);
                return $this->res;
            }
        }
    }
    function navega($i ){
        $this->dados = $this->res->fetch(PDO::FETCH_ASSOC, $i );
        if ($this->dados  ){
            return   true ;
        }
        else{
            return   false;
        }
    }

    function fechar()
     {
         $this->pdo = null;
     }



     //FOR Mongo
      function MongoFind($filter, $params=array()){
//	$db = $this->Mongo->selectDB($this->MongoDB)	;
//	$collection = new MongoCollection ($db, $this->MongoTable);


	$db =    $this->MongoDB;
	$table = $this->MongoTable;
	$conectadoTabela = $this->Mongo->$db->$table ;
	$resultMongo = $conectadoTabela->find( $filter, $params )  ;
//var_dump(  ($resultMongo)  );
//        if (is_array($resultMongo)){
	  return iterator_to_array($resultMongo);
//        }
//	return $resultMongo;
      }

      function MongoInsertOne($json){
		$db =    $this->MongoDB;
		$table = $this->MongoTable;
		$conectadoTabela = $this->Mongo->$db->$table ;
	
		$resultMongo = $conectadoTabela->InsertOne( $json )  ;


        return $resultMongo;
      }

      function MongoUpdateOne($filter, $param=array(), $options=array()){
                $db =    $this->MongoDB;
                $table = $this->MongoTable;
                $conectadoTabela = $this->Mongo->$db->$table ;


//		$filter = array( "_id" =>  new MongoDB\BSON\ObjectID( $args["idtorneio"] )     );
//	        $options = array( 'upsert' => true, 'multi' => false ); //
//	        $param =   array(  '$set' => $jsonRAW );



                $resultMongo = $conectadoTabela->UpdateOne($filter, $param, $options )  ;


        return $resultMongo;
      }

      function MongoDeleteOne($filter){
                $db =    $this->MongoDB;
                $table = $this->MongoTable;
                $conectadoTabela = $this->Mongo->$db->$table ;


//              $filter = array( "_id" =>  new MongoDB\BSON\ObjectID( $args["idtorneio"] )     );
//                $options = array( 'upsert' => true, 'multi' => false ); //
//                $param =   array(  '$set' => $jsonRAW );



                $resultMongo = $conectadoTabela->DeleteOne($filter )  ;


        return $resultMongo;


      }



}
