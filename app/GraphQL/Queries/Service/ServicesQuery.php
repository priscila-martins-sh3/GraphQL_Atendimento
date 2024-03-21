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

class ServicesQuery extends Query
{
    protected $attributes = [
        'name' => 'services',
        'description' => 'Retorna todos os serviços'
    ];

    public function type(): Type
    {        
        return Type::listOf(GraphQL::type('Service'));
    }

    public function args(): array
    {
        return [

        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, SelectFields $selectFields)
    {
        $select = $selectFields->getSelect();
        $with = $selectFields->getRelations();
    
        $services = Service::select($select)->with($with)->get();
        
        return $services;
    }
}
