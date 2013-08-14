<?php

namespace Kalani\LaravelReflector;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class LaravelReflector extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'doc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shows documentation for a given class or alias.';

    protected $app;
    protected $config;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($app, $config)
    {
        $this->app = $app;
        $this->config = $config;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $name = $this->argument('name');

        $root = $this->getRoot($name);
        $reflector = new \ReflectionClass($root);
        $methods = $this->getMethods($reflector);
        $properties = $this->getProperties($reflector);
        $constants = $this->getConstants($reflector);

        $parent = $reflector->getParentClass();

        // Write the output
        echo PHP_EOL . 'Class:  ' . $name . PHP_EOL;
        echo 'Full:   ' . $reflector->getName() . PHP_EOL; 
        echo 'File:   ' . $reflector->getFileName() . PHP_EOL; 
        if ($parent) {
            echo 'Parent: ' . $parent->getName() . PHP_EOL;
        }

        echo PHP_EOL;

        $this->write('Constants:', $constants);
        $this->write('Properties:', $properties);
        $this->write('Methods:', $methods);
    }

    public function getMethods(\ReflectionClass $reflector)
    {
        return $this->getArray($reflector->getMethods());
    }

    public function getProperties(\ReflectionClass $reflector)
    {
        return $this->getArray($reflector->getProperties());
    }

    public function getConstants(\ReflectionClass $reflector)
    {
        return $reflector->getConstants();
    }

    /**
     * Returns an associative array of each item and it's related documentation
     */
    protected function getArray($items)
    {
        $itemArray = array();
        foreach($items as $item) {
            if (is_object($item) && method_exists($item, 'getDocComment')) {
                $comment = $item->getDocComment();
                $parser = new \phpDocumentor\Reflection\DocBlock($comment);
                $desc = $parser->getShortDescription();
                $desc = str_replace("\n", ' ', $desc);
                $prefix = $this->itemPrefix($item);
                $itemArray[$prefix . $item->getName()] = $desc;
            } else {
                $itemArray['  ' . $item] = '';
            }
        }
        ksort($itemArray);
        return $itemArray;
    }

    protected function itemPrefix($item)
    {
        if ($item->isPublic()) {
            return '  ';
        }
        if ($item->isProtected()) {
            return '- ';
        }
        if ($item->isPrivate()) {
            return 'x ';
        }
        return '* ';
    }

    /**
     * Get the root class name for a given alias or facade
     */
    public function getRoot($name)
    {
        $alias = $this->getAlias($name);
        if ($alias && ! $this->isFacade($alias)) {
            return $alias;
        }

        try {
            $facade = $this->getFacade($name);
            return $facade; 
        } catch (\Exception $e) {
            echo($e->getMessage());
        }

        return Null;
    }

    public function getAlias($name)
    {
        $aliases = $this->config->get('app.aliases');
        if (isset($aliases[$name])) {
            return $aliases[$name];
        }
    }

    public function isFacade($name)
    {
        if (strpos($name, 'Facade') > 0) {
            return True;
        }

        $class = $this->app->make($name);
        if (method_exists($class, 'getFacadeRoot')) {
            return True;
        }

        return False;
    }

    public function getFacade($name)
    {
        $class = $this->app->make($name);

        if (method_exists($class, 'getFacadeRoot')) {
            $root = $class->getFacadeRoot();
            return get_class($root);
        }
        return $name;
    }

    public function write($name, $array)
    {
        if (empty($array)) {
            return;
        }

        echo $name . PHP_EOL;
        foreach ($array as $key => $value) {
            printf("%-30s %s".PHP_EOL, $key, $value);
        }
        echo PHP_EOL;
    }


    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('name', InputArgument::REQUIRED, 'The class or facade for which to get information.'),
        );
    }

}