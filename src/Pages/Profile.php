<?php

namespace Sipai\UserManager\Pages;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;

class Profile extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = '用户管理';

    protected static ?string $slug = 'profile';

    protected static ?string $title = '个人信息';

    protected static string $view = 'vendor.user-resource.pages.profile';

    public $name;
    public $email;
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    public function mount()
    {
        $this->form->fill([
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
        ]);
    }

    public function submit()
    {
        $this->form->getState();

        $state = array_filter([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->new_password ? Hash::make($this->new_password) : null,
        ]);

        $user = auth()->user();

        $user->update($state);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        Notification::make()
            ->success()
            ->title(__('用户信息更新成功'))
            ->send();
    }

    public function getBreadcrumbs(): array
    {
        return [
            url()->current() => '个人信息',
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('简介')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('用户名')
                        ->required(),
                    TextInput::make('email')
                        ->label('邮箱地址')
                        ->required(),
                ]),
            Section::make('修改密码')
                ->columns(2)
                ->schema([
                    TextInput::make('current_password')
                        ->label('当前密码')
                        ->required()
                        ->password()
                        ->rules(['required_with:new_password'])
                        ->currentPassword()
                        ->autocomplete('off')
                        ->columnSpan(1),
                    Grid::make()
                        ->schema([
                            TextInput::make('new_password')
                                ->label('新密码')
                                ->required()
                                ->password()
                                ->rules(['confirmed'])
                                ->autocomplete('new-password'),
                            TextInput::make('new_password_confirmation')
                                ->label('确认新密码')
                                ->required()
                                ->password()
                                ->rules([
                                    'required_with:new_password',
                                ])
                                ->autocomplete('new-password'),
                        ]),
                ]),
        ];
    }

}
