<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Http\Services\Resources\NameSpaceFixer;

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
    protected $description = 'Make Repository and Contract for it';

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
        
        $title = title($class)."Contract";
        $baseName = $this->getBaseFileName($class)."Contract";

        $contractPath = 'app/Http/Repositories/Contracts/' . $title;
        $filePath = $contractPath . '.php';
        $contractNameSpacePath = $this->getNameSpacePath($this->getNameSpace($contractPath));
   
        if(! File::exists($filePath)) {
            $fileContent = "<?php\n\nnamespace ". $contractNameSpacePath .";\n\ninterface ". $baseName ."\n{\n\t/**\n\t * @return mixed\n\t */\n\tpublic function customRules();\n}";

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

        $titleContract = title($class)."Contract";
        $contract = $this->getBaseFileName($class)."Contract";

        $contractPath = 'app/Http/Repositories/Contracts/' . $titleContract;
        $contractNameSpace = Str::ucfirst($this->getNameSpace($contractPath));

        $title = $this->getBaseFileName($class);
        $titleRepository = title($class)."Repository";
        $baseName = $this->getBaseFileName($class)."Repository";

        $repoPath = 'app/Http/Repositories/' . $titleRepository;
        $filePath = $repoPath . '.php';
        
        $repositoryNamespacePath = $this->getNameSpacePath($this->getNameSpace($repoPath));
        
        if(! File::exists($filePath)) {
            $fileContent = "<?php\n\nnamespace ". $repositoryNamespacePath .";\n\nuse ". $contractNameSpace .";\nuse App\\Http\\Repositories\\BaseRepository;\n\nclass " . $baseName . " extends BaseRepository implements ". $contract ."\n{\n\t/** @var ". $title ." */\n\tprotected \$".Str::camel($title).";\n\n\tpublic function __construct(". $title ." \$".Str::camel($title).")\n\t{\n\t\tparent::__construct(\$". Str::camel($title) .");\n\t\t\$this->". Str::camel($title) ." = \$". Str::camel($title) .";\n\t}\n\n\tpublic function customRules()\n\t{\n\t\treturn;\n\t}\n}";

            File::put($filePath, $fileContent);

            $this->info('Repository Created Successfully.');
            return true;
        } else {
            $this->error('Repository Already Exists.');
            return false;
        }
    }
}
