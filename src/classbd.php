<?php
namespace babirondo\classbd;
use PDO,MongoDB;

class db
{
    function conecta($bd="Postgres")
    {
        require_once("globais.php");

        $this->globais = new Globais();
        //	echo "\n Conectando no banco: ".$this->globais->banco ;

        switch ($bd){
            case("Postgres"):
                try {
                    $this->pdo = new PDO("pgsql:host=".$this->globais->localhost."	dbname=".  $this->globais->db ,  $this->globais->username,    $this->globais->password);
                    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true );
                }
                catch(PDOException $e) {
                    $this->erro =  '<font color=red>Error: ' . $e->getMessage();
                    $this->conectado = false;
                    return false;
                }
                $this->conectado = true;
            break;

            case("Mongo"):

                $this->mongo = new MongoDB\Client("mongodb://localhost:27017");

                $this->conectado = true;

                return $this->mongo;
                break;
        }



        return true;
    }

    function executa($sql, $prepared=0, $l=__LINE__, $debug=null)
    {
        $this->dados = null;
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
            catch(PDOException $e) {
                // if ($debug == 1)
                echo "Error PDO: <font color=#00aa00><pre>".var_export($sql)."</font></pre>" . $e->getMessage();
            }
            catch(Exception $e) {
                // if ($debug == 1)
                echo "\n $sql";
                echo 'Error EXCEPTION: ' . $e->getMessage();
            }
            finally {
                // após exceptions roda isso
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
}
