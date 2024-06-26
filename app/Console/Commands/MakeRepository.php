<?php

namespace App\Console\Commands;

use App\Http\Services\Resources\NameSpaceFixer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeRepository extends Command
{
    use NameSpaceFixer;

    protected $basePath = 'App\Http\Repositories';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository-contract {class : The name of the migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make Repository and Contract with Besar Kecil Project Standard';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $className = $this->argument('class');

        if ($className === '' || is_null($className) || empty($className)) {
            $this->error('Name Invalid..!');
        }

        $isContractCreated = $this->createContract($className);

        if (! $isContractCreated) {
            return;
        }

        $this->createRepository($className);
    }

    /** @return bool  */
    public function createContract($class)
    {
        // create if folder Contract not exists
        if (! File::exists($this->getBaseDirectory('Contracts\\'.$class))) {
            File::makeDirectory($this->getBaseDirectory('Contracts\\'.$class), 0775, true);
        }

        $title = title($class).'Contract';
        $baseName = $this->getBaseFileName($class).'Contract';

        $contractPath = 'app/Http/Repositories/Contracts/'.$title;
        $filePath = $contractPath.'.php';
        $contractNameSpacePath = $this->getNameSpacePath($this->getNameSpace($contractPath));

        if (! File::exists($filePath)) {
            $fileContent = "<?php\n\nnamespace ".$contractNameSpacePath.";\n\ninterface ".$baseName."\n{\n    /**\n     * @return mixed\n     */\n    public function customRules();\n}\n";

            File::put($filePath, $fileContent);

            $this->info('Contract Created Successfully.');

            return true;
        } else {
            $this->error('Contract Already Exists.');

            return false;
        }
    }

    /** @return bool  */
    public function createRepository($class)
    {
        // create if folder Repositories not exists
        if (! File::exists($this->getBaseDirectory($class))) {
            File::makeDirectory($this->getBaseDirectory($class), 0775, true);
        }

        $titleContract = title($class).'Contract';
        $contract = $this->getBaseFileName($class).'Contract';

        $contractPath = 'app/Http/Repositories/Contracts/'.$titleContract;
        $contractNameSpace = Str::ucfirst($this->getNameSpace($contractPath));

        $title = $this->getBaseFileName($class);
        $titleRepository = title($class).'Repository';
        $baseName = $this->getBaseFileName($class).'Repository';

        $repoPath = 'app/Http/Repositories/'.$titleRepository;
        $filePath = $repoPath.'.php';

        $repositoryNamespacePath = $this->getNameSpacePath($this->getNameSpace($repoPath));

        if (! File::exists($filePath)) {
            $fileContent = "<?php\n\nnamespace ".$repositoryNamespacePath.";\n\nuse ".$contractNameSpace.";\n\nclass ".$baseName.' extends BaseRepository implements '.$contract."\n{\n    /** @var ".$title." */\n    protected \$".Str::camel($title).";\n\n    public function __construct(".$title.' $'.Str::camel($title).")\n    {\n        parent::__construct(\$".Str::camel($title).");\n        \$this->".Str::camel($title).' = $'.Str::camel($title).";\n    }\n\n    public function customRules()\n    {\n        return;\n    }\n}\n";

            File::put($filePath, $fileContent);

            $this->info('Repository Created Successfully.');

            return true;
        } else {
            $this->error('Repository Already Exists.');

            return false;
        }
    }
}
