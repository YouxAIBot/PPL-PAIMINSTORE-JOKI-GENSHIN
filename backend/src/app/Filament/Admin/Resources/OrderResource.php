<?php

namespace App\Filament\Admin\Resources;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Filament\Admin\Resources\OrderResource\Pages;
use App\Models\JokiPackage;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?string $navigationLabel = 'Order';

    protected static ?string $modelLabel = 'Order';

    protected static ?string $pluralModelLabel = 'Order';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'order_no';

    /** Badge jumlah order yang menunggu pembayaran. */
    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::query()
            ->where('status', OrderStatus::PendingPayment)
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Order')->schema([
                Forms\Components\TextInput::make('order_no')
                    ->label('Nomor order')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->default(fn (): string => 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4)))
                    ->disabled(fn (string $operation): bool => $operation === 'edit'),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options(static::statusOptions())
                    ->required()
                    ->default(OrderStatus::PendingPayment->value),
                Forms\Components\Select::make('user_id')
                    ->label('Pengguna')
                    ->relationship('user', 'name', fn ($query) => $query->where('role', UserRole::Customer->value))
                    ->searchable(['name', 'email'])
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('booster_id')
                    ->label('Booster')
                    ->relationship('booster', 'name', fn ($query) => $query->where('role', UserRole::Booster->value))
                    ->searchable()
                    ->preload()
                    ->placeholder('Belum ditugaskan'),
                Forms\Components\Select::make('package_id')
                    ->label('Paket')
                    ->relationship('package', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set): void {
                        $package = JokiPackage::query()->find($state);

                        if ($package) {
                            $set('price_snapshot', $package->price);
                            $set('total_amount', $package->price);
                            $set('est_hours_snapshot', $package->estimated_hours);
                        }
                    }),
                Forms\Components\TextInput::make('price_snapshot')
                    ->label('Harga saat pesan')
                    ->numeric()
                    ->required()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('total_amount')
                    ->label('Total tagihan')
                    ->numeric()
                    ->required()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('est_hours_snapshot')
                    ->label('Estimasi jam saat pesan')
                    ->numeric()
                    ->required()
                    ->suffix('jam'),
                Forms\Components\TextInput::make('queue_priority')
                    ->label('Prioritas antrian')
                    ->numeric()
                    ->default(0)
                    ->helperText('Angka lebih besar didahulukan.'),
                Forms\Components\Textarea::make('customer_notes')
                    ->label('Catatan pengguna')
                    ->rows(2)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('cancel_reason')
                    ->label('Alasan batal')
                    ->rows(2)
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Jejak waktu')->schema([
                Forms\Components\DateTimePicker::make('paid_at')
                    ->label('Dibayar pada'),
                Forms\Components\DateTimePicker::make('started_at')
                    ->label('Mulai dikerjakan'),
                Forms\Components\DateTimePicker::make('completed_at')
                    ->label('Selesai'),
            ])->columns(3)->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_no')
                    ->label('Nomor')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('package.name')
                    ->label('Paket')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('booster.name')
                    ->label('Booster')
                    ->placeholder('Belum ada')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (OrderStatus $state): string => $state->label())
                    ->color(fn (OrderStatus $state): string => static::statusColor($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR', divideBy: 1)
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Dibayar')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(static::statusOptions()),
                Tables\Filters\SelectFilter::make('package_id')
                    ->label('Paket')
                    ->relationship('package', 'name')
                    ->searchable(),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('dari')->label('Dari tanggal'),
                        Forms\Components\DatePicker::make('sampai')->label('Sampai tanggal'),
                    ])
                    ->query(fn ($query, array $data) => $query
                        ->when($data['dari'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                        ->when($data['sampai'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date))),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    /**
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return collect(OrderStatus::cases())
            ->mapWithKeys(fn (OrderStatus $status): array => [$status->value => $status->label()])
            ->all();
    }

    public static function statusColor(OrderStatus $status): string
    {
        return match ($status) {
            OrderStatus::PendingPayment => 'warning',
            OrderStatus::Queued => 'info',
            OrderStatus::InProgress => 'primary',
            OrderStatus::Completed => 'success',
            OrderStatus::Cancelled => 'danger',
        };
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
