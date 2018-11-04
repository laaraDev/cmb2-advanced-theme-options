<?php
    /*
    * block loader class
    */
    class BlocksLoader {

        private $args = array();

        /**
         * construct component class.
         *
         * @param $component
         * @param array $data
         * @param array $params
         */
        public function __construct($args)
        {
            $this->args = $args;
            // foreach ($this->args as $key => $val) {
            //     if (is_numeric($key)) {
            //         $this->args = $this->changeArrayKey( $this->args, $key, $val );
            //     }
            // }
        }

        /* load method from args */
        public function __call($method, $args) {
            return $this->block(substr($method,3,strlen($method)-3));
        }

        public function buildBlockKey($method)
        {
            return "Block".ucfirst($method);
        }

        public function block($method)
        {
            if (array_key_exists($this->getFileName($method), $this->args)) {
                $GLOBALS[$this->buildBlockKey($method)] = $this->args[$this->getFileName($method)];
                require dirname(__DIR__)."/blocks/".$this->getFileName($method).".php";
            }else{
                die("Error Processing Request, block {$method} does not exist.");
            }
        }

        public function getFileName($method)
        {
            return strtolower($method);
        }

        public function changeArrayKey( $array, $old_key, $new_key ) {
            if( ! array_key_exists( $old_key, $array ) )
                return $array;
            $keys = array_keys( $array );
            $keys[ array_search( $old_key, $keys ) ] = $new_key;
            return array_combine( $keys, $array );
        }
    }
?>