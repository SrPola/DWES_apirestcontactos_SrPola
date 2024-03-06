<?php
    namespace App\Models;

    class Contactos extends DBAsbtractModel {
        private static $instancia;

        public static function getInstancia() {
            if (!isset(self::$instancia)) {
                $miclase = __CLASS__;
                self::$instancia = new $miclase;
            }
            return self::$instancia;
        }

        public function __clone() {
            trigger_error('La clonación no está permitida', E_USER_ERROR);
        }

        public function set($sh_data=array()) {
            foreach ($sh_data as $campo=>$valor) {
                $this->params[$campo] = $valor;
            }
            $this->query = "INSERT INTO contactos (nombre, telefono, email) VALUES (:nombre, :telefono, :email)";
            $this->get_results_from_query();
            $this->mensaje = "SH añadido";
        }

        public function get($id='') {
            if ($id != '') {
                $this->query = "SELECT * FROM contactos WHERE id = :id";
                $this->params['id'] = $id;
                $this->get_results_from_query();
            }

            if (count($this->rows) == 1) {
                foreach ($this->rows[0] as $campo=>$valor) {
                    $this->$campo = $valor;
                }
                $this->mensaje = "SH encontrado";
            } else {
                $this->mensaje = "SH no encontrado";
            }

            return $this->rows[0] ?? null;
        }

        public function edit($id='', $user_data=array()) {
            foreach ($user_data as $campo=>$valor) {
                $this->params[$campo] = $valor;
            }
            $this->params["fecha"]= (new \DateTime())->format("Y-m-d H:m:s");
            $this->query = "UPDATE contactos SET nombre=:nombre, telefono=:telefono, email=:email, updated_at=:fecha WHERE id=:id";
            $this->get_results_from_query();
        }

        public function delete($id='') {
            $this->query = "DELETE FROM contactos WHERE id=:id";
            $this->params['id'] = $id;
            $this->get_results_from_query();
            $this->mensaje = "SH eliminado";
        }

        public function getAll() {
            $this->query = "SELECT * FROM contactos";
            $this->get_results_from_query();
            return $this->rows;
        }
    }
