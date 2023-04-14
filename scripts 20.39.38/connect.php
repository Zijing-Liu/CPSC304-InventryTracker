<?php
    // $servername = "localhost:3306";
    // $username = "project";
    // $password = "CPSC304!";

    class DBConnect {
        // properties
        public $servername;
        public $dbname;

        public $errmsg;

        public $conn;

        protected $stmt_cache;

        private $username;
        private $password;

        /* 
         * construct a new DBConnect object targeted at the given server and 
         * database
         */
        public function __construct(string $servername, string $dbname) {
            $this->servername = $servername;
            $this->dbname = $dbname;
            $this->conn = null;
        }

        /*
         * set credentials for login
         */
        public function set_login(string $username, string $password) {
            $this->username = $username;
            $this->password = $password;
        }

        /*
         * attempt to login to database. returns a boolean indicating operation
         * success. will return false if already connected. The error message 
         * can be found in the errmsg property.
         */
        public function login() {
            if ($this->conn != null) {
                $this->errmsg = "Already connected";
                return false;
            }

            try {
                $this->conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);

                // set the PDO error mode to exception
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                return true;
            } catch(PDOException $e) {
                $this->errmsg = "Connection failed: " . $e->getMessage();

                return false;
            }
        }

        /*
         * terminates connection to database. will do nothing if not called 
         * when not connected.
         */
        public function disconnect() {
            $this->conn = null;
        }

        /*
         * execute an SQL statement. returns true if successful. if there is an
         * exception, returns false and prints the exception information.
         */
        public function execute(string $sql) {
            try {
                $this->conn->exec($sql);
                return true;
            } catch (PDOException $e) {
                $this->errmsg = $sql . "<br>" . $e->getMessage();
                return false;
            }
        }

        /*
         * prepare and execute the given SQL statement. returns the statement
         * for data extraction.
         */
        public function query(string $sql, array $args = null) {
            $stmt = null;
            
            if ($args == null) {
                // use query() if no arguments
                $stmt = $this->conn->query($sql);
            } else {
                // use prepare and execute if arguments
                $stmt = $this->conn->prepare($sql);
                $stmt->execute($args);
            }

            return $stmt;
        }
    }
?>