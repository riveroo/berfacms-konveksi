<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RolePermissionResource\Pages;
use App\Models\Role;
use App\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RolePermissionResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $modelLabel = 'Role & Permission';
    protected static ?string $pluralModelLabel = 'Roles & Permissions';

    public static function canViewAny(): bool
    {
        return canAccessMenu('admin/role-permissions');
    }

    public static function getMenusConfig(): array
    {
        return [
            'Page Editor' => [
                ['name' => 'Landing Page', 'route' => 'admin/landing-page'],
                ['name' => 'Appearance', 'route' => 'admin/appearance'],
            ],
            'Catalog' => [
                ['name' => 'Products', 'route' => 'admin/products'],
                ['name' => 'Product Inventory', 'route' => 'cek-stok/product'],
            ],
            'Sales' => [
                ['name' => 'Orders', 'route' => 'admin/transactions'],
                ['name' => 'Pre Order', 'route' => 'admin/pre-orders'],
                ['name' => 'Sales Dashboard', 'route' => 'admin/transactions/report'],
                ['name' => 'Sales Report', 'route' => 'admin/sales-report'],
            ],
            'Inventory' => [
                ['name' => 'Items', 'route' => 'admin/items'],
                ['name' => 'Inventory Overview', 'route' => '/inventory/overview'],
                ['name' => 'Stock In', 'route' => '/coming-soon'],
                ['name' => 'Stock Out', 'route' => '/coming-soon'],
                ['name' => 'Adjustment', 'route' => '/coming-soon'],
            ],
            'Master Data' => [
                ['name' => 'Product Type', 'route' => 'admin/product-types'],
                ['name' => 'Size Option', 'route' => 'admin/size-options'],
                ['name' => 'Units', 'route' => 'admin/units'],
            ],
            'User Management' => [
                ['name' => 'Account', 'route' => 'admin/accounts'],
                ['name' => 'Roles & Permission', 'route' => 'admin/role-permissions'],
            ],
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->columnSpanFull(),
                Forms\Components\ViewField::make('permissions_matrix')
                    ->label('Permissions')
                    ->view('filament.forms.components.permission-matrix')
                    ->columnSpanFull()
                    ->default(function () {
                        $matrix = [];
                        foreach (self::getMenusConfig() as $group => $items) {
                            foreach ($items as $item) {
                                $matrix[] = [
                                    'group' => $group,
                                    'menu_name' => $item['name'],
                                    'route' => $item['route'],
                                    'can_access' => false,
                                ];
                            }
                        }
                        return $matrix;
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->rowIndex()->label('No'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Role Name'),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('Assigned Permissions'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Is Active'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->boolean()
                    ->trueLabel('Active')
                    ->falseLabel('Inactive'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modal()
                    ->mutateRecordDataUsing(function (\Illuminate\Database\Eloquent\Model $record, array $data): array {
                        $matrix = [];
                        foreach (self::getMenusConfig() as $group => $items) {
                            foreach ($items as $item) {
                                $perm = $record->permissions
                                    ->where('menu_name', $item['name'])
                                    ->where('route', $item['route'])
                                    ->first();
                                $matrix[] = [
                                    'group' => $group,
                                    'menu_name' => $item['name'],
                                    'route' => $item['route'],
                                    'can_access' => $perm ? $perm->can_access : false,
                                ];
                            }
                        }
                        $data['permissions_matrix'] = $matrix;
                        return $data;
                    })
                    ->using(function (\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model {
                        $matrix = $data['permissions_matrix'] ?? [];
                        unset($data['permissions_matrix']);
                        
                        $record->update($data);
                        
                        $permissionIds = [];
                        foreach ($matrix as $row) {
                            if (!empty($row['can_access'])) {
                                $perm = \App\Models\Permission::firstOrCreate([
                                    'menu_name' => $row['menu_name'],
                                    'route' => $row['route'],
                                    'can_access' => true,
                                ]);
                                $permissionIds[] = $perm->id;
                            }
                        }
                        $record->permissions()->sync($permissionIds);
                        
                        return $record;
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRolePermissions::route('/'),
        ];
    }
}
