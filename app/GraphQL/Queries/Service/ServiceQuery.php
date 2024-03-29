<?php

declare(strict_types=1);

namespace App\GraphQL\Queries\Service;

use App\Models\Service;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ServiceQuery extends Query
{
    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {        
        try {
            $this->auth = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return false;
        }         

        return (bool) $this->auth;        
    }
    
    protected $attributes = [
        'name' => 'service',
        'description' => 'Retorna um único serviço com base no ID'
    ];

    public function type(): Type
    {
        return GraphQL::type('Service');
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::int(),
                'description' => 'ID do service',
                'rules' =>
                [
                    'required',
                    'exists:services,id,deleted_at,NULL'
                ]
            ],
        ];
    }
    public function validationErrorMessages(array $args = []): array
    {
        return [
            'id.exists' => 'Service não encontrado.',
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, SelectFields $selectFields)
    {        
        $select = $selectFields->getSelect();
        $with = $selectFields->getRelations();
        
        $service = Service::with($with)->select($select)->findOrFail($args['id']);

        return $service; 
    }
}