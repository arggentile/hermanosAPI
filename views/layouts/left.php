<aside class="main-sidebar">

    <section class="sidebar">
       
        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
                    [
                        'label' => 'Alumnos',
                        'icon' => 'users',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Listado', 'icon' => 'arrow-right', 'url' => ['/alumno/listado'], 'active' => (strpos($this->context->route,  'alumno/listado')!==FALSE)?true:false,'visible' => Yii::$app->user->can('listarAlumnos')],
                            ['label' => 'Carga Alumno', 'icon' => 'arrow-right', 'url' => ['/alumno/empadronamiento'], 'active' => (strpos($this->context->route,  'alumno/empadronamiento')!==FALSE)?true:false, 'visible' => Yii::$app->user->can('cargarAlumno')],
                            ['label' => 'Familias', 'icon' => 'arrow-right', 'url' => ['/grupo-familiar/listado'], 'active' => (strpos($this->context->route,  'grupo-familiar')!==FALSE)?true:false, 'visible' => Yii::$app->user->can('listarFamilias')],        
                            ['label' => 'Egresar', 'icon' => 'arrow-right', 'url' => ['/alumno/egresar-alumnos'], 'active' => (strpos($this->context->route,  'alumno/egresar-alumnos')!==FALSE)?true:false,'visible' => Yii::$app->user->can('egresarAlumnos')],           
                        ],
                        'visible' => (Yii::$app->user->can('cargarAlumno') || Yii::$app->user->can('listarFamilias') || Yii::$app->user->can('listarAlumnos') || Yii::$app->user->can('egresarAlumnos')),
                    ],
                    [   'label' => 'Establecimientos', 
                        'active' => (strpos($this->context->route,  'establecimiento')!==FALSE)?true:false,
                        'icon' => 'university', 
                        'url' => ['/establecimiento/admin'],
                        'visible' => Yii::$app->user->can('listarEstablecimientos')
                    ],
//                    [
//                        'label' => 'Caja',
//                        'icon' => 'dollar',
//                        'url' => '#',
//                        'items' => [
//                            ['label' => 'Cobrar Servicios', 'icon' => 'arrow-right', 'url' => ['/caja/cobrar','oper'=>'1'],],
//                            ['label' => 'Cobrar Ingreso', 'icon' => 'arrow-right', 'url' => ['/caja/cobrar','oper'=>'2'],],
//                            
//                        ],
//                        //'visible' => Yii::$app->user->can('cobrarServicios')
//                    ],
//                    [
//                        'label' => 'Cuentas',
//                        'icon' => 'share',
//                        'url' => '#',
//                        'items' => [
//                            ['label' => 'Cuentas', 'icon' => 'arrow-right', 'url' => ['/cuentas/listado'],],
//                        ],
//                        'visible' => Yii::$app->user->can('visualizarCuentas')
//                    ],
//                    [   'label' => 'Fondo Fijo', 
//                        'icon' => 'handshake-o', 
//                        'url' => ['/fondo-fijo/listado'],
//                        'visible' => Yii::$app->user->can('listarFondosFijos')
//                    ],
                    [   'label' => 'Convenio Pago', 
                        'icon' => 'handshake-o', 
                        'url' => ['/convenio-pago/administrar'],
                        'visible' => Yii::$app->user->can('listarConveioPago')
                    ],
                    [   'label' => 'Débito Automático', 
                        'icon' => 'credit-card', 
                        'url' => ['/debito-automatico/administrar'],
                        'visible' => Yii::$app->user->can('listarDebitoAutomatico')
                    ],
                    [
                        'label' => 'Reportes',
                        'icon' => 'bar-chart',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Servicios Brindados', 'icon' => 'fa fa-arrow-right', 'url' => ['/servicio-alumno/reporte']],        
                            ['label' => 'Alumnos con Bonificación', 'icon' => 'fa fa-arrow-right', 'url' => ['/alumno/bonificaciones-alumno']],        
                            ['label' => 'Tiket Emitidas', 'icon' => 'fa fa-arrow-right', 'url' => ['/caja/reporte-tiket-emitidos']],         
                        ],
                        //'visible' => Yii::$app->user->can('gestionarBonificacionAlumno') 
                    ],
                    [
                        'label' => 'Servicios', 
                        'icon' => 'briefcase', 
                        'active' => (strpos($this->context->route,  'servicio-ofrecido')!==FALSE)?true:false,
                        'url' => ['/servicio-ofrecido/admin'],
                        'visible'=>Yii::$app->user->can('gestionarServicios'),  
                    ],
                    [
                        'label' => 'Configuraciones',
                        'icon' => 'cogs',
                        'url' => '',
                        'items' => [
                            ['label' => 'Tipo Documentos', 'icon' => 'arrow-right', 'url' => ['/tipo-documento/index'],'visible'=>(Yii::$app->user->can('gestionarDocumentos'))],
                            ['label' => 'Tipo Sexos', 'icon' => 'arrow-right', 'url' => ['/tipo-sexo/index'],'visible'=>(Yii::$app->user->can('gestionarSexos'))],
                            ['label' => 'Forma Pago', 'icon' => 'arrow-right', 'url' => ['/forma-pago/index'],'visible'=>(Yii::$app->user->can('gestionarFormaPago'))],
                            ['label' => 'Tipo Responsables', 'icon' => 'arrow-right', 'url' => ['/tipo-responsable/index'],'visible'=>(Yii::$app->user->can('gestionarTipoResponsable'))],
                            ['label' => 'Bonificaciones', 'icon' => 'arrow-right', 'url' => ['/bonificaciones/index'],'visible'=>(Yii::$app->user->can('gestionarCategoriaDescuentos'))],
                            //['label' => 'T.Servicios Cobro', 'icon' => 'arrow-right', 'url' => ['/categoria-servicio-ofrecido/index'],'visible'=>(Yii::$app->user->can('gestionarCategoriaServicios'))],
                        ],
                        'visible'=>(Yii::$app->user->can('gestionarDocumentos') || Yii::$app->user->can('gestionarSexos') || 
                                Yii::$app->user->can('gestionarFormaPago') || Yii::$app->user->can('gestionarTipoResponsable') 
                                || Yii::$app->user->can('gestionarCategoriaServicios') 
                                ||  Yii::$app->user->can('gestionarCategoriaServicios') 
                                ||  Yii::$app->user->can('gestionarClasificacionEgresosFondoFijo')),                         
                        
                        ],
   
                        [
                            'label' => 'Usuarios',
                            'icon' => 'users',
                            'url' => ['/user/admin/index'],
                            'visible' => Yii::$app->user->can('gestionUsuarios')
                        ],       
                    
                ],
            ]
        ) ?>

    </section>

</aside>
