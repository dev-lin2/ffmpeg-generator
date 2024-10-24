<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BirthDayUserResource\Pages;
use App\Filament\Resources\BirthDayUserResource\RelationManagers;
use App\Models\BirthdayUser;
use App\Models\TemplateVideo;
use App\Services\AdminVideoService;
use App\Services\LineWorkService;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class BirthDayUserResource extends Resource
{
    protected static ?string $model = BirthdayUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static ?string $label = 'お誕生日方一覧';

    public static function getPluralLabel(): ?string
    {
        return 'お誕生日方一覧';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('動画名')
                    ->required(),
                TextInput::make('email')
                    ->label('メールアドレス')
                    ->required(),
                TextInput::make('employee_id')
                    ->label('従業員番号')
                    ->required(),
                DatePicker::make('join_date')
                    ->label('入社日')
                    ->required(),
                DatePicker::make('birthday')
                    ->label('お誕生日')
                    ->required(),
                TextInput::make('video_url')
                    ->label('動画 URL')
                    ->visibleOn('view')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('氏名')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('メールアドレス')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('employee_id')
                    ->label('従業員番号')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('join_date')
                    ->label('入社日')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('birthday')
                    ->label('お誕生日')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('is_wish_sent')
                    ->label('誕生日の願い送信')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function (TextColumn $column) {
                        return $column->getState() ? 'Yes' : 'No';
                    }),
                TextColumn::make('templateVideo.name')
                    ->label('動画テンプレート')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('is_video_generated')
                    ->label('動画作成')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function (TextColumn $column) {
                        return $column->getState() ? 'Yes' : 'No';
                    }),
                TextColumn::make('video_url')
                    ->label('動画 URL')
                    ->searchable()
                    ->sortable()
                    ->url(fn($record) => $record->video_url)
                    ->openUrlInNewTab(),
            ])
            ->filters([
                SelectFilter::make('birthday')
                    ->label('Birthday Period')
                    ->options([
                        'today' => 'Today',
                        'this_week' => 'This Week',
                        'next_week' => 'Next Week',
                        'this_month' => 'This Month',
                    ])
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;

                        if ($value === null) {
                            return $query;
                        }

                        switch ($value) {
                            case 'today':
                                return $query->whereBetween('birthday', [now()->startOfDay(), now()->endOfDay()]);
                            case 'this_week':
                                return $query->whereBetween('birthday', [now()->startOfWeek(), now()->endOfWeek()]);
                            case 'next_week':
                                return $query->whereBetween('birthday', [now()->startOfWeek()->addWeek(), now()->endOfWeek()->addWeek()]);
                            case 'this_month':
                                return $query->whereBetween('birthday', [now()->startOfMonth(), now()->endOfMonth()]);
                            default:
                                return $query;
                        }
                    })
            ])
            ->recordAction('view')
            ->recordUrl(null)
            ->actions([
                Tables\Actions\ViewAction::make()->label('表示'),
                Tables\Actions\EditAction::make()->label('編集'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('generateVideo')
                        ->label('Generate Video')
                        ->modalHeading('Generate Video')
                        ->modalContent(fn() => new HtmlString('<p class="text-gray-500">Select a video template to generate the video</p>'))
                        ->icon('heroicon-o-video-camera')
                        ->requiresConfirmation(false)
                        ->form([
                            Select::make('template_id')
                                ->label('Video Template')
                                ->options(TemplateVideo::pluck('name', 'id'))
                                ->required()
                                ->searchable()
                        ])
                        ->action(function (Collection $records, array $data) {
                            $adminVideoService = app(AdminVideoService::class);

                            $userIds = $records->pluck('id')->toArray();
                            $templateId = $data['template_id'];

                            $adminVideoService->generateVideo([
                                'user_ids' => $userIds,
                                'template_id' => $templateId,
                            ]);

                            Notification::make()
                                ->title('Videos generation queued')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('sendWish')
                        ->label('Send Wish')
                        ->modalHeading('Send Wish')
                        ->modalContent(fn() => new HtmlString('<p class="text-gray-500">Send a wish to the selected users</p>'))
                        ->icon('heroicon-o-paper-airplane')
                        ->requiresConfirmation(false)
                        ->action(function (Collection $records) {
                            $adminService = app(AdminVideoService::class);
                            $adminService->sendVideo($records);

                            Notification::make()
                                ->title('Wishes sent')
                                ->success()
                                ->send();
                        })
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
            'index' => Pages\ListBirthDayUsers::route('/'),
            'create' => Pages\CreateBirthDayUser::route('/create'),
            'edit' => Pages\EditBirthDayUser::route('/{record}/edit'),
        ];
    }
}
