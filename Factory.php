<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Workbench;

use Illuminate\Contracts\View\Factory as View;
use Laradic\Console\Traits\SlugPackageTrait;
use Laradic\Support\Filesystem;
use Laradic\Support\Path;
use Laradic\Support\String;
use Symfony\Component\Finder\Finder;
use Symfony\Component\VarDumper\VarDumper;
use vierbergenlars\SemVer\version;

/**
 * This is the Factory class.
 *
 * @package        Laradic\Workbench
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
class Factory
{

    use SlugPackageTrait;

    /** @var \Laradic\Support\Filesystem */
    protected $files;

    /**
     * @var \Illuminate\View\Factory
     */
    protected $view;

    /**
     * The workbench config as dot notation
     *
     * @var array
     */
    protected $config;

    /**
     * The workbench dir path
     *
     * @var string
     */
    protected $workbenchPath;

    protected $stubsPath = __DIR__ . '/resources/stubs';

    /** Instantiates the class
     *
     * @param \Laradic\Support\Filesystem        $files
     * @param \Illuminate\Contracts\View\Factory $view
     * @param array                              $config
     * @internal param \Illuminate\View\Compilers\BladeCompiler $compiler
     * @internal param \Laradic\Support\Contracts\Parser $parser
     */
    public function __construct(Filesystem $files, View $view, array $config)
    {
        $this->files = $files;
        $this->view  = $view;
        $this->setConfig($config);
    }

    public function exists($slug)
    {
        $packageDir = $this->getPackageDir($slug);

        return $this->files->isDirectory($packageDir);
    }

    public function getPackages()
    {
        $finder   = new Finder;
        $packages = [ ];
        $dirs     = $finder->in($this->workbenchPath)->directories()->depth('== 1')->followLinks();
        $cwd = getcwd();
        foreach ( $dirs as $dir )
        {
            /** @var \SplFileInfo $dir */
            $slug              = String::remove($dir->getPathname(), $this->workbenchPath . '/');
            chdir($dir->getPathname());

            exec('git describe --abbrev=0 --tags 2>&1', $lastTag, $return);

            $version = '';
            if($return === 0)
            {
                $version = with(new version(head($lastTag)))->valid();
            }
            unset($lastTag);

            $packages[ $slug ] = [
                'slug' => $slug,
                'path' => $dir->getPathname(),
                'version' => $version
            ];
            chdir($cwd);
        }

        return $packages;
    }

    public function getPackageDir($slug)
    {
        return Path::join($this->workbenchPath, $slug);
    }


    public function create($slug)
    {
        $packageDir = $this->getPackageDir($slug);
        list($vendor, $package) = $this->getSlugVendorAndPackage($slug);

        $this->mkdir($packageDir);

        $files = [
            'composer.dev.json.stub'                    => 'composer.dev.json',
            'composer.json.stub'                        => 'composer.json',
            'gitignore.stub'                            => '.gitignore',
            'phpunit.xml.stub'                          => 'phpunit.xml',
            'travis.yml.stub'                           => 'travis.yml',
            'resources/config/config.stub'              => false,
            'src/Providers/ConsoleServiceProvider.stub' => 'ConsoleServiceProvider.php',
            'src/PackageServiceProvider.stub'           => ucfirst($package) . 'ServiceProvider.php',
            'src/Console/ListCommand.stub'              => ucfirst($package) . 'ListCommand.php'
        ];

        foreach ( $files as $src => $fileName )
        {
            $segments    = explode('/', $src);
            $srcFileName = last($segments);
            array_pop($segments);
            $srcDir = implode('/', $segments);


            $destinationDir = path_join($packageDir, $srcDir);
            if ( $fileName === false )
            {
                $fileName = String::replace($srcFileName, '.stub', '.php');
            }
            $destinationPath = path_join($destinationDir, $fileName);

            $src = path_join($this->stubsPath, $src);

            if ( ! $this->files->isDirectory($destinationDir) )
            {
                $this->mkdir($destinationDir);
            }

            $content = $this->view
                ->file($src)
                ->with([
                    'open'      => '<?php',
                    'vendor'    => $vendor,
                    'package'   => $package,
                    'namespace' => ucfirst($vendor) . '\\' . ucfirst($package),
                    'config'    => $this->config
                ])
                ->render();


            $this->files->put($destinationPath, $content);
        }
    }


    //
    /* COMPOSER */
    //

    protected function useComposerFile($slug, $file)
    {
        $dir     = $this->getPackageDir($slug);
        $backup  = Path::join($dir, '.composer.json');
        $current = Path::join($dir, 'composer.json');
        $dev     = Path::join($dir, 'composer.dev.json');

        if ( $file === 'dev' and $this->files->exists($dev) )
        {
            $this->files->delete($backup);
            $this->files->copy($current, $backup);
            $this->files->delete($current);
            $this->files->copy($dev, $current);
        }
        elseif ( $file === 'dist' and $this->files->exists($backup) )
        {
            $this->files->delete($current);
            $this->files->copy($backup, $current);
            $this->files->delete($backup);
        }
    }

    public function useComposerDevFile($slug)
    {
        $this->useComposerFile($slug, 'dev');
    }

    public function useComposerDistFile($slug)
    {
        $this->useComposerFile($slug, 'dist');
    }

    /**
     * Call the composer update routine on the path.
     *
     * @param      $slug
     * @param      $command
     * @param bool $dev
     */
    public function callComposer($slug, $command)
    {
        $cwd = getcwd();
        chdir($this->getPackageDir($slug));
        $command = 'composer ' . $command;
        passthru($command);
        chdir($cwd);
    }


    public function callRadicGitInit($slug)
    {
        $cwd = getcwd();
        chdir($this->getPackageDir($slug));
        passthru('radic git:init');
        chdir($cwd);
    }

    /**
     * mkdir
     *
     * @internal string $path,.. paths
     */
    protected function mkdir()
    {
        $this->files->makeDirectory(path_join(func_get_args()), 0755, true);
    }

    //
    /* GETTERS AND SETTERS */
    //

    /**
     * get files value
     *
     * @return Filesystem
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Set the files value
     *
     * @param Filesystem $files
     * @return Factory
     */
    public function setFiles($files)
    {
        $this->files = $files;

        return $this;
    }

    /**
     * get view value
     *
     * @return \Illuminate\View\Factory
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Set the view value
     *
     * @param \Illuminate\View\Factory $view
     * @return Factory
     */
    public function setView($view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * get config value
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set the config value
     *
     * @param array $config
     * @return Factory
     */
    public function setConfig($config)
    {
        $this->config = $config;
        $this->setWorkbenchPath($config[ 'workbench_path' ]);
        $this->setStubsPath($config[ 'stubs_path' ]);

        return $this;
    }

    /**
     * get workbenchPath value
     *
     * @return string
     */
    public function getWorkbenchPath()
    {
        return $this->workbenchPath;
    }

    /**
     * Set the workbenchPath value
     *
     * @param string $workbenchPath
     * @return Factory
     */
    public function setWorkbenchPath($workbenchPath)
    {
        $this->workbenchPath = $workbenchPath;

        return $this;
    }

    /**
     * get stubsPath value
     *
     * @return string
     */
    public function getStubsPath()
    {
        return $this->stubsPath;
    }

    /**
     * Set the stubsPath value
     *
     * @param string $stubsPath
     * @return Factory
     */
    public function setStubsPath($stubsPath)
    {
        $this->stubsPath = $stubsPath;

        return $this;
    }
}
