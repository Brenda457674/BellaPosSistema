<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear el primer usuario administrador de forma interactiva';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->components->info('Creación de administrador — Bella Flor POS');

        // 1. Verificar que el rol Administrador (id=1) exista
        $rol = Rol::find(1);
        if (!$rol || $rol->nombre !== 'Administrador') {
            $this->components->error('El rol Administrador (id=1) no está disponible en la tabla roles.');
            return 1;
        }

        // 2. Solicitar datos del usuario
        $nombres = $this->ask('Nombres:');
        $apellidos = $this->ask('Apellidos:');
        $email = $this->ask('Correo electrónico:');

        // 3. Validar que el correo no exista
        if (User::where('email', $email)->exists()) {
            $this->components->error('Ya existe un usuario con ese correo electrónico.');
            return 1;
        }

        // 4. Solicitar contraseña oculta y confirmarla
        $password = $this->secret('Contraseña:');
        $passwordConfirm = $this->secret('Confirme la contraseña:');

        if ($password !== $passwordConfirm) {
            $this->components->error('Las contraseñas no coinciden. Inténtelo de nuevo.');
            return 1;
        }

        // 5. Validar longitud mínima de contraseña
        if (strlen($password) < 8) {
            $this->components->error('La contraseña debe tener al menos 8 caracteres.');
            return 1;
        }

        // 6. Crear el usuario administrador
        $user = User::create([
            'nombres' => $nombres,
            'apellidos' => $apellidos,
            'email' => $email,
            'password' => Hash::make($password),
            'rol_id' => 1,
            'estado' => 'activo',
        ]);

        $this->components->info('Usuario administrador creado exitosamente.');
        $this->line(sprintf('  Nombre: %s %s', $nombres, $apellidos));
        $this->line(sprintf('  Correo: %s', $email));
        $this->line(sprintf('  Rol: %s', $rol->nombre));
        $this->line(sprintf('  Estado: activo'));
        $this->line('  ID: ' . $user->id);

        return 0;
    }
}