<?php

namespace App\Filament\Resources;

use Filament\Actions\Exports\Models\Export;
use App\Filament\Resources\MaintenanceHistoryResource\Pages;
use App\Models\MaintenanceHistory;
use App\Models\MaintenanceSchedule;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\SparePart;
use Filament\Tables\Filters\Filter; 
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action;
use App\Enums\MaintenanceStatus;
use App\Models\Truck;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;


class MaintenanceHistoryResource extends Resource
{
    protected static ?string $model = MaintenanceHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Detail Servis')
                        ->schema([
                            Forms\Components\Select::make('truck_id')
                                ->relationship('truck', 'nopol')
                                ->searchable()
                                ->required()
                                ->label('Truk')
                                ->reactive()
                                ->afterStateUpdated(function ($state, Set $set) {
                                // Cari truk berdasarkan ID yang dipilih
                                $truck = Truck::find($state);
                                // Jika truk ditemukan, isi field 'truck_model'
                                if ($truck) {
                                    $set('truck_model', $truck->model);
                                }
                                }),
                            Forms\Components\DatePicker::make('tanggal_servis')
                                ->required()
                                ->default(now())
                                ->reactive() // Membuat field ini reaktif
                                ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                    if (blank($get('maintenance_schedule_id')) || blank($state)) return;
                                    self::calculateNextServiceDate($get, $set);
                                }),
                            Forms\Components\Select::make('maintenance_schedule_id')
                                ->relationship('maintenanceSchedule', 'nama_servis')
                                ->required()
                                ->label('Jenis Servis')
                                ->reactive() // Membuat field ini reaktif
                                ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                    if (blank($get('tanggal_servis')) || blank($state)) return;
                                    self::calculateNextServiceDate($get, $set);
                                }),
                            Forms\Components\DatePicker::make('tanggal_servis_berikutnya')
                                ->readonly()
                                ->required(),
                            Forms\Components\TextInput::make('truck_model')
                                ->label('Model Truk')
                                ->disabled() // Buat agar tidak bisa diedit
                                ->dehydrated(false),
                            Forms\Components\Textarea::make('catatan')
                                ->columnSpanFull(),
                            Forms\Components\Select::make('status')
                                ->options(MaintenanceStatus::class)
                                ->required()
                                ->default(MaintenanceStatus::Scheduled)
                                ->columnSpanFull(),
                        ])->columns(2),

                    Forms\Components\Wizard\Step::make('Spare Part Digunakan')
                        ->schema([
                            Forms\Components\Repeater::make('spareParts')
                                ->schema([
                                    Forms\Components\Select::make('spare_part_id')
                                        ->label('Spare Part')
                                        ->options(SparePart::query()->pluck('nama', 'id'))
                                        ->searchable()
                                        ->required(),
                                    Forms\Components\TextInput::make('jumlah')
                                        ->numeric()
                                        ->required()
                                        ->default(1),
                                ])->columns(2),
                        ]),
                ])->columnSpanFull(),
            ]);
    }

public static function table(Table $table): Table
{
    return $table
        ->headerActions([
            ExportAction::make()
                ->label('Export ke Excel')
                ->exports([
                    ExcelExport::make('table')->fromTable(),
                ])
                ->color('success'),
            ])
        ->columns([
            Tables\Columns\TextColumn::make('truck.nopol')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('maintenanceSchedule.nama_servis')->label('Jenis Servis'),
            Tables\Columns\TextColumn::make('tanggal_servis')->date()->sortable(),
            Tables\Columns\TextColumn::make('status')->badge(),
            Tables\Columns\TextColumn::make('total_biaya_spare_part')
                ->label('Total Biaya Spare Part')
                ->money('IDR')
                ->sortable(),
            Tables\Columns\TextColumn::make('tanggal_servis_berikutnya')->date()->sortable(),
        ])
        ->filters([
            // Filter archive
            Tables\Filters\TernaryFilter::make('status')
                ->label('archive')
                ->placeholder('All Status') // Teks untuk tombol "All"
                ->trueLabel('Completed') // Teks untuk tombol "Archived"
                ->falseLabel('Active') // Teks untuk tombol "Not Archived"
                ->queries(
                    // Logika saat tombol "Archived" ditekan
                    true: fn (Builder $query) => $query->where('status', 'Completed'),
                    // Logika saat tombol "Not Archived" ditekan
                    false: fn (Builder $query) => $query->where('status', '!=', 'Completed'),
                )
                // Secara default, tampilkan yang "Not Archived"
                ->default(false),

            // Filter tanggal
            Filter::make('tanggal_servis')
                ->form([
                    DatePicker::make('servis_dari')->label('Servis dari tanggal'),
                    DatePicker::make('servis_sampai')->label('Servis sampai tanggal'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['servis_dari'],
                            fn (Builder $query, $date): Builder => $query->whereDate('tanggal_servis', '>=', $date),
                        )
                        ->when(
                            $data['servis_sampai'],
                            fn (Builder $query, $date): Builder => $query->whereDate('tanggal_servis', '<=', $date),
                        );
                })
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
            'index' => Pages\ListMaintenanceHistories::route('/'),
            'create' => Pages\CreateMaintenanceHistory::route('/create'),
            'edit' => Pages\EditMaintenanceHistory::route('/{record}/edit'),
        ];
    }

    // Fungsi helper untuk kalkulasi
    public static function calculateNextServiceDate(Get $get, Set $set): void
    {
        $schedule = MaintenanceSchedule::find($get('maintenance_schedule_id'));
        if (!$schedule) return;
        
        $nextDate = Carbon::parse($get('tanggal_servis'))->addDays($schedule->interval_hari);
        $set('tanggal_servis_berikutnya', $nextDate->format('Y-m-d'));
    }
}