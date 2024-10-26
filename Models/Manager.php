<?php

	require_once 'Bdd.php';

	class Manager {
	    public function getConnexion() {
	        try {
	            return Bdd::getConnexion();
	        } catch (PDOException $e) {
	            logError("Reconnecting due to lost connection: " . $e->getMessage());
	            return Bdd::getConnexion(); // Attempt to reconnect
	        }
	    }
		public function readTableAll($table, $userId = null){
			$connexion=$this->getConnexion();
			$sql="select * from $table";
			if ($userId !== null) {
				$sql .= " where user_id = :user_id";
			}
			$query=$connexion->prepare($sql);
			if ($userId !== null) {
				$query->bindValue(':user_id', $userId, PDO::PARAM_INT);
			}
			$query->execute();
			$resultat=$query->fetchAll();
			return $resultat;
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
		
		public function updateTable($table,$data,$id){
			$connexion=$this->getConnexion();
			$update="";
			foreach($data as $truc=>$machin){
				if($update==""){
					$update=$update."$truc='$machin'";
				}else{
				$update=$update.",$truc='$machin'";
				}
			}
			$sql="update $table set $update where id=?";
			//$connexion->query($sql);
			$statement=$connexion->prepare($sql);
			$statement->execute(array($id));
			$resultat=$this->readTableOne($table,$id);
			return $resultat;
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
	}

?>
