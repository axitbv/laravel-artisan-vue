<?php

namespace Axit\ArtisanVue;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class VueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vue:feature
                    {--force : Overwrite existing views by default}
                    {name}
                    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold a Vue.js feature application with a Vuex store';

    /**
     * The components that need to be exported.
     *
     * @var array
     */
    protected $components = [
        'stubs/components/App.vue.stub' => 'components/App.vue',
        'stubs/index.js.stub' => 'index.js',
        'stubs/store/actions.js.stub' => 'store/actions.js',
        'stubs/store/state.js.stub' => 'store/state.js',
        'stubs/store/mutations.js.stub' => 'store/mutations.js',
        'stubs/store/getters.js.stub' => 'store/getters.js',
        'stubs/store/index.js.stub' => 'store/index.js',
        'stubs/store/mutation_types.js.stub' => 'store/mutation_types.js',
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->ensureDirectoriesExist();

        $this->exportComponents();

        $this->info('Vue feature app scaffolding generated successfully.');
    }

    /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected function ensureDirectoriesExist()
    {
        if (!is_dir($directory = 'resources/js/components/' . Str::kebab($this->argument('name')))) {
            mkdir($directory, 0755, true);
        }

        if (!is_dir($directory = 'resources/js/components/' . Str::kebab($this->argument('name')) . '/components/')) {
            mkdir($directory, 0755, true);
        }

        if (!is_dir($directory = 'resources/js/components/' . Str::kebab($this->argument('name')) . '/store/')) {
            mkdir($directory, 0755, true);
        }
    }

    /**
     * Export the authentication views.
     *
     * @return void
     */
    protected function exportComponents()
    {
        foreach ($this->components as $source => $destination) {
            if (file_exists($target = 'resources/js/components/' . Str::kebab($this->argument('name')) . '/' . $destination) && !$this->option('force')) {
                if (!$this->confirm("The [{$target}] view already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            // get the contents from stub
            $contents = file_get_contents(__DIR__ . '/' . $source);

            // replace the variables inside
            // {{ camelCase }}
            $contents = str_replace('{{ camelCase }}', Str::camel($this->argument('name')), $contents);

            // {{ kebab-case }}
            $contents = str_replace('{{ kebab-case }}', Str::kebab($this->argument('name')), $contents);

            // {{ StudlyCase }}
            $contents = str_replace('{{ StudlyCase }}', Str::studly($this->argument('name')), $contents);

            // {{ Title Case }}
            $contents = str_replace('{{ Title Case }}', Str::title($this->argument('name')), $contents);

            // put the contents on the target destination
            file_put_contents(
                'resources/js/components/' . Str::kebab($this->argument('name')) . '/' . $destination,
                $contents
            );
        }
    }

    // /**
    //  * Export the authentication backend.
    //  *
    //  * @return void
    //  */
    // protected function exportBackend()
    // {
    //     file_put_contents(
    //         app_path('Http/Controllers/HomeController.php'),
    //         $this->compileControllerStub()
    //     );

    //     file_put_contents(
    //         base_path('routes/web.php'),
    //         file_get_contents(__DIR__ . '/Auth/stubs/routes.stub'),
    //         FILE_APPEND
    //     );
    // }

    // /**
    //  * Compiles the "HomeController" stub.
    //  *
    //  * @return string
    //  */
    // protected function compileControllerStub()
    // {
    //     return str_replace(
    //         '{{namespace}}',
    //         $this->laravel->getNamespace(),
    //         file_get_contents(__DIR__ . '/Auth/stubs/controllers/HomeController.stub')
    //     );
    // }
}
