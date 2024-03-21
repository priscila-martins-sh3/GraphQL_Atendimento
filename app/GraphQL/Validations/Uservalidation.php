<?php

namespace App\GraphQL\Validations;

use App\Models\Support;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\NullableType;

class UserValidation
{
    public static function make(array $data)
    {
        $id = isset($data['id']) ? $data['id'] : null;

        $rules = [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:6'],
            'tipo_funcionario' => ['required', 'in:' .  User::tiposValidos()],
            'area_atuacao' => [$data['tipo_funcionario'] === 'suporte' ? 'required' : 'nullable'],       
        ];      

        if (!is_null($id)) {
            $adaptativeRules = [];
            foreach ($rules as $property => $propertyRules) {
                foreach ($propertyRules as $rule) {
                    if ($rule !== 'required') {
                        $adaptativeRules[$property][] = $rule;
                    }
                }
            }
            $rules = $adaptativeRules;
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return $validator;
        }           

        return $validator;
    }
}

