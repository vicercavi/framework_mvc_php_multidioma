<?php

class indexModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function getRole($roleID)
    {
        try{
            $roleID = (int) $roleID;        
            $role = $this->_db->query("SELECT * FROM rol WHERE Rol_IdRol = {$roleID}");
            return $role->fetch();
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "getRole", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    public function getRolTraducido($Rol_IdRol,$Idi_IdIdioma)
    {
        try{
            $Rol_IdRol = (int) $Rol_IdRol;        
            $role = $this->_db->query(
                    "SELECT "
                    . "Rol_IdRol,"
                    . "fn_TraducirContenido('rol','Rol_role',Rol_IdRol,'$Idi_IdIdioma',Rol_role) Rol_role,"
                    . "fn_devolverIdioma('rol',Rol_IdRol,'$Idi_IdIdioma',Idi_IdIdioma) Idi_IdIdioma"
                    . " FROM rol WHERE Rol_IdRol = {$Rol_IdRol}");
            return $role->fetch();
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "getRolTraducido", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    public function verificarRol($role)
    {
        try{
            $role = $this->_db->query("SELECT * FROM rol WHERE Rol_role = '$role'");
            return $role->fetch();
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "verificarRol", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    public function editarRole($Rol_IdRol,$Rol_role) {
        try{
            $usuarios = $this->_db->query(
                "UPDATE rol SET Rol_role = '$Rol_role' where Rol_IdRol = $Rol_IdRol"
            );
            return $usuarios->rowCount();
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "editarRole", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    public function editarTraduccion($Rol_IdRol, $Rol_role, $Idi_IdIdioma) {

        $ContTradNombre = $this->buscarCampoTraducido('rol', $Rol_IdRol, 'Rol_role', $Idi_IdIdioma);
       
        $idContTradNombre = $ContTradNombre['Cot_IdContenidoTraducido'];
        
        if (isset($idContTradNombre)) {
            try{
                $rol = $this->_db->query(
                    "UPDATE contenido_traducido SET Cot_Traduccion = '$Rol_role' WHERE Cot_IdContenidoTraducido = $idContTradNombre"
                );
                return $rol->rowCount();
            } catch (PDOException $exception) {
                $this->registrarBitacora("acl(indexModel)", "editarTraduccion", "Error Model", $exception);
                return $exception->getTraceAsString();
            }
        } else {
            try{
                $rol = $this->_db->prepare(
                        "INSERT INTO contenido_traducido VALUES (null, 'rol', :Cot_IdRegistro, 'Rol_role' , :Idi_IdIdioma, :Cot_Traduccion)"
                    )
                    ->execute(array(
                        ':Cot_IdRegistro' => $Rol_IdRol,
                        ':Idi_IdIdioma' => $Idi_IdIdioma,
                        ':Cot_Traduccion' => $Rol_role
                ));
                return $rol->rowCount();
            } catch (PDOException $exception) {
                $this->registrarBitacora("acl(indexModel)", "editarTraduccion", "Error Model", $exception);
                return $exception->getTraceAsString();
            }            
        }
    }
    
    public function buscarCampoTraducido($tabla, $Rol_IdRol, $columna, $Idi_IdIdioma) {
        try{
            $post = $this->_db->query(
                    "SELECT * FROM contenido_traducido WHERE Cot_Tabla = '$tabla' AND Cot_IdRegistro =  $Rol_IdRol AND  Cot_Columna = '$columna' AND Idi_IdIdioma= '$Idi_IdIdioma'");
            return $post->fetch();
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "buscarCampoTraducido", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    
    
    
    public function editarPermiso($Per_IdPermiso,$Per_Nombre,$Per_Ckey) {
        try{
            $permiso = $this->_db->query(
                "UPDATE permisos SET Per_Nombre = '$Per_Nombre', Per_Ckey = '$Per_Ckey' where Per_IdPermiso = $Per_IdPermiso"
            );
            return $permiso->rowCount(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "editarPermiso", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    public function cambiarEstadoRole($idRol, $estado)
    {
        try{
            if($estado==0)
            {
                $usuarios = $this->_db->query(
                "UPDATE rol SET Rol_Estado = 1 where Rol_IdRol = $idRol"
                );
            }
            if($estado==1)
            {
                $usuarios = $this->_db->query(
                "UPDATE rol SET Rol_Estado = 0 where Rol_IdRol = $idRol"
                );
            }
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "cambiarEstadoRole", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    //util
    public function cambiarEstadoPermisos($Per_IdPermiso, $Per_Estado)
    {
        try{
            if($Per_Estado==0)
            {

                $sql = "call s_u_cambiar_estado_permiso(?,1)";
                $result = $this->_db->prepare($sql);
                $result->bindParam(1, $Per_IdPermiso, PDO::PARAM_INT);
                $result->execute();

                return $result->rowCount(PDO::FETCH_ASSOC);
                
                // $permiso = $this->_db->query(
                // " UPDATE permisos SET Per_Estado = 1 where Per_IdPermiso = $Per_IdPermiso "
                // );
            }
            if($Per_Estado==1)
            {

                $sql = "call s_u_cambiar_estado_permiso(?,0)";
                $result = $this->_db->prepare($sql);
                $result->bindParam(1, $Per_IdPermiso, PDO::PARAM_INT);
                $result->execute();

                return $result->rowCount(PDO::FETCH_ASSOC);

                // $permiso = $this->_db->query(
                // " UPDATE permisos SET Per_Estado = 0 where Per_IdPermiso = $Per_IdPermiso "
                // );
            }

        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "cambiarEstadoPermisos", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    public function getRoles($condicion = '')
    {
        try{
            $roles = $this->_db->query("SELECT * FROM rol $condicion ");
            return $roles->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "getRoles", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    public function getUsuarioRol($Rol_IdROl) {
        try{
            $roles = $this->_db->query("SELECT U.* FROM ( SELECT DISTINCT(Rol_IdROl) FROM usuario ) U WHERE U.Rol_IdROl = $Rol_IdROl ");        
            return $roles->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "getUsuarioRol", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    public function getPermisosRole($roleID)
    {
        $data = array();
        try{
            $permisos = $this->_db->query(
                    "SELECT * FROM permisos_rol WHERE Rol_IdRol = {$roleID}"
                    );

            $permisos = $permisos->fetchAll(PDO::FETCH_ASSOC);

            for($i = 0; $i < count($permisos); $i++){
                $key = $this->getPermisoKey($permisos[$i]['Per_IdPermiso']);

                if($key == ''){continue;}
                if($permisos[$i]['Rol_Valor'] == 1){
                    $v = true;
                }
                else{
                    $v = false;
                }

                $data[$key] = array(
                    'key' => $key,
                    'valor' => $v,
                    'nombre' => $this->getPermisoNombre($permisos[$i]['Per_IdPermiso']),
                    'id' => $permisos[$i]['Per_IdPermiso']
                );
            }

            $todos = $this->getPermisosAll();
            $data = array_merge($todos, $data);
        
            return $data;
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "getPermisosRole", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    public function getPermiso($permisoID)
    {
        try{
            $permisoID = (int) $permisoID;
            $key = $this->_db->query(
                    "SELECT * FROM permisos WHERE Per_IdPermiso = $permisoID"
                    );
            return $key->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "getPermiso", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    public function getPermisoKey($permisoID)
    {
        $permisoID = (int) $permisoID;
        try{
            $key = $this->_db->query(
                    "SELECT Per_Ckey as 'key' FROM permisos WHERE Per_IdPermiso = $permisoID"
                    );

            $key = $key->fetch();
            return $key['key'];
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "getPermisoKey", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    
    public function getPermisoNombre($permisoID)
    {
        $permisoID = (int) $permisoID;
        try{
            $key = $this->_db->query(
                    "SELECT Per_Nombre FROM permisos WHERE Per_IdPermiso = $permisoID"
                    );

            $key = $key->fetch();
            return $key['Per_Nombre'];
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "getPermisoNombre", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    
    public function getPermisosAll()
    {
        try{
            $permisos = $this->_db->query(
                    "SELECT * FROM permisos"
                    );

            $permisos = $permisos->fetchAll(PDO::FETCH_ASSOC);

            for($i = 0; $i < count($permisos); $i++){
                $data[$permisos[$i]['Per_Ckey']] = array(
                    'key' => $permisos[$i]['Per_Ckey'],
                    'valor' => 'x',
                    'nombre' => $permisos[$i]['Per_Nombre'],
                    'id' => $permisos[$i]['Per_IdPermiso']
                );
            }

            return $data;
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "getPermisosAll", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    
    public function insertarRol($iRol_role, $iIdi_IdIdioma="", $iRol_Estado=1)
    {
        try {            
            $sql = "call s_i_rol(?,?,?)";
            $result = $this->_db->prepare($sql);
            $result->bindParam(1, $iRol_role, PDO::PARAM_STR);
            $result->bindParam(2, empty($iIdi_IdIdioma) ? null : $iIdi_IdIdioma, PDO::PARAM_NULL | PDO::PARAM_STR);
            $result->bindParam(3, $iRol_Estado, PDO::PARAM_INT);
            $result->execute();
            return $result->fetch();
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "insertarRol", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    
    //Util Permisos
    public function getPermisos($pagina,$registrosXPagina,$activos = 0)
    {
        try{
            $sql = "call s_s_listar_permisos_con_modulo(?,?,?)";
            $result = $this->_db->prepare($sql);
            $result->bindParam(1, $pagina, PDO::PARAM_INT);
            $result->bindParam(2, $registrosXPagina, PDO::PARAM_INT);
            $result->bindParam(3, $activos, PDO::PARAM_INT);
            $result->execute();
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "getPermisos", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    
    //Util
    public function getPermisosRowCount($condicion = "")
    {
        try{
            $sql = " SELECT COUNT(p.Per_IdPermiso) AS CantidadRegistros FROM permisos p LEFT JOIN modulo m ON p.Mod_IdModulo = m.Mod_IdModulo  $condicion ";
            $result = $this->_db->prepare($sql);
            $result->execute();
            return $result->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "getPermisosRowCount", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    //Util
    public function getPermisosCondicion($pagina,$registrosXPagina,$condicion = "")
    {
        try{
            $registroInicio = 0;
            if ($pagina > 0) {
                $registroInicio = ($pagina - 1) * $registrosXPagina;
                
            }
            $sql = " SELECT p.*, m.Mod_Nombre FROM permisos p
                LEFT JOIN modulo m ON p.Mod_IdModulo = m.Mod_IdModulo  $condicion 
                LIMIT $registroInicio, $registrosXPagina ";
            $result = $this->_db->query($sql);
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "getPermisos", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    //Util
    public function getModulos(){
        try{
            $sql = "call s_s_listar_modulos()";
            $result = $this->_db->prepare($sql);
            $result->execute();
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "getModulos", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    //util
    public function verificarPermiso($permiso)
    {
        try{
            $permiso = $this->_db->query("SELECT * FROM permisos WHERE Per_Nombre = '$permiso'");
            return $permiso->fetch();
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "verificarPermiso", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    //util
    public function verificarPermisoRol($Per_IdPermiso)
    {
        try{
            $permiso = $this->_db->query("SELECT * FROM permisos_rol WHERE Per_IdPermiso = '$Per_IdPermiso' and Per_Valor = 1");
            return $permiso->fetchAll();
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "verificarPermisoRol", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    //util
    public function verificarPermisoUsuario($Per_IdPermiso)
    {
        try{
            $permiso = $this->_db->query("SELECT * FROM permisos_usuario WHERE Per_IdPermiso = '$Per_IdPermiso' and Peu_Valor = 1");
            return $permiso->fetchAll();
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "verificarPermisoUsuario", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    //util
    public function verificarKey($ckey)
    {
        try{
            $ckey = $this->_db->query("SELECT * FROM permisos WHERE Per_Ckey = '$ckey'");
            return $ckey->fetch();
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "verificarKey", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    public function getPermisosUsuario()
    {
        try{
            $permisos = $this->_db->query("SELECT * FROM permisos_usuario");        
            return $permisos->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "getPermisosUsuario", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    //util
    public function eliminarPermisosRol($permisoID)
    {
        try{
            $permiso = $this->_db->query(
                " UPDATE permisos_rol SET Rol_Valor = 0  WHERE Per_IdPermiso = {$permisoID} "
                            
                );
            return $permiso->rowCount(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "eliminarPermisosRol", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    public function eliminarPermisoRole($roleID, $permisoID)
    {
        try{
            $this->_db->query(
                "DELETE FROM permisos_rol " . 
                "WHERE Per_IdPermiso = {$permisoID} " .
                "AND Rol_IdRol = {$roleID} "               
                );
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "eliminarPermisoRole", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }

    public function eliminarPermisosUsuario($permisoID)
    {
        try{
            $permiso = $this->_db->query(
                "DELETE FROM permisos_usuario " . 
                "WHERE Per_IdPermiso = {$permisoID} " .
                "AND Usu_Valor = 0 "               
                );
            return $permiso->rowCount(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "eliminarPermisosUsuario", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    public function eliminarRole($roleID)
    {
        try{
            $this->_db->query(
                "DELETE FROM rol WHERE Rol_IdRol = $roleID "               
                );
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "eliminarRole", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    //UTIL
    public function eliminarHabilitarPermiso($Per_IdPermiso,$Per_Eliminar)
    {
        try{
            $permiso = $this->_db->query(
                " UPDATE permisos SET Per_Eliminar = $Per_Eliminar WHERE Per_IdPermiso = $Per_IdPermiso "               
                );
            return $permiso->rowCount(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "eliminarHabilitarPermiso", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    public function editarPermisoRole($roleID, $permisoID, $valor)
    {
        try{
            $this->_db->query(
                "replace into permisos_rol set Rol_IdRol = {$roleID}, Per_IdPermiso = {$permisoID}, Rol_Valor = '{$valor}'"
                );
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "editarPermisoRole", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }

    //utilizo
    public function insertarPermiso($iPer_Nombre, $iPer_Ckey, $iMod_Modulo = "", $iIdi_IdIdioma="")
    {
        try {            
            $sql = "call s_i_permisos(?,?,?,?)";
            $result = $this->_db->prepare($sql);
            $result->bindParam(1, $iPer_Nombre, PDO::PARAM_STR);
            $result->bindParam(2, $iPer_Ckey, PDO::PARAM_STR);
            $result->bindParam(3, empty($iMod_Modulo) ? null : $iMod_Modulo, PDO::PARAM_NULL | PDO::PARAM_INT);            
            $result->bindParam(4, empty($iIdi_IdIdioma) ? null : $iIdi_IdIdioma, PDO::PARAM_NULL | PDO::PARAM_STR);
           
            $result->execute();
            return $result->fetch();
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "insertarPermiso", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
    
    public function getIdiomas() {
        try {
            $idiomas = $this->_db->query("SELECT * FROM idioma");
            return $idiomas->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            $this->registrarBitacora("acl(indexModel)", "getIdiomas", "Error Model", $exception);
            return $exception->getTraceAsString();
        }
    }
}

?>
