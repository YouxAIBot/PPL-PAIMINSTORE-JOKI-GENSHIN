<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ActivityLogResource\Pages;
use App\Models\ActivityLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Audit API (tabel activity_logs dari ERD).
 * Berbeda dari menu "Activity Logs" milik panel (activity_log spatie).
 * Hanya baca: log tidak boleh diubah dari panel.
 */
class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static ?string $slug = 'audit-logs';

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Sistem';

    protected static ?string $navigationLabel = 'Audit API';

    protected static ?string $modelLabel = 'Audit API';

    protected static ?string $pluralModelLabel = 'Audit API';

    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Audit')->schema([
                Forms\Components\Placeholder::make('actor')
                    ->label('Pelaku')
                    ->content(fn (?ActivityLog $record): string => $record?->actor?->name ?? 'Sistem'),
                Forms\Components\TextInput::make('action')->label('Aksi')->disabled(),
                Forms\Components\TextInput::make('entity_type')->label('Tipe entitas')->disabled(),
                Forms\Components\TextInput::make('entity_id')->label('ID entitas')->disabled(),
                Forms\Components\TextInput::make('ip')->label('IP')->disabled(),
                Forms\Components\Textarea::make('meta')
                    ->label('Metadata')
                    ->rows(6)
                    ->formatStateUsing(fn ($state): string => is_array($state)
                        ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                        : (string) $state)
                    ->disabled()
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('action')
                    ->label('Aksi')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                Tables\Columns\TextColumn::make('actor.name')
                    ->label('Pelaku')
                    ->placeholder('Sistem')
                    ->searchable(),
                Tables\Columns\TextColumn::make('entity_type')
                    ->label('Entitas')
                    ->formatStateUsing(fn ($state, ActivityLog $record): string => $state . ' #' . $record->entity_id),
                Tables\Columns\TextColumn::make('ip')
                    ->label('IP')
                    ->placeholder('-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('meta')
                    ->label('Metadata')
                    ->formatStateUsing(fn ($state): string => is_array($state)
                        ? json_encode($state, JSON_UNESCAPED_UNICODE)
                        : (string) $state)
                    ->limit(60)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->label('Aksi')
                    ->options(fn (): array => ActivityLog::query()
                        ->distinct()
                        ->orderBy('action')
                        ->pluck('action', 'action')
                        ->all()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Lihat'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
            'edit' => Pages\EditActivityLog::route('/{record}/edit'),
        ];
    }
}
