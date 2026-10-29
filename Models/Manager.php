<?php
namespace Models;

use Models\Bdd;
use Models\DatabaseException;
use PDO;
use PDOException;
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Fonctions/launch.php';

class Manager {
        private $connexion = null;
        private $inTransaction = false;

        /**
         * Obtient une connexion à la base de données
         * 
         * @return PDO Instance de PDO
         * @throws DatabaseException Si la connexion échoue
         */
        public function getConnexion() {
            try {
                if ($this->connexion === null) {
                    $this->connexion = Bdd::getConnexion();
                    
                    // Vérifier que la connexion est active
                    if (!$this->testConnection()) {
                        $this->connexion = null;
                        throw new DatabaseException("La connexion à la base de données n'est pas active");
                    }
                }
                return $this->connexion;
            } catch (PDOException $e) {
                logError("Erreur de connexion à la base de données: " . $e->getMessage());
                throw new DatabaseException("Impossible de se connecter à la base de données", 0, $e);
            }
        }
        
        /**
         * Teste si la connexion à la base de données est active
         * 
         * @return bool True si la connexion est active
         */
        private function testConnection(): bool {
            try {
                if ($this->connexion === null) {
                    return false;
                }
                
                // Exécuter une requête simple pour vérifier la connexion
                $this->connexion->query("SELECT 1");
                return true;
            } catch (PDOException $e) {
                return false;
            }
        }

        public function beginTransaction(): void {
            if ($this->inTransaction) {
                throw new DatabaseException("Une transaction est déjà en cours");
            }
        
            try {
                $this->getConnexion()->beginTransaction();
                $this->inTransaction = true;
            } catch (PDOException $e) {
                throw new DatabaseException("Erreur lors du démarrage de la transaction : " . $e->getMessage(), 0, $e);
            }
        }

        public function commit(): void {
            if (!$this->inTransaction) {
                throw new DatabaseException('Aucune transaction en cours');
            }
        
            try {
                $this->getConnexion()->commit();
                $this->inTransaction = false;
            } catch (PDOException $e) {
                $this->inTransaction = false;
                throw new DatabaseException('Erreur lors du commit : ' . $e->getMessage(), 0, $e);
            }
        }

        public function rollback() {
            if ($this->inTransaction) {
                $this->getConnexion()->rollBack();
                $this->inTransaction = false;
            }
        }
        public function readTableAll(string $table, ?int $userId = null): array {
            if (empty($table)) {
                throw new DatabaseException("Le nom de la table est requis");
            }
            
            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $table)) {
                throw new DatabaseException("Nom de table invalide");
            }

            try {
                $connexion = $this->getConnexion();
                $sql = "SELECT * FROM " . $this->escapeIdentifier($table);
                
                // Vérification de l'existence de la table
                if (!$this->tableExists($table)) { 
                    throw new DatabaseException("La table '$table' n'existe pas"); 
                }
                
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
		
		public function readTableOne($table, $id, $type="array"){
			if (empty($table) || !is_numeric($id)) {
				throw new DatabaseException("Paramètres invalides pour la lecture");
			}
			
			try {
				$connexion = $this->getConnexion();
				$sql = "SELECT * FROM " . $this->escapeIdentifier($table) . " WHERE id = :id";
				$statement = $connexion->prepare($sql);
				$statement->bindValue(':id', $id, PDO::PARAM_INT);
				$statement->execute();
				$result = $statement->fetch();
				
				if (!$result) {
					return null;
				}
				
				if ($type == 'array') {
					return $result;
				} else {
					$class = ucfirst($table);
					if (!class_exists($class)) {
						throw new DatabaseException("La classe '$class' n'existe pas");
					}
					$obj = new $class($result);
					return $obj;
				}
			} catch (PDOException $e) {
				logError("Erreur lors de la lecture de l'enregistrement $id dans la table $table: " . $e->getMessage());
				throw new DatabaseException("Erreur lors de la lecture des données", 0, $e);
			}
		}
		
		public function readTableFirst($table){
			if (empty($table)) {
				throw new DatabaseException("Le nom de la table est requis");
			}
			
			try {
				$connexion = $this->getConnexion();
				$sql = "SELECT * FROM " . $this->escapeIdentifier($table) . " ORDER BY id ASC LIMIT 1";
				$statement = $connexion->prepare($sql);
				$statement->execute();
				$resultat = $statement->fetch();
				return $resultat ?: null;
			} catch (PDOException $e) {
				logError("Erreur lors de la lecture du premier enregistrement de la table $table: " . $e->getMessage());
				throw new DatabaseException("Erreur lors de la lecture des données", 0, $e);
			}
		}
		
		public function readTableLast($table){
			if (empty($table)) {
				throw new DatabaseException("Le nom de la table est requis");
			}
			
			try {
				$connexion = $this->getConnexion();
				$sql = "SELECT * FROM " . $this->escapeIdentifier($table) . " ORDER BY id DESC LIMIT 1";
				$statement = $connexion->prepare($sql);
				$statement->execute();
				$resultat = $statement->fetch();
				return $resultat ?: null;
			} catch (PDOException $e) {
				logError("Erreur lors de la lecture du dernier enregistrement de la table $table: " . $e->getMessage());
				throw new DatabaseException("Erreur lors de la lecture des données", 0, $e);
			}
		}
		
        public function updateTable($table, $data, $id) {
            if (empty($table) || empty($data) || !is_array($data) || !is_numeric($id)) {
                throw new DatabaseException("Paramètres invalides pour la mise à jour");
            }

            try {
                $this->beginTransaction();
                $connexion = $this->getConnexion();
                
                // Vérifier si l'enregistrement existe
                $checkSql = "SELECT id FROM " . $this->escapeIdentifier($table) . " WHERE id = :id";
                $checkStmt = $connexion->prepare($checkSql);
                $checkStmt->bindValue(':id', $id, PDO::PARAM_INT);
                $checkStmt->execute();
                
                if ($checkStmt->rowCount() === 0) {
                    throw new DatabaseException("L'enregistrement avec l'ID $id n'existe pas dans la table $table");
                }
                
                $updates = [];
                $params = [];
                
                foreach ($data as $column => $value) {
                    if ($column !== 'id') { // Ignorer la colonne id dans les données à mettre à jour
                        $updates[] = $this->escapeIdentifier($column) . " = :$column";
                        $params[$column] = $value;
                    }
                }
                
                if (empty($updates)) {
                    throw new DatabaseException("Aucune donnée valide à mettre à jour");
                }
                
                $sql = "UPDATE " . $this->escapeIdentifier($table) . 
                       " SET " . implode(", ", $updates) . 
                       " WHERE id = :id";
                
                $statement = $connexion->prepare($sql);
                $statement->bindValue(":id", $id, PDO::PARAM_INT);
                
                foreach ($params as $key => $value) {
                    $statement->bindValue(":$key", $value, $this->getParamType($value));
                }
                
                if (!$statement->execute()) {
                    throw new DatabaseException("Erreur lors de la mise à jour");
                }
                
                $this->commit();
                return $this->readTableOne($table, $id);
                
            } catch (PDOException $e) {
                $this->rollback();
                logError("Erreur lors de la mise à jour de la table $table: " . $e->getMessage());
                throw new DatabaseException("Erreur lors de la mise à jour des données", 0, $e);
            } catch (\Exception $e) {
                $this->rollback();
                logError("Exception générique lors de la mise à jour de la table $table: " . $e->getMessage());
                throw new DatabaseException("Erreur lors de la mise à jour des données", 0, $e);
            }
        }

        private function getParamType($value) {
            if (is_int($value)) return PDO::PARAM_INT;
            if (is_bool($value)) return PDO::PARAM_BOOL;
            if (is_null($value)) return PDO::PARAM_NULL;
            return PDO::PARAM_STR;
        }
        
        /**
         * Vérifie si une table existe dans la base de données
         * @param string $tableName Nom de la table à vérifier
         * @return bool True si la table existe, false sinon
         */
        public function tableExists(string $tableName): bool {
            try {
                $connexion = $this->getConnexion();
                $stmt = $connexion->query("SHOW TABLES LIKE " . $connexion->quote($tableName));
                return $stmt !== false && $stmt->fetchColumn() !== false;
            } catch (PDOException $e) {
                logError("Erreur lors de la vérification de l'existence de la table $tableName: " . $e->getMessage());
                throw new DatabaseException("Erreur lors de la vérification de l'existence de la table", 0, $e);
            }
        }
        
        /**
         * Vérifie si la table users a la structure requise
         * @return bool True si la structure est correcte
         */
        public function checkUsersTableStructure(): bool {
            try {
                $connexion = $this->getConnexion();
                if (!$this->tableExists('users')) {
                    logError("La table users n'existe pas");
                    return false;
                }
                $requiredColumns = ['id', 'pseudo', 'email', 'mdp', 'role'];
                $stmt = $connexion->query("DESCRIBE `users`");
                $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

                foreach ($requiredColumns as $column) {
                    if (!in_array($column, $columns)) {
                        logError("La colonne $column est manquante dans la table users");
                        return false;
                    }
                }
                return true;
            } catch (PDOException $e) {
                logError("Erreur lors de la vérification de la structure de la table users: " . $e->getMessage());
                return false;
            }
        }
		
		public function insertTable($table, $data){
			if (empty($table) || empty($data) || !is_array($data)) {
				throw new DatabaseException("Paramètres invalides pour l'insertion");
			}
			
			try {
				$this->beginTransaction();
				$connexion = $this->getConnexion();
				
				$columns = [];
				$placeholders = [];
				$values = [];
				
				foreach ($data as $key => $value) {
					if ($key != 'id') {
						$columns[] = $this->escapeIdentifier($key);
						$placeholders[] = ":$key";
						$values[$key] = $value;
					}
				}
				
				if (empty($columns)) {
					throw new DatabaseException("Aucune donnée valide à insérer");
				}
				
				$sql = "INSERT INTO " . $this->escapeIdentifier($table) . 
					   " (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $placeholders) . ")";
				
				$statement = $connexion->prepare($sql);
				
				foreach ($values as $key => $value) {
					$statement->bindValue(":$key", $value, $this->getParamType($value));
				}
				
				if (!$statement->execute()) {
					throw new DatabaseException("Erreur lors de l'insertion");
				}
				
				$lastId = $connexion->lastInsertId();
				$this->commit();
				
				return $this->readTableOne($table, $lastId);
			} catch (PDOException $e) {
				$this->rollback();
				logError("Erreur lors de l'insertion dans la table $table: " . $e->getMessage());
				throw new DatabaseException("Erreur lors de l'insertion des données", 0, $e);
			} catch (\Exception $e) {
				$this->rollback();
				throw new DatabaseException("Erreur lors de l'insertion: " . $e->getMessage(), 0, $e);
			}
		}
		
		public function deleteTable($table, $id){
			if (empty($table) || !is_numeric($id)) {
				throw new DatabaseException("Paramètres invalides pour la suppression");
			}
			
			try {
				$this->beginTransaction();
				$connexion = $this->getConnexion();
				
				// Vérifier si l'enregistrement existe
				$checkSql = "SELECT id FROM " . $this->escapeIdentifier($table) . " WHERE id = :id";
				$checkStmt = $connexion->prepare($checkSql);
				$checkStmt->bindValue(':id', $id, PDO::PARAM_INT);
				$checkStmt->execute();
				
				if ($checkStmt->rowCount() === 0) {
					throw new DatabaseException("L'enregistrement avec l'ID $id n'existe pas dans la table $table");
				}
				
				$sql = "DELETE FROM " . $this->escapeIdentifier($table) . " WHERE id = :id";
				$statement = $connexion->prepare($sql);
				$statement->bindValue(':id', $id, PDO::PARAM_INT);
				
				if (!$statement->execute()) {
					throw new DatabaseException("Erreur lors de la suppression");
				}
				
				$this->commit();
				return ["status" => "success", "message" => "Suppression réussie"];
			} catch (PDOException $e) {
				$this->rollback();
				logError("Erreur lors de la suppression de l'enregistrement $id dans la table $table: " . $e->getMessage());
				throw new DatabaseException("Erreur lors de la suppression des données", 0, $e);
			} catch (\Exception $e) {
				$this->rollback();
				throw new DatabaseException("Erreur lors de la suppression: " . $e->getMessage(), 0, $e);
			}
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
                
                // Vérifier si la table existe
                if (!$this->tableExists($tableName)) {
                    throw new DatabaseException("La table '$tableName' n'existe pas");
                }
                
                // Vérifier si la colonne existe déjà (avec quote() au lieu de prepare)
                $stmt = $connexion->query(
                    "SHOW COLUMNS FROM " . $this->escapeIdentifier($tableName) . 
                    " LIKE " . $connexion->quote($columnName)
                );
                
                if ($stmt !== false && $stmt->fetchColumn() !== false) {
                    throw new DatabaseException("La colonne '$columnName' existe déjà dans la table '$tableName'");
                }
                
                // Valider la syntaxe de la définition
                if (!preg_match('/^[A-Za-z]+(\([0-9,]+\))?\s*(NOT NULL|NULL)?\s*(DEFAULT .*)?$/i', $definition)) {
                    throw new DatabaseException("La définition de la colonne est invalide. Format attendu: TYPE(taille) [NOT NULL] [DEFAULT valeur]");
                }
                
                // Ajouter la colonne
                $sql = "ALTER TABLE " . $this->escapeIdentifier($tableName) . 
                       " ADD COLUMN " . $this->escapeIdentifier($columnName) . " " . $definition;
                
                if ($connexion->exec($sql) === false) {
                    throw new DatabaseException("Échec de l'ajout de la colonne. Vérifiez la syntaxe de la définition.");
                }
                
                logInfo("Colonne '$columnName' ajoutée avec succès à la table '$tableName'");
                return true;
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

        /**
         * Trouve un utilisateur par son pseudo
         * 
         * @param string $pseudo Pseudo de l'utilisateur
         * @return array|null Données de l'utilisateur ou null si non trouvé
         * @throws DatabaseException En cas d'erreur de base de données
         */
        public function findUserByPseudo(string $pseudo) {
            if (empty($pseudo)) {
                throw new DatabaseException("Le pseudo ne peut pas être vide");
            }
            
            try {
                $connexion = $this->getConnexion();
                $sql = "SELECT id, pseudo, mdp, role, last_login, active FROM users WHERE pseudo = :pseudo";
                $stmt = $connexion->prepare($sql);
                $stmt->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($result) {
                    // Ne pas logger le mot de passe dans les logs
                    $logInfo = "Utilisateur trouvé: " . $pseudo . " avec le rôle: " . ($result['role'] ?? 'non défini');
                    if (isset($result['active']) && $result['active'] == 0) {
                        $logInfo .= " (compte inactif)";
                    }
                    logInfo($logInfo);
                } else {
                    logInfo("Tentative de connexion avec un pseudo inexistant: " . $pseudo);
                }
                
                return $result;
            } catch (PDOException $e) {
                logError("Erreur lors de la recherche de l'utilisateur: " . $e->getMessage());
                throw new DatabaseException("Erreur lors de la recherche de l'utilisateur", 0, $e);
            }
        }
        
        /**
         * Met à jour la date de dernière connexion d'un utilisateur
         * 
         * @param int $userId ID de l'utilisateur
         * @return bool True si la mise à jour a réussi
         */
        public function updateLastLogin(int $userId): bool {
            try {
                $connexion = $this->getConnexion();
                $sql = "UPDATE users SET last_login = NOW() WHERE id = :id";
                $stmt = $connexion->prepare($sql);
                $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
                return $stmt->execute();
            } catch (PDOException $e) {
                logError("Erreur lors de la mise à jour de la dernière connexion: " . $e->getMessage());
                return false;
            }
        }
        
        /**
         * Vérifie si un utilisateur existe
         * 
         * @param string $pseudo Pseudo de l'utilisateur
         * @return bool True si l'utilisateur existe
         */
        public function userExists(string $pseudo): bool {
            try {
                $connexion = $this->getConnexion();
                $sql = "SELECT COUNT(*) FROM users WHERE pseudo = :pseudo";
                $stmt = $connexion->prepare($sql);
                $stmt->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
                $stmt->execute();
                return (int)$stmt->fetchColumn() > 0;
            } catch (PDOException $e) {
                logError("Erreur lors de la vérification de l'existence de l'utilisateur: " . $e->getMessage());
                return false;
            }
        }
    }
?>
