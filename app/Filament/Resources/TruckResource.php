<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TruckResource\Pages;
use App\Filament\Resources\TruckResource\RelationManagers;
use App\Models\Truck;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TruckResource extends Resource
{
    protected static ?string $model = Truck::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nopol')
                    ->label('Nomor Polisi')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('merek')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('model')
                    ->options([
                        'CDE' => 'CDE (Colt Diesel Engkel)',
                        'CDD' => 'CDD (Colt Diesel Double)',
                        'Tronton' => 'Tronton',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('tahun_pembuatan')
                    ->label('Tahun')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'operasional' => 'Operasional',
                        'perbaikan' => 'Dalam Perbaikan',
                        'tidak aktif' => 'Tidak Aktif',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('kir_tanggal_kadaluarsa')
                    ->label('Tanggal Kadaluarsa KIR'),
                Forms\Components\DatePicker::make('pajak_tanggal_kadaluarsa')
                    ->label('Tanggal Kadaluarsa Pajak'),
                Forms\Components\Select::make('driver_id')
                   ->label('Driver Utama')
                ->relationship(
                    name: 'driver',
                    titleAttribute: 'nama',
                    modifyQueryUsing: function (Builder $query, ?Truck $record) {
                        // Jika sedang edit, driver yg sekarang harus tetap muncul
                        $assignedDriverId = $record?->driver_id;

                        // Tampilkan driver yang belum punya truk ATAU
                        // driver yang saat ini ditugaskan ke truk ini
                        return $query->whereDoesntHave('truck')
                                     ->orWhere('id', $assignedDriverId);
                    }
                )
                ->searchable()
                ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nopol')
                    ->label('Nomor Polisi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('merek')
                    ->searchable(),
                Tables\Columns\TextColumn::make('driver.nama')
                    ->label('Driver')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Belum ada driver'),
                Tables\Columns\TextColumn::make('model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tahun_pembuatan')
                    ->label('Tahun')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'operasional' => 'success',
                        'perbaikan' => 'warning',
                        'tidak aktif' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('kir_tanggal_kadaluarsa'),
                Tables\Columns\TextColumn::make('pajak_tanggal_kadaluarsa'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

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
            'index' => Pages\ListTrucks::route('/'),
            'create' => Pages\CreateTruck::route('/create'),
            'edit' => Pages\EditTruck::route('/{record}/edit'),
        ];
    }
}
