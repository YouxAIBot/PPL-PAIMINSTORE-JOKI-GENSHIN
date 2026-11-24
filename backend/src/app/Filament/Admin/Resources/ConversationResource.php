<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ConversationResource\Pages;
use App\Models\Conversation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ConversationResource extends Resource
{
    protected static ?string $model = Conversation::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Interaksi';

    protected static ?string $navigationLabel = 'Chat';

    protected static ?string $modelLabel = 'Percakapan';

    protected static ?string $pluralModelLabel = 'Percakapan';

    protected static ?int $navigationSort = 1;

    /** Percakapan dibuat otomatis oleh sistem, bukan dari panel. */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Percakapan')->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Pengguna')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\DateTimePicker::make('last_message_at')
                    ->label('Pesan terakhir'),
                Forms\Components\TextInput::make('admin_unread')
                    ->label('Belum dibaca admin')
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('user_unread')
                    ->label('Belum dibaca pengguna')
                    ->numeric()
                    ->default(0),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('messages_count')
                    ->label('Pesan')
                    ->counts('messages')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('admin_unread')
                    ->label('Belum dibaca admin')
                    ->badge()
                    ->color(fn ($state): string => (int) $state > 0 ? 'warning' : 'gray'),
                Tables\Columns\TextColumn::make('last_message_at')
                    ->label('Pesan terakhir')
                    ->since()
                    ->placeholder('Belum ada pesan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('last_message_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConversations::route('/'),
            'edit' => Pages\EditConversation::route('/{record}/edit'),
        ];
    }
}
