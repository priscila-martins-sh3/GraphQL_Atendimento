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

class ServicesByClientQuery extends Query
{
    protected $attributes = [
        'name' => 'service/ServiceNotFinished',
        'description' => 'Retorna os serviços  do dia'
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Service'));
    }

    public function args(): array
    {
        return [
            'data' => [
                'name' => 'data',
                'description' => 'Data da busca (formato: YYYY-MM-DD)',
                'type' => Type::nonNull(Type::string()),                
            ],
            'contact_id' => [
                'name' => 'contact_id',
                'description' => 'O ID do contato buscado',
                'type' => Type::nonNull(Type::int()), 
                'rules' => ['required', 'exists:contacts,id,deleted_at,NULL'],               
            ],
        ];
    }

    public function validationErrorMessages(array $args = []): array
    {
        return [
            'contact_id.exists' => 'ID do contato não encontrado',
        ];
    }
       
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, SelectFields $selectFields)
    {
        $select = $selectFields->getSelect();
        $with = $selectFields->getRelations();
            
        $query = Service::whereDate('created_at', $args['data'] )
            ->where('contact_id', $args['contact_id'])
            ->select($select)->with($with)
            ->get();

        return $query;
    }
}