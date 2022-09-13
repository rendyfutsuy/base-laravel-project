<?php

namespace App\Console\Commands;

use App\Http\Services\Resources\NameSpaceFixer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeHttpSearch extends Command
{
    use NameSpaceFixer;

    protected $basePath = 'App\Http\Services\Searches';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:http-search {class : The name of the migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make Http Search with ROKETIN Project Standard';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $searchName = $this->argument('class');

        if ($searchName === '' || is_null($searchName) || empty($searchName)) {
            $this->error('Http Search Name Invalid..!');
        }

        // create if folder Searches not exists
        if (! File::exists($this->getBaseDirectory($searchName))) {
            File::makeDirectory($this->getBaseDirectory($searchName), 0775, true);
        }

        $title = title($searchName);
        $baseName = $this->getBaseFileName($searchName);

        $searchPath = 'app/Http/Services/Searches/'.$title;
        $filePath = $searchPath.'.php';
        $searchNameSpacePath = $this->getNameSpacePath($this->getNameSpace($searchPath));

        if (! File::exists($filePath)) {
            $eloquentFileContent = "<?php\n\nnamespace ".$searchNameSpacePath.";\n\nuse Illuminate\\Database\\Eloquent\\Model;\n\nclass ".$baseName." extends HttpSearch\n{\n    protected function passable()\n    {\n        return Model::query();\n    }\n\n    protected function filters(): array\n    {\n        return [\n        ];\n    }\n\n    protected function thenReturn(\$".Str::camel($baseName).")\n    {\n        return \$".Str::camel($baseName).";\n    }\n}\n";

            File::put($filePath, $eloquentFileContent);

            $this->info('Http Search Files Created Successfully.');
        } else {
            $this->error('Http Search Files Already Exists.');
        }
    }
}
