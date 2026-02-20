<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Auth\Infrastructure\Model\UserModel;
use App\Profile\Infrastructure\Model\ProfileModel;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    /**
     * Tworzy profile dla wszystkich użytkowników, którzy ich jeszcze nie mają.
     */
    public function run(): void
    {
        $users = UserModel::query()
            ->whereDoesntHave('profile')
            ->get();

        foreach ($users as $user) {
            ProfileModel::query()->create([
                'user_id' => $user->id,
                'first_name' => null,
                'last_name' => null,
                'phone' => null,
                'avatar' => null,
                'birth_date' => null,
            ]);
        }

        $this->command->info(sprintf(
            'Utworzono %d profil(e/ów) dla użytkowników.',
            $users->count()
        ));
    }
}
