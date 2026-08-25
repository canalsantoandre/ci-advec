<?php
namespace App\Validation;

use App\Controllers\Empreendedor;
use App\Models\EmpreendedorModel;
use App\Models\ContatoModel;
use App\Models\UsuarioModel;

class MobileRules
{

  public function mobileValidation(string $str, string $fields, array $data)
  {
    /*Checking: Number must start from 5-9{Rest Numbers}*/


    $regex = '/^(?:(?:\+|00)?(55)\s?)?(?:\(?([1-9][0-9])\)?\s?)?(?:((?:9\d|[2-9])\d{3})\-?(\d{4}))$/';
    $phone = $data['t_telefone'];
    if (preg_match($regex, $phone) == false) {

      // O número não foi validado.
      return false;
    } else {

      // Telefone válido.
      return true;
    }


    // if(preg_match( '/^[5-9]{1}[0-9]+/', $data['t_telefone'])){

    /*Checking: Mobile number must be of 10 digits*/
    //  $bool = preg_match('/^[0-9]{10}+$/', $data['t_telefone']);
    //  return $bool == 0 ? false : true; 

    // }else{

    // return false;

    // }
  }

  public function alreadyFoneExists(string $str, string $fields, array $data)
  {

    if (isset($data['t_tipo']) && $data['t_tipo'] == 'contato'){
      $model = new ContatoModel();
    }else{
      $model = new EmpreendedorModel();
    }
    $telefone = preg_replace('/[^0-9]/', '', $data['t_telefone']);

    if (strlen($telefone) == 9) {
      $telefone = "11" . $telefone;
    }
    if (strlen($telefone) == 11) {
      $telefone = "55" . $telefone;
    }
    $data = $model->where('telefone', $telefone)
      ->first();
    return !$data;

  }
  public function alreadyEmailExists(string $str, string $fields, array $data)
  {

    if (isset($data['t_tipo']) && $data['t_tipo'] == 'contato'){
      $model = new ContatoModel();
    }else{
      $model = new EmpreendedorModel();
    }
    $data = $model->where('email', $data['t_email'])
      ->first();
    return !$data;

  }
}
