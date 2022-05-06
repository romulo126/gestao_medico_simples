<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class especialidadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       
        $especialidades = [
            ['nome' => 'Alergologia'],
            ['nome' => 'Angiologia'],
            ['nome' => 'Buco maxilo'],
            ['nome' => 'Cardiologia clínca'],
            ['nome' => 'Cardiologia infantil'],
            ['nome' => 'Cirurgia cabeça e pescoço'],
            ['nome' => 'Cirurgia cardíaca'],
            ['nome' => 'Cirurgia de tórax'],
            
        ];

        foreach ($especialidades as $especialidade) {
            \App\Models\especialidade::create($especialidade);
        }
    }
}
