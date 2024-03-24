<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CrudGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:generator {--create=mcrtfi} {--model=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $functions = $this->option('create');
        $modelName = $this->option('model');
        $lista = str_split($functions);
        $model = in_array('m', $lista);
        $controller = in_array('c', $lista);
        $request = in_array('r', $lista);
        $test = in_array('t', $lista);
        $factory = in_array('f', $lista);
        $migration = in_array('i', $lista);
        if ($model) {
            $this->comment('Generando modelo...');
            $this->generateElem($modelName, 'Model');
            $this->comment('Generado modelo');
        }
        if ($controller) {
            $this->comment('Generando controlador...');
            $this->generateElem($modelName, 'Controller');
            $this->comment('Generado controlador');
        }
        if ($request) {
            $this->comment('Generando request...');
            $this->generateElem($modelName, 'Request');
            $this->comment('Generado request');
        }
        if ($test) {
            $this->comment('Generando pruebas...');
            $this->generateElem($modelName, 'Test');
            $this->comment('Generado pruebas');

        }
        if ($factory) {
            $this->comment('Generando factory...');
            $this->generateElem($modelName, 'Factory');
            $this->comment('Generado factories');

        }
        if ($migration) {
            $this->comment('Generando migrations...');
            $name = strtolower(Str::snake(Str::plural($modelName)));
            Artisan::call('make:migration create_' . $name . "_table --create=$name");
            $this->comment('Generado migrations');

        }
        $this->createResourcePath($modelName);
        $this->comment('Generado rutas');
    }

    public function getStubs($type): bool|string
    {
        return file_get_contents(resource_path("stubs/$type.stub"));
    }


    /**
     * @param $name
     * This will create file from stub file
     */
    public function generateElem($name, $type): bool
    {
        $model_lower = Str::lower(trim($name)); // lowercase
        $model_plural = Str::plural($model_lower); // plural case
        $model_name = $name;
        $controller_name = $model_name . 'Controller';
        $request_name = $model_name . 'Request';
        $factory_name = $model_name . 'Factory';
        $test_name = $model_name . 'Test';
        $snake_name = Str::lower(Str::snake($name, '-'));
        $snake_under = Str::lower(Str::snake($name));

        $template = str_replace(
            [
                '{{ModelName}}',
                '{{model_lower}}',
                '{{model_plural}}',
                '{{ControllerName}}',
                '{{RequestName}}',
                '{{TestName}}',
                "{{snake_name}}",
                "{{snake_under}}",
            ],
            [
                $model_name,
                $model_lower,
                $model_plural,
                $controller_name,
                $request_name,
                $test_name,
                $snake_name,
                $snake_under
            ],
            $this->getStubs($type)
        );
        if ($type == 'Model') {
            $path = app_path("Models/{$model_name}.php");
            if (!is_file($path))
                file_put_contents($path, $template);
        }
        if ($type == 'Controller') {
            $path = app_path("Http/Controllers/{$controller_name}.php");
            if (!is_file($path))
                file_put_contents($path, $template);
        }
        if ($type == 'Request') {
            $path = app_path("Http/Requests/{$request_name}.php");
            if (!is_file($path))
                file_put_contents($path, $template);
        }
        if ($type == 'Factory') {
            $path = database_path("factories/{$factory_name}.php");
            if (!is_file($path))
                file_put_contents($path, $template);
        }
        if ($type == 'Test') {
            $path = "tests/Feature/{$test_name}.php";
            if (!is_file($path))
                file_put_contents($path, $template);
        }
        return true;
    }

    private function createResourcePath($name): void
    {
        $snake = Str::lower(Str::snake($name, '-'));
        $path_to_file = base_path('routes/api.php');
        $append_route = "Route::apiResource('$snake'," . $name . "Controller::class);\n";
        File::append($path_to_file, $append_route);
    }
}
