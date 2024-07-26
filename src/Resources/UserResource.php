<?php

namespace Sipai\UserManager\Resources;

use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Section;


class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-s-user-circle';
    protected static ?string $navigationGroup = '用户管理';
    protected static ?string $navigationLabel = '账号管理';

    protected static ?string $modelLabel = '用户账号';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(1)
                    ->schema([
                        TextInput::make('name')->required()->label(trans('用户名')),
                        TextInput::make('password_confirmation')->label(trans('密码'))
                            ->password()
                            ->revealable()
                            ->reactive()
                            ->before(function (Get $get, Set $set, ?string $state) {
                                if (!empty($state)) {
                                    return;
                                }
                                if (empty($get('id'))) {
                                    $set('password', 'sipai123');
                                    $set('password_confirmation', 'sipai123');
                                }
                            })
                        ,
                        TextInput::make('password')->label(trans('确认密码'))
                            ->password()
                            ->hidden(fn(Get $get) => empty($get('password_confirmation')))
                            ->required()
                            ->confirmed()
                            ->revealable()
                            ->reactive(),

                        TextInput::make('email')->email()->required()->label(trans('邮箱')),
                        TextInput::make('telephone')->label(trans('手机号'))->tel()
                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
//                TextColumn::make('id')->sortable()->label(trans('ID')),
                TextColumn::make('name')->sortable()->searchable()->label(trans('用户名')),
                TextColumn::make('email')->sortable()->searchable()->label(trans('邮箱')),
                TextColumn::make('telephone')->sortable()->searchable()->label(trans('手机')),
                //Tables\Columns\IconColumn::make('email_verified_at')->boolean()->sortable()->searchable()->label(trans('邮箱验证于')),
                TextColumn::make('created_at')->label(trans('创建时间'))
                    ->dateTime('M j, Y')->sortable(),
                TextColumn::make('updated_at')->label(trans('更新时间'))
                    ->dateTime('M j, Y')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                Action::make('reset-password')
                    ->label(trans('重置密码'))
                    ->color('success')
                    ->icon('heroicon-s-arrow-path-rounded-square')
                    ->action(function (User $record) {
                        $record->update([
                            'password' => 'sipai@123'
                        ]);
                        Notification::make()
                            ->success()
                            ->title(__('密码重置成功'))
                            ->send();
                    })
                    ->requiresConfirmation('确定要重置该账户的密码吗？')
                    ->hidden(false),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => UserResource\Pages\ListUsers::route('/'),
//            'create' => UserResource\Pages\CreateUser::route('/create'),
//            'edit' => UserResource\Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

