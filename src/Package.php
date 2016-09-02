<?php
namespace Laradic\Workbench;

use Illuminate\Contracts\Support\Arrayable;

abstract class Package implements Arrayable
{
    protected $name;

    /**
     * composer method
     *
     * @var ComposerFile
     */
    protected $composer;

    /**
     * getComposerFile method
     *
     * @return ComposerFile
     */
    public function getComposerFile()
    {
    }

    public function toArray()
    {
        return $this->composer->toArray();
    }
}