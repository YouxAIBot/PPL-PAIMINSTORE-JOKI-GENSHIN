<?php

namespace App\Filament\Admin\Resources;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Filament\Admin\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?string $navigationLabel = 'Pembayaran';

    protected static ?string $modelLabel = 'Pembayaran';

    protected static ?string $pluralModelLabel = 'Pembayaran';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'gateway_ref';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Pembayaran')->schema([
                Forms\Components\Select::make('order_id')
                    ->label('Order')
                    ->relationship('order', 'order_no')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('method')
                    ->label('Metode')
                    ->options(static::methodOptions())
                    ->required()
                    ->default(PaymentMethod::Gateway->value),
                Forms\Components\TextInput::make('gateway_ref')
                    ->label('Referensi gateway')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Untuk transfer manual, isi referensi buatan sendiri.'),
                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah')
                    ->numeric()
                    ->required()
                    ->prefix('Rp'),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options(static::statusOptions())
                    ->required()
                    ->default(PaymentStatus::Pending->value),
                Forms\Components\TextInput::make('refunded_amount')
                    ->label('Jumlah refund')
                    ->numeric()
                    ->default(0)
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('proof_url')
                    ->label('Bukti transfer (URL)')
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Select::make('verified_by')
                    ->label('Diverifikasi oleh')
                    ->relationship('verifier', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Belum diverifikasi'),
                Forms\Components\DateTimePicker::make('paid_at')
                    ->label('Dibayar pada'),
                Forms\Components\DateTimePicker::make('refunded_at')
                    ->label('Direfund pada'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.order_no')
                    ->label('Nomor order')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('method')
                    ->label('Metode')
                    ->badge()
                    ->formatStateUsing(fn (PaymentMethod $state): string => $state->label())
                    ->color(fn (PaymentMethod $state): string => $state === PaymentMethod::Gateway ? 'info' : 'gray'),
                Tables\Columns\TextColumn::make('gateway_ref')
                    ->label('Referensi')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR', divideBy: 1)
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (PaymentStatus $state): string => $state->label())
                    ->color(fn (PaymentStatus $state): string => static::statusColor($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Dibayar')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('verifier.name')
                    ->label('Verifikator')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(static::statusOptions()),
                Tables\Filters\SelectFilter::make('method')
                    ->label('Metode')
                    ->options(static::methodOptions()),
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
        return collect(PaymentStatus::cases())
            ->mapWithKeys(fn (PaymentStatus $status): array => [$status->value => $status->label()])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public static function methodOptions(): array
    {
        return collect(PaymentMethod::cases())
            ->mapWithKeys(fn (PaymentMethod $method): array => [$method->value => $method->label()])
            ->all();
    }

    public static function statusColor(PaymentStatus $status): string
    {
        return match ($status) {
            PaymentStatus::Pending => 'warning',
            PaymentStatus::Paid => 'success',
            PaymentStatus::Failed => 'danger',
            PaymentStatus::Refunded => 'gray',
        };
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
