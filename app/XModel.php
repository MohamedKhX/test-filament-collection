<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Sushi\Sushi;

class XModel extends Model
{
    use Sushi;

    public function getRows(): array
    {
        return [
         [   'title' => 'Title',
            'description' => 'Description',
            'price' => 100,
            'rating' => 5,
            'category' => 'Category',]
        ];
    }
}
