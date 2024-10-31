<?php
	use App\Log;

	function nuevoLog($user, $accion, $error) {
		Log::create([
			'nombre' => $user,
			'accion' => $accion,
			'error' => $error,
			'activo' => 1,
			'eliminado' => 0
		]);
	}
?>