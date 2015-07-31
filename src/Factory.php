<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Workbench;

use Composer\IO\BufferIO;
use Composer\Package\Loader\ValidatingArrayLoader;
use Composer\Util\ConfigValidator;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\View\Factory as View;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Illuminate\View\Compilers\BladeCompiler;
use Stringy\Stringy;
use Symfony\Component\Finder\Finder;
use vierbergenlars\SemVer\version;

/**
 * This is the Factory.
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
    /** @var \Illuminate\Contracts\Filesystem\Filesystem */
    protected $files;

    /** @var \Illuminate\Contracts\View\View */
    protected $view;

    /** @var \Illuminate\Contracts\Config\Repository */
    protected $config;

    /** @var \Illuminate\View\Compilers\BladeCompiler */
    protected $blade;

    /** @var string Absolute path to the workbench directory */
    protected $workbenchPath;

    /** @var  string The workbench directory relative path */
    protected $workbenchDir;

    /** @var string Absolute path to the stubs directory */
    protected $stubsPath;

    /**
     * Instantiates the class
     *
     * @param \Illuminate\Contracts\Filesystem\Filesystem $files
     * @param \Illuminate\Contracts\View\Factory          $view
     * @param \Illuminate\Contracts\Config\Repository     $config
     * @param \Illuminate\View\Compilers\BladeCompiler    $blade
     */
    public function __construct(Filesystem $files, View $view, Repository $config, BladeCompiler $blade)
    {
        $this->files  = $files;
        $this->view   = $view;
        $this->config = $config;
        $this->blade  = $blade;

        $this->workbenchDir  = $this->config->get('laradic.workbench.workbench_dir');
        $this->workbenchPath = base_path($this->workbenchDir);
        $this->stubsPath     = $this->config->get('laradic.workbench.stubs_path');
    }


    /**
     * Generate a new package
     *
     * @param       $slug
     * @param array $files
     * @param array $vars
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function generate($slug, array $files, array $vars = array())
    {
        if ( ! $this->validatePackageName($slug) )
        {
            throw new \InvalidArgumentException("Package name [{$slug}] is not valid");
        }

        if ( $this->packageExists($slug) )
        {
            throw new \RuntimeException("Package [{$slug}] already exists");
        }

        $packageDir = $this->getPackageDir($slug);
        list($vendor, $package) = explode('/', $slug);

        $this->mkdir($packageDir);

        $vars = array_merge_recursive([
            'open'      => '<?php',
            'vendor'    => $vendor,
            'package'   => $package,
            'namespace' => $this->getPackageNamespace($slug),
            'config'    => array_dot($this->config->get('laradic.workbench'))
        ], $vars);

        foreach ( $files as $src => $fileName )
        {
            $segments    = explode('/', $src);
            $srcFileName = last($segments);
            array_pop($segments);
            $srcDir = implode('/', $segments);


            $destinationDir = $this->joinPath($packageDir, $srcDir);
            if ( $fileName === false )
            {
                $fileName = (string)Stringy::create($srcFileName)->replace('.stub', '.php');
            }
            $destinationPath = $this->joinPath($destinationDir, $fileName);

            $src = $this->joinPath($this->stubsPath, $src);

            if ( ! $this->files->isDirectory($destinationDir) )
            {
                $this->mkdir($destinationDir);
            }

            #$content = $this->render($this->files->get($src), $vars);
            $content = $this->view->file($src)->with($vars)->render();


            $this->files->put($destinationPath, $content);
        }
    }

    /**
     * Returns a collection of workbench packages
     *
     * @return array
     */
    public function getPackages()
    {
        $finder   = new Finder;
        $packages = [ ];
        $dirs     = $finder->in($this->workbenchPath)->directories()->depth('== 1')->followLinks();
        $cwd      = getcwd();
        foreach ( $dirs as $dir )
        {
            /** @var \SplFileInfo $dir */
            $slug = (string)Stringy::create($dir->getPathname())->removeLeft($this->workbenchPath . '/');
            chdir($dir->getPathname());

            # version
            exec('git describe --abbrev=0 --tags 2>&1', $lastTag, $return);

            $version = '';
            if ( $return === 0 )
            {
                $version = new version(head($lastTag));
            }
            unset($lastTag);

            # branch
            exec('git symbolic-ref -q HEAD', $ref);
            $branch = last(explode('/', head($ref)));


            $packages[ $slug ] = [
                'slug'    => $slug,
                'path'    => $dir->getPathname(),
                'version' => $version,
                'branch'  => $branch
            ];
            chdir($cwd);
        }

        return $packages;
    }

    /**
     * Get the patch to the package directory
     *
     * @param            $slug
     * @param bool|false $relative
     * @return string
     */
    public function getPackageDir($slug, $relative = false)
    {
        if ( $relative === true )
        {
            return $this->joinPath($this->workbenchDir, $slug);
        }

        return $this->joinPath($this->workbenchPath, $slug);
    }

    /**
     * Get the namespace for the provided package name
     *
     * @param $slug
     * @return string
     */
    public function getPackageNamespace($slug)
    {
        list($vendor, $package) = explode('/', $slug);

        return Str::studly($vendor) . '\\' . Str::studly($package);
    }

    /**
     * Checks if the package exists in the workbench
     *
     * @param $slug
     * @return bool
     */
    public function packageExists($slug)
    {
        $packageDir = $this->getPackageDir($slug);

        return $this->files->isDirectory($packageDir);
    }

    /**
     * Checks if the package name is valid
     *
     * @param $slug
     * @return bool
     */
    public function validatePackageName($slug)
    {
        if ( ! preg_match('/([a-z]*)\/([a-z]*)/', $slug, $matches) or count($matches) !== 3 )
        {
            return false;
        }

        return true;
    }

    /**
     * Merges and saves the given array to the root composer.json file
     *
     * @param array $merge
     */
    public function updateComposerFile(array $merge = array())
    {
        $composerFilePath = base_path('composer.json');
        $originalConfig   = json_decode($this->files->get($composerFilePath), true);
        $newConfig        = array_merge_recursive($originalConfig, $merge);
        if ( $this->validateComposer($newConfig) )
        {
            $this->files->put($composerFilePath, json_encode($newConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES, 4));
        }
    }

    /**
     * Validates the given array with the composer schema
     *
     * @param array $data
     * @return bool
     */
    public function validateComposer(array $data)
    {
        $tmpFile = storage_path(uniqid(time(), false) . 'composer-validate.json');
        $this->files->put($tmpFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES, 4));
        $validator = new ConfigValidator(new BufferIO());
        $checkAll  = ValidatingArrayLoader::CHECK_ALL;
        list($errors, $publishErrors, $warnings) = $validator->validate($tmpFile, $checkAll);
        $this->files->delete($tmpFile);

        return count($errors) === 0;
    }

    /**
     * Runs the given composer command(s) in the root project directory
     *
     * @param $command
     */
    public function composerCommand($command)
    {
        $args = func_get_args();
        $cwd  = getcwd();
        chdir(base_path());

        if ( func_num_args() === 1 and is_array($args[ 0 ]) )
        {
            $args = $args[ 0 ];
        }

        foreach ( $args as $i => $arg )
        {
            passthru('composer ' . $arg);
        }

        chdir($cwd);
    }

    /**
     * Runs the given shell commands in the package directory
     *
     * @param $slug
     * @param $commands
     */
    public function packageCommand($slug, $commands)
    {
        if ( ! $this->packageExists($slug) )
        {
            return;
        }

        if ( ! is_array($commands) )
        {
            $commands = [ $commands ];
        }

        $cwd = getcwd();
        chdir($this->getPackageDir($slug));

        foreach ( $commands as $command )
        {
            passthru($command);
        }

        chdir($cwd);
    }

    public function getPackageGitBranch($slug)
    {
        $cwd = getcwd();
        chdir($this->getPackageDir($slug));

        exec('git symbolic-ref -q HEAD', $ref);
        $branch = last(explode('/', head($ref)));

        chdir($cwd);

        return $branch;
    }

    public function getPackageVersion($slug)
    {
        $cwd = getcwd();
        chdir($this->getPackageDir($slug));

        exec('git describe --abbrev=0 --tags 2>&1', $lastTag, $return);

        if ( $return === 0 )
        {
            $version = head($lastTag);
        }
        else
        {
            $version = '0.0.0';
        }
        unset($lastTag);
        chdir($cwd);

        return new version($version);
    }

    /**
     * Joins a split file system path.
     *
     * @param string $path,... Array or parameters of strings , The split path.
     *
     * @return string The joined path.
     */
    public function joinPath()
    {
        $args = func_get_args();
        if ( func_num_args() === 1 and is_array($args[ 0 ]) )
        {
            $args = $args[ 0 ];
        }
        foreach ( $args as $i => $arg )
        {
            $str = Stringy::create($args[ $i ]);
            $str->removeRight('/');
            if ( $i > 0 )
            {
                $str->removeLeft('/');
            }
            $args[ $i ] = (string)$str;
        }

        return implode(DIRECTORY_SEPARATOR, $args);
    }

    /**
     * mkdir
     *
     * @internal string $path,.. paths
     */
    protected function mkdir()
    {
        $this->files->makeDirectory($this->joinPath(func_get_args()), 0755, true);
    }
}
