<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesResource\Pages;
use App\Filament\Resources\SalesResource\RelationManagers;
use App\Models\Products;
use App\Models\Sales;
use App\Models\Inventory;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SalesResource extends Resource
{
    protected static ?string $model = Sales::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('saleTotal')
                    ->numeric()
                    ->default('0.00')
                    ->readOnly(),
                Forms\Components\Repeater::make('sale_details')
                    ->relationship('saleDetails')
                    ->label('Detalle de venta')
                    ->schema([
                        Forms\Components\Select::make('productId')
                            ->options(function () {
                                return Products::join('inventory', 'products.productId', '=', 'inventory.productId')
                                    ->where('inventory.currentStock', '>', 0)
                                    ->select('products.productId', 'products.productName', 'products.presentationType')
                                    ->get()
                                    ->mapWithKeys(function ($product) {
                                        return [
                                            $product->productId => "{$product->productName} - {$product->presentationType}"
                                        ];
                                    });
                            })
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (callable $set, $state) {
                                if ($state) {
                                    $inventory = Inventory::where('productId', $state)->first();
                                    if ($inventory) {
                                        $set('availableStock', $inventory->currentStock);
                                    }
                                    $product = Products::where('productId', $state)->first();
                                    if ($product) {
                                        $set('unitSalePrice', $product->priceByFormat);
                                    }
                                }
                            }),
                        Forms\Components\TextInput::make('availableStock')
                            ->readOnly(),
                        Forms\Components\TextInput::make('quantity')
                            ->integer()
                            ->required()
                            ->live()
                            ->minValue(1)
                            ->maxValue(function (callable $get) {
                                return $get('availableStock') ?? 999999;
                            })
                            ->extraAttributes([
                                'inputmode' => 'numeric',
                                'onkeydown' => "if (!['Backspace','Tab','ArrowLeft','ArrowRight','Delete'].includes(event.key) && !/^[0-9]$/.test(event.key)) event.preventDefault();",
                            ])
                            ->rule(function (callable $get) {
                                return function (string $attribute, $value, \Closure $fail) use ($get) {
                                    $availableStock = $get('availableStock');
                                    if (!preg_match('/^\d+$/', (string) $value)) {
                                        $fail("La cantidad debe ser un número entero. {$value}");
                                    }
                                    if ((int) $value >= $availableStock) {
                                        $fail("La cantidad no puede ser mayor al stock disponible ({$availableStock}).");
                                    }
                                };
                            })
                            ->afterStateUpdated(function (callable $get, callable $set, $state) {
                                static::updateSubtotalAndTotal($get, $set);
                            }),
                        Forms\Components\TextInput::make('unitSalePrice')
                            ->numeric()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (callable $get, callable $set, $state) {
                                static::updateSubtotalAndTotal($get, $set);
                            }),
                        Forms\Components\TextInput::make('subtotalSDetail')
                            ->label('Subtotal')
                            ->numeric()
                            ->default('0.00')
                            ->readOnly(),
                    ])
                    ->defaultItems(1)
                    ->columns(3)
                    ->addActionLabel('Agregar producto')
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        static::calculateSaleTotal($get, $set);
                    })
                    ->deleteAction(fn (Action $action) => $action->after(function (callable $get, callable $set) {
                        static::calculateSaleTotal($get, $set);
                    })),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('saleTotal')
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
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSales::route('/create'),
            'edit' => Pages\EditSales::route('/{record}/edit'),
        ];
    }

    protected static function updateSubtotalAndTotal(callable $get, callable $set): void
    {
        $quantity = floatval($get('quantity') ?? 0);
        $unitSalePrice = floatval($get('unitSalePrice') ?? 0);

        $subtotal = $quantity * $unitSalePrice;
        $truncatedSubtotal = bcdiv($subtotal, '1', 2);

        $set('subtotalSDetail', $truncatedSubtotal);

        static::calculateSaleTotal($get, $set);
    }

    protected static function calculateSaleTotal(callable $get, callable $set): void
    {
        $saleDetails = collect($get('../../sale_details') ?? $get('sale_details') ?? []);

        $total = $saleDetails->sum(function ($detail) {
            return floatval($detail['subtotalSDetail'] ?? 0);
        });

        $truncatedTotal = bcdiv($total, '1', 2);

        $set('../../saleTotal', $truncatedTotal) ?? $set('saleTotal', $truncatedTotal);
    }
}
