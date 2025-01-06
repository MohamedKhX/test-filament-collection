<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

trait HasCollection
{
    public function getQuery(): Builder | Relation|null
    {
        if ($query = $this->evaluate($this->query)) {
            return $this->applyQueryScopes($query->clone());
        }

        if ($query = $this->getRelationshipQuery()) {
            return $this->applyQueryScopes($query->clone());
        }

        $livewireClass = $this->getLivewire()::class;

        throw new Exception("Table [{$livewireClass}] must have a [query()].");
    }
}
