<?php

namespace App\Console\Commands;

use App\Http\Services\Resources\NameSpaceFixer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeHttpSearchFilter extends Command
{
    use NameSpaceFixer;

    protected $basePath = 'App\Http\Services\Searches\Filters';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:http-search-filter {name : The name of the migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make Http Search\'s Filter with Besar Kecil Project Standard';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filter = $this->argument('name');

        if ($filter === '' || is_null($filter) || empty($filter)) {
            $this->error('Http Search Filter Name Invalid..!');
        }

        // create if folder Searches not exists
        if (! File::exists($this->getBaseDirectory($filter))) {
            File::makeDirectory($this->getBaseDirectory($filter), 0775, true);
        }

        $title = title($filter);
        $baseName = $this->getBaseFileName($filter);

        $filterPath = 'app/Http/Services/Searches/Filters/'.$title;
        $filePath = $filterPath.'.php';
        $filterNameSpacePath = $this->getNameSpacePath($this->getNameSpace($filterPath));

        if (! File::exists($filePath)) {
            $eloquentFileContent = "<?php\n\nnamespace ".$filterNameSpacePath.";\n\nuse Closure;\nuse Illuminate\Database\Eloquent\Builder;\nuse App\Http\Services\Searches\Contracts\FilterContract;\n\nclass ".$baseName." implements FilterContract\n{\n    /** @var string|null */\n    protected \$".Str::camel($baseName).";\n\n    /**\n     * @param  string|null  \$".Str::camel($baseName)."\n     * @return void\n     */\n    public function __construct(\$".Str::camel($baseName).")\n    {\n        \$this->".Str::camel($baseName).' = $'.Str::camel($baseName).";\n    }\n\n    /**\n     * @return mixed\n     */\n    public function handle(Builder \$query, Closure \$next)\n    {\n        if (! \$this->keyword()) {\n            return \$next(\$query);\n        }\n        \$query->where('".Str::snake($baseName)."', 'LIKE', '%'.\$this->".Str::camel($baseName).".'%');\n\n        return \$next(\$query);\n    }\n\n    /**\n     * Get ".Str::camel($baseName)." keyword.\n     *\n     * @return mixed\n     */\n    protected function keyword()\n    {\n        if (\$this->".Str::camel($baseName).") {\n            return \$this->".Str::camel($baseName).";\n        }\n\n        \$this->".Str::camel($baseName)." = request('".Str::snake($baseName)."', null);\n\n        return request('".Str::snake($baseName)."');\n    }\n}\n";

            File::put($filePath, $eloquentFileContent);

            $this->info('Http Search Filter Files Created Successfully.');
        } else {
            $this->error('Http Search Filter Files Already Exists.');
        }
    }
}
