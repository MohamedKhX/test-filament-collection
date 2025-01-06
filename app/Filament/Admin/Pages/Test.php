<?php

namespace App\Filament\Admin\Pages;

use App\CollectionTable;
use App\Models\Product;
use App\Models\User;
use App\XModel;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class Test extends Page implements HasTable, HasForms
{
    use InteractsWithTable,
        InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.admin.pages.test';

    public array $data = [];

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->placeholder('Name')
                    ->required()
                    ->live(onBlur: true),

                Actions::make([
                    Action::make('submit')
                        ->label('Submit')
                        ->action(function ($get) {
                            $this->data[] = ['name' => 'Hello world'];
                        })
                ]),
            ]);
    }

    /*
     * AND see if you can add delete query method to table And Binding problem
     * Second, see how to convert it to filament package
     * Third, see if you can add collection method to table
     *
     * Forth, see if its needing a tests
     *
     * */


    public function table(CollectionTable $table): CollectionTable
    {
        return $table
            ->collection($this->data)
            ->query(Product::query())
            ->columns([
                //title
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('a')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('state')
                    ->searchable()
                    ->sortable(),
            ]);
    }
}
