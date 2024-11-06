<?php

	require_once 'Bdd.php';
	require_once __DIR__ . '/../config.php';

	class Manager {
        private $connexion = null;
        private $inTransaction = false;

        public function getConnexion() {
            try {
                if ($this->connexion === null) {
                    $this->connexion = Bdd::getConnexion();
                }
                return $this->connexion;
            } catch (PDOException $e) {
                logError("Erreur de connexion à la base de données: " . $e->getMessage());
                throw new DatabaseException("Impossible de se connecter à la base de données", 0, $e);
            }
        }

        public function beginTransaction() {
            if (!$this->inTransaction) {
                $this->getConnexion()->beginTransaction();
                $this->inTransaction = true;
            }
        }

        public function commit() {
            if ($this->inTransaction) {
                $this->getConnexion()->commit();
                $this->inTransaction = false;
            }
        }

        public function rollback() {
            if ($this->inTransaction) {
                $this->getConnexion()->rollBack();
                $this->inTransaction = false;
            }
        }
        public function readTableAll($table, $userId = null) {
            if (empty($table)) {
                throw new DatabaseException("Le nom de la table est requis");
            }

            try {
                $connexion = $this->getConnexion();
                $sql = "SELECT * FROM " . $this->escapeIdentifier($table);
                
                if ($userId !== null) {
                    $sql .= " WHERE user_id = :user_id";
                }
                
                $query = $connexion->prepare($sql);
                
                if ($userId !== null) {
                    $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
                }
                
                if (!$query->execute()) {
                    throw new DatabaseException("Erreur lors de l'exécution de la requête");
                }
                
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                logError("Erreur lors de la lecture de la table $table: " . $e->getMessage());
                throw new DatabaseException("Erreur lors de la lecture des données", 0, $e);
            }
        }

        private function escapeIdentifier($identifier) {
            return '`' . str_replace('`', '``', $identifier) . '`';
        }
		
		public function readTableOne($table,$id,$type="array"){
			$connexion=$this->getConnexion();
			$sql="select * from $table where id=$id";
			$query=$connexion->query($sql);
			$result=$query->fetch();
			if($type=='array'){
				return $result;
			}else{
				$class=ucfirst($table);
				$obj=new $class($result);
				return $obj;
			}
		}
		
		public function readTableFirst($table){
			$connexion=$this->getConnexion();
			$sql="select * from $table order by id asc";
			$statement=$connexion->prepare($sql);
			$statement->execute();
			$resultat=$statement->fetch();
			return $resultat;
		}
		
		public function readTableLast($table){
			$connexion=$this->getConnexion();
			$sql="select * from $table order by id desc";
			$statement=$connexion->prepare($sql);
			$statement->execute();
			$resultat=$statement->fetch();
			return $resultat;
		}
		
        public function updateTable($table, $data, $id) {
            if (empty($table) || empty($data) || !is_array($data)) {
                throw new DatabaseException("Paramètres invalides pour la mise à jour");
            }

            try {
                $this->beginTransaction();
                $connexion = $this->getConnexion();
                
                $updates = [];
                $params = [];
                
                foreach ($data as $column => $value) {
                    $updates[] = $this->escapeIdentifier($column) . " = :$column";
                    $params[$column] = $value;
                }
                
                $sql = "UPDATE " . $this->escapeIdentifier($table) . 
                       " SET " . implode(", ", $updates) . 
                       " WHERE id = :id";
                
                $statement = $connexion->prepare($sql);
                $params['id'] = $id;
                
                foreach ($params as $key => $value) {
                    $statement->bindValue(":$key", $value, $this->getParamType($value));
                }
                
                if (!$statement->execute()) {
                    throw new DatabaseException("Erreur lors de la mise à jour");
                }
                
                if ($statement->rowCount() === 0) {
                    throw new DatabaseException("Aucune ligne mise à jour");
                }
                
                $this->commit();
                return $this->readTableOne($table, $id);
                
            } catch (Exception $e) {
                $this->rollback();
                logError("Erreur lors de la mise à jour de la table $table: " . $e->getMessage());
                throw new DatabaseException("Erreur lors de la mise à jour des données", 0, $e);
            }
        }

        private function getParamType($value) {
            if (is_int($value)) return PDO::PARAM_INT;
            if (is_bool($value)) return PDO::PARAM_BOOL;
            if (is_null($value)) return PDO::PARAM_NULL;
            return PDO::PARAM_STR;
        }
		
		public function insertTable($table,$data){
			$connexion=$this->getConnexion();
			$columns="";
			$values="";
			foreach($data as $key=>$valeur){
				if($key!='id'){
					if($columns==""){
						$columns=$key;
						$values="'$valeur'";
					}else{
					$columns=$columns.",$key";
					$values=$values.",'$valeur'";
					}
				}
			}
			$sql="insert into $table ($columns) values ($values) ";
			$statement=$connexion->prepare($sql);
			$statement->execute();
			$resultat=$this->readTableLast($table);
			return $resultat;
		}
		
		public function deleteTable($table,$id){
			$connexion=$this->getConnexion();
			$sql="delete from $table where id=?";
			$statement=$connexion->prepare($sql);
			$statement->execute(array($id));
			$resultat=array("Suppression okay");
			return $resultat;
		}
	
        /**
         * Exécute une requête de création de table
         * @param string $tableName Nom de la table
         * @param array $columns Définition des colonnes ['nom_colonne' => 'type définition']
         * @return bool
         * @throws DatabaseException
         */
        public function createTable(string $tableName, array $columns) {
            if (empty($tableName) || empty($columns)) {
                throw new DatabaseException("Le nom de la table et les colonnes sont requis");
            }

            try {
                $connexion = $this->getConnexion();

                $columnDefinitions = [];
                foreach ($columns as $name => $definition) {
                    $columnDefinitions[] = $this->escapeIdentifier($name) . ' ' . $definition;
                }

                $sql = "CREATE TABLE IF NOT EXISTS " . $this->escapeIdentifier($tableName) . " (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    " . implode(",\n                ", $columnDefinitions) . ",
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )";

                return $connexion->exec($sql) !== false;
            } catch (PDOException $e) {
                logError("Erreur lors de la création de la table $tableName: " . $e->getMessage());
                throw new DatabaseException("Erreur lors de la création de la table", 0, $e);
            }
        }

        /**
         * Ajoute une colonne à une table existante
         * @param string $tableName Nom de la table
         * @param string $columnName Nom de la colonne
         * @param string $definition Définition de la colonne (type et contraintes)
         * @return bool
         * @throws DatabaseException
         */
        public function addColumn(string $tableName, string $columnName, string $definition) {
            if (empty($tableName) || empty($columnName) || empty($definition)) {
                throw new DatabaseException("Le nom de la table, de la colonne et sa définition sont requis");
            }

            try {
                $connexion = $this->getConnexion();
                
                $sql = "ALTER TABLE " . $this->escapeIdentifier($tableName) . 
                       " ADD COLUMN " . $this->escapeIdentifier($columnName) . " " . $definition;
                
                return $connexion->exec($sql) !== false;
            } catch (PDOException $e) {
                logError("Erreur lors de l'ajout de la colonne $columnName à la table $tableName: " . $e->getMessage());
                throw new DatabaseException("Erreur lors de l'ajout de la colonne", 0, $e);
            }
        }
    
        /**
         * Modifie une colonne existante
         * @param string $tableName Nom de la table
         * @param string $columnName Nom de la colonne
         * @param string $definition Nouvelle définition de la colonne
         * @return bool
         * @throws DatabaseException
         */
        public function modifyColumn(string $tableName, string $columnName, string $definition) {
            if (empty($tableName) || empty($columnName) || empty($definition)) {
                throw new DatabaseException("Le nom de la table, de la colonne et sa définition sont requis");
            }

            try {
                $connexion = $this->getConnexion();
                
                $sql = "ALTER TABLE " . $this->escapeIdentifier($tableName) . 
                       " MODIFY COLUMN " . $this->escapeIdentifier($columnName) . " " . $definition;
                
                return $connexion->exec($sql) !== false;
            } catch (PDOException $e) {
                logError("Erreur lors de la modification de la colonne $columnName dans la table $tableName: " . $e->getMessage());
                throw new DatabaseException("Erreur lors de la modification de la colonne", 0, $e);
            }
        }
    }
?>
