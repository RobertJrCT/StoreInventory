<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchasesResource\Pages;
use App\Filament\Resources\PurchasesResource\RelationManagers;
use App\Models\Purchases;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PurchasesResource extends Resource
{
    protected static ?string $model = Purchases::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('purchaseTotal')
                    ->numeric()
                    // ->disabled()
                    // ->dehydrated() // Guarda el valor del campo aunque este esté deshabilitado
                    ->readOnly()
                    ->reactive()
                    ->afterStateHydrated(function (callable $get, callable $set) {
                        // Calcular el total al cargar el formulario (por si ya hay datos)
                        $set('purchaseTotal', collect($get('purchase-details'))
                            ->sum(fn ($item) => $item['subtotalPDetail'] ?? 0));
                    }),
                Forms\Components\Repeater::make('purchase_details')
                    ->label('Detalle de compra')
                    ->relationship('purchaseDetails')
                    ->schema([
                        Forms\Components\Select::make('productId')
                            ->relationship('product', 'productName')
                            ->required(),
                        Forms\Components\Select::make('countType')
                            ->options([
                                'unit' => 'Unidades',
                                'package' => 'Paquetes',
                            ])
                            ->required()
                            ->live(),
                        Forms\Components\TextInput::make('unitsPerPackage')
                            ->numeric()
                            ->visible(fn ($get) => $get('countType') === 'package'),
                        Forms\Components\TextInput::make('quantity')
                            ->numeric()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $get, callable $set) {
                                $set('subtotalPDetail', $get('quantity') * $get('unitPurchasePrice'));
                            }),
                        Forms\Components\TextInput::make('unitPurchasePrice')
                            ->numeric()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $get, callable $set) {
                                $set('subtotalPDetail', $get('quantity') * $get('unitPurchasePrice'));
                            }),
                        Forms\Components\TextInput::make('subtotalPDetail')
                            ->label('Subtotal')
                            ->numeric()
                            ->readOnly(),
                        Forms\Components\TextInput::make('recommendedSalePrice')
                            ->numeric()
                            ->required(),
                    ])
                    ->defaultItems(1)
                    // ->createItemButtonLabel('Agregar producto')
                    ->addActionLabel('Agregar producto')
                    ->columns(4)
                    ->reactive()
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        // Esto ayuda a recalcular el total si se cambia algo dentro del repeater
                        $set('purchaseTotal', collect($get('purchase-details'))
                            ->sum(fn ($item) => $item['subtotalPDetail'] ?? 0));
                    })
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('purchaseTotal')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchases::route('/'),
            'create' => Pages\CreatePurchases::route('/create'),
            'edit' => Pages\EditPurchases::route('/{record}/edit'),
        ];
    }
}
