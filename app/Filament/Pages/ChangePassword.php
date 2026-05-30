<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ChangePassword extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static string $view = 'filament.pages.change-password';

    protected static ?string $title = 'Change Password';

    // Hide from the main sidebar navigation
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('current_password')
                    ->label('Current Password')
                    ->password()
                    ->required()
                    ->revealable()
                    ->rule(function () {
                        return function (string $attribute, $value, \Closure $fail) {
                            if (! Hash::check($value, auth()->user()->password)) {
                                $fail('The current password you entered is incorrect.');
                            }
                        };
                    }),
                TextInput::make('new_password')
                    ->label('New Password')
                    ->password()
                    ->required()
                    ->revealable()
                    ->rule(Password::min(6))
                    ->same('new_password_confirmation'),
                TextInput::make('new_password_confirmation')
                    ->label('Confirm New Password')
                    ->password()
                    ->required()
                    ->revealable()
                    ->dehydrated(false),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('updatePassword')
                ->label('Update Password')
                ->submit('form')
                ->color('primary'),
        ];
    }

    public function updatePassword(): void
    {
        $this->validate();

        $user = auth()->user();
        $user->update([
            'password' => Hash::make($this->data['new_password']),
        ]);

        $this->form->fill();

        Notification::make()
            ->title('Password updated successfully!')
            ->success()
            ->send();
    }
}
