<?php

namespace Database\Seeders;

use App\Models\Click;
use App\Models\Link;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'demo@example.com'],
            ['name' => 'Usuario Demo', 'password' => Hash::make('password')]
        );

        // Si ya se ejecutó antes, borramos los enlaces demo para no duplicar.
        $user->links()->delete();

        $links = [
            ['code' => 'laravel', 'url' => 'https://laravel.com/docs', 'weight' => 5],
            ['code' => 'portfolio', 'url' => 'https://github.com/tu-usuario', 'weight' => 3],
            ['code' => 'blog', 'url' => 'https://ejemplo.com/articulos/como-aprender-laravel-rapido', 'weight' => 2],
            ['code' => 'cv', 'url' => 'https://ejemplo.com/mi-cv.pdf', 'weight' => 1],
        ];

        // Valor => peso (más peso = más frecuente).
        $browsers = ['Chrome' => 55, 'Safari' => 20, 'Firefox' => 12, 'Microsoft Edge' => 10, 'Opera' => 3];
        $systems  = ['Windows' => 45, 'Android' => 25, 'iOS' => 15, 'Mac' => 10, 'GNU/Linux' => 5];
        $devices  = ['desktop' => 55, 'smartphone' => 38, 'tablet' => 7];
        $referers = [
            null => 30,
            'https://www.google.com/' => 25,
            'https://twitter.com/' => 15,
            'https://www.linkedin.com/' => 15,
            'https://www.facebook.com/' => 10,
            'https://github.com/' => 5,
        ];

        foreach ($links as $data) {
            $link = $user->links()->create([
                'code' => $data['code'],
                'original_url' => $data['url'],
            ]);

            $rows = [];
            $total = $data['weight'] * 120; // 600, 360, 240, 120 clics

            for ($i = 0; $i < $total; $i++) {
                $rows[] = [
                    'link_id'    => $link->id,
                    'ip'         => long2ip(random_int(1, 4294967295)),
                    'user_agent' => null,
                    'browser'    => $this->pick($browsers),
                    'os'         => $this->pick($systems),
                    'device'     => $this->pick($devices),
                    'referer'    => $this->pick($referers),
                    'created_at' => $this->randomDate(),
                ];
            }

            foreach (array_chunk($rows, 200) as $chunk) {
                Click::insert($chunk);
            }
        }
    }

    /**
     * Elige una clave al azar respetando los pesos.
     */
    private function pick(array $weighted): ?string
    {
        $roll = random_int(1, array_sum($weighted));
        $acc = 0;

        foreach ($weighted as $value => $weight) {
            $acc += $weight;
            if ($roll <= $acc) {
                return $value === '' ? null : $value;
            }
        }

        return null;
    }

    /**
     * Fecha aleatoria de los últimos 30 días, con más clics en los días recientes.
     */
    private function randomDate(): string
    {
        // Elevar al cuadrado sesga el número hacia 0 => más clics recientes.
        $daysAgo = (int) floor((random_int(0, 1000) / 1000) ** 2 * 30);

        return now()
            ->subDays($daysAgo)
            ->setTime(random_int(7, 23), random_int(0, 59), random_int(0, 59))
            ->toDateTimeString();
    }
}