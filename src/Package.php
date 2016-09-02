<?php
namespace Laradic\Workbench;

abstract class Package
{
    protected $name;

    /**
     * getComposerFile method
     *
     * @return ComposerFile
     */
    public function getComposerFile()
    {
    }
}