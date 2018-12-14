<?php
class Globais{

    public $env;
    public $banco;

    function __construct( ){

        switch($this->banco){

            case("local");
                $this->localhost = $servidor["campeonato"];
                $this->username = "postgres";
                $this->password = "postgres";
                $this->db ="championship_local";
                break;

            case("prod");
                $this->localhost = "localhost";
                $this->username = "postgres";
                $this->password = "bruno";
                $this->db ="championship";
                break;

        }
    }
}
