<?php
	//Paginas
	$router->get('pagina/traerPaginas', ['uses' => 'PaginaController@traerPaginas']);
	$router->post('pagina/nuevaPagina', ['uses' => 'PaginaController@nuevaPagina']);
	$router->post('pagina/modificarPagina', ['uses' => 'PaginaController@modificarPagina']);

	//Menus
	$router->get('pagina/traerMenus', ['uses' => 'PaginaController@traerMenus']);
	$router->post('pagina/nuevoMenu', ['uses' => 'PaginaController@nuevoMenu']);
	$router->post('pagina/modificarMenu', ['uses' => 'PaginaController@modificarMenu']);
	$router->post('pagina/activarMenu', ['uses' => 'PaginaController@activarMenu']);
	$router->post('pagina/desactivarMenu', ['uses' => 'PaginaController@desactivarMenu']);

	//Submenus
	$router->post('pagina/traerSubmenus', ['uses' => 'PaginaController@traerSubmenus']);
	$router->post('pagina/nuevoSubmenu', ['uses' => 'PaginaController@nuevoSubmenu']);
	$router->post('pagina/modificarSubmenu', ['uses' => 'PaginaController@modificarSubmenu']);
	$router->post('pagina/activarSubmenu', ['uses' => 'PaginaController@activarSubmenu']);
	$router->post('pagina/desactivarSubmenu', ['uses' => 'PaginaController@desactivarSubmenu']);

	//Componentes
	$router->get('pagina/traerComponentes', ['uses' => 'PaginaController@traerComponentes']);
	$router->post('pagina/nuevoComponente', ['uses' => 'PaginaController@nuevoComponente']);
	$router->post('pagina/modificarComponente', ['uses' => 'PaginaController@modificarComponente']);

	//Configuraciones de Pagina
	$router->post('pagina/nuevaConfiguracionPagina', ['uses' => 'PaginaController@nuevaConfiguracionPagina']);
	$router->post('pagina/eliminarConfiguracionPagina', ['uses' => 'PaginaController@eliminarConfiguracionPagina']);
	$router->post('pagina/traerConfiguracionPagina', ['uses' => 'PaginaController@traerConfiguracionPagina']);
	$router->post('pagina/guardarConfiguracion', ['uses' => 'PaginaController@guardarConfiguracion']);

	//Configuraciones de Banners
	$router->post('pagina/nuevoBanner', ['uses' => 'PaginaController@nuevoBanner']);
	$router->post('pagina/traerBanners', ['uses' => 'PaginaController@traerBanners']);
	$router->post('pagina/actualizarPosicionesBanner', ['uses' => 'PaginaController@actualizarPosicionesBanner']);
	$router->post('pagina/eliminarBanner', ['uses' => 'PaginaController@eliminarBanner']);

	//Configuraciones de Titulo
	$router->post('pagina/traerTitulo', ['uses' => 'PaginaController@traerTitulo']);
	$router->post('pagina/guardarTitulo', ['uses' => 'PaginaController@guardarTitulo']);

	//Configuraciones de Subtitulo
	$router->post('pagina/traerSubtitulo', ['uses' => 'PaginaController@traerSubtitulo']);
	$router->post('pagina/guardarSubtitulo', ['uses' => 'PaginaController@guardarSubtitulo']);

	//Configuraciones de Video
	$router->post('pagina/traerVideo', ['uses' => 'PaginaController@traerVideo']);
	$router->post('pagina/guardarVideo', ['uses' => 'PaginaController@guardarVideo']);

	//Configuracion de Curso
	$router->post('pagina/traerConfiguracionCurso', ['uses' => 'PaginaController@traerConfiguracionCurso']);
	$router->post('pagina/guardarConfiguracionCurso', ['uses' => 'PaginaController@guardarConfiguracionCurso']);

	//Beneficios de Curso
	$router->post('pagina/guardarBeneficioCurso', ['uses' => 'PaginaController@guardarBeneficioCurso']);
	$router->post('pagina/traerBeneficiosCurso', ['uses' => 'PaginaController@traerBeneficiosCurso']);
	$router->post('pagina/eliminarBeneficioCurso', ['uses' => 'PaginaController@eliminarBeneficioCurso']);

	//Extras de Curso
	$router->post('pagina/guardarExtraCurso', ['uses' => 'PaginaController@guardarExtraCurso']);
	$router->post('pagina/traerExtrasCurso', ['uses' => 'PaginaController@traerExtrasCurso']);
	$router->post('pagina/eliminarExtraCurso', ['uses' => 'PaginaController@eliminarExtraCurso']);

	//Configuraciones de Altas de Cursos
	$router->post('pagina/traerCursos', ['uses' => 'PaginaController@traerCursos']);
	$router->post('pagina/guardarCursos', ['uses' => 'PaginaController@guardarCursos']);

	//Configuraciones de Parrafo
	$router->post('pagina/traerParrafo', ['uses' => 'PaginaController@traerParrafo']);
	$router->post('pagina/guardarParrafo', ['uses' => 'PaginaController@guardarParrafo']);

	//Configuraciones de Listado
	$router->post('pagina/guardarDatoLista', ['uses' => 'PaginaController@guardarDatoLista']);
	$router->post('pagina/eliminarDatoLista', ['uses' => 'PaginaController@eliminarDatoLista']);
	$router->post('pagina/traerDatosLista', ['uses' => 'PaginaController@traerDatosLista']);

	//Configuracion de Blog
	$router->post('pagina/guardarNoticias', ['uses' => 'PaginaController@guardarNoticias']);
	$router->post('pagina/traerNoticias', ['uses' => 'PaginaController@traerNoticias']);
	$router->post('pagina/buscarNoticias', ['uses' => 'PaginaController@buscarNoticias']);

	//Configuracion de Vigencia
	$router->post('pagina/guardarVigencia', ['uses' => 'PaginaController@guardarVigencia']);	
	$router->get('pagina/traerVigencia', ['uses' => 'PaginaController@traerVigencia']);	

	//Configuracion de Testimonios
	$router->post('pagina/traerTestimonios', ['uses' => 'PaginaController@traerTestimonios']);	
	$router->post('pagina/guardarTestimonio', ['uses' => 'PaginaController@guardarTestimonio']);	
	$router->post('pagina/eliminarTestimonio', ['uses' => 'PaginaController@eliminarTestimonio']);	
?>