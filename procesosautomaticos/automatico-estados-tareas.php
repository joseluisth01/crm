<?php

require('funcionesbbdd.php');


//Conectamos con BBDD
        $mysqli=conexion();
		
		$mysqli->select_db("gestiontictaccom_admin");
		
		$sql='SELECT t.id id, t.title title, t.project_id project_id, t.status_id status_id, t.blocking blocking, t.blocked_by blocked_by FROM crm_tasks t, crm_projects p WHERE t.project_id=p.id AND t.status_id=7 AND p.title NOT LIKE "%[NOTOCAR]%"';	
					if (!$resultado=$mysqli->query($sql)) 
	                    {
	                                 echo "Lo sentimos, no se ha podido realizar correctamente la consulta";
	                    }
		
		$nregistros=$resultado->num_rows;
		
		//Aquí sería primero para los proyectos recien creados y no puestas los estados ponerlos
		
		while($fila= $resultado->fetch_array())
		{
				//Primero comprobamos si la tarea no está bloqueada por ninguna otra tarea y entonces la ponemos como pendiente de hacer
				$sql2='SELECT * FROM crm_tasks WHERE blocking='.$fila['id'];	
					if (!$resultado2=$mysqli->query($sql2)) 
	                    {
	                                 echo "Lo sentimos, no se ha podido realizar correctamente la consulta";
	                    }
		
				$bloqueos=$resultado2->num_rows;

				//Para ello lo primero es ver si la tarea que estamos revisando es bloqueada o bloqueando por alguna tarea, es decir, es la primera
						if($fila['blocked_by']==null && $bloqueos==0)
						{
							$sqlupdate='UPDATE crm_tasks SET status_id=1 WHERE id='.$fila['id'];
							if (!$mysqli->query($sqlupdate)) 
										{
													echo "Lo sentimos, no se ha podido realizar correctamente la consulta";
										}
						}
						else
						{
							//En segundo lugar comprobamos si la tarea es bloqueada y asignamos status
							$sqlupdate='UPDATE crm_tasks SET status_id=4 WHERE id='.$fila['id'];
							if (!$mysqli->query($sqlupdate)) 
										{
													echo "Lo sentimos, no se ha podido realizar correctamente la consulta";
										}
						}
		}

		//Ahora comprobaríamos en los casos en los que se ha cambiado el estado

		//Tenemos que quitar 121 minutos, es decir, 120 minutos porque se guarda en la bbdd -2 horas y 1 minuto porque revisa lo anterior a 5 minutos
		$mPastTime = strtotime('-121 minutes');
     	$fechahora= date('Y-m-d H:i:s', $mPastTime);
		$sql='SELECT * FROM crm_tasks WHERE status_id=3 AND status_changed_at >= "'.$fechahora.'"';	
		//echo $sql.'<br/>';
					if (!$resultado=$mysqli->query($sql)) 
	                    {
	                                 echo "Lo sentimos, no se ha podido realizar correctamente la consulta";
	                    }
                        
                        //Revisamos todas las filas y vemos si bloquea o es bloqueado
                        while($fila= $resultado->fetch_array())
                        {
                            //print_r($fila);
                            //Si blocking no está vacio es porque está bloqueando a otra tarea y esa tarea la activamos
                            if($fila['blocking']!=null)
                            {
                                //Se actualiza la tarea a por hacer
                                //En segundo lugar comprobamos si la tarea es bloqueada y asignamos status
                                $sqlupdate='UPDATE crm_tasks SET status_id=1 WHERE id='.$fila['blocking'];
                                //echo $sqlupdate.'<br/>';
                                if (!$mysqli->query($sqlupdate)) 
										{
													echo "Lo sentimos, no se ha podido realizar correctamente la consulta";
										}

                            }
                            else
                            {
                                //Ahora buscamos en caso de que una tarea sea bloqueada por la anterior
                                $sql2='SELECT * FROM crm_tasks WHERE blocked_by='.$fila['id'];	
                                if (!$resultado2=$mysqli->query($sql2)) 
                                    {
                                                echo "Lo sentimos, no se ha podido realizar correctamente la consulta";
                                    }
                                    
                                    //Entra si encuentra una tarea que fuese bloqueada por otra
                                    while($fila2= $resultado2->fetch_array())
                                    {
                                        $sqlupdate='UPDATE crm_tasks SET status_id=1 WHERE id='.$fila2['id'];
                                        if (!$mysqli->query($sqlupdate)) 
                                                {
                                                            echo "Lo sentimos, no se ha podido realizar correctamente la consulta";
                                                }
                                    }
                            }

                        }
		
		
			$mysqli->close();
?>

		