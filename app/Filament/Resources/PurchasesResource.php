<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchasesResource\Pages;
use App\Filament\Resources\PurchasesResource\RelationManagers;
use App\Models\Products;
use App\Models\Purchases;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
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
                    ->default('0.00')
                    // ->disabled()
                    // ->dehydrated() // Guarda el valor del campo aunque este esté deshabilitado
                    ->readOnly(),
                Forms\Components\Repeater::make('purchase_details')
                    ->label('Detalle de compra')
                    ->relationship('purchaseDetails')
                    ->schema([
                        Forms\Components\Select::make('productId')
                            ->options(Products::select('productId', 'productName', 'presentationType')
                                ->get()
                                ->mapWithKeys(function ($product) {
                                    return [
                                        $product->productId => "{$product->productName} - {$product->presentationType}"
                                    ];
                                })
                            )
                            ->required(),
                        Forms\Components\TextInput::make('quantity')
                            ->numeric()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (callable $get, callable $set, $state) {
                                static::updateSubtotalAndTotal($get, $set);
                            }),
                        Forms\Components\TextInput::make('unitPurchasePrice')
                            ->numeric()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (callable $get, callable $set, $state) {
                                static::updateSubtotalAndTotal($get, $set);
                            }),
                        Forms\Components\TextInput::make('subtotalPDetail')
                            ->label('Subtotal')
                            ->numeric()
                            ->default('0.00')
                            ->readOnly(),
                        Forms\Components\TextInput::make('recommendedSalePrice')
                            ->numeric(),
                    ])
                    ->defaultItems(1)
                    ->columns(3)
                    // ->createItemButtonLabel('Agregar producto')
                    ->addActionLabel('Agregar producto')
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        static::calculatePurchaseTotal($get, $set);
                    })
                    ->deleteAction(fn (Action $action) => $action->after(function (callable $get, callable $set) {
                        static::calculatePurchaseTotal($get, $set);
                    })),
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

    protected static function updateSubtotalAndTotal(callable $get, callable $set): void
    {
        $quantity = floatval($get('quantity') ?? 0);
        $unitPurchasePrice = floatval($get('unitPurchasePrice') ?? 0);

        $subtotal = $quantity * $unitPurchasePrice;
        $truncatedSubtotal = bcdiv($subtotal, '1', 2);

        $set('subtotalPDetail', $truncatedSubtotal);

        static::calculatePurchaseTotal($get, $set);
    }

    protected static function calculatePurchaseTotal(callable $get, callable $set): void
    {
        $purchaseDetails = collect($get('../../purchase_details') ?? $get('purchase_details') ?? []);

        $total = $purchaseDetails->sum(function ($detail) {
            return floatval($detail['subtotalPDetail'] ?? 0);
        });

        $truncatedTotal = bcdiv($total, '1', 2);

        $set('../../purchaseTotal', $truncatedTotal) ?? $set('purchaseTotal', $truncatedTotal);
    }
}
