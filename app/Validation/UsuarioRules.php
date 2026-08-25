<?php
namespace App\Validation;

use App\Models\UsuarioModel;

class UsuarioRules
{

  public function validateUsuario (string $str, string $fields, array $data){
    $model = new UsuarioModel();

    $usuario = $model->where('usuario', $data['txtUsuario'])
                  ->first();
    if(!$usuario)
      return false;
    
    return password_verify($data['txtSenhaAtual'], $usuario->senha); 
  }

  public function validateStatus (string $str, string $fields, array $data){
    $model = new UsuarioModel();

    $usuario = $model->where('usuario', $data['txtUsuario'])
                  ->first();
    if(!$usuario)
      return false;
    
    return ($usuario->status_usuario == 1); 
  }
}
