<?php

namespace App;

use App\Models\Product;
use Closure;
use Exception;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

class CollectionTable extends Table
{
    protected Collection $collection;

    public function collection(Collection|array $collection): static
    {
        if(is_array($collection)) {
            $collection = collect($collection);
        }

        $this->collection = $collection;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function query(Builder | Closure | null $query): static
    {
        // Convert the collection to an array
        $collectionToArray = $this->collection->toArray();

        // Check if the collection is an array
        if (!is_array($collectionToArray)) {
            throw new \Exception("Expected collection to be an array, got " . gettype($collectionToArray));
        }

        // Check if each item in the collection is an array (or can be converted to an array)
        foreach ($collectionToArray as $item) {
            if (!is_array($item) && !is_object($item)) {
                throw new \Exception("Each item in the collection must be an array or object, got " . gettype($item));
            }
        }

        // Start building the dynamic array
        $rowsArray = [];

        // Iterate over the collection and build the array dynamically
        foreach ($collectionToArray as $item) {
            // Convert the item to an array if it's an object
            $itemArray = (is_object($item)) ? (array) $item : $item;

            // Initialize a row array for the current item
            $row = [];

            // Iterate through each key-value pair of the item
            foreach ($itemArray as $key => $value) {
                // If the value is an array, serialize it to JSON
                if (is_array($value)) {
                    $value = json_encode($value); // Serialize nested array to JSON
                } elseif (is_object($value)) {
                    $value = json_encode((array) $value); // Serialize object to JSON
                }

                // Add to the row array dynamically
                $row[$key] = $value;
            }

            // Add the row to the rows array
            $rowsArray[] = $row;
        }

        // Define the class name dynamically
        $className = 'YModel';

        // Create the class dynamically with the built array data
        $classCode = <<<EOF
        use Illuminate\Database\Eloquent\Model;
        use Sushi\Sushi;

        class {$className} extends Model
        {
            use Sushi;

            public function getRows(): array
            {
                // Return the dynamically built rows array using var_export for safe array syntax
                return {$this->buildArrayString($rowsArray)};
            }
        }
        EOF;

        // Dynamically define the class
        eval($classCode);

        // Ensure the class is available and query can be executed
        if (!class_exists($className)) {
            throw new \Exception("Class {$className} does not exist");
        }

        // Query the model
        $this->query = $className::query();

        return $this;
    }

    private function buildArrayString(array $array): string
    {
        // Use var_export to generate a valid PHP array string, ensuring correct formatting
        return var_export($array, true);
    }

    private function escapeValue($value): string
    {
        if (is_string($value)) {
            // Return the string without wrapping it in extra quotes
            return $value;
        }

        if (is_null($value)) {
            return 'null';
        }

        return $value;
    }


}
