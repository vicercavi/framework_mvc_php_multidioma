INSERT INTO `rol` VALUES (1,'Administrador',NULL,1);
INSERT INTO `usuario` VALUES (1,'nombre administrador','apellido administrador',12345685,'av. brazil 737','123456','iiap','poster - administrador del PHP MVC','admin','f3f4c7ac10098d04b0c99a68f0322e61cc42cc53','admin@admin.adm',5,'2012-03-21 20:53:07',1,'1963007335');
INSERT INTO `permisos` VALUES (1,'Listar Arquitectura Web','listar_arquitectura_web',NULL,1),(2,'Agregar Arquitectura Web','agregar_arquitectura_web',NULL,1),(3,'Editar Arquitectura Web','editar_arquitectura_web',NULL,1),(4,'Eliminar Arquitectura Web','eliminar_arquitectura_web',NULL,1),(5,'Habilitar/Deshabilitar Arquitectura Web','habilitar_deshabilitar_arquitectura_web',NULL,NULL),(6,'Agregar Usuario','agregar_usuario',NULL,NULL),(7,'Editar usuario','editar_usuario',NULL,NULL),(8,'Eliminar Usuario','eliminar_usuario',NULL,NULL),(9,'Habilitar/Deshabilitar Usuario','habilitar_deshabilitar_usuario',NULL,NULL),(10,'Listar Usuarios','listar_usuarios',NULL,NULL),(11,'Agregar Rol','agregar_rol',NULL,NULL),(12,'Editar Rol','editar_rol',NULL,NULL),(13,'Habilitar/Deshabilitar Rol','habilitar_deshabilitar_rol',NULL,NULL),(14,'Eliminar Rol','eliminar_rol',NULL,NULL),(15,'Listar Bitácora','listar_bitacora',NULL,NULL),(16,'Exportar Bitácora','exportar_bitacora',NULL,NULL),(17,'Listar Visita','listar_visita',NULL,NULL),(18,'Exportar Visita','exportar_visita',NULL,NULL),(19,'Listar Idiomas','listar_idiomas',NULL,NULL),(20,'Agregar Idioma','agregar_idioma',NULL,NULL),(21,'Editar Idioma','editar_idioma',NULL,NULL),(22,'Ver Perfil','ver_perfil',NULL,NULL),(23,'Editar Perfil','editar_perfil',NULL,NULL);
INSERT INTO `permisos_rol` VALUES (1,7,1),(1,8,1),(1,9,1),(1,10,1),(1,11,1),(1,12,1),(1,13,1),(1,14,1),(1,22,1),(1,23,1);
INSERT INTO `idioma` VALUES ('en','Ingles',1),('es','Español',1),('pt','Portugues',1);