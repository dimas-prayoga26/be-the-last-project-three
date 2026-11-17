<?php

namespace App\Filament\Resources\CommunityEvents;

use App\Enums\EventType;
use App\Filament\Resources\CommunityEvents\Pages\ManageCommunityEvents;
use App\Filament\Support\Translatable;
use App\Models\CommunityEvent;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class CommunityEventResource extends Resource
{
    protected static ?string $model = CommunityEvent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Studio';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'event';

    protected static ?string $navigationLabel = 'Events';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Translatable::tabs('title', 'Title', fn (string $statePath) => TextInput::make($statePath)->maxLength(200), required: true),
                Select::make('type')
                    ->options(EventType::class)
                    ->required()
                    ->default(EventType::Talk),
                TextInput::make('registration_url')
                    ->label('Registration link')
                    ->url()
                    ->maxLength(255),
                DateTimePicker::make('starts_at')
                    ->label('Starts')
                    ->seconds(false)
                    ->required(),
                DateTimePicker::make('ends_at')
                    ->label('Ends')
                    ->seconds(false)
                    ->after('starts_at'),
                TextInput::make('venue')
                    ->placeholder('Studio Kolektif')
                    ->maxLength(150),
                TextInput::make('city')
                    ->placeholder('Jakarta')
                    ->maxLength(100),
                Translatable::tabs('summary', 'Short intro', fn (string $statePath) => Textarea::make($statePath)->rows(2)->maxLength(300)),
                Translatable::tabs('description', 'Description', fn (string $statePath) => Textarea::make($statePath)->rows(6)->maxLength(10000)),
                SpatieMediaLibraryFileUpload::make('cover')
                    ->collection('cover')
                    ->image()
                    ->imageEditor()
                    ->maxSize(15360)
                    ->helperText('Shown on cards (cropped to 4:5) and at the top of the event page.'),
                Toggle::make('is_published')
                    ->label('Show on the website')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('cover')
                    ->collection('cover')
                    ->conversion('thumb')
                    ->imageHeight(48),
                TextColumn::make('title')
                    ->searchable()
                    ->wrap()
                    ->description(fn (CommunityEvent $record): ?string => $record->venue),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('starts_at')
                    ->label('Starts')
                    ->dateTime('j M Y, H:i')
                    ->sortable(),
                TextColumn::make('city')
                    ->searchable()
                    ->placeholder('-'),
                IconColumn::make('is_published')
                    ->label('Visible')
                    ->boolean()
                    ->falseColor('gray'),
            ])
            ->defaultSort('starts_at', 'desc')
            ->filters([
                SelectFilter::make('when')
                    ->label('When')
                    ->options([
                        'upcoming' => 'Upcoming',
                        'past' => 'Past',
                    ])
                    ->query(fn (Builder $query, array $data) => match ($data['value'] ?? null) {
                        'upcoming' => $query->upcoming(),
                        'past' => $query->past(),
                        default => $query,
                    }),
                SelectFilter::make('type')
                    ->options(EventType::class),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCommunityEvents::route('/'),
        ];
    }
}
